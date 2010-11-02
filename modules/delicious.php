<?php
class Delicious extends BMModule {
	public $name = 'Delicious';
	public $icon = 'delicious.png';
	public $registrationUrl = 'https://secure.delicious.com/register';
	
	protected function _login(Array $data) {
		$http = $this->http;
		$fp = new FormsParser;
		
		$page = $http->get('https://secure.delicious.com/login');
		
		// ѕолучаем ссылку на страницу авторизации Yahoo
		if (!preg_match('/"(https:\/\/login.yahoo.com.*?)"/is', $page, $loginLink)) {
			return false;
		}
		$loginLink = $loginLink[1];
		
		$page = $http->get($loginLink);
		if (!$postdata = $fp->getForm($page, array('login', 'passwd'))) {
			return false;
		}
		
		$postdata['login'] = $this->login;
		$postdata['passwd'] = $this->password;
		
		$page = $http->post(http::fixUrl($loginLink, $fp->action), $postdata);
		if (!preg_match('/signedInAs/is', $page)) {
			return false;
		}
		
		return true;
		/*
		$postdata = array(
			'username' => $this->login,
			'password' => $this->password
		);
		$options = array(
			'testCondition' => 'Signed in as',
		);
		return $this->process('https://secure.delicious.com/login', $postdata, $options);
		*/
	}
	
	protected function _addBookmark(Array $data) {
		extract($data);
		
		$postdata = array(
			'url' => $url
		);
		$options = array(
			'testCondition' => 'Now add tags and notes',
			'cache' => false
		);
		if (!$this->process('http://www.delicious.com/save', $postdata, $options)) {
			return false;
		}
		$postdata = array(
			'title' => $name,
			'notes' => $description,
			'tags' => implode(' ', $tags),
			'share' => false
		);
		$options = array(
			'testCondition' => 'taggedlink',
			'fromBuffer' => true,
			'cache' => false,
		);
		
		if (!$this->process('http://www.delicious.com/save', $postdata, $options)) {
			return false;
		}

		$this->bookmarkUrl = $this->http->current_url;
		return true;
	}
}
?>