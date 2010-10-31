<?php
class MoeMesto extends BMModule {
	public $name = 'Моё место';
	public $icon = 'moemesto.png';
	public $registrationUrl = 'http://moemesto.ru/register/';
	
	protected function _login(Array $data) {
		$postdata = array(
			'login' => $this->login,
			'password' => $this->password,
			'bind_ip' => false
		);
		$options = array(
			'testCondition' => 'вы вошли как',
		);
		return $this->process('http://moemesto.ru/login/', $postdata, $options);
	}
	
	protected function _addBookmark(Array $data) {
		extract($data);
		$postdata = array(
			'url' => $url,
			'title' => $name,
			'tags' => implode(' ', $tags),
			'desc' => $description,
			'status' => 'ALL',
			'save' => false
		);
		$options = array(
			'testType' => 'redirect'
		);
		$this->bookmarkUrl = 'http://moemesto.ru/'.urlencode($this->login);
		return $this->process('http://moemesto.ru/post.php', $postdata, $options);
	}
}
?>