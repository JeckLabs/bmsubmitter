<?php
header('Content-type: text/html; charset=utf-8');
set_magic_quotes_runtime(false);

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