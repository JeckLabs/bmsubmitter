<?

class AntiCaptcha {
	const FILE_NOT_FOUND = 0;
	const CURL_ERROR = 1;
	const SERVER_ERROR = 2;
	const TIMEOUT = 3;
	
	public $key;
	public $server = 'http://ac-service.info';
	public $phrase = false;
	public $regsense = true;
	public $numeric = false;
	public $min_len = 0;
	public $max_len = 0;
	
	public $ocr_step_time = 10;
	public $ocr_timeout = 120;
	
	public $curl_timeout = 120;
	
	private $id;
	
	function __construct($key) {
		$this->key = $key;
	}
	
	function recognize($file,$ext=null) {
		if (is_null($ext)) {
			if (!file_exists($file)) {
				throw new Exception('File not found',self::FILE_NOT_FOUND);
			} else {
			    $postdata = array(
			        'method'    => 'post', 
			        'key'       => $this->key, 
			        'file'      => '@'.realpath($file),
			        'phrase'	=> (int)$this->phrase,
			        'regsense'	=> (int)$this->regsense,
			        'numeric'	=> (int)$this->numeric,
			        'min_len'	=> $this->min_len,
			        'max_len'	=> $this->max_len,
			        
			    );
			}
		} else {
			if ($ext == 'auto') {
				$file = imageCreateFromString($file);
				ob_start();
				imageJpeg($file, null, 80);
				$file = ob_get_contents();
				$ext = 'jpg';
				ob_end_clean();
			}
			$postdata = array(
				'method'    => 'base64', 
				'key'       => $this->key, 
				'body'      => base64_encode($file),
				'ext'      => strtolower($ext),
				'phrase'	=> (int)$this->phrase,
				'regsense'	=> (int)$this->regsense,
				'numeric'	=> (int)$this->numeric,
				'min_len'	=> $this->min_len,
				'max_len'	=> $this->max_len,
				
			);
		}
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $this->server.'/in.php');
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_TIMEOUT, $this->curl_timeout);
	    curl_setopt($curl, CURLOPT_POST, 1);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
	    $result = curl_exec($curl);
	    if (curl_errno($curl)) {
			throw new Exception(curl_error($curl), self::CURL_ERROR);
	    }
		curl_close($curl);
		if (strpos($result, 'OK') === 0) {
			list(,$this->id) = explode('|',$result);
			$start = time();
			while (true) {
				sleep($this->ocr_step_time);
				$curl = curl_init();
			    curl_setopt($curl, CURLOPT_URL, $this->server.'/res.php?key='.$this->key.'&action=get&id='.$this->id);
			    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			    curl_setopt($curl, CURLOPT_TIMEOUT, $this->curl_timeout);
				$result = curl_exec($curl);
				if (curl_errno($curl)) {
					throw new Exception(curl_error($curl), self::CURL_ERROR);
				}
				curl_close($curl);
				if (strpos($result, 'OK') === 0) {
					list(,$result) = explode('|',$result);
					return $result;
				} else if ($result == 'CAPCHA_NOT_READY') {
					if ((time() - $start) > $this->ocr_timeout) {
						throw new Exception("OCR timeout", self::TIMEOUT);
					}
				}
			}
		} else {
			throw new Exception($result,self::SERVER_ERROR);
		}
	}
}

?>