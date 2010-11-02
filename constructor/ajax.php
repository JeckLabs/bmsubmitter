<?php

include './init.php';


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
		if (!isset($loginData['loginForm'])) {
			exit;
		}
		$postfields = parseFields(
			$loginData['loginForm'],
			array(
				'login' => $_POST['login'],
				'password' => $_POST['password']
			)
		);
		$data = array(
			'testCondition' => $_POST['loginTestString']
		);
		$cm = new CModule;
		$http->cookies = $cm->getLoginCookies($_POST['loginFormUrl'], $postfields, $data);
		$page = $http->GET($_POST['formUrl'], 'UTF-8');
		$forms = $fp->getForms($page);
		include './templates/addform.html';
	}
}

?>