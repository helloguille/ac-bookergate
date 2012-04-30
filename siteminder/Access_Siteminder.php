<?php

class Access_Siteminder {

	function __construct($ro = null) {
		
		if (is_object($ro)) { 
			$data = $ro->getExternalSyncDetails("siteminder");
			$this->username = $data["username"];
			$this->password = $data["password"];
			$this->ext_siteminder_iHotelier = $data["siteminder_iHotelier"];
			//print_r($data);
		}
		elseif (is_null($ro)) {
			/*
				This is the configuration used for testing
			*/
			$this->username = "agent2";
			$this->password = "5ramblas5";		
			$this->ext_siteminder_iHotelier = 122;
		}
		
		$this->default_referer = "https://www.siteminder.co.uk/web/login";
		$this->cookie_file = $_SERVER["DOCUMENT_ROOT"]."/cache/siteminder_".md5(uniqid()).".cookie";
	}
	private function init_curl($url, $method = 1, $referer = null) {
		if (!$referer) {
			$referer	 = $this->default_referer;
		}
		
		$curl_dscr = curl_init($url);
		curl_setopt($curl_dscr, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl_dscr, CURLOPT_HEADER, 0);
		curl_setopt($curl_dscr, CURLOPT_POST, $method);
		curl_setopt($curl_dscr, CURLOPT_COOKIEFILE, $this->cookie_file);
		curl_setopt($curl_dscr, CURLOPT_COOKIEJAR, $this->cookie_file);
		curl_setopt($curl_dscr, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl_dscr, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.19) Gecko/2010031422 Firefox/3.0.19");
		curl_setopt($curl_dscr, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_dscr, CURLOPT_REFERER, $referer);

		return $curl_dscr;
	}

	private function uninit_curl($curl_dscr) {
		$this->referer = curl_getinfo($curl_dscr, CURLINFO_EFFECTIVE_URL);
		curl_close($curl_dscr);
	}

	function login() {
		$url_login = "https://www.siteminder.co.uk/web/userSessions";	
		
		$ch = $this->init_curl($url_login, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "password=".$this->password."&username=".$this->username."&x=0&y=0");

		$info = curl_exec($ch);
		$this->uninit_curl($ch);
		
		$this->debug_data["POST_LOGIN_RESULT"] = $info;
		
		$html = phpQuery::newDocument($info);
		$status = $html->find('a[href="/web/logout"]')->length > 0;
		phpQuery::unloadDocuments();

		$this->debug_data["POST_LOGIN_STATUS"] = $status;


		return $status;
	}

	function fetch($date) {
		$url_fetch = "https://www.siteminder.co.uk/web/extranet/inventory/".$this->ext_siteminder_iHotelier."?startDate=".$date;

		$ch = $this->init_curl($url_fetch, 0);

		$info = curl_exec($ch);
		$this->uninit_curl($ch);

		return $info;
	}
	function send_stock($cells) {
		$cells["_method"] = "PUT";
		$cells["state[y]"] = "114";
		$cells["org.codehaus.groovy.grails.SYNCHRONIZER_URI"] = "/web/extranet/inventory/".$this->ext_siteminder_iHotelier;
		$cells["org.codehaus.groovy.grails.SYNCHRONIZER_TOKEN"] = $this->getToken();
		return $this->send_post($cells);
	}
	function getToken() {
		$url_fetch = "https://www.siteminder.co.uk/web/extranet/inventory/".$this->ext_siteminder_iHotelier;
		$ch = $this->init_curl($url_fetch, 0);
		$info = curl_exec($ch);
		$html = phpQuery::newDocument($info);
		$token_value = $html->find('input[name="org.codehaus.groovy.grails.SYNCHRONIZER_TOKEN"]')->val();
		return $token_value;
	}
	function send_post($mixed) {
		
		$url_post = "https://www.siteminder.co.uk/web/extranet/inventory/".$this->ext_siteminder_iHotelier;
		$this->debug_data["url_post"] = $url_post;

		$post = '';

		foreach ( $mixed as $id => $val ) $post .= "&$id=$val";

		$ch = $this->init_curl($url_post, 1, $url_post);
		curl_setopt($ch, CURLOPT_POSTFIELDS, substr($post, 1));
		$info = curl_exec($ch);
		$this->uninit_curl($ch);
		$this->debug_data["POST_SEND_STOCK"] = $info;
		/*$info = trim($info, "{}");
		$result = explode(',', $info);
		$result = explode(':', $result[0]);

		return ( strlen($result[1]) > 2 ? TRUE : FALSE );
		*/		
		return $info;
	}
}

?>