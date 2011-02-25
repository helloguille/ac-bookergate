<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of UpdateAvailability
 *
 * @author Mohamed
 */



class Access_Venere {

    private $venere_services = null;

    function __construct($params) {
        $this->venere_services = new VenereWSBase($params);
    }

    function update_availability($room_category, $from, $to, $values) {
        $time_from = strtotime($from);
        $time_to = strtotime($to);

        if ($time_from > $time_to) {
            throw new Exception("To date should be greater than from date");
        }

        if((date_Diff("-", $to, $from) + 1) != count($values)) {
            throw new Exception("Number of rooms don't match number of days");
        }

        else {
            $params = '<OTA_HotelAvailNotifRQ '.$this->venere_services->get_common_payload_attrs('2.000').'>
                            <AvailStatusMessages HotelCode="'.$this->venere_services->get_hotel_code().'">';
            foreach ($values as &$value) {
                $params = $params.'<AvailStatusMessage BookingLimit="'.$value.'" BookingLimitMessageType="SetLimit">
                                    <StatusApplicationControl Start="'.$from.'" End="'.$from.'"
                                    RatePlanCode="URP"
                                    RatePlanCodeType="RatePlanCode"
                                    InvCode="'.$room_category.'"
                                    IsRoom="true"/>
                                    <LengthsOfStay ArrivalDateBased="true"/>
                                </AvailStatusMessage>';
                $from = date('Y-m-d', strtotime("tomorrow",strtotime($from)));
            }
            $params = $params.'</AvailStatusMessages></OTA_HotelAvailNotifRQ>';
            $result = $this->venere_services->make_request("OTA_HotelAvailNotif", $params, 'OTA_HotelAvailNotif_Request');
            $this->venere_services->print_response($this->venere_services->get_client(),$result);
        }
    }

}
?>
