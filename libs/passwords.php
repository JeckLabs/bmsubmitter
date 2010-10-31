<?php

class Passwords extends Storage {	
	function set($module, $data) {
		$module = mb_strtolower($module);
		parent::set($module, $data);
	}
	
	function importOld($importData, $profile) {
		$data = array();
		$importData = trim($importData);
		$importData = preg_split('/[\r\n]+/', $importData);
		for ($i=0;$i<count($importData);$i++) {
			list($moduleName, $passwords) = explode('--,', $importData[$i], 2);
			$moduleName = trim($moduleName);
			$moduleName = str_replace('-', '', $moduleName);
			$passwords = explode(',', $passwords);
			$data[$moduleName][$profile] = array();
			foreach ($passwords as $password) {
				list($login, $password) = explode(':', $password);
				$login = trim($login);
				$password = trim($password);
				$data[$moduleName][$profile][] = array(
					'login' => $login,
					'password' => $password
				);
			}
		}
		$this->import($data);
	}
	
	function import($importData) {
		foreach ($importData as $moduleName => $data) {
			$oldData = parent::get($moduleName);
			if (!$oldData) {
				$oldData = array();
			}
			foreach ($oldData as $oldProfile => $passwords) {
				if (isset($data[$oldProfile])) {
					$data[$oldProfile] = $this->mergeData($data[$oldProfile], $data[$oldProfile]);
				} else {
					$data[$oldProfile] = $passwords;
				}
			}
			parent::set($moduleName, $data);
		}
	}
	
	private function mergeData($oldData, $data) {
		if (!$oldData) {
			return $data;
		}
		$logins = array();
		foreach ($data as $key => $entry) {
			if (!in_array($entry['login'], $logins)) {
				$logins[] = $entry['login'];
			} else {
				unset($data[$key]);
				continue;
			}
			for ($i=0;$i<count($oldData);$i++) {
				if ($oldData[$i]['login'] == $entry['login']) {
					$oldData[$i]['password'] = $entry['password'];
					unset($data[$key]);
					continue 2;
				}
			}
		}
		return array_values($oldData + $data);
	}
	
	function get($module, $cookies=false) {
		$module = mb_strtolower($module);
		if ($data = parent::get($module)) {
			if (!$cookies) {
				for ($i=0;$i<count($data);$i++) {
					if (isset($data[$i]['cookies'])) {
						unset($data[$i]['cookies']);
					}
				}
			}
		} else {
			$data = array();
		}
		return $data;
	}
}

?>