<?php
session_start();
require("./includes/config.inc.php");
require_once("./includes/user.php");

$user = new user($db);

//If login attempt else check login session
$checkLogin = $user->valid_login();
//user info var
$user_info = $user->user_info;

function giveDownload($downloadCards, $file_name) {
	$content = "CARDNUMBER|MM/YYYY|CVV|NAME|ADDRESS|CITY|STATE|ZIPCODE|COUNTRY|PHONE|SSN|DOB|CHECK RESULT\r\n";
	if (count($downloadCards) > 0) {
		foreach ($downloadCards as $key=>$value) {
			switch ($value['card_check']) {
				case strval(CHECK_VALID):
					$value['card_checkText'] = "APPROVED";
					break;
				case strval(CHECK_INVALID):
					$value['card_checkText'] = "TIMEOUT";
					break;
				case strval(CHECK_REFUND):
					$value['card_checkText'] = "DECLINE";
					break;
				case strval(CHECK_UNKNOWN):
					$value['card_checkText'] = "UNKNOWN";
					break;
				case strval(CHECK_WAIT_REFUND):
					$value['card_checkText'] = "WAIT REFUND";
					break;
				default :
					$value['card_checkText'] = "UNCHECK";
					break;
			}
			$content .= $value['card_number']."|".$value['card_month']."/".$value['card_year']."|".$value['card_cvv']."|".$value['card_name']."|".$value['card_address']."|".$value['card_city']."|".$value['card_state']."|".$value['card_zip']."|".$value['card_country']."|".$value['card_phone']."|".$value['card_ssn']."|".$value['card_dob']."|".$value['card_checkText']."|"."\r\n";
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
	if (isset($_POST["download_all"]) || isset($_POST["download_expired"]) || (isset($_POST["download_select"]) && is_array($_POST["cards"]))) {
		if (isset($_POST["download_all"])) {
			$file_name = $user_info["user_name"]."_DOWNLOAD_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(card_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS card_fullinfo, AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number FROM `".TABLE_CARDS."` WHERE card_status = '".STATUS_DEFAULT."' AND card_userid = '".$user_info["user_id"]."'";
		} else if (isset($_POST["download_expired"])) {
			$file_name = $user_info["user_name"]."_DOWNLOAD_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(card_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS card_fullinfo, AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number FROM `".TABLE_CARDS."` WHERE card_status = '".STATUS_DEFAULT."' AND card_userid = '".$user_info["user_id"]."' AND(card_year < ".date("Y")." OR (card_year = ".date("Y")." AND card_month < ".date("n")."))";
		} else {
			$file_name = $user_info["user_name"]."_DOWNLOAD_".date("Y_m_d").".txt";
			$allCards = $db->escape($_POST["cards"]);
			$lastCard = $db->escape($allCards[count($allCards)-1]);
			unset($allCards[count($allCards)-1]);
			$sql = "SELECT *, AES_DECRYPT(card_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS card_fullinfo, AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number FROM `".TABLE_CARDS."` WHERE card_status = '".STATUS_DEFAULT."' AND card_userid = '".$user_info["user_id"]."' AND card_id IN (";
			if (count($allCards) > 0) {
				$t_vals=array();
				foreach ($allCards as $key=>$value) {
					$t_vals[]=$value;
					$sql .= "?, ";
				}
			}
			$sql .= $lastCard.")";
		}
		$downloadCards = $db->fetch_array($sql, $t_vals);
		giveDownload($downloadCards, $file_name);
	}
	else if (isset($_POST["delete_invalid"]) || (isset($_POST["delete_select"]) && is_array($_POST["cards"]))) {
		
		if (isset($_POST["delete_invalid"])) {
			$sql = "UPDATE `".TABLE_CARDS."` SET card_status = '".strval(STATUS_DELETED)."' WHERE card_userid = '".$user_info["user_id"]."' AND (card_check = '".strval(CHECK_INVALID)."' OR card_check = '".strval(CHECK_REFUND)."')";
		} else {
			$allCards = $_POST["cards"];
			$lastCard = $allCards[count($allCards)-1];
			unset($allCards[count($allCards)-1]);
			$sql = "UPDATE `".TABLE_CARDS."` SET card_status = '".strval(STATUS_DELETED)."' WHERE card_userid = '".$user_info["user_id"]."' AND card_id IN (";
			if (count($allCards) > 0) {
				$t_vals=array();
				foreach ($allCards as $key=>$value) {
					$t_vals[]=$value;
					$sql .= "?, ";
				}
			}
			$t_vals[] = $lastCard;
			$sql .= "?)";
		}
		$db->query($sql, $t_vals);
		if ($_SERVER["HTTP_REFERER"] != "") {
			header("Location: ".$_SERVER["HTTP_REFERER"]);
		}
		else {
			header("Location: mycards.php");
		}
	}
	else if ($_SESSION['user_groupid'] <= PER_SELLER && (isset($_POST["seller_export_unsold"]) || isset($_POST["seller_export_sold"]) || isset($_POST["seller_export_expired"]))) {
		if (isset($_POST["seller_export_unsold"])) {
			$file_name = $user_info["user_name"]."_UNSOLD_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(card_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS card_fullinfo, AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number FROM `".TABLE_CARDS."` WHERE card_userid = '0' AND card_sellerid = '".$user_info["user_id"]."'";
		} else if (isset($_POST["seller_export_sold"])) {
			$file_name = $user_info["user_name"]."_SOLD_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(card_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS card_fullinfo, AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_sellerid = '".$user_info["user_id"]."'";
		} else if (isset($_POST["seller_export_expired"])) {
			$file_name = $user_info["user_name"]."_EXPIRED_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(card_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS card_fullinfo, AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number FROM `".TABLE_CARDS."` WHERE (card_year < ".date("Y")." OR (card_year = ".date("Y")." AND card_month < ".date("n").")) AND card_sellerid = '".$user_info["user_id"]."'";
		}
		$downloadCards = $db->fetch_array($sql);
		giveDownload($downloadCards, $file_name);
	}
	else if ($_SESSION['user_groupid'] <= PER_ADMIN && (isset($_POST["export_unsold"]) || isset($_POST["export_sold"]) || isset($_POST["export_expired"]) || isset($_POST["export_seller_unsold"]) || isset($_POST["export_seller_sold"]) || isset($_POST["export_seller_expired"]))) {
		if (isset($_POST["export_unsold"])) {
			$file_name = "STORE_UNSOLD_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(card_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS card_fullinfo, AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number FROM `".TABLE_CARDS."` WHERE card_userid = '0' AND card_sellerid >= '0'";
		} else if (isset($_POST["export_sold"])) {
			$file_name = "STORE_SOLD_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(card_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS card_fullinfo, AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_sellerid >= '0'";
		} else if (isset($_POST["export_expired"])) {
			$file_name = "STORE_EXPIRED_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(card_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS card_fullinfo, AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number FROM `".TABLE_CARDS."` WHERE (card_year < ".date("Y")." OR (card_year = ".date("Y")." AND card_month < ".date("n").")) AND card_sellerid >= '0'";
		}
		else if (isset($_POST["export_seller_unsold"])) {
			$file_name = "SELLER_UNSOLD_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(card_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS card_fullinfo, AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number FROM `".TABLE_CARDS."` WHERE card_userid = '0' AND card_sellerid > '0' GROUP BY card_sellerid";
		} else if (isset($_POST["export_seller_sold"])) {
			$file_name = "SELLER_SOLD_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(card_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS card_fullinfo, AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_sellerid > '0' GROUP BY card_sellerid";
		} else if (isset($_POST["export_seller_expired"])) {
			$file_name = "SELLER_EXPIRED_".date("Y_m_d").".txt";
			$sql = "SELECT *, AES_DECRYPT(card_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS card_fullinfo, AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number FROM `".TABLE_CARDS."` WHERE (card_year < ".date("Y")." OR (card_year = ".date("Y")." AND card_month < ".date("n").")) AND card_sellerid > '0' GROUP BY card_sellerid";
		}
		$downloadCards = $searchBinWhere = $db->fetch_array($sql);
		giveDownload($downloadCards, $file_name, false);
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