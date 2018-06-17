<?php

namespace Agility\Data\Connection\Mysql;

use Agility\Configuration;
use Agility\Data\Connection\Base;
use Aqua;
use Aqua\Visitors\MysqlVisitor;
use Exception;
use Ds\Queue;
use PDO;

	class MysqlConnector extends Base {

		protected $_instanceType;

		protected $_host;
		protected $_port;
		protected $_unixSocket;
		protected $_charSet;
		protected $_dbName;
		protected $_username;
		protected $_password;
		protected $_extraConfig;
		protected $_tablePrefix;

		function __construct($connectionArray, $instanceType) {

			parent::__construct($connectionArray);

			$this->_instanceType = $instanceType;
			$this->_configure($connectionArray);

			return true;

		}

		protected function _configure($config) {

			$this->_host = $this->_setHost($config);
			$this->_port = $this->_setPort($config);
			$this->_unixSocket = $this->_setUnixSocket($config);

			$this->_charSet = $this->_setCharacterSet($config);

			$this->_dbName = $this->_setDBName($config);
			$this->_username = $this->_setUsername($config);
			$this->_password = $this->_setPassword($config);

			$this->_extraConfig = $this->_setExtraConfig($config);

			/*$i = 0;
			while ($i < $this->_poolSize) {

				$connection = $this->getPdoConnection(
						$this->_setDsn(
							$this->_dbName, $this->_host, $this->_port, $this->_unixSocket, $this->_charSet),
						$this->_username, $this->_password, $this->_extraConfig
					);
				$this->_connectionPool->push($connection);

				$i++;

			}*/

		}

		protected function _connect() {

			return $this->getPdoConnection(
					$this->_setDsn(
						$this->_dbName, $this->_host, $this->_port, $this->_unixSocket, $this->_charSet),
					$this->_username, $this->_password, $this->_extraConfig
				);

		}

		function delete($query, $params = []) {

			$sql = $query;
			if (is_a($query, "Aqua\\Visitors\\MysqlVisitor")) {

				$sql = $query->query;
				$params = $query->params;

			}

			return $this->_executeQuery($query, $params, 1);

		}

		function exec($query, $params = []) {

			$sql = $query;
			if (is_a($query, "Aqua\\Visitors\\MysqlVisitor")) {

				$sql = $query->query;
				$params = $query->params;

			}

			return $this->_executeQuery($sql, $params, 1);

		}

		function execute($query, $params = []) {

			if (is_string($query)) {
				return $this->query($query, $params);
			}
			else {

				$mysqlVisitor = new MysqlVisitor;
				$query->toSql($mysqlVisitor);
				if (is_a($query, Aqua\SelectStatement::class)) {
					return $this->query($mysqlVisitor);
				}
				else if (is_a($query, Aqua\InsertStatement::class)) {
					return $this->insert($mysqlVisitor);
				}
				else if (is_a($query, Aqua\UpdateStatement::class)) {
					return $this->update($mysqlVisitor);
				}
				else if (is_a($query, Aqua\DeleteStatement::class)) {
					return $this->delete($mysqlVisitor);
				}
				else if (is_a($query, Aqua\DescribeStatement::class)) {
					return $this->query($mysqlVisitor);
				}

			}

		}

		protected function _executeQuery($sql, $params, $type = 0) {

			$connection = $this->_connect();
			$this->_logQuery($connection, $sql, $params);
			$stmt = $connection->prepare($sql);

			if ($type == 0) {
				$stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->_instanceType);
			}

			if (!empty($params)) {

				if (is_array($params[0])) {

					foreach ($params as $paramsSet) {
						$this->_executeQueryForParamsSet($stmt, $paramsSet);
					}

				}
				else {
					$this->_executeQueryForParamsSet($stmt, $params);
				}

			}
			else {

				try {
					$stmt->execute();
				}
				catch(Exception $e) {
					die($e->getMessage());
				}

			}

			$result = false;
			if ($type == 0) {
				$result = $stmt->fetchAll();
			}
			else {

				$result = $stmt->rowCount();
				if ($type == 2 && $stmt->rowCount() > 0) {
					$result = $connection->lastInsertId();
				}

			}

			$stmt->closeCursor();
			$stmt = null;
			$connection = null;

			return $result;

		}

		protected function _executeQueryForParamsSet($stmt, $paramsSet) {

			try {
				$stmt->execute($paramsSet);
			}
			catch(Exception $e) {
				die($e->getMessage());
			}

		}

		private function _getConfiguration($config, $key, $errorString, $exception = true) {

			if (!isset($config[$key])) {

				if ($exception) {
					throw new MysqlConnectionException($errorString);
				}
				else {
					Logger::log($errorString);
				}

			}

			return $config[$key];

		}

		function getTypeMapper() {
			return new MysqlTypes;
		}

		function insert($query, $params = []) {

			$sql = $query;
			if (is_a($query, "Aqua\\Visitors\\MysqlVisitor")) {

				$sql = $query->query;
				$params = $query->params;

			}

			return $this->_executeQuery($query, $params, 2);

		}

		protected function _logQuery($connection, $sql, $params, $return = false) {

			foreach ($params as $param) {
				$sql = preg_replace("/\?/", $connection->quote($param), $sql, 1);
			}

			if ($return) {
				return $sql;
			}

			echo $sql."\n";

		}

		function query($query, $params = []) {

			$sql = $query;
			if (is_a($query, "Aqua\\Visitors\\MysqlVisitor")) {

				$sql = $query->query;
				$params = $query->params;

			}

			return $this->_executeQuery($sql, $params);

		}

		private function _setHost($connectionConfig) {

			if (!isset($connectionConfig["host"])) {
				return "127.0.0.1";
			}

			return $connectionConfig["host"];

		}

		private function _setPort($config) {
			return $config["port"] ?? null;
		}

		private function _setUnixSocket($config) {
			return $config["unix_socket"] ?? null;
		}

		private function _setDBName($config) {
			return $this->_getConfiguration($config, "database", "Database name not specified.");
		}

		private function _setUsername($config) {
			return $this->_getConfiguration($config, "username", "Username not specified.");
		}

		private function _setPassword($config) {
			return $this->_getConfiguration($config, "password", "Password not specified. Using empty password", false);
		}

		private function _setCharacterSet($config) {
			return $config["charset"] ?? null;
		}

		private function _setExtraConfig($config) {

			$configuration = [];
			if (isset($config["config"])) {

				if (!empty($config["config"]["persistent"]) || intval($config["config"]["persistent"]) != 0) {
					$configuration[PDO::ATTR_PERSISTENT] = true;
				}

			}

			$configuration[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			// if (Configuration::environment() == "development") {
			// 	$configuration[PDO::ATTR_ERRMODE] |= PDO::ERRMODE_WARNING;
			// }

			return $configuration;

		}

		// If both hostname and Unix socket are specified, precedence will be given to the Unix socket
		private function _setDsn($db, $host = null, $port = null, $unixSocket = null, $charSet = null) {

			if (empty($unixSocket) && empty($host)) {
				throw new MysqlConnectionException("Cannot connect to Mysql database, neither host nor unix socket is specified.");
			}

			return "mysql:dbname=".$db.(!empty($unixSocket) ? ";unix_socket=".$unixSocket : "").(empty($unixSocket) && !empty($host) ? ";host=".$host.(!empty($port) ? ";port=".$port : "") : "").(!empty($charSet) ? ";charset=".$charSet : "");

		}

		function toSql($query, $params = []) {

			$sql = $query;
			if (is_string($query)) {
				return $this->_logQuery($query, $params, true);
			}
			else {

				$mysqlVisitor = new MysqlVisitor;
				$query->toSql($mysqlVisitor);

				return $this->_logQuery($mysqlVisitor->query, $mysqlVisitor->params, true);

			}

		}

		function update($query, $params = []) {

			$sql = $query;
			if (is_a($query, "Aqua\\Visitors\\MysqlVisitor")) {

				$sql = $query->query;
				$params = $query->params;

			}

			return $this->_executeQuery($query, $params, 1);

		}

	}

?>