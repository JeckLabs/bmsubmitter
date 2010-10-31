<?php

class Groups extends Storage {	
	function set($group, $modules) {
		$modules = array_values(array_unique($modules)); 
		$modules = array_map('mb_strtolower', $modules);
		parent::set($group, $modules);
	}
	function get($group = null) {
		global $Modules;
		
		$modules = parent::get($group);
		if (!is_null($group)) {
			foreach ($modules as $key => $moduleName) {
				if (!isset($Modules[$moduleName])) {
					unset($modules[$key]);
				}
			}
		}
		return $modules;
	}
}

?>