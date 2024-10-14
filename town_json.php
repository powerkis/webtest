<?php
include 'JSON.php';
$json = new Services_JSON();

function check_return($is_db_error, $is_query_error,$ad_list) {
	global $json;
	if ($is_db_error) {
		$result = array("check" => 'invalid', "res" => -1,"data" => $ad_list);
	} else {
		if ($is_query_error) {
			$result = array("check" => 'valid', "res" => -1,"data" => $ad_list);
		} else {
			$result = array("check" => 'valid', "res" => 0,"data" => $ad_list);
		}
	}
	$result = json_encode($result);
	echo $result;
}


?>