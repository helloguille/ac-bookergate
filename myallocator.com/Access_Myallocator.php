<?

class Access_Myallocator {


    function __construct($stockupdate) {
    	$this->stockupdate = $stockupdate;
    }

    function SetAllocation($from, $to, $stock, $rate) {
		$UserId = "agent";
		$UserPassword = "11madrid11";

		$xml_request = "<?xml version=\"1.0\"?> 
		<SetAllocation>
		<Auth>
		  <UserId>".$UserId."</UserId> 
		  <UserPassword>".$UserPassword."</UserPassword> 
		  <PropertyId>".$this->stockupdate->syncconnection->myallocator_propertyid."</PropertyId> 
		  <VendorId>madridac</VendorId> 
		  <VendorPassword>7ENk42zQLN</VendorPassword>
		</Auth>
		<Channels>
		  <Channel>all</Channel>
		</Channels>
		<Allocations>
		  <Allocation>
		    <RoomTypeId>".$this->stockupdate->syncconnection->myallocator_roomid."</RoomTypeId>
		￼￼  <StartDate>".date("Y-m-d", $from)."</StartDate>
		    <EndDate>".date("Y-m-d", $to)."</EndDate>
		    <Units>".$stock."</Units>
		    <Prices>
		      <Price>".$rate."</Price>
		      <Price weekend=\"true\">".$rate."</Price>
		    </Prices>
		  </Allocation>
		</Allocations>
		</SetAllocation>";


		//exit($xml);
		$postfields = array(
		  'xmlRequestString' => $xml_request
		);
		// We send XML via CURL using POST with a http header of text/xml.
		$ch = curl_init();
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, "http://api.myallocator.com");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$ch_result = curl_exec($ch);
		curl_close($ch);
		// Print CURL result.

		$xml = simplexml_load_string($ch_result);
		if ($xml->Success == "true") {
			return true;
		}
		else {
			$this->stockupdate->log_Event("Access_Myallocator::SetAllocation: XML failed. xmlRequestString:", $xml_request);
			$this->stockupdate->log_Event("Access_Myallocator::SetAllocation: XML failed. Response:", $ch_result);
		}
		return false;

    }
}


?>