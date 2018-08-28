<?php

namespace Agility\Logger;

use Agility\Configuration;
use Agility\Console\Helpers\EchoHelper;
use Agility\Logger\Psr\LogLevel;

	trait PsrImplementor {

		use EchoHelper;

		function alert($message, $context = []) {
			$this->log(LogLevel::ALERT, $message, $context);
		}

		function critical($message, $context = []) {
			$this->log(LogLevel::CRITICAL, $message, $context);
		}

		function debug($message, $context = []) {
			$this->log(LogLevel::DEBUG, $message, $context);
		}

		function emergency($message, $context = []) {
			$this->log(LogLevel::EMERGENCY, $message, $context);
		}

		function error($message, $context = []) {
			$this->log(LogLevel::ERROR, $message, $context);
		}

		function info($message, $context = []) {
			$this->log(LogLevel::INFO, $message, $context);
		}

		function log($level, $message, $context = []) {

			$message = "[".date("Y-m-d H:i:s")."]  ".strtoupper($level)."\t".$message."\n";
			if (Configuration::environment() != "production") {

				$this->echo($message);
				return;

			}

			if ($level == LogLevel::DB && Configuration::logDbQueries()) {
				$this->writeLog($message, Configuration::dbLog());
			}
			else if (in_array($level, [LogLevel::DEBUG, LogLevel::INFO, LogLevel::NOTICE, LogLevel::WARNING]) && Log::LogLevels[$level] <= Configuration::logLevel()) {
				$this->writeLog($message, Configuration::infoLog());
			}
			else if ($level == LogLevel::ERROR) {
				$this->writeLog($message, Configuration::errorLog());
			}
			else if (in_array($level, [LogLevel::ALERT, LogLevel::CRITICAL, LogLevel::EMERGENCY])) {

				$this->writeLog($message, Configuration::errorLog());
				$this->notifyAdmin($message, $level);

			}

		}

		function notice($message, $context = []) {
			$this->log(LogLevel::NOTICE, $message, $context);
		}

		protected function notifyAdmin($message, $level) {

		}

		protected function prepareLogFile($file) {

			// Some 148 bytes less than 2 GB
			if ($file->size > 2147483500) {
				$this->renameLogFile($file);
			}

		}

		protected function renameLogFile($file) {

			$newName = $file->name.".1";
			$files = $file->parent->find($file->name.".*");
			if (!$files->empty) {
				$newName = $files->last->extension + 1;
			}

			rename($file->path, $file->cwd."/".$newName);
			$file->touch;

		}

		function warning($message, $context = []) {
			$this->log(LogLevel::WARNING, $message, $context);
		}

		protected function writeLog($message, $file) {

			$this->prepareLogFile($file);
			error_log($message, 3, $file);

		}

	}

?>