<?php

function _e() {
	if (!$args = func_get_args()) {
		return;
	}
	$string = array_shift($args);
	$string = Translator::getTranslation($string);
	array_unshift($args, $string);
	if (count($args) > 0) {
		return call_user_func_array('sprintf', $args);
	} else {
		return $string;
	}
}

class Translator {
	public static $table = array();
	
	public static function getTranslation($string) {
		if (isset(self::$table[$string]) && !empty(self::$table[$string])) {
			return rtrim(self::$table[$string]);
		} else {
			return $string;
		}
	}
	
	public static function loadTable($filename) {
		$file = array_map('rtrim', file($filename));
		$length = count($file);
		$table = array();
		for ($i=0;$i<$length;$i++) {
			$line = $file[$i];
			if (strpos($line, 'STRING') === 0 || strpos($line, 'TRANSLATION') === 0) {
				$parts = preg_split('/\s+/i', $line, 2);
				$keyword = $parts[0];
				if (isset($parts[1])) {
					$buffer = $parts[1]."\r\n";
				} else {
					$buffer = '';
				}
				while (++$i < $length) {
					$line = $file[$i];
					if (empty($line) || $line{0} == "#") {
						continue;
					}
					if ($line{0} == "\t") {
						$buffer .= substr($line, 1)."\r\n";
					} else {
						$i--;
						break;
					}
				}
				if ($keyword == 'STRING') {
					$lastKey = rtrim($buffer);
					$table[$lastKey] = '';
				}
				if ($keyword == 'TRANSLATION' && isset($lastKey)) {
					$table[$lastKey] .= $buffer;
				}
			}
		}
		self::$table = $table;
	}
}

?>