<?php
set_time_limit(0);
session_start();
require("./includes/config.inc.php");require_once './api_ukdob/DOB_API.php';require_once './api_ukdob/DOB_API_Exceptions.php';function ukdob_log($errorMsg){	$filename = "ukdob_log.txt";	if ($handle = fopen($filename, 'a+')) {		if (fwrite($handle, $errorMsg) === FALSE) {			echo "Cannot write to file ($filename)";		}	}	else {		echo "Cannot open file ($filename)";	}	fclose($handle);}
if (checkLogin(PER_USER)) {
	if (isset($_GET["id"])) {		$dob_id = intval($_GET["id"]);
		$sql = "SELECT * FROM `".TABLE_USERS."` WHERE user_id = ?";
		$record = $db->query_first($sql, $_SESSION["user_id"]);
		$user_balance = $record["user_balance"];
		if (doubleval($user_balance) >= doubleval($db_config["ukdob_fee"])) {			try {				$ob = DOB_API::get_instance($db_config['ukdob_url'], $db_config['ukdob_key']);				if (doubleval($ob->get_dob_price()) > doubleval($db_config["ukdob_fee"])) {					echo "<span class=\"red bold centered\">Bad Price, please contact administrator to fix this.</span>";				} else {					$credit_update["user_balance"] = doubleval($user_balance)-doubleval($db_config["ukdob_fee"]);					if (!$db->update(TABLE_USERS, $credit_update, "user_id=?", $db->escape($_SESSION["user_id"]))) {						echo "<span class=\"red bold\">Update credit error, please try again</span>";					} else {						$ukdob = $ob->buy_result(array("$dob_id"));						if (count($ukdob["data"]) > 0) {							$ukdob = $ukdob["data"][0];							echo "<span class=\"yellow bold centered\">$ukdob[name]|$ukdob[city]|$ukdob[zip]|$ukdob[dob]</span>";						} else {							echo "<span class=\"red bold centered\">(Not found any record that meets your search criteria.)</span>";						}					}				}			} catch(API_Client_Exception $e) {				/*					This exception means that you passed some incorrect settings or params to DOB_API class					it's client side exception so - your responsibility				*/				echo "<span class=\"red bold centered\">(DOB API Client config error, please contact administrator.)</span>";				ukdob_log('Client Exception: ' . $e->getMessage());			} catch(API_Server_Exception $e) {				/*					This exception means that we have some problems with our DOB API on server.					Please contact DOB API support for details.				*/				echo "<span class=\"red bold centered\">(DOB API Server config error, please contact administrator.)</span>";				ukdob_log('Server Exception: ' . $e->getMessage());			}
		} else {
			echo "<span class=\"red bold\">(Need $".number_format($db_config["ukdob_fee"], 2, '.', '')." to search)</span>";
		}
	} else {		echo "<span class=\"red bold\">Wrong id</span>";	}
}
else {
	header("Location: login.php");
}
exit(0);
?>