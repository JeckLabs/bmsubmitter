<?php
class Delicious extends BMModule {
	public $name = 'Delicious';
	public $icon = 'delicious.png';
	public $registrationUrl = 'https://secure.delicious.com/register';
	
	protected function _login(Array $data) {
		$postdata = array(
			'username' => $this->login,
			'password' => $this->password
		);
		$options = array(
			'testCondition' => 'Signed in as',
		);
		return $this->process('https://secure.delicious.com/login', $postdata, $options);
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
		if (!$this->process('http://delicious.com/save', $postdata, $options)) {
			return false;
		}
		$postdata = array(
			'title' => $name,
			'notes' => $description,
			'tags' => implode(' ', $data['tags']),
			'share' => false
		);
		$options = array(
			'testCondition' => 'taggedlink',
			'fromBuffer' => true,
			'cache' => false,
		);
		$this->bookmarkUrl = 'http://delicious.com/'.urlencode($this->login);
		return $this->process('http://delicious.com/save', $postdata, $options);
	}
}
?>