<?
	include("fetchid_Lastminute.php");
	include("Access_Lastminute.php");
	include("../phpQuery/phpQuery.php");


	$a_siteminder = new Access_Siteminder();
	
	if ( $a_siteminder->login() ) {
		echo $a_siteminder->fetch('2010-08-06');
		echo var_dump($a_siteminder->send_post(array("hrtda_149362" => 2, "hrtda_149763" => 3, "hotelierId" => 41)));
	}
	else {
		echo "login not successfull";	
	}