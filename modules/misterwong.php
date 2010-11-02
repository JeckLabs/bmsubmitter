<?php
class MisterWong extends BMModule {
	public $name = 'Mister Wong';
	public $icon = 'misterwong.png';
	public $registrationUrl = 'http://www.mister-wong.ru/register/';
	
	protected function _login(Array $data) {
		$postdata = array(
			'user_name' => $this->login,
			'user_password' => $this->password
		);
		$options = array(
			'testCondition' => 'Свежие закладки',
		);
		return $this->process('http://www.mister-wong.ru/index.php?action=login', $postdata, $options);
	}
	
	protected function _addBookmark(Array $data) {
		extract($data);

		//*
		$page = $this->http->GET('http://www.mister-wong.ru/add_url/');
		
		
		$fp = new FormsParser;
		$postdata = $fp->getForm($page, array('hash', 'bm_url'));
		
		$postdata['bm_url'] = $url;
		$postdata['bm_description'] = $name;
		$postdata['bm_notice'] = $description;
		$postdata['bm_tags'] = implode(', ', $tags);
		$postdata['bm_status'] = 'public';
		
		preg_match('/([0-9]+) \+ ([0-9]+) =/is', $page, $match);
		$postdata['password'] = $match[1] + $match[2];
		
		$page = $this->http->POST('http://www.mister-wong.ru/add_url/', $postdata);
		$this->bookmarkUrl = 'http://www.mister-wong.ru/user/'.urlencode($this->login).'/';
		if (!preg_match('/Ошибочка вышла/iu', $page)) {
			return true;
		}
		return false;
		//*/
	}
}
?>