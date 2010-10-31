<?

class http {
	public $user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 6.0; ru; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5';
	
	public $cookies = array();
	public $referer = '';
	public $timeout = 10;
	
	public $proxy = '';
	public $proxy_type = CURLPROXY_HTTP;
	
	// Режим следования по редиректу (true - включен, false - выключен)
	public $follow_location = true;
	
	// Кодировка страницы 
	public $encoding = '';
	
	public $info;
	
	private $ch;
	private $result;
	private $result_headers;
	private $result_body;
	public $location = '';
	
	public function GET($url,$encoding='UTF-8') {
		$this->init($url);
		$this->setDefaults();
		$this->exec();
		
		if ($this->follow_location && !empty($this->location)) {
			$result = $this->GET(self::fixURL($url,$this->location));
		} else {
			$result = $this->processEncoding($this->result_body,$encoding);
		}
		
		$this->result = null;
		$this->result_body = null;
		$this->result_headers = null;
		
		return $result;
	}
	
	public function POST($url,$postdata,$encoding='UTF-8') {
		$this->init($url);
		$this->setPostFields($postdata);
		$this->setDefaults();
		$this->exec();
		
		if ($this->follow_location && !empty($this->location)) {
			$result = $this->GET(self::fixURL($url,$this->location));
		} else {
			$result = $this->processEncoding($this->result_body,$encoding);
		}
		
		$this->result = null;
		$this->result_body = null;
		$this->result_headers = null;
		
		return $result;
	}
	
	/**
		Выполняет начальную инициализацию запроса 
	*/
	private function init($url) {
		$this->ch = curl_init($url);
		
		// Если запрос с использованием SSL - отключаем проверку сертификата
		$scheme = parse_url($url,PHP_URL_SCHEME);
		$scheme = strtolower($scheme);
		if ($scheme == 'https') {
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		}
	}
	
	/**
		Запускает обработку запроса
	*/
	private function exec() {
		// Установливает параметры необходимые для правильной обработки данных
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_HEADER, true);
		
		// Выполняем запрос и получаем информацию
		$this->result = curl_exec($this->ch);
		$this->info = curl_getinfo($this->ch);
		
