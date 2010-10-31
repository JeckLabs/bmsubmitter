<?php

class CModule extends BMModule {
	protected function _login(Array $data) {
	}
	protected function _addBookmark(Array $data) {
	}
	
	public function getLoginCookies($url, $fields) {
		$this->process($url, $fields, array());
		return $this->cookies;
	}

}

?>