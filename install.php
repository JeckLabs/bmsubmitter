<?php
@error_reporting(0);
header('Content-type: text/html; charset=utf-8');

require './libs/translator.php';
Translator::loadTable('./data/translation.dat');
//Ошибка авторизации

if (file_exists('./data/install.lock')) {
	exit('Already installed');
}

$extensions = array('mbstring', 'json', 'iconv', 'curl', 'dom');
$errors = array();

if (PHP_VERSION < 5.2) {
	$errors[] = _e('Вы используете устаревший php обновите до %f', 5.2);
}

foreach ($extensions as $extension) {
	if (!extension_loaded($extension)) {
		$errors[] = _e('Не установленно расширение %s', $extension);
	}
}

$accessError = false;
if (!is_writable('./data')) {
	$accessError = true;
	$errors[] = _e('Нет доступа к папке "%s"', './data');
}

$groups = 'a:1:{s:16:"Основной";a:13:{i:0;s:8:"bobrdobr";i:1;s:9:"communizm";i:2;s:9:"delicious";i:3;s:14:"diggcollection";i:4;s:5:"klikz";i:5;s:10:"linkomatic";i:6;s:6:"memori";i:7;s:10:"misterwong";i:8;s:8:"moemesto";i:9;s:10:"ontrackday";i:10;s:11:"stozakladok";i:11;s:11:"zakladoknet";i:12;s:7:"znatoki";}}';
$profiles = 'a:1:{i:0;s:16:"Основной";}';
$config = "<?php

define('AC_KEY', '');

define('PASSWORD', '%PASSWORD%');
define('MODULES_PATH', './modules');
define('HISTORY_FILE', './data/history.dat');
define('GROUPS_FILE', './data/groups.dat');
define('PASSWORDS_FILE', './data/%PASSWORDS_PATH%.dat');
define('PROFILES_FILE', './data/profiles.dat');
define('TRANSLATOR_FILE', './data/translation.dat');

\$includePaths = array(
	'./libs/core',
	'./libs',
	MODULES_PATH	
);

?>";

if (isset($_POST['password']) && count($errors) == 0) {
	$password = $_POST['password'];
	 
	if (get_magic_quotes_gpc()) {
		$password = stripslashes($password);
	}
	
	$config = str_replace('%PASSWORD%', md5($password), $config);
	$config = str_replace('%PASSWORDS_PATH%', md5(microtime()), $config);
	
	file_put_contents('./data/config.php', $config);
	file_put_contents('./data/install.lock', 'installed');
	file_put_contents('./data/groups.dat', $groups);
	file_put_contents('./data/profiles.dat', $profiles);
	
	header('location: ./');
}
include './templates/install.php';
?>