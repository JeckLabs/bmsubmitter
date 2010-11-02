<?php
class BobrDobr extends BMModule {
	public $name = 'БобрДобр';
	public $icon = 'bobrdobr.png';
	public $registrationUrl = 'http://bobrdobr.ru/registration/';
	
	protected function _login(Array $data) {
		$postdata = array(
			'username' => $this->login,
			'password' => $this->password,
			'remember_user' => ''
		);
		$options = array(
			'testCondition' => '/logout/',
			'cache' => false
		);
		return $this->process('http://bobrdobr.ru/', $postdata, $options);
	}
	
	protected function _addBookmark(Array $data) {
		extract($data);
		$postdata = array(
			'url' => $url,
			'name' => $name,
			'description' => $description,
			'tags' => implode(', ', $tags)
		);
		$options = array(
			'testType' => 'redirect'
		);
		$this->bookmarkUrl = 'http://bobrdobr.ru/people/'.urlencode($this->login).'/';
		return $this->process('http://bobrdobr.ru/add/', $postdata, $options);
	}
}
?>