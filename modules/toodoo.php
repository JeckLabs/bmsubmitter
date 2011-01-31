<?php 

class TooDoo extends BMModule { 
    public $name = "TooDoo"; 
    public $icon = "toodoo.png"; 
    public $registrationUrl = ""; 
     
    protected function _login(Array $data) { 
        $postdata = array (
            "email" => $this->login,
            "password" => $this->password,
            "remember_me" => true,
        ); 
        $options = array( 
            "testCondition" => "/settings" 
        ); 
        return $this->process("http://toodoo.ru/", $postdata, $options); 
    } 
     
    protected function _addBookmark(Array $data) { 
        extract($data); 
		
		if (!preg_match('/\/user\/([0-9]+)\/blogs/i', $this->buffer, $bookmarksUrl)) {	
			return false;
		}
		$bookmarksUrl = $bookmarksUrl[0];
		$bookmarksUrl = http::fixUrl('http://toodoo.ru/', $bookmarksUrl);
		
        $postdata = array (
            "url" => $url,
        ); 
        $options = array( 
            "testCondition" => "flash_good_mood_content" 
        ); 

		$this->bookmarkUrl = $bookmarksUrl;
		return $this->process($bookmarksUrl, $postdata, $options);
    } 

} 

?>