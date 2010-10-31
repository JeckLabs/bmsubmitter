<?php
class Zakladoknet extends BMModule {
	public $name = 'Закладок.нет';
	public $icon = 'zakladoknet.png';
	public $registrationUrl = 'http://zakladok.net/register.php';
	
	protected function _login(Array $data) {
		$postdata = array(
			'login' => $this->login,
			'passwd' => $this->password
		);
		$options = array(
			'testCondition' => 'моя анкета',
		);
		return $this->process('http://zakladok.net/', $postdata, $options);
	}
	
	protected function _addBookmark(Array $data) {
		extract($data);
		$tags[] = strtolower($this->login);
		
		$page = $this->http->GET('http://www.zakladok.net/add_link.php');
		preg_match('/name=folder_id>\s+<option value="([0-9]+)">/is', $page, $match);
		$folderId = $match[1];
		
		$postdata = array(
			'action' => 'edit',
			'mode' => 'insert',
			'original_id' => '0',
			'original_id' => '0',
			'url' => $url,
			'title' => $name,
			'tags_line' => implode(', ', $tags),
			'description' => $description,
			'is_public' => '1',
			'folder_id' => $folderId,
			'namefolder' => '',
			'submit' => 'Сохранить закладку',
		);
		
		$options = array(
			'testCondition' => 'Выбранные объекты Вы можете',
			'encoding' => 'WINDOWS-1251',
			'dontParse' => true,
		);
		$this->bookmarkUrl = 'http://www.zakladok.net/search_result.php?searchstring='.urlencode(strtolower($this->login)).'&tagged=1';
		return $this->process('http://www.zakladok.net/add_link.php', $postdata, $options);
	}
}
?>