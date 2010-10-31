<?php
chdir('../');
require './init.php';

$action = $_GET['action'];

$data = false;
switch ($action) {
	case 'empty':
		$History->removeAll();
		$data = true;
	break;
	case 'loadLast':
		$id = max($History->get());
		$data = $History->get($id);
		$data = $data['data'];
		$data['tags'] = implode(', ', $data['tags']);
	break;
}

echo json_encode($data);
?>