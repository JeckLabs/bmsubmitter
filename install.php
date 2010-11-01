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

$includePaths = array(
	'./libs/core',
	'./libs',
	'./modules'
);
set_include_path(implode(PATH_SEPARATOR, $includePaths));

function __autoload($name) {
	$name = strtolower($name);
	include $name.'.php';
}

$modules = array();
foreach (glob('./modules/*.php') as $modulePath) {
	$moduleClass = pathinfo($modulePath, PATHINFO_FILENAME);
	$moduleClass = mb_strtolower($moduleClass);
	$module = new $moduleClass;
	if ($module instanceOf BMModule && $module->name) {
		$modules[] = $moduleClass;
	}
}

$Groups = new Groups('./data/groups.dat');
$Groups->set('Основной', $modules);

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
	file_put_contents('./data/profiles.dat', $profiles);
	
	header('location: ./');
}
include './templates/install.php';
?>