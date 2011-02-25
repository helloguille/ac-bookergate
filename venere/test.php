<?php
		include 'libs/venere/VenereWSBase.php';
		include 'base_methods.php';
		require 'libs/nusoap/nusoap.php';
		require 'config/Credentials.php';
    include ('Access_Venere.php');

    $room_serivce = new Access_Venere(array("hotel_id" => 311025, "password" => "Wa8puChu"));
    $room_serivce->update_availability("1201708", "2011-02-25", "2011-02-25", array(8));
?>
