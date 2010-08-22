<?php


function fetchid_Lastminute($fn) {
	$arr = array();

	$html = phpQuery::newDocument($fn);

	$year = $html->find('input[name="selectedYear"]')->val();
	$month = $html->find('input[name="selectedMonth"]')->val();

	foreach ($html->find('tr[id^="row"]') as $tr) {
		if ( pq($tr)->find('input[name$=".remainingUnits"]')->length > 0 )
			$arr[sprintf("%04d-%02d-%02d", $year, $month, trim(pq($tr)->find('td:eq(1)')->text()))] = substr(pq($tr)->attr('id'), 3);
	}
	phpQuery::unloadDocuments();

	return $arr;
}

?>