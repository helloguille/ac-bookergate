<?
	include("fetchid_Lastminute.php");
	include("Access_Lastminute.php");
	include("../phpQuery/phpQuery.php");

	var_dump(sitem_parse_ids(file_get_contents('test/sm.html')));


	$hw = new BookerGate_sitem();
	if ( $hw->login() ) {
		$data = $hw->fetch('2011', '3');
	
		$new_values = array("334" => array('to_sell' => 11, 'rate' => '111'),
									"335" => array('to_sell' => 22, 'rate' => '122'),
									"336" => array('to_sell' => 33, 'rate' => '133'),
									"337" => array('to_sell' => 44, 'rate' => '144'),
									"338" => array('to_sell' => 55, 'rate' => '155'));
	
		$res = $hw->send_post('2011', '3', $new_values);
		var_dump($hw->check($new_values, $res));
	}
	
	
	