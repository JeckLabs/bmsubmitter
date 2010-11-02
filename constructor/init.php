<?php
header('Content-type: text/html; charset=utf-8');
set_magic_quotes_runtime(false);

$includePaths = array(
	'./libs',
	'../libs',
	'../libs/core',
);
set_include_path(implode(PATH_SEPARATOR, $includePaths));

function __autoload($name) {
	$name = strtolower($name);
	include $name.'.php';
}
function parseFields($data, $fields) {
	$postfields = array();
	foreach ($data as $string) {
		if (!empty($string)) {
			list($key, $value) = explode(':', $string);
			if (isset($fields[$value])) {
				$postfields[$key] = $fields[$value];
			}
			if ($value == 'null') {
				$postfields[$key] = false;			
			}
			if ($value == 'check') {
				$postfields[$key] = null;			
			}
		}
	}
	return $postfields;
}

?>