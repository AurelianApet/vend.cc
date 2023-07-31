<?php
session_start();
if(isset($_POST["otheraccounts"]))
{
	if(is_array($_POST["otheraccounts"]))
	{
		foreach($_POST["otheraccounts"] as $card)
		{
			if(!is_numeric($card) || $card=="" || $card<1)
			{
			echo "Fuck you !";
			exit;
			}
		}
	}
	else
	{
		if(!is_numeric($_POST["otheraccounts"]) || $_POST["otheraccounts"]=="" || $_POST["otheraccounts"]<1)
		{
		echo "Fuck you !";
		exit;
		}
	}
}
require("./includes/config.inc.php");
function giveDownload($downloadOtheraccounts, $file_name) {
	$content = "TYPE|[ACCOUNT INFORMATION]|COMMENT\r\n";
	if (count($downloadOtheraccounts) > 0) {
		foreach ($downloadOtheraccounts as $key=>$value) {
			$content .= $value['otheraccount_type']."|[".$value['otheraccount_fullinfo'].$value['otheraccount_comment']."]|"."\r\n";
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
	if (isset($_POST["download_all"]) || isset($_POST["download_expired"]) || (isset($_POST["download_select"]) && is_array($_POST["otheraccounts"]))) {
		if (isset($_POST["download_all"])) {
			$file_name = $_SESSION["user_name"]."_OTHERACCOUNT_DOWNLOAD_".date("Y_m_d").".txt";
			$sql = "SELECT * FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_status = '".STATUS_DEFAULT."' AND otheraccount_userid = '".$db->escape($_SESSION["user_id"])."'";
		} else {
			$file_name = $_SESSION["user_name"]."_OTHERACCOUNT_DOWNLOAD_".date("Y_m_d").".txt";
			$allOtheraccounts = $_POST["otheraccounts"];
			$lastOtheraccount = $db->escape($allOtheraccounts[count($allOtheraccounts)-1]);
			unset($allOtheraccounts[count($allOtheraccounts)-1]);
			$sql = "SELECT * FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_status = '".STATUS_DEFAULT."' AND otheraccount_userid = '".$db->escape($_SESSION["user_id"])."' AND otheraccount_id IN (";
			if (count($allOtheraccounts) > 0) {
				foreach ($allOtheraccounts as $key=>$value) {
					$sql .= "'".$db->escape($value)."', ";
				}
			}
			$sql .= $lastOtheraccount.")";
		}
		$downloadOtheraccounts = $db->fetch_array($sql);
		giveDownload($downloadOtheraccounts, $file_name);
	}
	else if (isset($_POST["delete_invalid"]) || (isset($_POST["delete_select"]) && is_array($_POST["otheraccounts"]))) {
		if (isset($_POST["delete_invalid"])) {
			$sql = "UPDATE `".TABLE_OTHERACCOUNTS."` SET otheraccount_status = '".strval(STATUS_DELETED)."' WHERE otheraccount_userid = '".$db->escape($_SESSION["user_id"])."' AND (otheraccount_check = '".strval(CHECK_INVALID)."' OR otheraccount_check = '".strval(CHECK_REFUND)."')";
		} else {
			$allOtheraccounts = $_POST["otheraccounts"];
			$lastOtheraccount = $db->escape($allOtheraccounts[count($allOtheraccounts)-1]);
			unset($allOtheraccounts[count($allOtheraccounts)-1]);
			$sql = "UPDATE `".TABLE_OTHERACCOUNTS."` SET otheraccount_status = '".strval(STATUS_DELETED)."' WHERE otheraccount_userid = '".$db->escape($_SESSION["user_id"])."' AND otheraccount_id IN (";
			if (count($allOtheraccounts) > 0) {
				foreach ($allOtheraccounts as $key=>$value) {
					$sql .= "'".$db->escape($value)."', ";
				}
			}
			$sql .= "'".$lastOtheraccount."')";
		}
		$db->query($sql);
		if ($_SERVER["HTTP_REFERER"] != "") {
			header("Location: ".$_SERVER["HTTP_REFERER"]);
		}
		else {
			header("Location: mycards.php");
		}
	}
	else if ($_SESSION['user_groupid'] <= PER_SELLER && (isset($_POST["seller_export_unsold"]) || isset($_POST["seller_export_sold"]))) {
		if (isset($_POST["seller_export_unsold"])) {
			$file_name = $_SESSION["user_name"]."_UNSOLD_".date("Y_m_d").".txt";
			$sql = "SELECT * FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid = '0' AND otheraccount_sellerid = '".$db->escape($_SESSION["user_id"])."'";
		} else if (isset($_POST["seller_export_sold"])) {
			$file_name = $_SESSION["user_name"]."_SOLD_".date("Y_m_d").".txt";
			$sql = "SELECT * FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid <> '0' AND otheraccount_sellerid = '".$db->escape($_SESSION["user_id"])."'";
		}
		$downloadOtheraccounts = $db->fetch_array($sql);
		giveDownload($downloadOtheraccounts, $file_name);
	}
	else if ($_SESSION['user_groupid'] <= PER_ADMIN && (isset($_POST["export_unsold"]) || isset($_POST["export_sold"]) || isset($_POST["export_seller_unsold"]) || isset($_POST["export_seller_sold"]))) {
		if (isset($_POST["export_unsold"])) {
			$file_name = "STORE_UNSOLD_".date("Y_m_d").".txt";
			$sql = "SELECT * FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid = '0' AND otheraccount_sellerid = '0'";
		} else if (isset($_POST["export_sold"])) {
			$file_name = "STORE_SOLD_".date("Y_m_d").".txt";
			$sql = "SELECT * FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid <> '0' AND otheraccount_sellerid = '0'";
		}
		else if (isset($_POST["export_seller_unsold"])) {
			$file_name = "SELLER_UNSOLD_".date("Y_m_d").".txt";
			$sql = "SELECT * FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid = '0' AND otheraccount_sellerid > '0' GROUP BY otheraccount_sellerid";
		} else if (isset($_POST["export_seller_sold"])) {
			$file_name = "SELLER_SOLD_".date("Y_m_d").".txt";
			$sql = "SELECT * FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid <> '0' AND otheraccount_sellerid > '0' GROUP BY otheraccount_sellerid";
		}
		$downloadOtheraccounts = $db->fetch_array($sql);
		giveDownload($downloadOtheraccounts, $file_name, false);
	}
	else {
		if ($_SERVER["HTTP_REFERER"] != "") {
			header("Location: ".$_SERVER["HTTP_REFERER"]);
		}
		else {
			header("Location: ./mycards.php");
		}
	}
} else {
	header("Location: ./login.php");
}
exit(0);
?>