<?php

require './init.php';

$moduleName = 'Delicious';

$bookmarkData = array();
$bookmarkData['url'] = 'http://ya.ru';
$bookmarkData['name'] = 'Yandex';
$bookmarkData['description'] = 'Какое то глупое описание';
$bookmarkData['tags'] = array();

try {
	$module = new $moduleName;

	if (!$module instanceOf BMModule) {
		throw new Exception('MODULE_NOT_COMPATIBLE');
	}
	
	$module->setDebug(true);

	$passwordData = $Passwords->get($moduleName);
	$passwordData = $passwordData[$profile];
	$passwordData = $passwordData[array_rand($passwordData)];
	$module->login($passwordData);

	$module->addBookmark($bookmarkData);
	echo '<pre style="font: 10pt monospace;text-align: left;">';
	echo 'SUCCESS '.$module->bookmarkUrl."\r\n";
	echo '</pre>';
} catch (Exception $e) {
	echo '<pre style="font: 10pt monospace;text-align: left;">';
	echo 'EXCEPTION '.$e->getMessage()."\r\n";
	echo '</pre>';
}

?>