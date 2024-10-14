<?

//	session_cache_expire(60);
//	if (!session_id()) {
//	  session_start();
//	}

	include "init.php";
	$proc_tag = isset($_GET['pt']) ? $_GET['pt'] : '';
	$proc_data = isset($_GET['n']) ? $_GET['n'] : '';

	$uuid	= "";
	$mf_func	= "";
	$item_status	= "";
	$item_seq		= "";

	$table_name	= "wb_t_data";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style>
	.img_view {position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
	}
	
	.img_conf_div {
			display: flex;
			justify-content: center;
	}	
	.img_conf_div2 {
			display: flex;
			justify-content: center;
			padding-top: 10px;
	}
	.img_conf {
/*	    width: 80%;*/
	}

	.img_fin {
	    width: 80%;
	}

	.info { position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			font-size:50px;
			font-weight:700;
			color:#13289f;
			text-align:center;
			padding-bottom:120px;  }

	.info_error { position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			font-size:50px;
			font-weight:700;
			color:#ff0000;
			text-align:center;
			padding-bottom:120px;  }
</style>
<body>

<?

$cookie_name	= 'checkQr';

if (empty($proc_tag))
	$proc_tag	= isset($_POST['pt']) ? $_POST['pt'] : '';
if (empty($proc_qr))
	$proc_qr	= isset($_POST['qr']) ? $_POST['qr'] : '';
if (empty($proc_data))
	$proc_data	= isset($_POST['n']) ? $_POST['n'] : '';

switch ($proc_tag)
{
	case "sam":

		if (strlen($proc_data) == 47) {

			$uuid			= substr($proc_data,  0, 11);
			$mf_func		= substr($proc_data, 11, 28);
			$item_status	= substr($proc_data, 39, 02);
			$item_seq		= substr($proc_data, 41, 06);

			$sql	= "SELECT product_flag FROM $table_name WHERE key_nfc = '$proc_data' and key_qr = '$proc_data' and product_flag = '1' LIMIT 1";
			$result	= mysql_query($sql);
			$count	= mysql_num_rows($result);

			if ($count == 0) {
				$proc_status	= "01_not";	// 당사제품아님
			} else {
				$proc_status	= "01";	// 당사제품임
			}
		} else {
			$proc_status	= "01_error";
		}

		break;

	case "nfcck":

		echo $_COOKIE[$cookie_name];
//		$proc_qr	= isset($_SESSION[$cookie_name]) ? $_SESSION[$cookie_name] : '';	// get qr
		$proc_qr	= isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : '';	// get qr
//		$proc_qr	 = $proc_data;

		$sql	= "SELECT product_flag FROM $table_name WHERE key_nfc = '$proc_data' and key_qr = '$proc_qr' LIMIT 1";
		$result	= mysql_query($sql);
		$count	= mysql_num_rows($result);

		echo $sql;

		if ($count == 0) {
			$proc_status	= "02_error";	// 당사제품아님
		} else {
			$row		= mysql_fetch_assoc($result);
			if ($row["product_flag"] == "1")
				$proc_status	= "02";	// 당사제품
			else
				$proc_status	= "02_not";	// 당사제품아님
		}
			
		break;

	case "usitem":

//		$proc_qr	= '1221';
//		$proc_qr	= $proc_data;

		if (empty($proc_tag) || empty($proc_qr) || empty($proc_data)) {
			$proc_status	= "02_empty";	// 파라메타오류
		} else {
			$sql	= "SELECT key_qr, key_nfc, insert_datetime, proc_datetime, scan_count FROM $table_name WHERE key_nfc = '$proc_data' and key_qr = '$proc_qr' LIMIT 1";
			$result	= mysql_query($sql);
			$count	= mysql_num_rows($result);

//			echo $sql;

			if ($count == 0) {
				$proc_status	= "02_error";	// 당사제품아님
			} else {
				$bef_date	= date("Y-m-d",strtotime("-6 month"));	// 6개월체크
				$row		= mysql_fetch_assoc($result);
				$ins_date	= $row["insert_datetime"];	// 6개월체크
				if (is_null($ins_date)) {	// 등록하지 않음
					$proc_status	= "02_unreg";	// 정품 미등록상품
				} else if ($bef_date < $ins_date) {
					$proc_status	= "02_reg_low";	// 등록상품(6개월미만)
				} else {
					$proc_status	= "02_reg_over";	// 등록상품(6개월초과)
				}
			}
		}
			
		break;

	default:
		
		$proc_status	= $proc_tag;

		switch ($proc_tag)
		{
			case "02_unreg":	// 정품 미등록상품
			case "02_reg_low":	// 등록상품(6개월미만)
			case "02_reg_over":	// 등록상품(6개월초과)
			case "fin":			// 완료
				break;
			default:
				$proc_status	= "00_error";	// qr읽을경우 구분값 오류
				break;
		}

		break;
}

