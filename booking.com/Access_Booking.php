<?php

class Access_Booking {
	/*
		TODO: metodo close_session() para unlink($this->cookie_file)
	*/
	
	function __construct() {

		$this->loginname = "242484";
		$this->password = "6912";
		$this->hotel_id = "242484";

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
		$this->raw = curl_exec($ch);
		$this->uninit_curl($ch);
		
		$html = phpQuery::newDocument($this->raw);
		$this->ses = $html->find('input[name="ses"]')->attr('value');
		phpQuery::unloadDocuments();
		return $this->ses;
	}
	function login() {
		$this->get_ses();
		$ch = $this->init_curl("https://admin.bookings.org/hotel/login.html", 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "loginname=".$this->loginname."&password=".$this->password."&ses=".$this->ses."&login=Login");
		$this->raw = curl_exec($ch);
		$this->uninit_curl($ch);
		$status = strpos($this->raw, "Bookings") > 0;
		phpQuery::unloadDocuments();
		return $status;
	}
	function get_contents($url, $method = 0) {
		$ch = $this->init_curl($url, $method);
		$this->raw = curl_exec($ch);
		$this->uninit_curl($ch);
		return $this->raw;
	}
	function get_bookings() {
		$previous_month = date("m", strtotime("-1 month"));
		$current_month = date("m");

		$previous_year = date("Y", strtotime("-1 month"));
		$current_year = date("Y");		
		
		//exit("previous_year = $previous_year / current_year = $current_year");
		
		$this->raw = $this->get_contents("https://admin.bookings.org/hotel/hoteladmin/bookings.html?ses=".$this->ses."&hotel_id=".$this->hotel_id."&last_period=month&type=booking&period=month&selected_period=".$previous_year."-".$previous_month."-01&show=Show")	;
		$html = phpQuery::newDocument($this->raw);
		foreach ($html->find('a[href*="booking.html"]') as $b) {
			$list[] = $b->nodeValue;	
		}
		phpQuery::unloadDocuments();

		$this->raw = $this->get_contents("https://admin.bookings.org/hotel/hoteladmin/bookings.html?ses=".$this->ses."&hotel_id=".$this->hotel_id."&last_period=month&type=booking&period=month&selected_period=".$current_year."-".$current_month."-01&show=Show")	;
		$html = phpQuery::newDocument($this->raw);
		foreach ($html->find('a[href*="booking.html"]') as $b) {
			$list[] = $b->nodeValue;	
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
			"Total costs all rooms",
			"Status",
			"Arrival",
			"Departure",
			"Room type",
			"Number of persons",
			
		);
		$this->raw = $this->get_contents("https://admin.bookings.org/hotel/hoteladmin/booking.html?bn=".$booking_id.";hotel_id=".$this->hotel_id.";ses=".$this->ses)	;
		$html = phpQuery::newDocument($this->raw);
		$rows = $html->find('.tablebig td');
		$list["_rooms"] = array();
		foreach ($rows as $key => $b) {
			$value = utf8_decode(trim(ereg_replace(' +', ' ', $b->nodeValue)));
			
			if (strlen($put_value_into_key) > 0 ) 
			{
				if ($put_value_into_key != "Room type") {
					$list[$put_value_into_key] = $value;
				}
				else {
					$list["_rooms"][] = $value;
					unset($list[$put_value_into_key]);
				}
				$put_value_into_key = null;
			}
			if (in_array($value, $fields)) {
				$list[$value] = "N";
				$put_value_into_key = $value;
			}			
		}
		
		$rows = $html->find('.tablebig td');
		
		
		
		return $list;
	}
	function getRaw() {
		return $this->raw;	
	}
}

