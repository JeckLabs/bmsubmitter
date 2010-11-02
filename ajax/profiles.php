<?php
ob_start();

chdir('../');
require './init.php';

$action = $_GET['action'];

switch ($action) {
	case 'remove':
		$profile = $_GET['profile'];
		$Profiles->remove($profile);
		$data = $profile;
	break;
}
ob_end_clean();
echo json_encode($data);

?>