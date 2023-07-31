<?
session_start();
require("../includes/config.inc.php");
function paygate_log($errorMsg, $filename = "LR_payment_paygate_log_fg3454ff44.txt")
{
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
foreach ($_POST as $k => $v)
{
	$msgBody .= $k.":".$v."\r\n";
}
$msgBody .= $_POST["lr_paidto"].":".$_POST["lr_paidby"].":".stripslashes($_POST["lr_store"]).":".$_POST["lr_amnt"].":".$_POST["lr_transfer"].":".$_POST["lr_merchant_ref"].":"."user_id=".$_POST["user_id"].":".$_POST["lr_currency"].":xxxx"."\r\n";
$hash = strtoupper(hash('sha256', $str));
$msgBody .= "\r\n";
if (isset($_POST["lr_paidto"]) && isset($_POST["lr_store"]) && isset($_POST["lr_encrypted2"])) {
	if ($_POST["lr_encrypted2"] == $hash) {
		if ($_POST["lr_paidto"] == strtoupper($db_config["lr_account"]) && stripslashes($_POST["lr_store"]) == $db_config["lr_store_name"]) {
			$user_id = $db->escape($_POST["user_id"]);
			$sql = "SELECT * from `".TABLE_USERS."` WHERE user_id = '".$user_id."'";
			$user_info = $db->query_first($sql);
			$sql = "SELECT count(*) from `".TABLE_DEPOSITS."` WHERE deposit_proof = '".$db->escape($_POST["lr_transfer"])."'";
			$record = $db->query_first($sql);
			if ($record) {
				$record = $record["count(*)"];
				if (intval($record) == 0) {
					$totalBonus = 0;
					$sql = "SELECT * FROM `".TABLE_BONUS."`";
					$records = $db->fetch_array($sql);
					if (count($records)>0) {
						foreach ($records as $value) {
							if ($value_groups = unserialize($value['bonus_groupid'])) {
								if (in_array($user_info["user_groupid"], $value_groups)) {
									if ((doubleval($value['bonus_start']) >= 0) && (doubleval($value['bonus_end']) == 0 || doubleval($value['bonus_start']) <= doubleval($value['bonus_end'])) && (doubleval($_POST["lr_amnt"]) >= doubleval($value['bonus_start'])) && (doubleval($value['bonus_end']) == 0 || doubleval($_POST['lr_amnt']) < doubleval($value['bonus_end']))) {
										$allBonus[] = $value;
										$totalBonus += $value["bonus_value"];
									}
								}
							}
						}
					}
					$realAmount = doubleval($_POST["lr_amnt"])*(1+$totalBonus/100);
					$user_referenceid = $user_info["user_referenceid"];
					$user_balance = $user_info["user_balance"];
					if (intval($user_referenceid) != 0) {
						$sql = "SELECT user_balance from `".TABLE_USERS."` WHERE user_id = '".$user_referenceid."'";
						$reference_balance = $db->query_first($sql);
						$reference_balance = $reference_balance["user_balance"];
						$reference_update["user_balance"] = doubleval($reference_balance)+($_POST["lr_amnt"]*$db_config["affiliate_percent"]);
					}
					$credit_update["user_balance"] = doubleval($user_balance)+$realAmount;
					$deposits_add["deposit_userid"] = $user_id;
					$deposits_add["deposit_paygate"] = "Liberty Reserve";
					$deposits_add["deposit_amount"] = $realAmount;
					$deposits_add["deposit_price"] = doubleval($_POST["lr_amnt"]);
					$deposits_add["deposit_bonus"] = $totalBonus;
					$deposits_add["deposit_before"] = doubleval($user_balance);
					$deposits_add["deposit_proof"] = $_POST["lr_transfer"];
					$deposits_add["deposit_time"] = time();
					$msgBody .= $user_referenceid."|".$reference_balance."|".$reference_update["user_balance"]."\r\n";
					if ($db->insert(TABLE_DEPOSITS, $deposits_add)) {
						if ($db->update(TABLE_USERS, $credit_update, "user_id='".$user_id."'")) {
							if (intval($user_referenceid) == 0 || $db->update(TABLE_USERS, $reference_update, "user_id='".$user_referenceid."'")) {
								$msgBody .= "Payment was verified and is successful.\r\n";
							} else {
								$msgBody .= "Update Reference Credit: SQL Error.\r\n";
							}
						} else {
							$msgBody .= "Update Credit: SQL Error.\r\n";
						}
					}
					else {
						$msgBody .= "Insert Deposit Record: SQL Error.\r\n";
					}
				}
				else {
					$msgBody .= "Duplicate recored.\r\n";
				}
			}
			else {
				$msgBody .= "Check duplicate: SQL Error.\r\n";
			}
		}
		else
		{
			$msgBody .= "Cheating recored.\r\n";
		}
	}
	else
	{
		$msgBody .= "Invalid response. Sent hash didn't match the computed hash.\r\n";
	}
	paygate_log($msgBody."\r\n");
	//echo $msgBody;
} else {
	paygate_log(print_r($_REQUEST, true));
}
?>