//echo $proc_status;

switch ($proc_status)
{
	case "00_error":	// qr읽을경우 구분값 오류

		$img_src_msg	= "This is<br>not normal QR<br>information.";
		$img_src_class	= "info_error";

?>
		<div>
			<img class="img_view" src="./image/filter_01.png">
			<div class="<?=$img_src_class?>"><?=$img_src_msg?></div>
		</div>
<?

		break;

	case "01_not":	// qr 촬영 당사제품은 아니나 등록되어있음
?>
		<script>
			var err_count	= 1;
			self.location = "https://powerkis.github.io/webtest/qr_code.html?ec=" + err_count;
			
			function setQr(arg) {
				var arrPara = arg.split("?");
				var para = "";
				if (arrPara.length > 1) {
					var params1   = arrPara[1].split('&');
					for( var i=0; i<params1.length; i++ )
					{
						var param = params1[i].split('=');
						para=param[1];
					}
				}


				if (para.length == 47) {

					var uuid			= para.substr(0, 11);
					var mf_func			= para.substr(11, 28);
					var item_status		= para.substr(39, 02);
					var item_seq		= para.substr(41, 06);
					
					var rtn	= false;
					var params;
					params 	 = { pt:'sam'
								,n : para };

					$.ajax({
						type : "POST",
						url : "microcheck.php", //요청 할 URL
						data : params , //넘길 파라미터
						async: false, 
						dataType : "json",
						success : function(data) {
							//통신이 정상적으로 되었을때 실행 할 내용
							if(data != null)    {
								if(data.result){
									err_count++;
									alert("QR Scan Retry : " + err_count);
								}else{
									console.log(data);
									alert("QR Scan ok : " + err_count);
								}
							}
						},
						error : function(data) {
							err_count++;
							console.log(data);
							alert("오류 " + data.message); //에러시 실행 할 내용
							rtn	= false;
						}
					});
				} else {
					err_count++;
				}

				//resultDiv.innerText	= "error : " + err_count;

			}

		</script>
		
<!-- 		<div class="img_view"> -->
<!-- 			<img src="./image/filter_02.png"> -->
<!-- 		</div> -->
<?
		break;

	case "01_error":	// qr 촬영 error

		$img_src_msg	= "This is<br>not normal QR<br>information.";
		$img_src_class	= "info_error";
?>
		<div class="img_view">
			<img src="./image/filter_01.png">
			<div class="<?=$img_src_class?>"><?=$img_src_msg?></div>
		</div>
<?
		break;

	case "01":	// qr 촬영 ok

		$img_src_msg	= "Use<br>an NFC card<br>to authenticate<br>the product.";
		$img_src_class	= "info";

		setcookie($cookie_name, $proc_data, time()+3600, "/");	/* 1 hour */
//		$_SESSION[$cookie_name]	= $proc_data;

?>
		<div>
			<img class="img_view" src="./image/filter_01.png">
			<div class="<?=$img_src_class?>"><?=$img_src_msg?></div>
		</div>
<?

		break;

	case "02":	// 사용품
?>
		<map name="03_con">
		  <area shape="rect" coords="80, 820, 370, 880" href="javascript:onNext()" />
		</map>
		<div>
			<img class="img_view" src="./image/filter_03.png" usemap="#03_con">
		</div>
		<form method="post" action="microfilter.php" name="frm" >
			<input type="hidden" id="pt" name="pt" value="usitem">
			<input type="hidden" id="qr" name="qr" value="<?=$proc_qr?>">
			<input type="hidden" id="n" name="n" value="<?=$proc_data?>">
		</form>
		<script>
			function onNext() {
				frm.submit();
			}
		</script>
<?		
		break;
		
	case "02_reg_over":	// 등록상품(6개월초과)
?>
		<map name="04_conf">
		  <area shape="rect" coords="110, 400, 250, 420" href="javascript:onConfirm()" />
		</map>
		<div class="img_conf_div">
			<img class="img_conf" src="./image/filter_04.png" usemap="#04_conf">
		</div>
		<form method="post" action="microfilter.php" name="frm" >
			<input type="hidden" id="pt" name="pt" value="02_reg_low">
			<input type="hidden" id="qr" name="qr" value="<?=$proc_qr?>">
			<input type="hidden" id="qr" name="qr" value="<?=$proc_data?>">
		</form>
		<script>
			function onConfirm() {
				alert('등록상품(6개월초과)');
				frm.submit();
			}
		</script>
<?		
		break;

	case "02_unreg":	// 정품 미등록상품
?>
		<map name="05_check">
		  <area shape="rect" coords="60, 805, 360, 870" href="javascript:onNext()" />
		</map>
		<div>
			<img class="img_view" src="./image/filter_05.png" usemap="#05_check">
		</div>
		<form method="post" action="microfilter.php" name="frm">
			<input type="hidden" id="pt" name="pt" value="02_reg_low">
			<input type="hidden" id="qr" name="qr" value="<?=$proc_qr?>">
			<input type="hidden" id="n" name="n" value="<?=$proc_data?>">
		</form>
		<script>
			function onNext() {
				frm.submit();
			}
		</script>
<?
		break;

	case "02_reg_low":	// 정품 미등록상품 - 등록상품(6개월미만) - 등록상품(6개월초과)
		// 정품표시
?>
		<style>
			.question_01 {
				position: absolute;
				padding-top:530px;
				padding-left:30px;
				font-size:10px;
				color:#000000;
		}
		</style>
		<map name="fin">
		  <area shape="rect" coords="105, 1222, 194, 1241" href="javascript:onFinish()" />
		</map>
		<div class="img_conf_div" onclick="onFinish()">
			<img class="img_conf" src="./image/filter_07_1.png">
		</div>
		<div class="img_conf_div2">
			<img class="img_conf" src="./image/filter_07_2.png" usemap="#fin">
		</div>
		<div class="img_conf_div2">
			<img class="img_conf" src="./image/filter_07_3.png">
		</div>
		<form method="post" action="microfilter.php" name="frm">
			<input type="hidden" id="pt" name="pt" value="fin">
			<input type="hidden" id="qr" name="qr" value="<?=$proc_qr?>">
			<input type="hidden" id="n" name="n" value="<?=$proc_data?>">
		</form>
		<script>
			function javascript:onFinish() {
			alert("fin");
				frm.submit();
			}
		</script>
<?

		break;

	case "02_error":	// 당사제품아님

		$img_src_msg	= "This is<br>not normal NFC<br>information.";
		$img_src_class	= "info_error";
?>
		<div>
			<img class="img_view" src="./image/filter_01.png">
			<div class="<?=$img_src_class?>"><?=$img_src_msg?></div>
		</div>
<?
		break;

	case "02_empty":	// 파라메타오류

		$img_src_msg	= $proc_tag . ' ' . "This is<br>not normal<br>NFC/QR<br>information.";
		$img_src_class	= "info_error";
?>
		<div>
			<img class="img_view" src="./image/filter_01.png">
			<div class="<?=$img_src_class?>">empty <?=$img_src_msg?></div>
		</div>
<?
		break;

	case "fin":	// 완료
?>
		<div class="img_view">
			<img src="./image/filter_08.png">
		</div>
<?
		break;
  }
?>

</body>
</html>