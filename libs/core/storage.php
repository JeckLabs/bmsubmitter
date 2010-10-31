<?php

abstract class Storage {
	protected $data = array();
	protected $filename;
	
	public function __construct($filename) {
		$this->filename = $filename;
		$this->load();
	}
	
	public function set($key, $value) {
		$this->data[$key] = $value;
		$this->save();
	}
	
	public function add($key, $value=null) {
		if (!is_null($value)) {
			(array)$this->data[$key][] = $value;
		} else {
			(array)$this->data[] = $key;
		}
		$this->save();
	}
	
	public function get($key=null) {
		$this->load();
		if (is_null($key)) {
			return array_keys($this->data);
		}
		if (isset($this->data[$key])) {
			return $this->data[$key];
		}
		return false;
	}
	
	public function remove($key) {
		unset($this->data[$key]);
		$this->save();
	}
	public function removeAll() {
		$this->data = array();
		$this->save();
	}
	
	public function count() {
		return count($this->data);
	}
	public function save() {
		file_put_contents($this->filename, serialize($this->data));
	}
	
	public function load() {
		if (!file_exists($this->filename)) {
			$this->save();
		}
		$data = file_get_contents($this->filename);
		$this->data = unserialize($data);
		if (!$this->data) {
			$this->data = array();
		}
	}
}

?>