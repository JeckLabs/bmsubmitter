<?php

require './init.php';

if (isset($_GET['action'])) {
	$action = $_GET['action'];
} else {
	$action = 'passwords';
}

$path = preg_replace('#/[^/]+$#i', '/', $_SERVER['SCRIPT_NAME']);
$bmUrl = 'http://'.$_SERVER['SERVER_NAME'].$path;

switch ($action) {
	case 'passwords_export':
		header('Content-type: plain/text');
		header('Content-Disposition: attachment; filename="passwords.txt"');
		readfile(PASSWORDS_FILE);
	break;
	case 'passwords_importold':
		include './templates/passwords_importold.php';
	break;
	case 'passwords_import':
		include './templates/passwords_import.php';
	break;
	case 'groups':
		if (isset($_POST['modules'])) {
			$groupName = $_POST['groupName'];
			$data = $_POST['modules'];
			$Groups->set($groupName, $data);
		}
		include './templates/groups.php';
	break;
	case 'profiles':
		if (isset($_POST['profileName'])) {
			$profileName = $_POST['profileName'];
			$Profiles->add($profileName);
		}
		include './templates/profiles.php';
	break;
	case 'passwords':
	default:
		include './templates/passwords.php';
	break;
}


?>