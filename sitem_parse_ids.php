<?
require_once("lib/phpQuery.php");

function sitem_parse_ids($fn) {
	$arr = array();

	$html = phpQuery::newDocument($fn);
	foreach ($html->find('input[id^="hrtda_"]') as $in) {
		$fcs = pq($in)->attr('onfocus');
		$id = pq($in)->attr('id');

		$indexs = explode(',', $fcs);
		$indexs[1] = trim($indexs[1]);
		$indexs[2] = ltrim($indexs[2], " '");
		$indexs[2] = rtrim($indexs[2], "')");

		$id_num = explode('_', $id);

		$arr[$indexs[1]][$indexs[2]] = $id_num[1];
	}
	phpQuery::unloadDocuments();

	return $arr;
}