		$this->processHeaders();
		$this->processBody();
	}
	
	/**
		Получает заголовки запроса и обрабатывает их содержимое
	*/
	private function processHeaders() {
		$this->location = '';
		$this->referer = $this->info['url'];
		$this->result_headers = substr($this->result,0,$this->info['header_size']);
		$headers = explode("\r\n",$this->result_headers);
		foreach ($headers as $header) {
			if (strpos($header,":") !== false) {
				list($key,$value) = explode(":",$header,2);
				$key = trim($key);
				$key = strtolower($key);
				switch ($key) {
					case 'set-cookie':
						$this->processSetCookie($value);
					break;
					case 'content-type':
						$this->processContentType($value);
					break;
					case 'location':
						$this->processLocation($value);
					break;
				}
			}
		}
	}
	
	/**
		Обрабатывает заголовок set-cookie
	*/
	private function processSetCookie($string) {
		if (($pos = strpos($string,";")) !== false) {
			$string = substr($string,0,$pos);
		}
		if (strpos($string,"=") !== false) {
			list($key,$value) = explode("=",$string,2);
			$key = urldecode(trim($key));
			$value = urldecode(trim($value));
		} else {
			$key = urldecode(trim($string));;
			$value = '';
		}
		$this->cookies[$key] = $value;
	}
	
	/**
		Обрабатывает заголовок content-type
	*/
	private function processContentType($string) {
		$pos = strpos($string,'charset');
		if ($pos !== false) {
			$endpos = strpos($string,';',$pos);
			if ($endpos === false) {
				$charset = substr($string,$pos);
			} else {
				$length = $endpos - $pos;
				$charset = substr($string,$pos,$length);
			}
			list(,$this->encoding) = explode("=",$charset,2);
		}
	}
	
	/**
		Обрабатывает заголовок location
	*/
	private function processLocation($string) {
		$this->location = trim($string);
	}
	
	/**
		Получает тело страницы
	*/
	private function processBody() {
		$this->result_body = substr($this->result,$this->info['header_size']);
		// Определяем кодировку по meta тегам
		if (is_null($this->encoding)) {
			if (preg_match("#<meta\b[^<>]*?\bcontent=\"text/html;\s*charset=(.*?)\"#is",$this->result_body,$match)) {
				$this->encoding = strtoupper($match[1]);
			}
		}
	}

	/**
		Возвращает тело страницы в нужной кодировке
	*/
	private function processEncoding($body,$encoding) {
		if ($encoding !== null && !empty($this->encoding)) {
			return iconv($this->encoding,$encoding.'//IGNORE',$body);
		}
		return $body;
	}
	
	/**
		Устанавливает начальные параметры заданные в свойствах
	*/
	private function setDefaults() {
		$this->encoding = null;
		$this->setUserAgent();
		$this->setReferer();
		$this->setCookies();
		$this->setProxy();
		$this->setTimeout();
	}
	
	/**
		Устанавливает UserAgent для curl
	*/
	private function setUserAgent() {
		if (!empty($this->user_agent)) {
			curl_setopt($this->ch, CURLOPT_USERAGENT, $this->user_agent);
		}
	}

	/**
		Устанавливает Referer для curl
	*/	
	private function setReferer() {
		if (!empty($this->referer)) {
			curl_setopt($this->ch, CURLOPT_REFERER, $this->referer);
		}
	}
	
	/**
		Преобразуем cookies в строку и устанавливаем их для curl
	*/	
	private function setCookies() {
		if (is_array($this->cookies)) {
			$cookie_string = '';
			foreach ($this->cookies as $key => $value) {
				$cookie_string .= urlencode($key).'='.urlencode($value).';';
			}
			curl_setopt($this->ch, CURLOPT_COOKIE, $cookie_string);
		}
	}
	
	/**
		Преобразуем postdata в строку и устанавливаем в качестве post_fields
	*/	
	private function setPostFields($postdata) {
		curl_setopt($this->ch, CURLOPT_POST, true);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
	}
	
	/**
		Устанавливаем proxy если необходимо
	*/	
	private function setProxy() {
		if (!empty($this->proxy)) {
			curl_setopt($this->ch, CURLOPT_PROXY, $this->proxy);
			curl_setopt($this->ch, CURLOPT_PROXYTYPE, $this->proxy_type);
		}
	}
	
	/**
		Устанавливает таймаут соединения
	*/	
	private function setTimeout() {
		if ($this->timeout > 0) {
			curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
		}
	}
	
	/**
	 * Преобразовывает относительные URL в абсолютные по базе
	 * TODO: Переписать наконец этот метод
	*/
	final static function fixURL($base,$link) {
		if (!preg_match('~^(https?://[^/?#]+)?([^?#]*)?(\?[^#]*)?(#.*)?$~i', $link.'#', $matchesLink)) {
			return false;
		}
		if (!empty($matchesLink[1])) {
			return $link;
		}
		if (!preg_match('~^(https?://)?([^/?#]+)(/[^?#]*)?(\?[^#]*)?(#.*)?$~i', $base.'#', $matchesBase)) {
			return false;
		}
		if ($matchesLink[2] == './') {
			return $base;
		}
		if (empty($matchesLink[2])) {
			if (empty($matchesLink[3])) {
				return 'http://'.$matchesBase[2].$matchesBase[3].$matchesBase[4];;
			}
			return 'http://'.$matchesBase[2].$matchesBase[3].$matchesLink[3];
		}
		$pathLink = explode('/', $matchesLink[2]);
		if ($pathLink[0] == '') {
			return 'http://'.$matchesBase[2].$matchesLink[2].$matchesLink[3];
		}
		$pathBase = explode('/', preg_replace('~^/~', '', $matchesBase[3]));
		if (sizeOf($pathBase) > 0) {
			array_pop($pathBase);
		}
		foreach ($pathLink as $p) {
			if ($p == '.') {
				continue;
			} elseif ($p == '..') {
				if (sizeOf($pathBase) > 0) {
					array_pop($pathBase);
				}
			} else {
				array_push($pathBase, $p);
			}
		}
		return 'http://'.$matchesBase[2].'/'.implode('/', $pathBase).$matchesLink[3];
	}
}

?>