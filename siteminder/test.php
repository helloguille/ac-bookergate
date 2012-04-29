<?
	include("fetchid_Siteminder.php");
	include("Access_Siteminder.php");
	include("../phpQuery/phpQuery.php");


	$a_siteminder = new Access_Siteminder();
	
	if ( $a_siteminder->login() ) {
		echo "logged";
		echo $a_siteminder->fetch('2010-08-06');
		echo var_dump($a_siteminder->send_stock(array("hrtda_149362" => 2, "hrtda_149763" => 3)));
	}
	else {
		echo "login not successfull";	
	}