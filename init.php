<?php
@error_reporting(0);
@set_time_limit(0);
define('VERSION', '2.1');

if (!file_exists('./data/install.lock')) {
	header('Location: ./install.php');
	exit();
}

require './data/config.php';
require './libs/translator.php';

$login = false;

if (isset($_COOKIE['password'])){
    if ($_COOKIE['password'] == PASSWORD){
        $login = true;
    }
}
if (!$login && isset($_POST['password'])) {
    if (md5($_POST['password']) == PASSWORD){
        $login = true;
		setCookie('password', md5($_POST['password']), time() + 365*24*3600);
		header('Location: ./');
		exit();
	}
}

if (!$login && !defined('LOGIN_PAGE')){
    header('Location: ./login.php');
	exit();
}
if ($login && defined('LOGIN_PAGE')){
	header('Location: ./');
	exit();
}


set_include_path(implode(PATH_SEPARATOR, $includePaths));

function __autoload($name) {
	$name = strtolower($name);
	include $name.'.php';
}

function array_map_r($call, $a) {
	foreach($a as $key => $value) {
		if (is_array($value)) {
			$a[$key] = array_map_r($call, $value);
		} else {
			$a[$key] = call_user_func($call, $value);
		}
	}
	return $a;
}

if (get_magic_quotes_gpc()) {
	$_POST = array_map_r('stripslashes', $_POST);
	$_GET = array_map_r('stripslashes', $_GET);
	$_REQUEST = array_map_r('stripslashes', $_REQUEST);
	$_COOKIE = array_map_r('stripslashes', $_COOKIE);
}

//die();
$History = new History(HISTORY_FILE);
$Groups = new Groups(GROUPS_FILE);
$Passwords = new Passwords(PASSWORDS_FILE);
$Profiles = new Profiles(PROFILES_FILE);
Translator::loadTable(TRANSLATOR_FILE);

$Modules = array();
foreach (glob(MODULES_PATH.'/*.php') as $modulePath) {
	$moduleClass = pathinfo($modulePath, PATHINFO_FILENAME);
	$moduleClass = mb_strtolower($moduleClass);
	$module = new $moduleClass;
	if ($module instanceOf BMModule && $module->name) {
		$counts = array();
		$passwordsData = $Passwords->get($moduleClass);
		foreach ($Profiles->get() as $profile) {
			if (isset($passwordsData[$profile])) {
				$counts[$profile] = count($passwordsData[$profile]);
			} else {
				$counts[$profile] = 0;
			}
		}
		//print_r($counts);
		$Modules[$moduleClass] = array(
			'name' => $module->name,
			'icon' => $module->icon,
			'registrationUrl' => $module->registrationUrl,
			'passwordsCount' => $counts,
		);
	}
}

header('Content-type: text/html; charset=utf-8');
?>