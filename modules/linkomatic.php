<?php
class Linkomatic extends BMModule {
	public $name = 'Linkomatic';
	public $icon = 'linkomatic.png';
	public $registrationUrl = 'http://linkomatic.ru/user/registration';
	
	protected function _login(Array $data) {
		$postdata = array(
			'login' => $this->login,
			'password' => $this->password
		);
		$options = array(
			'testCondition' => 'Популярные закладки',
			'cache' => false
		);
		return $this->process('http://linkomatic.ru/user/login', $postdata, $options);
	}
	
	protected function _addBookmark(Array $data) {
		extract($data);
		$tags[] = $this->login;
		$postdata = array(
			'url' => $url
		);
		$options = array(
			'testCondition' => 'tagsString',
			'cache' => false
		);
		if (!$this->process('http://linkomatic.ru/add', $postdata, $options)) {
			return false;
		}
		$postdata = array(
			'title' => $name,
			'description' => $description,
			'tagsString' => implode(', ', $tags)
		);
		$options = array(
			'testCondition' => 'Мои закладки',
			'fromBuffer' => true,
			'cache' => false,
		);
		// У этого сервиса нет страницы пользователя, но у нас есть хитрый план - добавим тег с именем юзера ... protit!
		$this->bookmarkUrl = 'http://linkomatic.ru/tag/'.urlencode(strtolower($this->login)).'/';
		return $this->process('http://linkomatic.ru/add', $postdata, $options);
	}
}
?>