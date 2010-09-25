<?

	include("Access_Hostelworld.php");
	include("../phpQuery/phpQuery.php");

	if ($_SERVER['DOCUMENT_ROOT'] == "") {
		$_SERVER['DOCUMENT_ROOT'] =	getcwd();
	}

$hw = new Access_Hostelworld();
if ( $hw->login() ) {
	$new = array('74132' => 3,'74133' => 1, '74129' => 5);

	$hw->send_post('2011-09-08', $new);

	$info = $hw->fetch('2011-09-08', '2011-09-09');
	//echo $info;
	var_dump($hw->check($new, $info));
}

