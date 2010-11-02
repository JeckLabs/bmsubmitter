<?php

class YandexBookmarks extends BMModule {
    public $name = "Яндекс.закладки";
    public $icon = "yandexbookmarks.png";
    public $registrationUrl = "http://passport.yandex.ru/passport?mode=register&retpath=http%3A%2F%2Fzakladki.yandex.ru&from=zakladki";
    
    protected function _login(Array $data) {
        $postdata = array (
            "login" => $this->login,
            "passwd" => $this->password,
        );
        $options = array(
            "testCondition" => "new-bookmark"
        );
        return $this->process("http://zakladki.yandex.ru/", $postdata, $options);
    }
    
    protected function _addBookmark(Array $data) {
        extract($data);
		
		$fp = new FormsParser;
		$page = $this->http->get("http://zakladki.yandex.ru/newlink.xml?folder_id=0");
		$postdata = $fp->getForm($page, array('name', 'url', 'tags'));
		
        $postdata['name'] = $name;
        $postdata['url'] = $url;
        $postdata['tags'] = implode(", ", $tags);
        $postdata['descr'] = $description;
        $postdata['folder_id'] = '0';

		$page = $this->submit('http://zakladki.yandex.ru/addlink.xml', $postdata, 'post');
        print_r($postdata);
		echo $page;
		if (!preg_match('/ссылка добавлена/iu', $page)) {
			return false;
		}
		$this->bookmarkUrl = "http://".$this->login.".ya.ru";
		return true;
    }

}

?>