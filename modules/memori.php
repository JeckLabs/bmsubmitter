<?php
class Memori extends BMModule {
	public $name = 'Memori';
	public $icon = 'memori.png';
	public $registrationUrl = 'http://memori.ru/registration/';
	
	protected function _login(Array $data) {
		$postdata = array(
			'login' => $this->login,
			'password' => $this->password
		);
		$options = array(
			'testCondition' => 'Вошли как',
		);
		return $this->process('http://memori.ru/loginform/', $postdata, $options);
	}
	
	protected function _addBookmark(Array $data) {
		extract($data);
		$postdata = array(
			'url' => $url,
			'title' => $name,
			'description' => $description,
			'tags' => implode(', ', $tags),
		);
		$options = array(
			'testCondition' => 'bookmarks',
			'cache' => false
		);
		$this->bookmarkUrl = 'http://memori.ru/'.urlencode($this->login).'?sort=date';
		return $this->process('http://memori.ru/linkadd', $postdata, $options);
	}
}
?>