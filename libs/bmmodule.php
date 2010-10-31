<?php
abstract class BMModule {
	protected $http;
	protected $cache;
	
	protected $login;
	protected $password;
	
	protected $encoding;
	
	public $bookmarkUrl;
	public $cookies;
	
	private $buffer;
	
	function __construct() {
		$this->http = new http;
	}
	
	public function login($data) {
		$this->login = $data['login'];
		$this->password = $data['password'];
		if ($this->cookiesLogin($data)) return true;
		if (!$this->_login($data)) {
			throw new Exception('CANT_LOGIN');
		}
	}
	public function addBookmark($data) {
		if (!$this->_addBookmark($data)) {
			throw new Exception('CANT_ADD_BOOKMARK');
		}
		return true;
	}
	
	abstract protected function _login(Array $data);
	abstract protected function _addBookmark(Array $data);
	
	protected function cookiesLogin($data) {
		if (isset($data['cookies'])) {
			$this->cookies = $data['cookies'];
			$this->http->cookies = $this->cookies;
			return true;
		}
		return false;
	}
	
	protected function process($url, $postdata, $options=array()) {
		$defaults = array(
			'testType' => 'string',
			'testCondition' => '',
			'fromBuffer' => false,
			'dontParse' => false,
			'method' => 'post',
		);
		$options = array_merge($defaults, $options);
		extract($options);
		if (isset($encoding)) {
			$this->encoding = $encoding;
		}
		if ($testType == 'redirect') {
			$this->http->follow_location = false;
		} else {
			$this->http->follow_location = true;
		}
		
		$this->buffer = $this->action($url, $postdata, $options);
		
		// TODO: Выделить этот код в отдельный метод
		$testSuccess = false;
		switch ($testType) {
			case 'string':
				if (mb_strpos($this->buffer, $testCondition) || empty($testCondition)) {
					$testSuccess = true;
				} else {
					//var_dump($this->buffer);
				}
			break;
			case 'regexp':
				if (preg_match($testCondition, $this->buffer)) {
					$testSuccess = true;
				}
			break;
			case 'cookies':
				if (isset($this->http->cookies[$testCondition])) {
					$testSuccess = true;
				}
			break;
			case 'redirect':
				if (!empty($this->http->location)) {
					$testSuccess = true;
				}
			break;
		}
		if ($testSuccess) {
			$this->cookies = $this->http->cookies;
			return true;
		}
		return false;
	}
	
	private function action($url, $data, $options) {
		extract($options);
		if (!$dontParse) {
			if (!$fromBuffer) {
				$page = $this->getPage($url);
			} else {
				$page = $this->buffer;
			}
			$fp = new FormsParser();
			$postdata = $fp->getForm($page, array_keys($data));
			if (!$postdata) {
				return false;
			}
			foreach ($postdata as $key => $value) {
				if ($value === false) {
					unset($postdata[$key]);
				}
			}
			$postdata = array_merge($postdata, $data);
			$action = http::fixURL($url, $fp->action);
			$method = $fp->method;
		} else {
			$postdata = $data;
			$action = $url;
		}
		return $this->submit($action, $postdata, $method);
	}
	
	private function submit($action, $data, $method='post') {
	
		$method = strtolower($method);
		if (isset($this->encoding)) {
			$data = $this->convert($data, $this->encoding);
		} else {
			$data = $this->convert($data, $this->http->encoding);
		}
		switch ($method) {
			case 'post':
				$page = $this->http->POST($action, $data);
				return $page;
			break;
			case 'get':
			default:
				$action = preg_replace('/\?.*$/i', '', $action);
				$query = http_build_query($data);
				$action .= '?'.$query;
				return $this->http->GET($action);
			break;
		}
	}
	
	private function getPage($url) {
		// TODO: Реализовать кеширование данных
		return $this->http->GET($url);
	}
	
	protected function convert($data, $encoding) {
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				$data[$key] = iconv('UTF-8', $encoding, $value);
			}
			return $data;
		}
		
		return iconv('UTF-8', $encoding, $data);
	}
}

?>
