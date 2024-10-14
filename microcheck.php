<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<?

	include "init.php";
	$proc_tag = isset($_POST['pt']) ? $_POST['pt'] : '';
	$proc_data = isset($_POST['n']) ? $_POST['n'] : '';

	$table_name	= "wb_t_data";

	$rtn_msg	= "";
	$rtn		= false;
  
	$sql	= "SELECT product_flag FROM $table_name WHERE key_nfc = '$proc_data' and key_qr = '$proc_data' and product_flag = '1' LIMIT 1";
	$result	= mysql_query($sql);
	$count	= mysql_num_rows($result);

	if ($count == 0) {
		$rtn_msg	= "01_not";	// 당사제품아님
	} else {
		$rtn_msg	= "01";	// 당사제품임
	}

	echo json_encode(array('result'=>$rtn, 'message'=>$rtn_msg));
	exit;

?>
