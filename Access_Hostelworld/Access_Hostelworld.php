<?php


class Access_Hostelworld {
	/*
		TODO: metodo close_session() para unlink($this->cookie_file)
	*/
	
	function __construct($ro = null, $stockupdate = null) {
		if (is_object($ro)) { 
			$data = $ro->getExternalSyncDetails("hw");
			$this->username = $data["username"];
			$this->password = $data["password"];
			$this->property = $data["hw_property_number"];
			//print_r($data);
		}
		elseif (is_null($ro)) {
			/*
				This is the configuration used for testing
			*/
			$this->property = "11590";
			$this->username = "agent";
			$this->password = "casanova11";
		}
		$this->cookie = "";
		$this->cookie_file = $_SERVER["DOCUMENT_ROOT"]."/cache/hw_".md5(uniqid()).".cookie";
		$this->referer = "https://secure.webresint.com/inbox/index.php";
	}

	private function init_curl($url, $method = 1) {
		$curl_dscr = curl_init($url);
		curl_setopt($curl_dscr, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl_dscr, CURLOPT_HEADER, 1);
		curl_setopt($curl_dscr, CURLOPT_POST, $method);
		curl_setopt($curl_dscr, CURLOPT_COOKIEFILE, $this->cookie_file);
		curl_setopt($curl_dscr, CURLOPT_COOKIEJAR, $this->cookie_file);
		curl_setopt($curl_dscr, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl_dscr, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.19) Gecko/2010031422 Firefox/3.0.19");
		curl_setopt($curl_dscr, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_dscr, CURLOPT_REFERER, $this->referer);

		return $curl_dscr;
	}

	private function uninit_curl($curl_dscr) {
		$this->referer = curl_getinfo($curl_dscr, CURLINFO_EFFECTIVE_URL);
		curl_close($curl_dscr);
	}

	private function getCaptcha() {
		$ch = $this->init_curl("https://secure.webresint.com/inbox/index.php", 0);
		$info = curl_exec($ch);

		$this->uninit_curl($ch);
		$this->referer = "https://secure.webresint.com/inbox/index.php";

		$html = phpQuery::newDocument($info);
		$captcha_url = $html->find('img[src^="/inbox/GenerateSecurityImage.php"]')->attr('src');
		phpQuery::unloadDocuments();

		$ch = $this->init_curl("https://secure.webresint.com".$captcha_url, 0);
		$info = curl_exec($ch);

		$this->uninit_curl($ch);
		$this->referer = "https://secure.webresint.com/inbox/index.php";

		$fh_cookie = fopen($this->cookie_file, "r");
		while ( !feof($fh_cookie) ) {
			$str = fgets($fh_cookie, 256);

			if ( ( $rand_pos = strpos($str, 'RandText') ) !== FALSE ) {
				$this->cookie = trim(substr($str, $rand_pos+8));
				break;
			}
		}
		fclose($fh_cookie);
	}

	function login() {
		$this->getCaptcha();

		$ch = $this->init_curl("https://secure.webresint.com/inbox/trylogin.php", 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "HostelNumber=".$this->property."&ImageText=".$this->cookie."&Password=".$this->password."&SessionLanguage=&Submit=1&Username=".$this->username);

		$info = curl_exec($ch);
		$this->uninit_curl($ch);
		
		//echo $info;
		
		$html = phpQuery::newDocument($info);
		$status = $html->find('a[href="/inbox/logout.php"]')->length > 0;
		phpQuery::unloadDocuments();

		return $status;
	}

	function fetch($start, $end) {
		$url_fetch = "https://secure.webresint.com/inbox/availability/shortterm.php?startDate=".$start."&endDate=".$end."&selAgent=";

		$ch = $this->init_curl($url_fetch, 0);

		$info = curl_exec($ch);
		$this->uninit_curl($ch);

		return $info;
	}

	function send_post($date, $mixed) {
		$url_post = 'https://secure.webresint.com/inbox/availability/shortterm.php';
		$post = 'SaveAllocationShortTerm=1&UpdateDates=0&selAllocatedTo=WRI&startDate='.$date.'&endDate='.$date;

		$date = str_replace('-', '_', $date);
		foreach ( $mixed as $id => $val ) $post .= '&lbl_'.$id.'_'.$date.'='.$val;

		$ch = $this->init_curl($url_post, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

		$info = curl_exec($ch);
		$this->uninit_curl($ch);

		return $info;
	}

	function check($mixed, $content) {
   	$html = phpQuery::newDocument($content);
   	foreach ($html->find('table.tableClass1 input[id^="lbl_"]') as $input) {
			$id = trim(pq($input)->attr('id'));
			$id = substr($id, 4, strlen($id)-15);

			if ( array_key_exists($id, $mixed) && $mixed[$id] == trim(pq($input)->val()) ) unset($mixed[$id]);
   	}
		phpQuery::unloadDocuments();

		return ( count($mixed) == 0 );
	}
}
