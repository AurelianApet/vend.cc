<?
session_start();
require("../includes/config.inc.php");
function paygate_log($errorMsg, $filename = "LR_Active_jhsgdfjsdf776h.txt") {
	if ($handle = fopen($filename, 'a+')) {
		if (fwrite($handle, $errorMsg) === FALSE) {
			echo "Cannot write to file ($filename)";
		}
	}
	else {
		echo "Cannot open file ($filename)";
	}
	fclose($handle);
}

$str = $_POST["lr_paidto"].":".$_POST["lr_paidby"].":".stripslashes($_POST["lr_store"]).":".$_POST["lr_amnt"].":".$_POST["lr_transfer"].":".$_POST["lr_merchant_ref"].":"."user_id=".$_POST["user_id"].":".$_POST["lr_currency"].":".$db_config["lr_security_word"];
foreach ($_POST as $k => $v) {
	$msgBody .= $k.":".$v."\r\n";
}
$msgBody .= $_POST["lr_paidto"].":".$_POST["lr_paidby"].":".stripslashes($_POST["lr_store"]).":".$_POST["lr_amnt"].":".$_POST["lr_transfer"].":".$_POST["lr_merchant_ref"].":"."user_id=".$_POST["user_id"].":".$_POST["lr_currency"].":"."*************"."\r\n";
$hash = strtoupper(hash('sha256', $str));
$msgBody .= "\r\n";
if (isset($_POST["lr_paidto"]) && isset($_POST["lr_store"]) && isset($_POST["lr_encrypted2"])) {
	if ($_POST["lr_encrypted2"] == $hash) {
		if ($_POST["lr_paidto"] == strtoupper($db_config["lr_account"]) && $_POST["lr_amnt"] == $db_config["activate_fee"] && stripslashes($_POST["lr_store"]) == $db_config["lr_store_name"]) {
			$user_id = $db->escape($_POST["user_id"]);
			$sql = "SELECT * from `".TABLE_USERS."` WHERE user_id = '".$user_id."'";
			$user_info = $db->query_first($sql);
			$sql = "SELECT count(*) from `".TABLE_ACTIVATES."` WHERE activate_proof = '".$db->escape($_POST["lr_transfer"])."'";
			$record = $db->query_first($sql);
			if ($record) {
				$record = $record["count(*)"];
				if (intval($record) == 0) {
					$user_balance = $user_info["user_balance"];
					$user_update["user_groupid"] = '3';
					$user_update["user_balance"] = doubleval($user_balance)+$db_config['activate_balance'];
					$activates_add["activate_userid"] = $user_id;
					$activates_add["activate_paygate"] = "Liberty Reserve";
					$activates_add["activate_price"] = doubleval($_POST["lr_amnt"]);
					$activates_add["activate_proof"] = $_POST["lr_transfer"];
					$activates_add["activate_time"] = time();
					if ($db->insert(TABLE_ACTIVATES, $activates_add)) {
						if ($db->update(TABLE_USERS, $user_update, "user_id='".$user_id."'")) {
							$msgBody .= "Payment was verified and is successful.\r\n";
						} else {
							$msgBody .= "Update Credit: SQL Error.\r\n";
						}
					} else {
						$msgBody .= "Insert Deposit Record: SQL Error.\r\n";
					}
				} else {
					$msgBody .= "Duplicate recored.\r\n";
				}
			} else {
				$msgBody .= "Check duplicate: SQL Error.\r\n";
			}
		} else {
			$msgBody .= "Cheating recored.\r\n";
		}
	} else {
		$msgBody .= "Invalid response. Sent hash didn't match the computed hash.\r\n";
	}
	paygate_log($msgBody."\r\n");
	//echo $msgBody;
} else {
	paygate_log(print_r($_REQUEST, true));
}
?>