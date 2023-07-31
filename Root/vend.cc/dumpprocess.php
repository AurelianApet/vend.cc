<?php
session_start();
require("./includes/config.inc.php");
require_once("./includes/user.php");

$user = new user($db);

//If login attempt else check login session
$checkLogin = $user->valid_login();
//user info var
$user_info = $user->user_info;

function giveDownload($downloadDumps, $file_name) {
	$content = "ID|NUMBER|EXP|[DUMP FULL INFO]|[ADDITIONAL INFORMATION]|CHECK RESULT\r\n";
	if (count($downloadDumps) > 0) {
		foreach ($downloadDumps as $key=>$value) {
			switch ($value['dump_check']) {
				case strval(CHECK_VALID):
					$value['dump_checkText'] = "APPROVED";
					break;
				case strval(CHECK_INVALID):
					$value['dump_checkText'] = "TIMEOUT";
					break;
				case strval(CHECK_REFUND):
					$value['dump_checkText'] = "DECLINE";
					break;
				case strval(CHECK_UNKNOWN):
					$value['dump_checkText'] = "UNKNOWN";
					break;
				case strval(CHECK_WAIT_REFUND):
					$value['dump_checkText'] = "WAIT REFUND";
					break;
				default :
					$value['dump_checkText'] = "UNCHECK";
					break;
			}
			$content .= $value['dump_id']."|".$value['dump_number']."|".$value['dump_exp']."|[".$value['dump_fullinfo']."]|[".$value['dump_additional']."]|".$value['dump_checkText']."|"."\r\n";
		}
	}
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-type: text/force-download");
	header("Content-Disposition: attachment; filename=".$file_name);
	header("Content-Description: File Transfer");
	echo $content;
}
if (checkLogin(PER_USER)) {
	if (isset($_POST["download_all"]) || isset($_POST["download_expired"]) || (isset($_POST["download_select"]) && is_array($_POST["dumps"]))) {
		if (isset($_POST["download_all"])) {
			$file_name = $_SESSION["user_name"]."_DUMPS_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(dump_number, '".strval(DB_ENCRYPT_PASS)."') AS dump_number, AES_DECRYPT(dump_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS dump_fullinfo FROM `".TABLE_DUMPS."` WHERE dump_status = '".STATUS_DEFAULT."' AND dump_userid = '".$db->escape($user_info["user_id"])."'";
		} else if (isset($_POST["download_expired"])) {
			$file_name = $_SESSION["user_name"]."_DUMPS_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(dump_number, '".strval(DB_ENCRYPT_PASS)."') AS dump_number, AES_DECRYPT(dump_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS dump_fullinfo FROM `".TABLE_DUMPS."` WHERE dump_status = '".STATUS_DEFAULT."' AND dump_userid = '".$db->escape($user_info["user_id"])."' AND (dump_exp < ".date("ym").")";
		} else {
			$file_name = $_SESSION["user_name"]."_DUMPS_".date("Y_m_d").".txt";
			$allDumps = $_POST["dumps"];
			$lastDump = $db->escape($allDumps[count($allDumps)-1]);
			unset($allDumps[count($allDumps)-1]);
			$sql = "SELECT *, AES_DECRYPT(dump_number, '".strval(DB_ENCRYPT_PASS)."') AS dump_number, AES_DECRYPT(dump_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS dump_fullinfo FROM `".TABLE_DUMPS."` WHERE dump_status = '".STATUS_DEFAULT."' AND dump_userid = '".$db->escape($user_info["user_id"])."' AND dump_id IN (";
			$t_vals=array();
			if (count($allDumps) > 0) {
				foreach ($allDumps as $key=>$value) {
					$t_vals[] = $value;
					$sql .= "?, ";
				}
			}
			$t_vals[] = $lastDump;
			$sql .= "?)";
		}
		$downloadDumps = $db->fetch_array($sql, $t_vals);
		giveDownload($downloadDumps, $file_name);
	}
	else if (isset($_POST["delete_invalid"]) || (isset($_POST["delete_select"]) && is_array($_POST["dumps"]))) {
		if (isset($_POST["delete_invalid"])) {
			$sql = "UPDATE `".TABLE_DUMPS."` SET dump_status = '".strval(STATUS_DELETED)."' WHERE dump_userid = '".$user_info["user_id"]."' AND (dump_check = '".strval(CHECK_INVALID)."' OR dump_check = '".strval(CHECK_REFUND)."')";
		} else {
			$allDumps = $_POST["dumps"];
			$lastDump = $allDumps[count($allDumps)-1];
			unset($allDumps[count($allDumps)-1]);
			$sql = "UPDATE `".TABLE_DUMPS."` SET dump_status = '".strval(STATUS_DELETED)."' WHERE dump_userid = '".$user_info["user_id"]."' AND dump_id IN (";
			$t_vals=array();
			if (count($allDumps) > 0) {
				foreach ($allDumps as $key=>$value) {
					$t_vals[] = $value;
					$sql .= "?, ";
				}
			}
			$t_vals[] = $lastDump;
			$sql .= "?)";
		}
		$db->query($sql, $t_vals);
		if ($_SERVER["HTTP_REFERER"] != "") {
			header("Location: ".$_SERVER["HTTP_REFERER"]);
		}
		else {
			header("Location: mydumps.php");
		}
	}
	else if ($_SESSION['user_groupid'] <= PER_SELLER && (isset($_POST["seller_export_unsold"]) || isset($_POST["seller_export_sold"]) || isset($_POST["seller_export_expired"]))) {
		if (isset($_POST["seller_export_unsold"])) {
			$file_name = $_SESSION["user_name"]."_UNSOLD_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(dump_number, '".strval(DB_ENCRYPT_PASS)."') AS dump_number, AES_DECRYPT(dump_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS dump_fullinfo FROM `".TABLE_DUMPS."` WHERE dump_userid = '0' AND dump_sellerid = '".$db->escape($user_info["user_id"])."'";
		} else if (isset($_POST["seller_export_sold"])) {
			$file_name = $user_info["user_name"]."_SOLD_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(dump_number, '".strval(DB_ENCRYPT_PASS)."') AS dump_number, AES_DECRYPT(dump_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS dump_fullinfo FROM `".TABLE_DUMPS."` WHERE dump_userid <> '0' AND dump_sellerid = '".$db->escape($user_info["user_id"])."'";
		} else if (isset($_POST["seller_export_expired"])) {
			$file_name = $user_info["user_name"]."_EXPIRED_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(dump_number, '".strval(DB_ENCRYPT_PASS)."') AS dump_number, AES_DECRYPT(dump_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS dump_fullinfo FROM `".TABLE_DUMPS."` WHERE (dump_year < ".date("Y")." OR (dump_year = ".date("Y")." AND dump_month < ".date("n").")) AND dump_sellerid = '".$db->escape($user_info["user_id"])."'";
		}
		$downloadDumps = $db->fetch_array($sql);
		giveDownload($downloadDumps, $file_name);
	}
	else if ((isset($_POST["export_unsold"]) || isset($_POST["export_sold"]) || isset($_POST["export_expired"]) || isset($_POST["export_seller_unsold"]) || isset($_POST["export_seller_sold"]) || isset($_POST["export_seller_expired"]))) {
		if (isset($_POST["export_unsold"])) {
			$file_name = "STORE_UNSOLD_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(dump_number, '".strval(DB_ENCRYPT_PASS)."') AS dump_number, AES_DECRYPT(dump_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS dump_fullinfo FROM `".TABLE_DUMPS."` WHERE dump_userid = '0' AND dump_sellerid >= '0'";
		} else if (isset($_POST["export_sold"])) {
			$file_name = "STORE_SOLD_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(dump_number, '".strval(DB_ENCRYPT_PASS)."') AS dump_number, AES_DECRYPT(dump_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS dump_fullinfo FROM `".TABLE_DUMPS."` WHERE dump_userid <> '0' AND dump_sellerid >= '0'";
		} else if (isset($_POST["export_expired"])) {
			$file_name = "STORE_EXPIRED_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(dump_number, '".strval(DB_ENCRYPT_PASS)."') AS dump_number, AES_DECRYPT(dump_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS dump_fullinfo FROM `".TABLE_DUMPS."` WHERE (dump_year < ".date("Y")." OR (dump_year = ".date("Y")." AND dump_month < ".date("n").")) AND dump_sellerid >= '0'";
		}
		else if (isset($_POST["export_seller_unsold"])) {
			$file_name = "SELLER_UNSOLD_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(dump_number, '".strval(DB_ENCRYPT_PASS)."') AS dump_number, AES_DECRYPT(dump_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS dump_fullinfo FROM `".TABLE_DUMPS."` WHERE dump_userid = '0' AND dump_sellerid > '0' GROUP BY dump_sellerid";
		} else if (isset($_POST["export_seller_sold"])) {
			$file_name = "SELLER_SOLD_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(dump_number, '".strval(DB_ENCRYPT_PASS)."') AS dump_number, AES_DECRYPT(dump_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS dump_fullinfo FROM `".TABLE_DUMPS."` WHERE dump_userid <> '0' AND dump_sellerid > '0' GROUP BY dump_sellerid";
		} else if (isset($_POST["export_seller_expired"])) {
			$file_name = "SELLER_EXPIRED_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(dump_number, '".strval(DB_ENCRYPT_PASS)."') AS dump_number, AES_DECRYPT(dump_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS dump_fullinfo FROM `".TABLE_DUMPS."` WHERE (dump_year < ".date("Y")." OR (dump_year = ".date("Y")." AND dump_month < ".date("n").")) AND dump_sellerid > '0' GROUP BY dump_sellerid";
		}
		$downloadDumps = $db->fetch_array($sql);
		giveDownload($downloadDumps, $file_name, false);
	}
	else {
		if ($_SERVER["HTTP_REFERER"] != "") {
			header("Location: ".$_SERVER["HTTP_REFERER"]);
		}
		else {
			header("Location: ./mydumps.php");
		}
	}
} else {
	header("Location: ./login.php");
}
exit(0);
?>