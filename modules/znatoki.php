<?php
class Znatoki extends BMModule {
	public $name = 'Знатоки';
	public $icon = 'znatoki.png';
	public $registrationUrl = 'http://znatoki.ru/register.php/';
	
	protected function _login(Array $data) {
		$postdata = array(
			'username' => $this->login,
			'password' => $this->password
		);
		$options = array(
			'testCondition' => '',
		);
		return $this->process('http://znatoki.ru/login.php/', $postdata, $options);
	}
	
	protected function _addBookmark(Array $data) {
		extract($data);
		$postdata = array(
			'address' => $url,
			'title' => $name,
			'description' => $description,
			'tags' => implode(', ', $tags),
		);
		$options = array(
			'testCondition' => 'success'
		);
		$this->bookmarkUrl = 'http://znatoki.ru/bookmarks.php/'.urlencode($this->login);
		return $this->process('http://znatoki.ru/bookmarks.php/'.urlencode($this->login).'?action=add', $postdata, $options);
	}
}
?>