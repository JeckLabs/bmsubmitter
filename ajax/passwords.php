<?php
chdir('../');
require './init.php';

$action = $_GET['action'];

switch ($action) {
	case 'get':
		$moduleName = $_GET['module'];
		$profileName = $_GET['profile'];
		$data = $Passwords->get($moduleName);
		if (isset($data[$profileName])) {
			$data = $data[$profileName];
			for ($i=0;$i<count($data);$i++) {
				$data[$i] = implode(';', $data[$i]);
			}
			$data = implode("\r\n", $data);
		} else {
			$data = '';
		}
	break;
	case 'set':
		$moduleName = $_GET['module'];
		$profileName = $_GET['profile'];
		$data = $_GET['data'];
		$data = trim($data);
		$data = preg_replace('/[\r\n,]+/is', "\r\n", $data);
		$data = explode("\r\n", $data);
		$result = array();
		for ($i=0;$i<count($data);$i++) {
			@list($login, $password) = preg_split('/[;:]/i', $data[$i]);
			$login = trim($login);
			$password = trim($password);
			if (!empty($login) || !empty($password)) {
				$result[] = array('login' => $login, 'password' => $password);
			}
		}
		$data = $result;
		$passwordsData = $Passwords->get($moduleName);
		$passwordsData[$profileName] = $data;
		$Passwords->set($moduleName, $passwordsData);
	break;
	case 'import':
		if ($data = @unserialize($_POST['data'])) {
			$Passwords->import($data);
		} else {
			$profile = $_POST['profile'];
			$Passwords->importOld($_POST['data'], $profile);
		}
		$data = true;
	break;
	/*
	case 'importOld':
		$Passwords->importOld($_GET['data']);
	break;
	*/
	case 'export':
		$profileName = $_GET['profile'];
		$data = array();
		foreach ($Modules as $moduleName => $info) {
			if ($exportData = $Passwords->get($moduleName)) {
				$data[$moduleName] = $exportData[$profileName];
			}
		}
		$data = serialize($data);
	break;
}

echo json_encode($data);
?>