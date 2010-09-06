<?php

class Access_Siteminder {

	function __construct($ro = null) {
		
		if (is_object($ro)) { 
			$data = $ro->getExternalSyncDetails("siteminder");
			$this->username = $data["username"];
			$this->password = $data["password"];
			$this->ext_siteminder_iHotelier = $data["siteminder_iHotelier"];
			print_r($data);
		}
		elseif (is_null($ro)) {
			/*
				This is the configuration used for testing
			*/
			$this->username = "agent";
			$this->password = "11madrid11";		
			$this->ext_siteminder_iHotelier = 41;
		}
		
		$this->referer = "https://www.siteminder.co.uk/siteminder/sm-login.html";
		$this->cookie_file = $_SERVER["DOCUMENT_ROOT"]."/cache/cookie_siteminder.sv";
	}
	private function init_curl($url, $method = 1) {
		$curl_dscr = curl_init($url);
		curl_setopt($curl_dscr, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl_dscr, CURLOPT_HEADER, 0);
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

	function login() {
		$url_login = "https://www.siteminder.co.uk/hoteliers/j_acegi_security_check";	
		
		$ch = $this->init_curl($url_login, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "j_password=".$this->password."&j_username=".$this->username."&x=0&y=0");

		$info = curl_exec($ch);
		$this->uninit_curl($ch);

		$html = phpQuery::newDocument($info);
		$status = $html->find('a[href="../j_acegi_logout"]')->length > 0;
		phpQuery::unloadDocuments();

		return $status;
	}

	function fetch($date) {
		$url_fetch = "https://www.siteminder.co.uk/hoteliers/inventory/editInventory.do?hotelierId=".$this->ext_siteminder_iHotelier."&showStopSells=&showMinStays=&fromDate=".$date."&scrollDirection=REFRESH&scrollDays=0&scrollTop=114";

		$ch = $this->init_curl($url_fetch, 0);

		$info = curl_exec($ch);
		$this->uninit_curl($ch);

		return $info;
	}
	function send_stock($cells) {
		$cells["hotelierId"] = $this->ext_siteminder_iHotelier;
		return $this->send_post($cells);
	}
	function send_post($mixed) {
		$url_post = "https://www.siteminder.co.uk/hoteliers/inventory/updateInventory.rpc";
		$post = '';

		foreach ( $mixed as $id => $val ) $post .= "&$id=$val";

		$ch = $this->init_curl($url_post, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, substr($post, 1));
		$info = curl_exec($ch);
		$this->uninit_curl($ch);
echo $info;
		/*$info = trim($info, "{}");
		$result = explode(',', $info);
		$result = explode(':', $result[0]);

		return ( strlen($result[1]) > 2 ? TRUE : FALSE );
		*/
		return $info;
	}
}

?>