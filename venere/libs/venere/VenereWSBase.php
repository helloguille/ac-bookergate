<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VenereWSInterface
 *
 * @author Mohamed
 */ 


class VenereWSBase {
    //put your code here

    private $root_url = "https://xhi.venere.com/xhi-1.0/services/";
    private $schema_path = "xmlns=\"http://www.opentravel.org/OTA/2003/05\"";
    private $version = null;
    private $target = "Target=\"Production\"";
    private $token = "EchoToken=\"A01256\"";
    private $common_payload_attrs = null;
    private $client;
    private $credentials;
    
    function __construct($params){
        $this->credentials = new Credentials($params);
    }

    function make_request($service_name,$params,$function)
    {
        $this->client = new soapclientnusoap($this->root_url.$service_name.".soap?wsdl",true);
        $this->client->setHeaders($this->get_soap_auth_header());
        $result = $this->client->call($function, $params);
        return $result;
    }

    function test_connection_service(){
        $params = '<OTA_PingRQ '.$this->get_common_payload_attrs('1.004').'>
                <EchoData>Hello</EchoData>
            </OTA_PingRQ>';
        $result = $this->make_request("OTA_Ping", $params, 'OTA_Ping_Request');
        $this->print_response($this->client,$result);
    }

    function get_client(){
        return $this->client;
    }

    function print_response($client,$result){
        if ($client->fault) {
            echo '<h2>Debug</h2><pre>'; echo htmlspecialchars($client->request, ENT_QUOTES); echo '</pre>';
            echo '<h2>Fault</h2><pre>'; print_r($result); echo '</pre>';
        }
        else {
            $err = $client->getError();
        }
        if ($err) {
            echo '<h2>Debug</h2><pre>'; echo htmlspecialchars($client->request, ENT_QUOTES); echo '</pre>';
            echo '<h2>Error</h2><pre>' . $err . '</pre>';
        }
        else
            {
            echo '<h2>Result</h2><pre>'; print_r($result); echo '</pre>';
        }
    }

    function get_common_payload_attrs($ver){
        $this->version = "Version=\"".$ver."\"";
        $this->common_payload_attrs = $this->token.' '.$this->target.' '.$this->version.' '.$this->schema_path;
        return $this->common_payload_attrs;
    }

    function get_soap_auth_header(){
       $header ="<Authentication xmlns=\"http://www.venere.com/XHI\">
                            <UserOrgID>".$this->credentials->get_organization_id()."</UserOrgID>
                            <UserID>".$this->credentials->get_user_id()."</UserID>
                            <UserPSW>".$this->credentials->get_password()."</UserPSW>
                        </Authentication>";
       return $header;
    }

    function get_hotel_code(){
        return $this->credentials->get_hotel_id();
    }
}
?>
