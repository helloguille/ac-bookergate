<?php
class HW {
	private $username = "agent";
	private $password = "11madrid11";

	private $cookie_file = "cookie.sv";

	private $referer = "https://www.siteminder.co.uk/siteminder/sm-login.html";

	private function init_curl($url) {
		$curl_dscr = curl_init($url);
		curl_setopt($curl_dscr, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl_dscr, CURLOPT_HEADER, 0);
		curl_setopt($curl_dscr, CURLOPT_POST, 1);
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

		$ch = $this->init_curl($url_login);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "j_password=".$this->password."&j_username=".$this->username."&x=0&y=0");

		curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD) > 5000;

		$this->uninit_curl($ch);

		return $status;
	}

	function fetch($date) {
		$url_fetch = "https://www.siteminder.co.uk/hoteliers/inventory/editInventory.do?hotelierId=41&showStopSells=&showMinStays=&fromDate=".$date."&scrollDirection=REFRESH&scrollDays=0&scrollTop=114";

		$ch = $this->init_curl($url_fetch);

		$info = curl_exec($ch);
		$this->uninit_curl($ch);

		return $info;
	}

	function send_post($mixed) {
		$url_post = "https://www.siteminder.co.uk/hoteliers/inventory/updateInventory.rpc";

		$ch = $this->init_curl($url_post);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "hotelierId=41&hrtda_".$mixed[0]."=".$mixed[1]);
		$info = curl_exec($ch);
		$this->uninit_curl($ch);

		$info = trim($info, "{}");
		$result = explode(',', $info);
		$result = explode(':', $result[0]);

		return ( strlen($result[1]) > 2 ? TRUE : FALSE );
	}
}

$hw = new HW();
echo $hw->login();
//echo $hw->fetch('2010-08-06');
echo var_dump($hw->send_post(array(149762, 3)));
?>