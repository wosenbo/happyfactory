<?php

class dbstuff {
	private $version = '';
	private $querynum = 0;
	private $link;
	private $stmt;

	public function connect($dbhost, $dbuser, $dbpw, $dbname = '', $pconnect = 0, $halt = TRUE) {
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

		try {
			if ($pconnect) {
				$this->link = mysqli_connect('p:' . $dbhost, $dbuser, $dbpw, $dbname);
			} else {
				$this->link = mysqli_connect($dbhost, $dbuser, $dbpw, $dbname);
			}

			$dbcharset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
			if (defined('API_MODE')) {
				$dbcharset = 'utf8mb4';
			}

			mysqli_set_charset($this->link, $dbcharset);
			mysqli_query($this->link, "SET sql_mode=''");

		} catch (Exception $e) {
			if ($halt) {
				$this->halt('Can not connect to MySQL server', $e->getMessage());
			}
			return false;
		}
	}

	public function select_db($dbname) {
		return mysqli_select_db($this->link, $dbname);
	}

	public function prepare($sql) {
		$this->stmt = mysqli_prepare($this->link, $sql);
		return $this->stmt;
	}

	public function execute($params = []) {
		if (!$this->stmt) return false;

		if (!empty($params)) {
			$types = '';
			$bind_params = [];

			foreach ($params as $param) {
				if (is_int($param)) {
					$types .= 'i';
				} elseif (is_double($param)) {
					$types .= 'd';
				} else {
					$types .= 's';
				}
				$bind_params[] = $param;
			}

			array_unshift($bind_params, $this->stmt, $types);
			call_user_func_array('mysqli_stmt_bind_param', $bind_params);
		}

		mysqli_stmt_execute($this->stmt);
		$this->querynum++;
		return $this->stmt;
	}

	public function query($sql, $type = '') {
		try {
			if ($type == 'UNBUFFERED') {
				$query = mysqli_query($this->link, $sql, MYSQLI_USE_RESULT);
			} else {
				$query = mysqli_query($this->link, $sql, MYSQLI_STORE_RESULT);
			}

			if (!$query) {
				throw new Exception(mysqli_error($this->link));
			}

			$this->querynum++;
			return $query;

		} catch (Exception $e) {
			if (in_array(mysqli_errno($this->link), [2006, 2013]) && substr($type, 0, 5) != 'RETRY') {
				$this->close();
				require './config.inc.php';
				$this->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
				return $this->query($sql, 'RETRY'.$type);
			} elseif ($type != 'SILENT' && substr($type, 5) != 'SILENT') {
				$this->halt('MySQL Query Error', $sql . ' - ' . $e->getMessage());
			}
			return false;
		}
	}

	public function fetch_array($query, $result_type = MYSQLI_ASSOC) {
		if ($query instanceof mysqli_stmt) {
			$result = mysqli_stmt_get_result($query);
			return mysqli_fetch_array($result, $result_type);
		}
		return mysqli_fetch_array($query, $result_type);
	}

	public function fetch_first($sql, $params = []) {
		if (!empty($params)) {
			$this->prepare($sql);
			$this->execute($params);
			return $this->fetch_array($this->stmt);
		}
		return $this->fetch_array($this->query($sql));
	}

	public function result_first($sql, $params = []) {
		if (!empty($params)) {
			$this->prepare($sql);
			$this->execute($params);
			$result = mysqli_stmt_get_result($this->stmt);
			$row = mysqli_fetch_row($result);
			return $row[0] ?? null;
		}

		$result = $this->query($sql);
		$row = mysqli_fetch_row($result);
		return $row[0] ?? null;
	}

	public function affected_rows() {
		return mysqli_affected_rows($this->link);
	}

	public function error() {
		return mysqli_error($this->link);
	}

	public function errno() {
		return mysqli_errno($this->link);
	}

	public function result($query, $row = 0) {
		if ($query instanceof mysqli_stmt) {
			$result = mysqli_stmt_get_result($query);
			$data = mysqli_fetch_row($result);
			return $data[$row] ?? null;
		}

		$data = mysqli_fetch_row($query);
		return $data[$row] ?? null;
	}

	public function num_rows($query) {
		if ($query instanceof mysqli_stmt) {
			$result = mysqli_stmt_get_result($query);
			return mysqli_num_rows($result);
		}
		return mysqli_num_rows($query);
	}

	public function num_fields($query) {
		if ($query instanceof mysqli_stmt) {
			$result = mysqli_stmt_get_result($query);
			return mysqli_num_fields($result);
		}
		return mysqli_num_fields($query);
	}

	public function free_result($query) {
		if ($query instanceof mysqli_stmt) {
			mysqli_stmt_free_result($query);
			mysqli_stmt_close($query);
		} else {
			mysqli_free_result($query);
		}
		$this->stmt = null;
	}

	public function insert_id() {
		return mysqli_insert_id($this->link);
	}

	public function fetch_row($query) {
		if ($query instanceof mysqli_stmt) {
			$result = mysqli_stmt_get_result($query);
			return mysqli_fetch_row($result);
		}
		return mysqli_fetch_row($query);
	}

	public function fetch_fields($query) {
		if ($query instanceof mysqli_stmt) {
			$result = mysqli_stmt_get_result($query);
			return mysqli_fetch_field($result);
		}
		return mysqli_fetch_field($query);
	}

	public function fetch_all($query, $result_type = MYSQLI_ASSOC) {
		if ($query instanceof mysqli_stmt) {
			$result = mysqli_stmt_get_result($query);
			return mysqli_fetch_all($result, $result_type);
		}
		return mysqli_fetch_all($query, $result_type);
	}

	public function version() {
		if (empty($this->version)) {
			$this->version = mysqli_get_server_info($this->link);
		}
		return $this->version;
	}

	public function close() {
		if ($this->stmt) {
			mysqli_stmt_close($this->stmt);
			$this->stmt = null;
		}
		return mysqli_close($this->link);
	}

	public function escape_string($string) {
		return mysqli_real_escape_string($this->link, $string);
	}

	public function begin_transaction() {
		return mysqli_begin_transaction($this->link);
	}

	public function commit() {
		return mysqli_commit($this->link);
	}

	public function rollback() {
		return mysqli_rollback($this->link);
	}

	private function halt($message = '', $sql = '') {
		$error = "";
		$errno = "";
		if($this->link){
			$error = mysqli_error($this->link);
			$errno = mysqli_errno($this->link);
		}

		if (defined('DEBUG') && DEBUG) {
			echo 'SQL Error: ' . htmlspecialchars($message) . '<br>';
			echo 'Error Code: ' . $errno . '<br>';
			echo 'Error Message: ' . htmlspecialchars($error) . '<br>';
			echo 'SQL: ' . htmlspecialchars($sql) . '<br>';
		} else {
			echo 'Database error occurred. Please try again later.';
		}

		error_log('DB Error [' . $errno . ']: ' . $error . ' - SQL: ' . $sql);
	}
}

?>
