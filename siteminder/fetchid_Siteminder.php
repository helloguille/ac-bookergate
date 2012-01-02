<?

function fetchid_Siteminder($fn) {
	/*
		Obtiene desde el formulario de edición de inventario de 
		siteminder los IDs de cada celda según corresponda.
		
		array (
		 ext_id_sitem => 
		 array(
		 	date => cell id
		 )
		)
	*/
	$arr = array();

	$html = phpQuery::newDocument($fn);
	foreach ($html->find('input[id^="hrtds_"]') as $in) {
		$ext_id_sitem = pq($in)->attr('data-hrt');
		$date = pq($in)->attr('data-date');
		$date = substr($date, 0, 4)."-".substr($date, 4, 2)."-".substr($date, 6, 2);
		$parts = explode('_', pq($in)->attr('id'));

		$arr[$ext_id_sitem][$date] = $parts[1];
	}
	phpQuery::unloadDocuments();

	return $arr;
}