<?php

class Groups extends Storage {	
	function set($group, $modules) {
		$modules = array_values(array_unique($modules)); 
		$modules = array_map('mb_strtolower', $modules);
		parent::set($group, $modules);
	}
}

?>