<?php
set_time_limit(0);
session_start();
require("./includes/config.inc.php");

$login_array = checkLogin(PER_USER);

$checklogin = $login_array[0];
$user_info = $login_array[1];


$checker_module = str_replace("/", "", $db_config["check_module"]);
if (ENABLE_CHECKER == true && $checker_module != "") {
	if (file_exists("./checkers/".$checker_module)) 
		require("./checkers/".$checker_module);
	else die("<span class=\"error\">Cannot find the checker module!</span>");

	if ($checklogin) {
		if ($_GET["card_id"] != "") {
			$card_id = $db->escape($_GET["card_id"]);
			$sql = "SELECT *, AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number FROM `".TABLE_CARDS."` WHERE card_id = ? AND card_check = '".strval(CHECK_DEFAULT)."'";
			$records = $db->fetch_array($sql, array($card_id));
			if (count($records)>0) {
				$value = $records[0];
				if ($value["card_userid"] == $user_info["user_id"]) {
                    //echo date("d/m/Y H:i:s",$value["card_buyTime"]);die;
                    //$db_config["check_timeout"] = 8640000;
					if (intval($value["card_buyTime"])+intval($db_config["check_timeout"]) >= time()) {
						  $user_balance = $user_info["user_balance"];
						  if (doubleval($user_balance) >= doubleval($db_config["check_fee"])) {
							  $check_add["check_userid"] = $_SESSION["user_id"];
							  $check_add["check_cardid"] = $card_id;
							  $check_add["check_time"] = time();
							  $check = checkCVV4($value["card_number"], $value["card_month"], $value["card_year"], $value["card_cvv"]);
							  if ($check == 1) {
								  $user_update["user_balance"] = doubleval($user_balance)-doubleval($db_config["check_fee"]);
								  $check_add["check_fee"] = $db_config["check_fee"];
								  $check_add["check_result"] = strval(CHECK_VALID);
								  $check_update["card_check"] = strval(CHECK_VALID);
								  $respond = "<span class=\"green bold\">APPROVED</span>";
							  }
							  else if ($check == 2) {
								  $seller_error = true;
								  if ($value["card_sellerid"] > 0) {
									  $check_add["check_result"] = strval(CHECK_WAIT_REFUND);
									  $check_update["card_check"] = strval(CHECK_WAIT_REFUND);
									  $sql = "SELECT user_id, user_balance from `".TABLE_USERS."` WHERE user_id = ? AND user_groupid = '".strval(PER_SELLER)."'";
									  $card_seller = $db->fetch_array($sql, $value["card_sellerid"]);
									  if (count($card_seller)>0) {
										  $card_seller = $card_seller[0];
										  if (doubleval($card_seller["user_balance"]) >= ((doubleval($value["card_price"])+doubleval($value["card_additionPrice"]))*(1-$db_config["commission"]))) {
											  $seller_update["user_balance"] = doubleval($card_seller["user_balance"])-((doubleval($value["card_price"])+doubleval($value["card_additionPrice"]))*(1-$db_config["commission"]));
											  $user_update["user_balance"] = doubleval($user_balance)+((doubleval($value["card_price"])+doubleval($value["card_additionPrice"])));
											  $check_add["check_result"] = strval(CHECK_REFUND);
											  $check_update["card_check"] = strval(CHECK_REFUND);
											  $respond = "<span class=\"pink bold\">DECLINE</span>";
										  } else {
											  $respond = "<span class=\"pink bold\">WAIT REFUND</span> - <span class=\"error\">Seller doesn't has enought money to refund, please contact him for refund manualy.</span>";
										  }
									  } else {
										  $respond = "<span class=\"pink bold\">WAIT REFUND</span> - <span class=\"error\">Cannot found seller to refund, please contact admin.</span>";
									  }
								  } else {
									  $user_update["user_balance"] = doubleval($user_balance)+((doubleval($value["card_price"])+doubleval($value["card_additionPrice"])));
									  $check_add["check_result"] = strval(CHECK_REFUND);
									  $check_update["card_check"] = strval(CHECK_REFUND);
									  $respond = "<span class=\"pink bold\">DECLINE</span>";
								  }
								  $check_add["check_fee"] = $db_config["check_fee"];
							  }
							  else {
								  $check_add["check_result"] = strval(CHECK_UNKNOWN);
								  $respond = "<span class=\"blue bold\">UNKNOWN</span>";
							  }
							} else {
								$respond = "<span class=\"error\">You must have $".number_format($db_config["check_fee"], 2, '.', '')." to check card</span>";
							}
					}
					else {
						$check_add["check_result"] = strval(CHECK_INVALID);
						$check_update["card_check"] = strval(CHECK_INVALID);
						$respond = "<span class=\"red bold\">TIMEOUT</span>";
					}
				}
				else {
					$respond = "<span class=\"error\">This card doesn't belong to you</span>";
				}
				if (count($check_update) > 0) {
                    $isChecked = $value['card_check'];
					if (!$db->update(TABLE_CARDS, $check_update, "card_id=?", $card_id)) {
						$respond = "<span class=\"error\">Update card check error, please try again</span>";
					} else if($isChecked == 0) { // check the first for update Invalid and Refund
                        $sqlBin = '';
                        if($check_update['card_check'] == CHECK_REFUND || $check_update['card_check'] == CHECK_WAIT_REFUND) {
                            $sqlBin = "card_refund=card_refund+1,card_valid=card_valid-1";
                        } else if($check_update['card_check'] == CHECK_INVALID || $check_update['card_check'] == CHECK_UNKNOWN) {
                            $sqlBin = "card_valid=card_valid-1";
                        }
                        if($sqlBin) {
                            $sqlBin = "UPDATE ".TABLE_BINS." SET ".$sqlBin." WHERE card_bin=".(int)$value["card_bin"];
                            $db->query($sqlBin);
                        }
                    }
				}
				if (count($check_add) > 0) {
					if (!$db->insert(TABLE_CHECKS, $check_add)) {
						$respond = "<span class=\"error\">Insert check information error, please try again</span>";
					}
				}
				if (count($user_update) > 0) {
					if ($db->update(TABLE_USERS, $user_update, "user_id='".$db->escape($user_info["user_id"])."'")) {
						$user_info["user_balance"] = $user_update["user_balance"];
					} else {
						$respond = "<span class=\"error\">Update user credit error, please try again</span>";
					}
				}
				if (count($seller_update) > 0) {
					if (!$db->update(TABLE_USERS, $seller_update, "user_id=?", $value["card_sellerid"])) {
						$respond = "<span class=\"error\">Update seller credit error, please try again</span>";
					}
				}
			}
			else {
				$respond = "<span class=\"error\">This card doesn't exist</span>";
			}
			echo $respond;
		}
		if ($_GET["dump_id"] != "") {
			$dump_id = $db->escape($_GET["dump_id"]);
			$sql = "SELECT *, AES_DECRYPT(dump_number, '".strval(DB_ENCRYPT_PASS)."') AS dump_number FROM `".TABLE_DUMPS."` WHERE dump_id = ? AND dump_check = '".strval(dump_check_DEFAULT)."'";
			$records = $db->fetch_array($sql, $dump_id);
			if (count($records)>0) {
				$value = $records[0];
				if ($value["dump_userid"] == $_SESSION["user_id"]) {
					if (intval($value["dump_buyTime"])+intval($db_config["dump_check_timeout"]) >= time()) {
						$sql = "SELECT * FROM `".TABLE_USERS."` WHERE user_id = '".$user_info["user_id"]."'";
						$user_info = $db->query_first($sql);
						if ($user_info) {
							$user_balance = $user_info["user_balance"];
							if (doubleval($user_balance) >= doubleval($db_config["dump_check_fee"])) {
								$dump_check_add["dump_check_userid"] = $_SESSION["user_id"];
								$dump_check_add["dump_check_dumpid"] = $dump_id;
								$dump_check_add["dump_check_time"] = time();
								$check = check_dump($value["dump_number"], $value["dump_exp"]);
								if ($check == 1) {
									$user_update["user_balance"] = doubleval($user_balance)-doubleval($db_config["dump_check_fee"]);
									$dump_check_add["dump_check_fee"] = $db_config["dump_check_fee"];
									$dump_check_add["dump_check_result"] = strval(dump_check_VALID);
									$dump_check_update["dump_check"] = strval(dump_check_VALID);
									$respond = "<span class=\"green bold\">APPROVED</span>";
								}
								else if ($check == 2) {
									$seller_error = true;
									if ($value["dump_sellerid"] > 0) {
										$dump_check_add["dump_check_result"] = strval(dump_check_WAIT_REFUND);
										$dump_check_update["dump_check"] = strval(dump_check_WAIT_REFUND);
										$sql = "SELECT user_id, user_balance from `".TABLE_USERS."` WHERE user_id = ? AND user_groupid = '".strval(PER_SELLER)."'";
										$dump_seller = $db->fetch_array($sql, $value["dump_sellerid"]);
										if (count($dump_seller)>0) {
											$dump_seller = $dump_seller[0];
											if (doubleval($dump_seller["user_balance"]) >= ((doubleval($value["dump_price"])+doubleval($value["dump_additionPrice"]))*(1-$db_config["commission"]))) {
												$seller_update["user_balance"] = doubleval($dump_seller["user_balance"])-((doubleval($value["dump_price"])+doubleval($value["dump_additionPrice"]))*(1-$db_config["commission"]));
												$user_update["user_balance"] = doubleval($user_balance)+((doubleval($value["dump_price"])+doubleval($value["dump_additionPrice"])));
												$dump_check_add["dump_check_result"] = strval(dump_check_REFUND);
												$dump_check_update["dump_check"] = strval(dump_check_REFUND);
												$respond = "<span class=\"pink bold\">DECLINE</span>";
											} else {
												$respond = "<span class=\"pink bold\">WAIT REFUND</span> - <span class=\"error\">Seller doesn't has enought money to refund, please contact him for refund manualy.</span>";
											}
										} else {
											$respond = "<span class=\"pink bold\">WAIT REFUND</span> - <span class=\"error\">Cannot found seller to refund, please contact admin.</span>";
										}
									} else {
										$user_update["user_balance"] = doubleval($user_balance)+((doubleval($value["dump_price"])+doubleval($value["dump_additionPrice"])));
										$dump_check_add["dump_check_result"] = strval(dump_check_REFUND);
										$dump_check_update["dump_check"] = strval(dump_check_REFUND);
										$respond = "<span class=\"pink bold\">DECLINE</span>";
									}
									$dump_check_add["dump_check_fee"] = $db_config["dump_check_fee"];
								}
								else {
									$dump_check_add["dump_check_result"] = strval(dump_check_UNKNOWN);
									$respond = "<span class=\"blue bold\">UNKNOWN</span>";
								}
							} else {
								$respond = "<span class=\"error\">You must have $".number_format($db_config["dump_check_fee"], 2, '.', '')." to check dump</span>";
							}
						} else {
							$respond = "<span class=\"error\">Get user information error, please try again</span>";
						}
					}
					else {
						$dump_check_add["dump_check_result"] = strval(dump_check_INVALID);
						$dump_check_update["dump_check"] = strval(dump_check_INVALID);
						$respond = "<span class=\"red bold\">TIMEOUT</span>";
					}
				}
				else {
					$respond = "<span class=\"error\">This dump doesn't belong to you</span>";
				}
				if (count($dump_check_update) > 0) {
					if (!$db->update(TABLE_DUMPS, $dump_check_update, "dump_id=?", $dump_id)) {
						$respond = "<span class=\"error\">Update dump check error, please try again</span>";
					}
				}
				if (count($dump_check_add) > 0) {
					if (!$db->insert(TABLE_DUMP_CHECKS, $dump_check_add)) {
						$respond = "<span class=\"error\">Insert check information error, please try again</span>";
					}
				}
				if (count($user_update) > 0) {
					if ($db->update(TABLE_USERS, $user_update, "user_id='".$user_info["user_id"]."'")) {
						$user_info["user_balance"] = $user_update["user_balance"];
					} else {
						$respond = "<span class=\"error\">Update user credit error, please try again</span>";
					}
				}
				if (count($seller_update) > 0) {
					if (!$db->update(TABLE_USERS, $seller_update, "user_id=?", $value["dump_sellerid"])) {
						$respond = "<span class=\"error\">Update seller credit error, please try again</span>";
					}
				}
			}
			else {
				$respond = "<span class=\"error\">This dump doesn't exist</span>";
			}
			echo $respond;
		}
	}
	else {
		header("Location: login.php");
	}
	exit(0);
} else {
	echo "<span class=\"error\">Checker is disabled</span>";
}
?>