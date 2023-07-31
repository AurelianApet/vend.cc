<?php
require("./header.php");
if ($checkLogin && $_SESSION["user_groupid"] < intval(PER_UNACTIVATE)) {
	if (isset($_POST["btnDeleteSelect"])) {
		$allCards = $_POST["cards"];
		$allOtheraccounts = $_POST["otheraccounts"];
		$allDumps = $_POST["dumps"];
		$countDeleted = 0;
		if (count($allCards) > 0) {
			foreach ($allCards as $key=>$value) {
				$countDeleted++;
				unset($_SESSION["shopping_card_items"][$value]);
			}
			if (is_array($_SESSION["shopping_card_items"])) {
				$_SESSION["shopping_cards"] = array_keys($_SESSION["shopping_card_items"]);
			}
		}
		if (count($allOtheraccounts) > 0) {
			foreach ($allOtheraccounts as $key=>$value) {
				$countDeleted++;
				unset($_SESSION["shopping_otheraccount_items"][$value]);
			}
			if (is_array($_SESSION["shopping_otheraccount_items"])) {
				$_SESSION["shopping_otheraccounts"] = array_keys($_SESSION["shopping_otheraccount_items"]);
			}
		}
		if (count($allDumps) > 0) {
			foreach ($allDumps as $key=>$value) {
				$countDeleted++;
				unset($_SESSION["shopping_dump_items"][$value]);
			}
			if (is_array($_SESSION["shopping_dump_items"])) {
				$_SESSION["shopping_dumps"] = array_keys($_SESSION["shopping_dump_items"]);
			}
		}
		if ($countDeleted > 0) {
			$add_msg = "<span class=\"success\">Successfuly deleted ".$countDeleted." item(s) from shopping cart.</span>";
		}
		else {
			$add_msg = "<span class=\"error\">Please select one or more card(s) from your shopping cart to delete.</span>";
		}
?>
				<script type="text/javascript">setTimeout("window.location = './cart.php'", 1000);</script>
				<div id="cart">
					<div class="section_title">YOUR SHOPPING CART</div>
					<div class="section_content centered">
						<?=$add_msg?><br/>
						<a href="./cart.php">Click here if your browser does not automatically redirect you.</a>
					</div>
				</div>
<?php
	}
	else if (isset($_POST["addToCart"]) && is_array($_POST["cards"])) {
		$allCards = $_POST["cards"];
		$lastCard = $db->escape($allCards[count($allCards)-1]);
		unset($allCards[count($allCards)-1]);
		$sql = "SELECT card_id, card_categoryid, AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number, card_bin, card_cvv, card_name, card_country, card_state, card_city, card_zip, card_ssn, card_dob, card_price FROM `".TABLE_CARDS."` WHERE card_status = '".STATUS_DEFAULT."' AND card_userid = 0 AND card_id IN (";
		$t_vals=array();
		if (count($allCards) > 0) {
			foreach ($allCards as $key=>$value) {
				$t_vals[]=$db->escape($value);
				$sql .= "?, ";
			}
		}
		$t_vals[]=$lastCard;
		$sql .= "?)";
		$addCards = $db->fetch_array($sql, $t_vals);
		if (count($addCards) > 0) {
			if (!isset($_SESSION["shopping_card_items"])) {
				$_SESSION["shopping_card_items"] = array();
			}
			$countAdded = 0;
			foreach ($addCards as $key=>$value) {
				if (in_array($value["card_id"], array_keys($_SESSION["shopping_card_items"]))) {
					unset($_SESSION["shopping_card_items"][$value["card_id"]]);
				}
				$countAdded++;
				$card["card_id"] = $value["card_id"];
				$card["cardPrice"] = $value['card_price'];
				if (strlen($_POST["txtBin"]) > 1) {
					$card["binPrice"] = $db_config["binPrice"];
				}
				else {
					$card["binPrice"] = 0;
				}
				if ($_POST["txtCountry"] != "") {
					$card["countryPrice"] = $db_config["countryPrice"];
				}
				else {
					$card["countryPrice"] = 0;
				}
				if ($_POST["lstState"] != "") {
					$card["statePrice"] = $db_config["statePrice"];
				}
				else {
					$card["statePrice"] = 0;
				}
				if ($_POST["lstCity"] != "") {
					$card["cityPrice"] = $db_config["cityPrice"];
				}
				else {
					$card["cityPrice"] = 0;
				}
				if ($_POST["txtZip"] != "") {
					$card["zipPrice"] = $db_config["zipPrice"];
				}
				else {
					$card["zipPrice"] = 0;
				}
				$_SESSION["shopping_card_items"][$value["card_id"]] = $card;
			}
			if (is_array($_SESSION["shopping_card_items"])) {
				$_SESSION["shopping_cards"] = array_keys($_SESSION["shopping_card_items"]);
			}
		}
		if ($countAdded > 0) {
			$add_msg = "<span class=\"success\">Successfuly added ".$countAdded." item(s) to shopping cart.</span>";
		}
		else {
			$add_msg = "<span class=\"error\">Please select one or more card(s) to add to your shopping cart.</span>";
		}
?>
				<script type="text/javascript">setTimeout("window.location = './cart.php'", 1000);</script>
				<div id="cart">
					<div class="section_title">YOUR SHOPPING CART</div>
					<div class="section_content centered">
						<?=$add_msg?><br/>
						<a href="./cart.php">Click here if your browser does not automatically redirect you.</a>
					</div>
				</div>
<?php
	}
	else if (isset($_POST["addToCart"]) && is_array($_POST["otheraccounts"])) {
		$allOtheraccounts = $_POST["otheraccounts"];
		$lastOtheraccount = $db->escape($allOtheraccounts[count($allOtheraccounts)-1]);
		unset($allOtheraccounts[count($allOtheraccounts)-1]);
		$sql = "SELECT * FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_status = '".STATUS_DEFAULT."' AND otheraccount_userid = 0 AND otheraccount_id IN (";
		$t_vals=array();
		if (count($allOtheraccounts) > 0) {
			foreach ($allOtheraccounts as $key=>$value) {
				$t_vals[]=$db->escape($value);
				$sql .= "?, ";
			}
		}
		$t_vals[]=$lastOtheraccount;
		$sql .= "?)";
		$addOtheraccounts = $db->fetch_array($sql, $t_vals);
		if (count($addOtheraccounts) > 0) {
			if (!isset($_SESSION["shopping_otheraccount_items"])) {
				$_SESSION["shopping_otheraccount_items"] = array();
			}
			$countAdded = 0;
			foreach ($addOtheraccounts as $key=>$value) {
				if (in_array($value["otheraccount_id"], array_keys($_SESSION["shopping_otheraccount_items"]))) {
					unset($_SESSION["shopping_otheraccount_items"][$value["otheraccount_id"]]);
				}
				$countAdded++;
				$otheraccount["otheraccount_id"] = $value["otheraccount_id"];
				$otheraccount["otherPrice"] = $value["otheraccount_price"];
				$otheraccount["additionPrice"] = $value["otheraccount_additionPrice"];
				$_SESSION["shopping_otheraccount_items"][$value["otheraccount_id"]] = $otheraccount;
			}
			if (is_array($_SESSION["shopping_otheraccount_items"])) {
				$_SESSION["shopping_otheraccounts"] = array_keys($_SESSION["shopping_otheraccount_items"]);
			}
		}
		if ($countAdded > 0) {
			$add_msg = "<span class=\"success\">Successfuly added ".$countAdded." item(s) to shopping cart.</span>";
		}
		else {
			$add_msg = "<span class=\"error\">Please select one or more item(s) to add to your shopping cart.</span>";
		}
?>
				<script type="text/javascript">setTimeout("window.location = './cart.php'", 1000);</script>
				<div id="cart">
					<div class="section_title">YOUR SHOPPING CART</div>
					<div class="section_content centered">
						<?=$add_msg?><br/>
						<a href="./cart.php">Click here if your browser does not automatically redirect you.</a>
					</div>
				</div>
<?php
	}
	else if (isset($_POST["addToCart"]) && is_array($_POST["dumps"])) {
		$allDumps = $_POST["dumps"];
		$lastDump = $db->escape($allDumps[count($allDumps)-1]);
		unset($allDumps[count($allDumps)-1]);
		$sql = "SELECT * FROM `".TABLE_DUMPS."` WHERE dump_status = '".STATUS_DEFAULT."' AND dump_userid = 0 AND dump_id IN (";
		$t_vals=array();
		if (count($allDumps) > 0) {
			foreach ($allDumps as $key=>$value) {
				$t_vals[]=$db->escape($value);
				$sql .= "?, ";
			}
		}
		$t_vals[]=$lastDump;
		$sql .= "?)";
		$addDumps = $db->fetch_array($sql,$t_vals);
		if (count($addDumps) > 0) {
			if (!isset($_SESSION["shopping_dump_items"])) {
				$_SESSION["shopping_dump_items"] = array();
			}
			$countAdded = 0;
			foreach ($addDumps as $key=>$value) {
				if (in_array($value["dump_id"], array_keys($_SESSION["shopping_dump_items"]))) {
					unset($_SESSION["shopping_dump_items"][$value["dump_id"]]);
				}
				$countAdded++;
				$dump["dump_id"] = $value["dump_id"];
				$dump["dumpPrice"] = $value['dump_price'];
				if (strlen($_POST["txtBin"]) > 1) {
					$dump["binPrice"] = $db_config["binPrice"];
				}
				else {
					$dump["binPrice"] = 0;
				}
				$_SESSION["shopping_dump_items"][$value["dump_id"]] = $dump;
			}
			if (is_array($_SESSION["shopping_dump_items"])) {
				$_SESSION["shopping_dumps"] = array_keys($_SESSION["shopping_dump_items"]);
			}
		}
		if ($countAdded > 0) {
			$add_msg = "<span class=\"success\">Successfuly added ".$countAdded." item(s) to shopping cart.</span>";
		}
		else {
			$add_msg = "<span class=\"error\">Please select one or more item(s) to add to your shopping cart.</span>";
		}
?>
				<script type="text/javascript">setTimeout("window.location = './cart.php'", 1000);</script>
				<div id="cart">
					<div class="section_title">YOUR SHOPPING CART</div>
					<div class="section_content centered">
						<?=$add_msg?><br/>
						<a href="./cart.php">Click here if your browser does not automatically redirect you.</a>
					</div>
				</div>
<?php
	}
	else {
		$allCards = $_SESSION["shopping_cards"];
		$allOtheraccounts = $_SESSION["shopping_otheraccounts"];
		$allDumps = $_SESSION["shopping_dumps"];
		
		$lastCard = $db->escape($allCards[count($allCards)-1]);
		$lastOtheraccount = $db->escape($allOtheraccounts[count($allOtheraccounts)-1]);
		$lastDump = $db->escape($allDumps[count($allDumps)-1]);
		
		unset($allCards[count($allCards)-1]);
		unset($allOtheraccounts[count($allOtheraccounts)-1]);
		unset($allDumps[count($allDumps)-1]);
		
		$sql = "SELECT card_id, card_categoryid, category_name, card_bin, card_cvv, card_name, card_country, card_state, card_city, card_zip, card_ssn, card_dob, card_price, card_sellerid FROM `".TABLE_CARDS."`  LEFT JOIN `".TABLE_CATEGORYS."` ON ".TABLE_CARDS.".card_categoryid = ".TABLE_CATEGORYS.".category_id WHERE card_status = '".STATUS_DEFAULT."' AND card_userid = '0' AND card_id IN (";
		$t_vals=array();
		if (count($allCards) > 0) {
			foreach ($allCards as $key=>$value) {
				$t_vals[]=$db->escape($value);
				$sql .= "?, ";
			}
		}
		$t_vals[]=$lastCard;
		$sql .= "?)";
		$card_rows = $db->fetch_array($sql, $t_vals);
		$sql = "SELECT `".TABLE_OTHERACCOUNTS."`.* FROM `".TABLE_OTHERACCOUNTS."` LEFT JOIN `".TABLE_OTHER_CATEGORYS."` ON ".TABLE_OTHERACCOUNTS.".otheraccount_categoryid = ".TABLE_OTHER_CATEGORYS.".other_category_id WHERE otheraccount_status = '".STATUS_DEFAULT."' AND otheraccount_userid = '0' AND otheraccount_id IN (";
		$t_vals=array();
		if (count($allOtheraccounts) > 0) {
			foreach ($allOtheraccounts as $key=>$value) {
				$t_vals[]=$db->escape($value);
				$sql .= "?, ";
			}
		}
		$t_vals[]=$lastOtheraccount;
		$sql .= "?)";
		$otheraccount_rows = $db->fetch_array($sql,$t_vals);
		$sql = "SELECT `".TABLE_DUMPS."`.* FROM `".TABLE_DUMPS."` LEFT JOIN `".TABLE_DUMP_CATEGORYS."` ON ".TABLE_DUMPS.".dump_categoryid = ".TABLE_DUMP_CATEGORYS.".dump_category_id WHERE dump_status = '".STATUS_DEFAULT."' AND dump_userid = '0' AND dump_id IN (";
		$t_vals=array();
		if (count($allDumps) > 0) {
			foreach ($allDumps as $key=>$value) {
				$t_vals[]=$db->escape($value);
				$sql .= "?, ";
			}
		}
		$t_vals[]=$lastDump;
		$sql .= "?)";
		$dump_rows = $db->fetch_array($sql, $t_vals);
		
		$shoppingCards = array();
		$shoppingOtheraccounts = array();
		$shoppingDumps = array();
		
		$totalCartPrice = 0;
		if (is_array($card_rows) && count($card_rows) > 0) {
			foreach ($card_rows as $key=>$value) {
				if (substr_count($value['card_name'], " ") > 0) {
					$value["card_firstname"] = explode(" ", $value['card_name']);
					$value["card_firstname"] = $value["card_firstname"][0];
				} else {
					$value["card_firstname"] = $value["card_name"];
				}
				$value['card_ssn'] = ($value['card_ssn'] == "")?"<img src='./images/untick.png' height='15px' width='15px' />":"<img src='./images/tick.png' height='15px' width='15px' />";
				$value['card_dob'] = ($value['card_dob'] == "")?"<img src='./images/untick.png' height='15px' width='15px' />":"<img src='./images/tick.png' height='15px' width='15px' />";
				$value['category_name'] = ($value['category_name'] == "")?"(No category)":$value['category_name'];
				$value['card_total_price_format'] .= "<i>(Price $".number_format($value['card_price'], 2);
				$value['card_total_price'] = $value['card_price'];
				$value['card_addition_price'] = 0;
				if ($_SESSION["shopping_card_items"][$value["card_id"]]["binPrice"] > 0) {
					$value['card_addition_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['binPrice'];
					$value['card_total_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['binPrice'];
					$value['card_total_price_format'] .= "<br/>Search Bin $".number_format($_SESSION["shopping_card_items"][$value["card_id"]]['binPrice'], 2);
				}
				if ($_SESSION["shopping_card_items"][$value["card_id"]]["countryPrice"] > 0) {
					$value['card_addition_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['countryPrice'];
					$value['card_total_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['countryPrice'];
					$value['card_total_price_format'] .= "<br/>Search Country $".number_format($_SESSION["shopping_card_items"][$value["card_id"]]['countryPrice'], 2);
				}
				if ($_SESSION["shopping_card_items"][$value["card_id"]]["statePrice"] > 0) {
					$value['card_addition_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['statePrice'];
					$value['card_total_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['statePrice'];
					$value['card_total_price_format'] .= "<br/>Search State $".number_format($_SESSION["shopping_card_items"][$value["card_id"]]['statePrice'], 2);
				}
				if ($_SESSION["shopping_card_items"][$value["card_id"]]["cityPrice"] > 0) {
					$value['card_addition_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['cityPrice'];
					$value['card_total_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['cityPrice'];
					$value['card_total_price_format'] .= "<br/>Search City $".number_format($_SESSION["shopping_card_items"][$value["card_id"]]['cityPrice'], 2);
				}
				if ($_SESSION["shopping_card_items"][$value["card_id"]]["zipPrice"] > 0) {
					$value['card_addition_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['zipPrice'];
					$value['card_total_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['zipPrice'];
					$value['card_total_price_format'] .= "<br/>Search Zip $".number_format($_SESSION["shopping_card_items"][$value["card_id"]]['zipPrice'], 2);
				}
				$value['card_total_price_format'] .= ")</i><br /><font class=\"bold pink\">$".number_format($value['card_total_price'], 2)."</font>";
				$totalCartPrice += $value['card_total_price'];
				$shoppingCards[$key] = $value;
			}
		}
		unset($card_rows);
		if (is_array($otheraccount_rows) && count($otheraccount_rows) > 0) {
			foreach ($otheraccount_rows as $key=>$value) {
				$value['other_category_name'] = ($value['other_category_name'] == "")?"(No category)":$value['other_category_name'];
				$value['otheraccount_total_price'] = $value['otheraccount_price'];
				$value['otheraccount_total_price_format'] = "<font class=\"bold pink\">$".number_format($value['otheraccount_price'], 2)."</font>";
				$totalCartPrice += $value['otheraccount_price'];
				$shoppingOtheraccounts[$key] = $value;
			}
		}
		unset($otheraccount_rows);
		if (is_array($dump_rows) && count($dump_rows) > 0) {
			foreach ($dump_rows as $key=>$value) {
				$value['dump_category_name'] = ($value['dump_category_name'] == "")?"(No category)":$value['dump_category_name'];
				
				

				$value['dump_total_price_format'] .= "<i>(Price $".number_format($value['dump_price'], 2);
				$value['dump_total_price'] = $value['dump_price'];
				$value['dump_addition_price'] = 0;
				if ($_SESSION["shopping_dump_items"][$value["dump_id"]]["binPrice"] > 0) {
					$value['dump_addition_price'] += $_SESSION["shopping_dump_items"][$value["dump_id"]]['binPrice'];
					$value['dump_total_price'] += $_SESSION["shopping_dump_items"][$value["dump_id"]]['binPrice'];
					$value['dump_total_price_format'] .= "<br/>Search Bin $".number_format($_SESSION["shopping_dump_items"][$value["dump_id"]]['binPrice'], 2);
				}
				$value['dump_total_price_format'] .= ")</i><br /><font class=\"bold pink\">$".number_format($value['dump_total_price'], 2)."</font>";
				$totalCartPrice += $value['dump_total_price'];
				$shoppingDumps[$key] = $value;
			}
		}
		unset($dump_rows);
		
		if ($_POST["btnBuy"] != "" && (is_array($shoppingCards) || is_array($shoppingOtheraccounts) || is_array($shoppingDumps)) && (count($shoppingCards) + count($shoppingOtheraccounts) + count($shoppingDumps) > 0)) {
			
			$user_balance = $user_info["user_balance"];
			if (doubleval($user_balance) >= doubleval($totalCartPrice)) {
				$cards_ids = $_SESSION["shopping_cards"];
				$cards_update["card_userid"] = $user_info["user_id"];
				$cards_update["card_buyTime"] = time();
				if (is_array($cards_ids)&&!empty($cards_ids)) {
					$cards_update_where = "card_id IN (";
					$lastCard = $db->escape($cards_ids[count($cards_ids) - 1]);
					unset($cards_ids[count($cards_ids) - 1]);
					$cards_update_where_vals = array();
					if (count($cards_ids) > 0) {
						foreach ($cards_ids as $k => $v) {
							$cards_update_where_vals[] = $v;
							$cards_update_where .= "?, ";
						}
					}
					$cards_update_where_vals[] = $lastCard;
					$cards_update_where .= "?";
					$cards_update_where .= ")";
				} else $cards_update_where = "0";
				
				
				$otheraccounts_ids = $_SESSION["shopping_otheraccounts"];
				$otheraccounts_update["otheraccount_userid"] = $user_info["user_id"];
				$otheraccounts_update["otheraccount_buyTime"] = time();
				if (is_array($otheraccounts_ids)&&!empty($otheraccounts_ids)) {
					$otheraccounts_update_where = "otheraccount_id IN (";
					$lastOtheraccount = $db->escape($otheraccounts_ids[count($otheraccounts_ids) - 1]);
					unset($otheraccounts_ids[count($otheraccounts_ids) - 1]);
					$otheraccounts_update_where_vals=array();
					if (count($otheraccounts_ids) > 0) {
						foreach ($otheraccounts_ids as $k => $v) {
							$otheraccounts_update_where_vals[] = $v;
							$otheraccounts_update_where .= "?, ";
						}
					}
					$otheraccounts_update_where_vals[] = $lastOtheraccount;
					$otheraccounts_update_where .= "?";
					$otheraccounts_update_where .= ")";
				} else {
					$otheraccounts_update_where = "0";
				}
				
				$dump_ids = $_SESSION["shopping_dumps"];
				$dumps_update["dump_userid"] = $user_info["user_id"];
				$dumps_update["dump_buyTime"] = time();
				
				if (is_array($dump_ids)&&!empty($dump_ids)) {
					$dumps_update_where = "dump_id IN (";
					$lastDump = $db->escape($dump_ids[count($dump_ids) - 1]);
					unset($dump_ids[count($dump_ids) - 1]);
					$dumps_update_where_vals=array();
					if (count($dump_ids) > 0) {
						foreach ($dump_ids as $k => $v) {
							$dumps_update_where_vals[] = $v;
							$dumps_update_where .= "?, ";
						}
					}
					$dumps_update_where_vals[]= $lastDump;
					$dumps_update_where .= "?";
					$dumps_update_where .= ")";
				} else {
					$dumps_update_where = '0';
				}		
				
				
				$orders_add["order_userid"] = $user_info["user_id"];
				$orders_add["order_item"] = serialize(array("shoppingCards" => $shoppingCards, "shoppingOtheraccounts" => $shoppingOtheraccounts, "shoppingDumps" => $shoppingDumps));
				$orders_add["order_total"] = doubleval($totalCartPrice);
				$orders_add["order_before"] = doubleval($user_balance);
				$orders_add["order_time"] = time();
				$user_update["user_balance"] = doubleval($user_balance)-doubleval($totalCartPrice);
				if ($db->insert(TABLE_ORDERS, $orders_add)) {
					if ($db->update(TABLE_USERS, $user_update, "user_id='".$user_info["user_id"]."'")) {
						$update_table_cards = $db->update(TABLE_CARDS, $cards_update, $cards_update_where, $cards_update_where_vals);
						$update_other_acc =  $db->update(TABLE_OTHERACCOUNTS, $otheraccounts_update, $otheraccounts_update_where, $otheraccounts_update_where_vals);
						$update_table_dumps = $db->update(TABLE_DUMPS, $dumps_update, $dumps_update_where, $dumps_update_where_vals );				
						
						if ( $update_table_cards || $update_other_acc || $update_table_dumps ) {
						
							if (is_array($shoppingCards) && count($shoppingCards) > 0) {
								foreach ($shoppingCards as $key=>$value) {
									$cardIncome = doubleval($value["card_total_price"] * (1-$db_config["commission"]));
									$card_update["card_additionPrice"] = $value["card_addition_price"];
									$db->query("UPDATE `".TABLE_USERS."` SET user_balance = user_balance + '".$cardIncome."' WHERE user_id = '".$value["card_sellerid"]."'");
									$db->update(TABLE_CARDS, $card_update, "card_id=?", $value["card_id"]);
								}
							}
							if (is_array($shoppingOtheraccounts) && count($shoppingOtheraccounts) > 0) {
								foreach ($shoppingOtheraccounts as $key=>$value) {
									$otheraccountIncome = doubleval($value["otheraccount_total_price"] * (1-$db_config["commission"]));
									$otheraccount_update["otheraccount_additionPrice"] = $value["otheraccount_addition_price"];
									$db->query("UPDATE `".TABLE_USERS."` SET user_balance = user_balance + '".$otheraccountIncome."' WHERE user_id = '".$value["otheraccount_sellerid"]."'");
									$db->update(TABLE_OTHERACCOUNTS, $otheraccount_update, "otheraccount_id=?", $value["otheraccount_id"]);
								}
							}
							if (is_array($shoppingDumps) && count($shoppingDumps) > 0) {
								foreach ($shoppingDumps as $key=>$value) {
									$dumpIncome = doubleval($value["dump_total_price"] * (1-$db_config["commission"]));
									$db->query("UPDATE `".TABLE_USERS."` SET user_balance = user_balance + '".$dumpIncome."' WHERE user_id = '".$value["dump_sellerid"]."'");
								}
							}
							$user_info["user_balance"] = $user_update["user_balance"];
							$_SESSION["shopping_card_items"] = array();
							$_SESSION["shopping_cards"] = array();
							$_SESSION["shopping_otheraccount_items"] = array();
							$_SESSION["shopping_otheraccounts"] = array();
							$_SESSION["shopping_dump_items"] = array();
							$_SESSION["shopping_dumps"] = array();
							if(  count($shoppingCards) > 0 ) {
									$buyResult = "<script type=\"text/javascript\">setTimeout(\"window.location = 'mycards.php'\", 1000);</script><span class=\"success\">Your order is completed, go to 'Bought Cards' to view your items.</span>";
							} elseif(  count($shoppingOtheraccounts) > 0 ) {
									$buyResult = "<script type=\"text/javascript\">setTimeout(\"window.location = 'myotheraccounts.php'\", 1000);</script><span class=\"success\">Your order is completed.</span>";
							} elseif( count($shoppingDumps) > 0) {
									$buyResult = "<script type=\"text/javascript\">setTimeout(\"window.location = 'mydumps.php'\", 1000);</script><span class=\"success\">Your order is completed, go to 'Bought Dumps' to view your items.</span>";
							} 
						} else {
								$buyResult = "<span class=\"error\">Update Items: SQL Error, please try again.1</span>";
						}
					} else {
						$buyResult = "<span class=\"error\">Update Credit: SQL Error, please try again.2</span>";
					}
				}
				else {
					$buyResult = "<span class=\"error\">Insert Order Record: SQL Error, please try again.3</span>";
				}
			}
			else {
				$buyResult = "<span class=\"error\">You don't have enough balance, please deposit more balance to buy.</span>";
			}
		}
?>
				<div id="cart">
					<div class="section_title">YOUR SHOPPING CART</div>
					<div class="section_title"><?=$buyResult?></div>
					<div class="section_content">
						<form name="shoping_cart" method="POST" action="">
                        <table cellspacing="1" cellpadding="0" border="0" class="tablesorter" id="tablesorter" style="border-spacing: 1px;">
                                <thead>
									<tr>
										<td colspan="8" class="centered bold" style="font-size: 30px; padding: 5px; color: #000000;">
											Credit Cards
										</td>
									</tr>
									<tr>
                                        <th class="header">
											<span>CARD NUMBER</span>
										</th>
                                        <th class="header">
											<span>CATEGORY<span>
										</th>
                                        <th class="header">
											<span>FIRST NAME</span>
										</th>
                                        <th class="header">
											<span>COUNTRY</span>
										</th>
										<!--td class="formstyle centered bold">
											<span>STATE</span>
										</td>
										<td class="formstyle centered bold">
											<span>CITY</span>
										</td>
										<td class="formstyle centered bold">
											<span>ZIP</span>
										</td-->
                                        <th class="header">
											<span>SSN</span>
										</th>
                                        <th class="header">
											<span>DOB</span>
										</th>
                                        <th class="header">
											<span>PRICE</span>
										</th>
                                        <th class="header">
                                            <center><input type="checkbox" name="selectAllCards" id="selectAllCards" onclick="checkAll(this.id, 'cards[]')" value=""></center>
										</th>
									</tr>
                                    </thead>
                                <tbody>
<?php
		if (is_array($shoppingCards) && count($shoppingCards) > 0) {
            $j=0;
			foreach ($shoppingCards as $key=>$value) {
                $j++;
?>
									<tr class="<?=($j%2==0?"odd":"even");?>"  style="font-size: 16px;">
										<td>
											<span><?=$value['card_bin']?>******</span>
										</td>
										<td>
											<span><?=$value['category_name']?></span>
										</td>
										<td>
											<span><?=$value["card_firstname"]?></span>
										</td>
										<td>
											<span><?=$value['card_country']?></span>
										</td>
										<!--td class="centered">
											<span><?//=$value['card_state']?></span>
										</td>
										<td class="centered">
											<span><?//=$value['card_city']?></span>
										</td>
										<td class="centered">
											<span><?//=$value['card_zip']?></span>
										</td-->
										<td>
											<span><?=$value['card_ssn']?></span>
										</td>
										<td>
											<span><?=$value['card_dob']?></span>
										</td>
										<td>
											<span><?=$value['card_total_price_format']?></span>
										</td>
										<td>
											<center><input class="formstyle" type="checkbox" name="cards[]" value="<?=$value['card_id']?>"></center>
										</td>
									</tr>
<?php
			}
		}
?>
								</tbody>
							</table>
							<table cellspacing="1" cellpadding="0" border="0" class="tablesorter" id="tablesorter" style="border-spacing: 1px;">
								<thead>
									<tr>
										<td colspan="8" class="centered bold" style="font-size: 20px; padding: 5px; color: #000000;">
											Other Accounts
										</td>
									</tr>
									<tr>
                                        <th class="header">
											<span>ACCOUNT INFORMATION</span>
										</th>
                                        <th class="header">
											<span>ACCOUNT TYPE</span>
										</th>
                                        <th class="header">
											<span>CATEGORY<span>
										</th>
                                        <th class="header">
											<span>PRICE</span>
										</th>
                                        <th class="header">
											<center><input type="checkbox" name="selectAllCards" id="selectAllOtheraccounts" onclick="checkAll(this.id, 'otheraccounts[]')" value=""></center>
										</th>
									</tr>
                                    </thead>
                                    <tbody>
<?php
		if (is_array($shoppingOtheraccounts) && count($shoppingOtheraccounts) > 0) {
            $j = 0;
			foreach ($shoppingOtheraccounts as $key=>$value) {
                $j++;
?>
									<tr class="<?=($j%2==0?"odd":"even");?>" style="font-size: 16px;">
										<td class="centered bold">
											<span><?=$value['otheraccount_info']?></span>
										</td>
										<td class="centered">
											<span><?=$value["otheraccount_type"]?></span>
										</td>
										<td class="centered">
											<span><?=$value['other_category_name']?></span>
										</td>
										<td class="centered">
											<span><?=$value['otheraccount_total_price_format']?></span>
										</td>
										<td class="centered">
											<center><input type="checkbox" name="otheraccounts[]" value="<?=$value['otheraccount_id']?>"></center>
										</td>
									</tr>
<?php
			}
		}
?>
								</tbody>
							</table>
							<table cellspacing="1" cellpadding="0" border="0" class="tablesorter" id="tablesorter" style="border-spacing: 1px;">
								<thead>
									<tr>
										<td colspan="11" class="centered bold" style="font-size: 20px; padding: 5px; color: #000000;">
											Dumps
										</td>
									</tr>
									<tr>
										<th class="header">
											<span class="bold">DUMP NUMBER</span>
										</th>
                                        <th class="header">
											<span class="bold">CATEGORY</span>
										</th>
                                        <th class="header">
											<span class="bold">EXPIRE</span>
										</th>
                                        <th class="header">
											<span class="bold">COUNTRY</span>
										</th>
                                        <th class="header">
											<span class="bold">BANK</span>
										</th>
                                        <th class="header">
											<span class="bold">LEVEL</span>
										</th>
                                        <th class="header">
											<span class="bold">CREDIT TYPE</span>
										</th>
                                        <th class="header">
											<span class="bold">CODE</span>
										</th>
                                        <th class="header">
											<span class="bold">TRACK</span>
										</th>
                                        <th class="header">
											<span class="bold">PRICE</span>
										</th>
                                        <th class="header">
											<center><input class="formstyle" type="checkbox" name="selectAllDumps" id="selectAllDumps" onclick="checkAll(this.id, 'dumps[]')" value=""></center>
										</th>
									</tr>
                                    </thead>
                                    <tbody>
<?php
		if (is_array($shoppingDumps) && count($shoppingDumps) > 0) {
            $j=0;
			foreach ($shoppingDumps as $key=>$value) {
                $j++;
?>
									<tr class="<?=($j%2==0?"odd":"even");?>" style="font-size: 16px;">
										<td class="centered bold">
											<span><?=$value['dump_bin']?>******</span>
										</td>
										<td class="centered bold">
											<span><?=($value['dump_category_name']=="")?"(No Category)":$value['dump_category_name']?></span>
										</td>
										<td class="centered">
											<span><?=$value['dump_exp']?></span>
										</td>
										<td class="centered">
											<span><?=$value['dump_country']?></span>
										</td>
										<td class="centered">
											<span><?=$value['dump_bank']?></span>
										</td>
										<td class="centered">
											<span><?=$value['dump_level']?></span>
										</td>
										<td class="centered">
											<span><?=$value['dump_ctype']?></span>
										</td>
										<td class="centered">
											<span><?=$value['dump_code']?></span>
										</td>
										<td class="centered">
											<span><?=($value['dump_tr1'] == '1')?"TR1":""?></span><?=($value['dump_tr1'] == 1 && $value['dump_tr2'] == 1)?"+":""?><?=($value['dump_tr1'] == 0 && $value['dump_tr2'] == 0)?" - ":""?><span><?=($value['dump_tr2'] == 1)?"TR2":""?></span>
										</td>
										<td class="centered bold">
											<span><?=$value['dump_total_price_format']?></span>
										</td>
										<td class="centered">
											<center><input type="checkbox" name="dumps[]" value="<?=$value['dump_id']?>"></center>
										</td>
									</tr>
<?php
			}
		}
?>
								</tbody>
							</table>
                            <table class="content_table">
								<tbody>
									<tr>
                                        <td colspan="3" class="centered">
                                            <p>
                                                <label>
                                                    <input name="btnBuy" type="submit" id="btnBuy" value="Purchase" />
                                                </label>
                                            </p>
                                        </td>
										<td class="red bold right" style="font-size: 18px; padding-top: 10px;">
											Total:
										</td>
										<td class="centered" style="width:70px; font-size: 18px;">
											<span class="red bold">$<?=number_format($totalCartPrice, 2)?></span>
										</td>
										<td class="centered" style="width:40px;">
											<input name="btnDeleteSelect" type="submit" id="btnDeleteSelect" value="Delete" />
										</td>
									</tr>

								</tbody>
							</table>
						</form>
					</div>
				</div>
<?php
	}
}
else if ($checkLogin && $_SESSION["user_groupid"] == intval(PER_UNACTIVATE)){
	require("./miniactivate.php");
}
else {
	require("./minilogin.php");
}
require("./footer.php");
?>