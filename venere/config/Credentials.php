<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of credentials
 *
 * @author Mohamed
 */
class Credentials {
    private $organization_id;
    private $user_id;
    private $password;
    private $hotel_id;

    function __construct($params){ 
        $this ->organization_id = "ACPropertyManagmentPRO";
        $this ->user_id = "Venere_ACPropertyManagementPRO";
        $this ->password = $params["password"];
        $this ->hotel_id = $params["hotel_id"];
    }

    function get_hotel_id(){
        return $this->hotel_id;
    }

    function get_organization_id(){
        return $this->organization_id;
    }

    function get_user_id(){
        return $this ->user_id;
    }

    function get_password(){
        return $this ->password;
    }

    function get_auth_string(){
        return $this ->user_id."@".$this->organization_id.":".$this ->password;
    }
}
?>
