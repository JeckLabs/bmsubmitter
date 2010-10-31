<?php

include './init.php';

function __autoload($name) {
	$name = strtolower($name);
	if (file_exists('./libs/'.$name.'.php')) {
		require './libs/'.$name.'.php';
	}
} 

if (isset($_POST['action'])) {
	$http = new http;
	$fp = new FormsParser;
	if ($_POST['action'] == 'getLoginForm') {
		$page = $http->GET($_POST['formUrl'], 'UTF-8');
		$forms = $fp->getForms($page);
		include './templates/loginform.html';
	} 
	if ($_POST['action'] == 'getAddForm') {
		parse_str($_POST['loginData'], $loginData);
		$postfields = parseFields(
			$loginData['loginForm'],
			array(
				'login' => $_POST['login'],
				'password' => $_POST['password']
			)
		);
		$cm = new CModule;
		$http->cookies = $cm->getLoginCookies($_POST['loginFormUrl'], $postfields);
		$page = $http->GET($_POST['formUrl'], 'UTF-8');
		$forms = $fp->getForms($page);
		include './templates/addform.html';
	}
}

?>