<?php
chdir('../');
require './init.php';

$action = $_GET['action'];

switch ($action) {
	case 'remove':
		$groupName = $_GET['group'];
		$Groups->remove($groupName);
		$data = $groupName;
	break;
}

echo json_encode($data);

?>