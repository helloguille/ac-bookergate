<?php

class Access_Booking {
	/*
		TODO: metodo close_session() para unlink($this->cookie_file)
	*/
	
	function __construct() {

		$this->loginname = "286161";
		$this->password = "4474";
		
		$this->cookie_file = $_SERVER["DOCUMENT_ROOT"]."/cache/booking_".md5(uniqid()).".cookie";
		$this->referer = "https://admin.bookings.org/hotel/";
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
	function get_ses() {
		$ch = $this->init_curl("https://admin.bookings.org/hotel/", 1);
		$info = curl_exec($ch);
		$this->uninit_curl($ch);
		
		$html = phpQuery::newDocument($info);
		$this->ses = $html->find('input[name="ses"]')->attr('value');
		phpQuery::unloadDocuments();
		return $this->ses;
	}
	function login() {
		$this->get_ses();
		$ch = $this->init_curl("https://admin.bookings.org/hotel/login.html", 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "loginname=".$this->loginname."&password=".$this->password."&ses=".$this->ses."&login=Login");
		$info = curl_exec($ch);
		$this->uninit_curl($ch);
		$status = strpos($info, "Bookings") > 0;
		phpQuery::unloadDocuments();
		return $status;
	}
	function get_contents($url, $method = 0) {
		$ch = $this->init_curl($url, $method);
		$info = curl_exec($ch);
		$this->uninit_curl($ch);
		return $info;
	}
	function get_bookings_list() {
		$info = $this->get_contents("https://admin.bookings.org/hotel/hoteladmin/bookings.html?ses=".$this->ses."&hotel_id=286161&last_period=month&type=booking&period=month&selected_period=2011-02-01&show=Show")	;
		$html = phpQuery::newDocument($info);
		foreach ($html->find('a[href*="booking.html"]') as $b) {
			$list[] = $b->nodeValue."<br>";	
		}
		phpQuery::unloadDocuments();
		return $list;
	}
	function get_booking($booking_id) {
		$fields = array(
			"Booker name",
			"E-mail",
			"Telephone",
			"Total number of rooms",
			"Total number of guests",
			"Total costs",
			"Status",
			"Arrival",
			"Departure"
		);
		$info = $this->get_contents("https://admin.bookings.org/hotel/hoteladmin/booking.html?bn=".$booking_id.";hotel_id=286161;ses=".$this->ses)	;
		$html = phpQuery::newDocument($info);
		$rows = $html->find('.tablebig td');
		foreach ($rows as $key => $b) {
			if (strlen($put_value_into) > 0 ) 
			{
				$list[$put_value_into] = $b->nodeValue;
				$put_value_into = null;
			}
			if (in_array($b->nodeValue, $fields)) {
				$list[$b->nodeValue] = "N";
				$put_value_into = $b->nodeValue;
			}			
		}
		return $list;
	}
}

$ab = new Access_Booking();
$ab->login();
print_r($ab->get_booking(399107999));

