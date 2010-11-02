<?php
ob_start();

chdir('../');
require './init.php';


$action = $_GET['action'];
$data = array();
switch ($action) {
	case 'getId':
		$historyData = array(
			'url' => $_GET['url'],
			'name' => $_GET['title'],
			'tags' => preg_split('/\s*,\s*/is', $_GET['tags']),
			'description' => $_GET['description']
		);
		$data = $History->getId($historyData);
	break;
	case 'start':
		$id = $_GET['id'];
		$moduleName = $_GET['module'];
		$profile = $_GET['profile'];
		$data['module'] = $moduleName;
		
		
		try {
			$module = new $moduleName;
			
			if (!$module instanceOf BMModule) {
				throw new Exception('MODULE_NOT_COMPATIBLE');
			}
		
			$passwordData = $Passwords->get($moduleName);
			$passwordData = $passwordData[$profile];
			$passwordData = $passwordData[array_rand($passwordData)];
			$module->login($passwordData);
			$historyData = $History->get($id);
			
			$bookmarkData = $historyData['data'];
			$bookmarkData['name'] = AnchorGenerator::getText($bookmarkData['name']);
			$bookmarkData['description'] = AnchorGenerator::getText($bookmarkData['description']);
			
			$module->addBookmark($bookmarkData);
			
			$History->set($id, $moduleName, array('url' => $module->bookmarkUrl));
			
			$data['status'] = 'success';
			$data['login'] = $passwordData['login'];
			$data['url'] = $module->bookmarkUrl;
		} catch (Exception $e) {
			$data['status'] = 'error';
			$data['login'] = $passwordData['login'];
			switch ($e->getMessage()) {
				case 'CANT_LOGIN':
					$data['msg'] = _e('Ошибка авторизации');
				break;
				case 'CANT_ADD_BOOKMARK':
					$data['msg'] = _e('Невозможно добавить закладку');
				break;
				case 'MODULE_NOT_COMPATIBLE':
					$data['msg'] = _e('Модуль не совместим');
				break;
			}
		}
	break;
}

ob_end_clean();

echo json_encode($data);

?>