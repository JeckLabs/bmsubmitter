<?php

class Scuttle extends BMModule {
	public $name = false;
	
	protected function _login(Array $data) {
		$postdata = array(
			'username' => $this->login,
			'password' => $this->password
		);
		$options = array(
			'testCondition' => strtolower($this->login),
		);
		$host = parse_url($this->registrationUrl, PHP_URL_HOST);
		return $this->process('http://'.$host.'/login.php/', $postdata, $options);
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
		$host = parse_url($this->registrationUrl, PHP_URL_HOST);
		$this->bookmarkUrl = 'http://'.$host.'/'.urlencode($this->login);
		return $this->process('http://'.$host.'/'.urlencode($this->login).'?action=add', $postdata, $options);
	}
}

?>