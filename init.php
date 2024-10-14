<?
include "db_connect_cfg.php";
include "cls.proc.php";
include 'town_json.php';
proc::get_instance()->__init();
function return_message($val, $idx, $msg) {
		$result_list		= array();
		if(!$val) {
			$result = array("result" => $idx, "msg" => $msg);
			array_push($result_list, $result);
			check_return(false, false, $result_list);
			//mysql_close($con);
			exit;
		}
}
?>