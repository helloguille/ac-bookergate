<?php

class Access_Lastminute {


	function __construct() {
		$this->username = "lasramblas";
		$this->password = "Bcn2010";
		$this->cookie_file = $_SERVER["DOCUMENT_ROOT"]."/cache/cookie_lastminute.cookie";
		$this->referer = "https://extranet.lastminute.com/extranet/index.do";
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
		$url_login = "https://extranet.lastminute.com/extranet/j_acegi_security_check";

		$ch = $this->init_curl($url_login, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "j_password=".$this->password."&j_username=".$this->username."&x=0&y=0");

		$info = curl_exec($ch);
		$this->uninit_curl($ch);

		$html = phpQuery::newDocument($info);
		$status = $html->find('a[href="/extranet/logout.do"]')->length > 0;
		phpQuery::unloadDocuments();

		if ( !$status ) return $status;

		$url_fetch = "https://extranet.lastminute.com/extranet/accomm/allocations/allocationcalendar.do?allocationId=940490&accommUnitId=108914&productId=1068401";
		$ch = $this->init_curl($url_fetch, 0);
		curl_exec($ch);
		$this->uninit_curl($ch);

		return $status;
	}
	function fetch($year, $month) {
		$url_fetch = "https://extranet.lastminute.com/extranet/accomm/allocations/allocationcalendar.do";
		$post = "alertCheck=true&rowUpdate=false&accommUnitId=108914&allocationId=940490&productId=1068401&priorityMode=ShowMonth&selectedDate=&selectedMonth=".$month."&selectedYear=".$year;

		$ch = $this->init_curl($url_fetch, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

		$info = curl_exec($ch);
		$this->uninit_curl($ch);

		return $info;
	}
	function send_stock($year, $month, $mixed) {
		return $this->send_post($year, $month, $mixed);
	}
	function send_post($year, $month, $mixed) {
		$url_post = "https://extranet.lastminute.com/extranet/accomm/allocations/allocationcalendar.do";
		$post = "alertCheck=true&mode=update&rowUpdate=true&accommUnitId=108914&allocationId=940490&productId=1068401&priorityMode=&selectedDate=&selectedMonth=".$month."&selectedYear=".$year;

		foreach ( $mixed as $id => $val ) $post .= '&calendar.dates['.$id.'].remainingUnits='.$val['to_sell'].'&calendar.dates['.$id.'].ratesById(2).rateActual='.$val['rate'];

		$ch = $this->init_curl($url_post, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, substr($post, 1));

		$info = curl_exec($ch);
		$this->uninit_curl($ch);

		return $info;
	}

	function check($updated, $doc) {
   	$html = phpQuery::newDocument($doc);

   	foreach ($html->find('tr[id^="row"]') as $tr) {
			$row_id = substr(pq($tr)->attr('id'), 3);

			if ( array_key_exists($row_id, $updated) ) {
				$to_sell = trim(pq($tr)->find('input[name$=".remainingUnits"]')->val());
				$rate = trim(pq($tr)->find('input[name$=".ratesById(2).rateActual"]')->val());

				// if ( $updated[$row_id]['to_sell'] == $to_sell && $updated[$row_id]['rate'] == $rate ) unset($updated[$row_id]);
				if ( $updated[$row_id]['to_sell'] == $to_sell) unset($updated[$row_id]);
			}
   	}
		phpQuery::unloadDocuments();

		return ( count($updated) == 0 );
	}
}


?>