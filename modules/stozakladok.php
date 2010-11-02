<?php
class Stozakladok extends BMModule {
	public $name = 'Сто закладок';
	public $icon = '100zakladok.png';
	public $registrationUrl = 'http://www.100zakladok.ru/register/';
	
	protected function _login(Array $data) {
		$postdata = array(
			'ln' => $this->login,
			'lp' => $this->password
		);
		$options = array(
			'testCondition' => '100zakladok.ru - переход',
			'encoding' => 'WINDOWS-1251',
		);
		return $this->process('http://www.100zakladok.ru/login/', $postdata, $options);
	}
	
	protected function _addBookmark(Array $data) {
		extract($data);
		$postdata = array(
			'bm_url_1' => $url,
			'title_1' => $name,
			'id_cat_1' => '1',
			'descr_1' => $description,
			'tags_1' => implode(', ', $tags),
			'id_bm_1' => '',
			'form_type' => '1',
			'id_form' => '1',
			'add_proc' => 'Добавить закладку',
		);
		$options = array(
			'testCondition' => 'Мои закладки',
			'dontParse' => true,
			'encoding' => 'WINDOWS-1251',
		);
		$this->bookmarkUrl = 'http://www.100zakladok.ru/'.urlencode(strtolower($this->login)).'/';
		return $this->process('http://www.100zakladok.ru/'.urlencode(strtolower($this->login)).'/', $postdata, $options);
	}
}
?>