<?php
ob_start();

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

ob_end_clean();
echo json_encode($data);

?>