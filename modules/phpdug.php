<?php

class PHPDug extends BMModule {
	public $name = false;
	
	protected function _login(Array $data) {
		$postdata = array(
			'username' => $this->login,
			'password' => $this->password
		);
		$options = array(
			'testCondition' => 'logout',
		);
		$host = parse_url($this->registrationUrl, PHP_URL_HOST);
		return $this->process('http://'.$host.'/login.php', $postdata, $options);
	}
	
	protected function _addBookmark(Array $data) {
		extract($data);
		$host = parse_url($this->registrationUrl, PHP_URL_HOST);
		
		$postdata = array (
			'Submit' => 'Continue Submitting My Story',
			'story_url' => $url,
			'dupe' => '1',
		);
		
		$page = $this->http->POST('http://'.$host.'/add_story.php', $postdata);
		$fp = new FormsParser;
		$postdata = $fp->getForm($page, array('story_title', 'story_desc'));
		
		$postdata['story_title'] = $name;
		$postdata['story_desc'] = $description;
		$postdata['dupe'] = '1';
		
		$this->bookmarkUrl = 'http://'.$host.'/upcoming.php';
		$page = $this->http->POST('http://'.$host.'/add_story.php', $postdata);
		
		if (mb_strpos($page, 'upcoming.php') !== false) {
			return true;
		}
		return false;
	}
}

?>