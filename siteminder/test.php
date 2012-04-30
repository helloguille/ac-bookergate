<?
	include("fetchid_Siteminder.php");
	include("Access_Siteminder.php");
	include($_SERVER["DOCUMENT_ROOT"]."/sites/default/lib/phpQuery/phpQuery.php");


	$a_siteminder = new Access_Siteminder();
	
	if ( $a_siteminder->login() ) {
		echo "logged";
		echo var_dump($a_siteminder->send_stock(array("hrtds[2238714]" => 0)));
	}
	else {
		echo "login not successfull";	
	}