<?php

class AnchorGenerator {
	const STATIC_TEXT = 1;
	const GENERATED_TEXT = 2;
	
	static $count = 1;
	
	static $parts = array();
	
	public function getText($text) {
		self::loadText($text);
		
		$text = '';
		foreach (self::$parts as $part) {
			if ($part['type'] == self::STATIC_TEXT) {
				$text .= $part['value'];
			} else {
				$key = array_rand($part['value']);
				$text .= $part['value'][$key];
			}
		}
		
		return $text;
	}
	private function loadText($text) {
		self::$parts = array();
		if (
			preg_match_all('#(.*?)(\{.*?\})#isu',$text,$match) ||
			preg_match_all('#(.*?)(\(.*?\))#isu',$text,$match) ||
			preg_match_all('#(.*?)(\[.*?\])#isu',$text,$match) 
		) {
			for ($i=0;$i<count($match[0]);$i++) {
				if (!empty($match[1][$i])) {
					self::$parts[] = array(
						'type' => self::STATIC_TEXT,
						'value' => $match[1][$i]
					);
				}
				if (!empty($match[2][$i])) {
					$parts = preg_split('/[|\\\]/is',substr($match[2][$i],1,-1));
					if (count($parts) > 1) {
						self::$count *= count($parts);
						self::$parts[] = array(
							'type' => self::GENERATED_TEXT,
							'value' => $parts
						);
					} else {
						self::$parts[] = array(
							'type' => self::STATIC_TEXT,
							'value' => $match[2][$i]
						);
					}
				}
			}
			preg_match('#\}([^}]*?)$#isu',$text,$match);
			if (!empty($match[1])) {
				self::$parts[] = array(
					'type' => self::STATIC_TEXT,
					'value' => $match[1]
				);
			}
		} else {
			self::$parts[] = array(
				'type' => self::STATIC_TEXT,
				'value' => $text
			);
		}
	}
}

?>