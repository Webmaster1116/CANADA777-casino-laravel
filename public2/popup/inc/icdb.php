<?php
define('OBJECT', 'OBJECT');
define('object', 'OBJECT');
define('OBJECT_K', 'OBJECT_K');
define('ARRAY_A', 'ARRAY_A');
define('ARRAY_N', 'ARRAY_N');

class ICDB {
	var $server = "";
	var $port = "";
	var $db = "";
	var $user = "";
	var $password = "";
	var $prefix = "";
	var $insert_id;
	var $link;
	var $use_mysqli = false;

	function __construct($_server, $_port, $_db, $_user, $_password, $_prefix) {
		$this->server = $_server;
		$this->port = $_port;
		$this->db = $_db;
		$this->user = $_user;
		$this->password = $_password;
		$this->prefix = $_prefix;
		$host = $this->server;
		if (!empty($this->port)) $host .= ':'.$this->port;
		if (function_exists('mysqli_connect')) {
			$this->use_mysqli = true;
			$this->link = mysqli_connect($host, $this->user, $this->password);
			if (!$this->link) throw new Exception(sprintf(esc_html__('Could not connect: %s', 'hap'), mysqli_connect_error()));
			if (!mysqli_select_db($this->link, $this->db)) throw new Exception(sprintf(esc_html__('Can not use database: %s', 'hap'), mysqli_error($this->link)));
			if (!mysqli_query($this->link, 'SET NAMES utf8')) throw new Exception(sprintf(esc_html__('Invalid query: %s', 'hap'), mysqli_error($this->link)));
		} else {
			$this->link = mysql_connect($host, $this->user, $this->password);
			if (!$this->link) throw new Exception(sprintf(esc_html__('Could not connect: %s', 'hap'), mysql_error()));
			if (!mysql_select_db($this->db, $this->link)) throw new Exception(sprintf(esc_html__('Can not use database: %s', 'hap'), mysql_error($this->link)));
			if (!mysql_query('SET NAMES utf8', $this->link)) throw new Exception(sprintf(esc_html__('Invalid query: %s', 'hap'), mysql_error($this->link)));
		}
	}

	function query($_sql) {
		if ($this->use_mysqli) {
			$result = mysqli_query($this->link, $_sql);
			if (!$result) throw new Exception(sprintf(esc_html__('Invalid query: %s', 'hap'), mysqli_error($this->link)));
		} else {
			$result = mysql_query($_sql, $this->link);
			if (!$result) throw new Exception(sprintf(esc_html__('Invalid query: %s', 'hap'), mysql_error($this->link)));
		}
		if (preg_match('/^\s*(insert|replace)\s/i', $_sql)) {
			if ($this->use_mysqli) {
				$this->insert_id = mysqli_insert_id($this->link);
			} else {
				$this->insert_id = mysql_insert_id($this->link);
			}
		}
		return $result;
	}

	function get_var($_sql, $_x = 0, $_y = 0) {
		$result = $this->query($_sql);
		$rows = array();
		if ($this->use_mysqli) {
			while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
				$rows[] = $row;
			}
			mysqli_free_result($result);
		} else {
			while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
				$rows[] = $row;
			}
			mysql_free_result($result);
		}
		if (sizeof($rows) <= $_y) return null;
		else if (!isset($rows[$_y][$_x])) return null;
		
		return $rows[$_y][$_x];
	}

	function get_row($_sql, $_output = 'OBJECT', $_y = 0) {
		$result = $this->query($_sql);
		$rows = array();
		if ($this->use_mysqli) {
			while ($row = mysqli_fetch_object($result)) {
				$rows[] = $row;
			}
			mysqli_free_result($result);
		} else {
			while ($row = mysql_fetch_object($result)) {
				$rows[] = $row;
			}
			mysql_free_result($result);
		}
		if (sizeof($rows) <= $_y) return null;
		if ($_output == OBJECT || strtoupper($_output) === OBJECT) {
			return $rows[$_y] ? $rows[$_y] : null;
		} else if ($_output == ARRAY_A) {
			return $rows[$_y] ? get_object_vars($rows[$_y]) : null;
		} else if ($_output == ARRAY_N) {
			return $rows[$_y] ? array_values(get_object_vars($rows[$_y])) : null;
		}
		return null;
	}
	
	function get_results($_sql, $_output = OBJECT) {
		$result = $this->query($_sql);
		$rows = array();
		if ($this->use_mysqli) {
			while ($row = mysqli_fetch_object($result)) {
				$rows[] = $row;
			}
			mysqli_free_result($result);
		} else {
			while ($row = mysql_fetch_object($result)) {
				$rows[] = $row;
			}
			mysql_free_result($result);
		}
		$new_array = array();
		if ($_output == OBJECT || strtoupper( $_output ) === OBJECT) {
			return $rows;
		} else if ($_output == OBJECT_K) {
			foreach ($rows as $row) {
				$var_by_ref = get_object_vars($row);
				$key = array_shift($var_by_ref);
				if (!isset($new_array[$key])) $new_array[$key] = $row;
			}
			return $new_array;
		} else if ( $_output == ARRAY_A || $_output == ARRAY_N ) {
			if ($rows) {
				foreach ($rows as $row ) {
					if ( $_output == ARRAY_N ) {
						$new_array[] = array_values(get_object_vars($row));
					} else {
						$new_array[] = get_object_vars($row);
					}
				}
			}
			return $new_array;
		}
		return null;
	}

	function escape_string($_string) {
		if ($this->use_mysqli) {
			return mysqli_real_escape_string($this->link, $_string);
		}
		return mysql_real_escape_string($_string, $this->link);
	}
	function esc_like($_text) {
		return addcslashes($_text, '_%\\');
	}
}
?>