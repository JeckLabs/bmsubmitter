<?php

class CModule extends BMModule {
	protected function _login(Array $data) {
	}
	protected function _addBookmark(Array $data) {
	}
	
	public function getLoginCookies($url, $fields, $data=array()) {
		$this->process($url, $fields, $data);
		return $this->http->cookies;
	}

}

?>