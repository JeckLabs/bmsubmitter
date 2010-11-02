<?php

class WebMarkz extends BMModule {
    public $name = "Web Markz";
    public $icon = "webmarkz.png";
    public $registrationUrl = "http://www.web-markz.com/newuser.php";
    
    protected function _login(Array $data) {
        $postdata = array (
            "name" => $this->login,
            "pass" => $this->password,
        );
        $options = array(
            "testCondition" => "Layout:"
        );
        return $this->process("http://www.web-markz.com/login.php", $postdata, $options);
    }
    
    protected function _addBookmark(Array $data) {
        extract($data);
        $postdata = array (
            "url" => $url,
            "title" => $name,
            "description" => $description,
            "tags" => implode(" ", $tags),
        );
        $options = array(
            "testCondition" => "Layout:"
        );
        $this->bookmarkUrl = "http://web-markz.com/userb.php?uname=".$this->login."";
        return $this->process("http://www.web-markz.com/add.php", $postdata, $options);
    }

}

?>