<?php
class SMA_Database {
	private $config = array();
	private $connected = false;
	private $link = NULL;
	private $statement = '';
	private $result = NULL;
	private $message = '';
	public function __construct($config = array()) {
		$this->config['host'] = 'localhost';
		$this->config['user'] = 'root';
		$this->config['pass'] = 'abcdurs1';
		$this->config['data'] = 'sma';
		$this->config['port'] = ini_get('mysqli.default_port');
		$this->config['sock'] = ini_get('mysqli.default_socket');
		if (is_array($config)) {
			foreach ($config as $item => $value) {
				if (array_key_exists($item, $this->config)) {
					$this->config[$item] = $value;
				}
			}
		}
		$this->connect();
	}
	public function connect() {
		if (!$this->connected) {
			$this->link = new \mysqli($this->config['host'],$this->config['user'],$this->config['pass'],$this->config['data'],$this->config['port'],$this->config['sock']);
			if (mysqli_connect_error()) {
				throw new \Exception('Mysqli connection error: ' . $this->link->connect_error, $this->link->connect_errno);
			} else {
				$this->connected = true;
			}
		}
	}
	public function prepare($query, $params = array()) {
		$params = array_map(array($this->link,'real_escape_string'), $params);
		array_unshift($params, $query);
		$query = call_user_func_array('sprintf', $params);
		$this->statement = $query;
	}
	public function execute() {
		if (empty($this->statement)) {
			return false;
		}
		$this->result = $this->link->query($this->statement);
		$this->statement = '';
		return $this->fetch_records();
	}
	public function fetch_records() {
		if (!$this->result) {
			$this->message = 'No rows found';
			return false;
		}
		if (is_object($this->result)) {
			$r = array();
			while ($res = $this->result->fetch_object()) {
				$r[] = $res;
			}
			return $r;
		} elseif ($this->result) {
			return true;
		} else {
			return false;
		}
	}
}
?>
