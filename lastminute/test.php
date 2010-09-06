<?
	include("fetchid_Lastminute.php");
	include("Access_Lastminute.php");
	include("../phpQuery/phpQuery.php");

	if ($_SERVER['DOCUMENT_ROOT'] == "") {
		$_SERVER['DOCUMENT_ROOT'] =	getcwd();
	}

	$hw = new Access_Lastminute();
	if ( $hw->login() ) {
		//file_put_contents("fetch.html", $hw->fetch('2011', '3'));
		var_dump(fetchid_Lastminute($hw->fetch('2011', '3')));
		$new_values = array("334" => array('to_sell' => 1),
									"335" => array('to_sell' => 2),
									"336" => array('to_sell' => 3),
									"337" => array('to_sell' => 4),
									"338" => array('to_sell' => 5));
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
	else {
		echo "Could not login";	
	}
	
	
	