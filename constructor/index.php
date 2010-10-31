<?php
include './init.php';
define('NL', "\r\n");
define('TAB', "\t");

function exportFields($data, $fields) {
	$vars = array();
	$vars[] = 'array (';
	foreach ($data as $string) {
		if (!empty($string)) {
			list($key, $value) = explode(':', $string);
			if (isset($fields[$value])) {
				$value = $fields[$value];
			} else if ($value == 'check') {
				$value = 'null';
			} else if ($value == 'null') {
				$value = 'false';
			} else {
				continue;
			}
			$vars[] = TAB.'"'.addslashes($key).'" => '.$value.',';
		}
	}
	$vars[] = ')';
	return $vars;
}

if (isset($_POST['className'])) {
	$loginForm = exportFields($_POST['loginForm'], array(
		'login' => '$this->login',
		'password' => '$this->password'
	));
	$addForm = exportFields($_POST['addForm'], array(
		'url' => '$url',
		'name' => '$name',
		'description' => '$description',
		'tags' => 'implode("'.$_POST['tagsDelimiter'].'", $tags)',
	));
	
	$moduleCode  = '<?php'.NL.
		NL.
		'class '.$_POST['className'].' extends BMModule {'.NL.
		TAB.'public $name = "'.$_POST['moduleName'].'";'.NL.
		TAB.'public $icon = "'.strtolower($_POST['className']).'.png";'.NL.
		TAB.'public $registrationUrl = "'.$_POST['registrationUrl'].'";'.NL.
		TAB.NL.
		TAB.'protected function _login(Array $data) {'.NL.
		TAB.TAB.'$postdata = '.
		implode("\n\t\t", $loginForm).';'.NL.
		TAB.TAB.'$options = array('.NL.
		TAB.TAB.TAB.'"testCondition" => "'.addslashes($_POST['loginTestString']).'"'.NL.
		TAB.TAB.');'.NL.
		TAB.TAB.'return $this->process("'.$_POST['loginFormUrl'].'", $postdata, $options);'.NL.
		TAB.'}'.NL.
		TAB.NL.
		TAB.'protected function _addBookmark(Array $data) {'.NL.
		TAB.TAB.'extract($data);'.NL.
		TAB.TAB.'$postdata = '.
		implode("\n\t\t", $addForm).';'.NL.
		TAB.TAB.'$options = array('.NL.
		TAB.TAB.TAB.'"testCondition" => "'.addslashes($_POST['addTestString']).'"'.NL.
		TAB.TAB.');'.NL.
		TAB.TAB.'$this->bookmarkUrl = "'.str_ireplace('{login}', '".$this->login."', addslashes($_POST['bookmarksUrl'])).'";'.NL.
		TAB.TAB.'return $this->process("'.$_POST['addFormUrl'].'", $postdata, $options);'.NL.
		TAB.'}'.NL.
		NL.
		'}'.NL.
		NL.
		'?>'
	;
	echo '<title>Закладочник 2.0 &#151; конструктор модулей</title>';
	highlight_string($moduleCode);
} else {
	include './templates/mainform.html';
}

?>