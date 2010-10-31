<?php
class Communizm extends BMModule {
	public $name = 'Коммунизм';
	public $icon = 'communizm.png';
	public $registrationUrl = 'http://communizm.ru/index.php?mode=register';
	
	protected function _login(Array $data) {
		$postdata = array(
			'login' => $this->login,
			'password' => $this->password
		);
		$options = array(
			'testCondition' => 'Добро пожаловать',
		);
		return $this->process('http://communizm.ru/', $postdata, $options);
	}
	
	protected function _addBookmark(Array $data) {
		extract($data);
		$postdata = array(
			'link' => $url, 
			'title' => $name, 
			'description' => $description, 
			'tags' => implode(', ', $tags), 
			'subm' => 'Донести'
		);
		$this->http->POST('http://communizm.ru/index.php?mode=addpost', $postdata);
		$this->bookmarkUrl = 'http://communizm.ru/index.php?mode=profile&username='.urlencode($this->login);
		return true;
	}
}
?>