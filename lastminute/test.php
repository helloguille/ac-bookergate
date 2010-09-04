<?
	include("fetchid_Lastminute.php");
	include("Access_Lastminute.php");
	include("../phpQuery/phpQuery.php");



	$hw = new Access_Lastminute();
	if ( $hw->login() ) {
		var_dump(fetchid_Lastminute($hw->fetch('2011', '3')));
		$new_values = array("334" => array('to_sell' => 11),
									"335" => array('to_sell' => 22),
									"336" => array('to_sell' => 33),
									"337" => array('to_sell' => 44),
									"338" => array('to_sell' => 55));
	/*
		$new_values = array("334" => array('to_sell' => 11, 'rate' => '111'),
									"335" => array('to_sell' => 22, 'rate' => '122'),
									"336" => array('to_sell' => 33, 'rate' => '133'),
									"337" => array('to_sell' => 44, 'rate' => '144'),
									"338" => array('to_sell' => 55, 'rate' => '155'));
	*/
		$res = $hw->send_post('2011', '3', $new_values);
		var_dump($hw->check($new_values, $res));
	}
	
	
	