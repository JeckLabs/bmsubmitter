<?php
class FormsParser {
	public $action = "./";
	public $method = "get";
	
	public static $form_rule = array(
		'regexp' => '#<form(\s)?(?(1)(.*))>(.*)</form>#isuU',
		'result' => array(
			2 => 'params',
			3 => 'body'
		)
	);
	public static $input_rule = array(
		'regexp' => '#<input(\s)?(?(1)(.*))/?>#isuU',
		'result' => array(
			2 => 'params'
		)
	);
	public static $select_rule = array(
		'regexp' => '#<select(\s)?(?(1)(.*))>(.*)</select>#isuU',
		'result' => array(
			2 => 'params',
			3 => 'body'
		)
	);
	public static $option_rule = array(
		'regexp' => '#<option(\s)?(?(1)([^>]*))>((?:.*)(?=</option>)|(?:[^<>]+?))#isuU',
		'result' => array(
			2 => 'params',
			3 => 'value'
		)
	);
	public static $textarea_rule = array(
		'regexp' => '#<textarea(\s)?(?(1)(.*))>(.*)</textarea>#isuU',
		'result' => array(
			2 => 'params',
			3 => 'value'
		)
	);
	public static $params_rule = array(
		'regexp' => '#(\w*)\s*=\s*["\']?([^<>\s"\']*)["\']?#isu',
		'result' => array(
			1 => 'key',
			2 => 'value'
		)
	);
	
	
	public function getForms($page) {
		return $this->parseForms($page);
	}
	
	public function getForm($forms,$data) {
		if (!is_array($forms)) {
			$forms = $this->parseForms($forms);
		}
		if ($forms) {
			foreach ($forms as $form) {
				if ($data == array_intersect($data,array_keys($form['data']))) {
					foreach ($form['data'] as $key => $value) {
						$this->action = $form['action'];
						$this->method = $form['method'];
						
						if (is_array($value)) {
							if (count($value) > 1) {
								$form['data'][$key] = $value[1];
							} else {
								$form['data'][$key] = $value[0];
							}
						}
					}
					return $form['data'];
				}
			}
		}
		return false;
	}
	
	private function parseForm($body,$params) {
		$data = array();
		
		$params = $this->parseParams($params);
		$params = array_map('htmlspecialchars_decode', $params);
		$data['action'] = (isset($params['action']) ? $params['action'] : './');
		$data['method'] = (isset($params['method']) ? strtolower($params['method']) : 'get');
		
		if (isset($parmas['name'])) {
			$data['data'][$parmas['name']] = "";
		}
		
		$inputs = $this->parseInputs($body);
		$selects = $this->parseSelects($body);
		$textareas = $this->parseTextareas($body);
		
		$data['data'] = array_merge($inputs,$selects,$textareas);
		return $data;
	}
	
	private function parseInputs($body) {
		$data = array();
		if ($inputs = self::matchRule(self::$input_rule,$body)) {
			foreach ($inputs['params'] as $params) {
				$params = $this->parseParams($params);
				if (isset($params['name'])) {
					$name = $params['name'];
					$value = (isset($params['value']) ? $params['value'] : '');
					$data = self::addValue($data,$name,$value);
				}
			}
		}
		return $data;
	}
	
	private function parseSelects($body) {
		$data = array();
		if ($selects = self::matchRule(self::$select_rule,$body)) {
			$count = count($selects['params']);
			for ($i=0;$i<$count;$i++) {
				$params = $this->parseParams($selects['params'][$i]);
				if (isset($params['name'])) {
					$name = $params['name'];
					if ($options = $this->parseOptions($selects['body'][$i])) {
						$data = self::addValue($data,$name,$options);
					} else {
						$data = self::addValue($data,$name);
					}
				}
			}
		}
		return $data;
	}
	
	private function parseOptions($body) {
		if ($options = self::matchRule(self::$option_rule,$body)) {
			$data = array();
			$count = count($options['params']);
			for ($i=0;$i<$count;$i++) {
				$params = $this->parseParams($options['params'][$i]);
				if (isset($params['value'])) {
					$data[] = $params['value'];
				} else {
					$data[] = $options['body'][$i];
				}
			}
			return $data;
		}
		return false;
	}
	
	private function parseTextareas($body) {
		$data = array();
		if ($textareas = self::matchRule(self::$textarea_rule,$body)) {
			$count = count($textareas['params']);
			for ($i=0;$i<$count;$i++) {
				$params = $this->parseParams($textareas['params'][$i]);
				if (isset($params['name'])) {
					$name = $params['name'];
					$value = (isset($params['value']) ? $params['value'] : $textareas['value'][$i]);
					$data = self::addValue($data,$name,$value);
				}
			}
		}
		return $data;
	}
	
	public function parseForms($page) {
		if ($forms = self::matchRule(self::$form_rule,$page)) {
			$count = count($forms['params']);
			for ($i=0;$i<$count;$i++) {
				$data[$i] = $this->parseForm($forms['body'][$i],$forms['params'][$i]);
			}
			return $data;
		}
		return false;
	}
	
	public static function matchRule($rule,$text) {
		if (preg_match_all($rule['regexp'],$text,$match)) {
			$result = array();
			foreach ($rule['result'] as $matchKey => $resultKey) {
				$result[$resultKey] = $match[$matchKey];
			}
			return $result;
		}
		return false;
	}
	
	private function parseParams($str) {
		if ($params = self::matchRule(self::$params_rule,$str)) {
			$params['key'] = array_map('strtolower',$params['key']);
			$result = array_combine($params['key'],$params['value']);
			return $result;
		}
		return false;
	}
	
	public static function addValue($data,$key,$values='') {
		if (!is_array($values)) {
			$values = array($values);
		}
		foreach ($values as $value) {
			if (isset($data[$key])) {
				if (!is_array($data[$key])) {
					$data[$key] = array($data[$key]);
				}
				$data[$key][] = $value;
			} else {
				$data[$key] = $value;
			}
		}
		return $data;
	}
}
?>