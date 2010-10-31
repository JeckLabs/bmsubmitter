<?php

class History extends Storage {
	
	function get($key=null) {
		if (is_null($key)) {
			foreach (parent::get() as $id) {
				$data = parent::get($id);
				if (count($data['modules']) == 0) {
					parent::remove($id);
				}
			}
			return array_reverse(parent::get());
		}
		return parent::get($key);
	}
	
	function getId($data) {
		$ids = parent::get();
		if (!$ids) {
			$id = 1;
		} else {
			$id = (max($ids) + 1);
		}
		$historyData  = array('date' => time(), 'data' => $data, 'modules' => array());
		parent::set($id, $historyData);
		return $id;
	}
	
	function set($id, $module, $moduleData) {
		$module = mb_strtolower($module);
		$data = parent::get($id);
		$data['modules'][$module] = $moduleData;
		parent::set($id, $data);
	}
}


?>