<?php
require("./header.php");
if ($checkLogin) {
	if ($_GET["type"] == "seller") {
		$errorMsg = "";
		$sql = "SELECT user_id, user_name, user_groupid from `".TABLE_USERS."` WHERE user_groupid <= '".strval(PER_SELLER)."' ORDER BY user_name";
		$sellers = $db->fetch_array($sql);
		$allSellers = array();
		if (is_array($sellers) && count($sellers) > 0) {
			foreach ($sellers as $seller) {
				$allSellers[$seller["user_id"]] = $seller;
			}
		}
		if ($_GET["lstSeller"] != "" && intval($_GET["lstSeller"]) > 0) {
			$seller_id = strval(intval($_GET["lstSeller"]));
			if (count($allSellers[$seller_id]) > 0) {
				$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_sellerid = '".$seller_id."'";
				$total_cards = $db->query_first($sql);
				
				$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_sellerid = '".$seller_id."'";
				$sold_cards = $db->query_first($sql);
				
				$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check = '".strval(CHECK_VALID)."' AND card_sellerid = '".$seller_id."'";
				$valid_cards = $db->query_first($sql);
				
				$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check = '".strval(CHECK_INVALID)."' AND card_sellerid = '".$seller_id."'";
				$invalid_cards = $db->query_first($sql);
				
				$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check = '".strval(CHECK_DEFAULT)."' AND card_sellerid = '".$seller_id."'";
				$uncheck_cards = $db->query_first($sql);
				
				$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check = '".strval(CHECK_REFUND)."' AND card_sellerid = '".$seller_id."'";
				$refund_cards = $db->query_first($sql);
				
				$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check = '".strval(CHECK_WAIT_REFUND)."' AND card_sellerid = '".$seller_id."'";
				$wait_refund_cards = $db->query_first($sql);
				
				
				
				$sql = "SELECT count(*), IFNULL(SUM(card_price + card_additionPrice), 0) AS income FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check <> '".strval(CHECK_REFUND)."' AND card_sellerid = '".$seller_id."'";
				$sold_income_cards = $db->query_first($sql);
				
				$sql = "SELECT count(*), IFNULL(SUM(card_price + card_additionPrice), 0) AS refund FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check = '".strval(CHECK_REFUND)."' AND card_sellerid = '".$seller_id."'";
				$refund_money_cards = $db->query_first($sql);
				
				
				
				$sql = "SELECT count(*) FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_sellerid = '".$seller_id."'";
				$total_otheraccounts = $db->query_first($sql);
				
				$sql = "SELECT count(*) FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid <> '0' AND otheraccount_sellerid = '".$seller_id."'";
				$sold_otheraccounts = $db->query_first($sql);
				
				$sql = "SELECT count(*) FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid <> '0' AND otheraccount_check = '".strval(CHECK_VALID)."' AND otheraccount_sellerid = '".$seller_id."'";
				$valid_otheraccounts = $db->query_first($sql);
				
				$sql = "SELECT count(*) FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid <> '0' AND otheraccount_check = '".strval(CHECK_INVALID)."' AND otheraccount_sellerid = '".$seller_id."'";
				$invalid_otheraccounts = $db->query_first($sql);
				
				$sql = "SELECT count(*) FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid <> '0' AND otheraccount_check = '".strval(CHECK_DEFAULT)."' AND otheraccount_sellerid = '".$seller_id."'";
				$uncheck_otheraccounts = $db->query_first($sql);
				
				$sql = "SELECT count(*) FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid <> '0' AND otheraccount_check = '".strval(CHECK_REFUND)."' AND otheraccount_sellerid = '".$seller_id."'";
				$refund_otheraccounts = $db->query_first($sql);
				
				$sql = "SELECT count(*) FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid <> '0' AND otheraccount_check = '".strval(CHECK_WAIT_REFUND)."' AND otheraccount_sellerid = '".$seller_id."'";
				$wait_refund_otheraccounts = $db->query_first($sql);
				
				
				
				$sql = "SELECT count(*), IFNULL(SUM(otheraccount_price + otheraccount_additionPrice), 0) AS income FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid <> '0' AND otheraccount_check <> '".strval(CHECK_REFUND)."' AND otheraccount_sellerid = '".$seller_id."'";
				$sold_income_otheraccounts = $db->query_first($sql);
				$sql = "SELECT count(*), IFNULL(SUM(otheraccount_price + otheraccount_additionPrice), 0) AS refund FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid <> '0' AND otheraccount_check = '".strval(CHECK_REFUND)."' AND otheraccount_sellerid = '".$seller_id."'";
				$refund_money_otheraccounts = $db->query_first($sql);

				
				
				
				$today_where = "(FROM_UNIXTIME(".TABLE_CARDS.".card_buyTime, '%d') = '".date("d")."') AND (FROM_UNIXTIME(".TABLE_CARDS.".card_buyTime, '%m') = '".date("m")."') AND (FROM_UNIXTIME(".TABLE_CARDS.".card_buyTime, '%Y') = '".date("Y")."')";
				
				$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_sellerid = '".$seller_id."' AND ".$today_where;
				$today_sold_cards = $db->query_first($sql);
				
				$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check = '".strval(CHECK_VALID)."' AND card_sellerid = '".$seller_id."' AND ".$today_where;
				$today_valid_cards = $db->query_first($sql);
				
				$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check = '".strval(CHECK_INVALID)."' AND card_sellerid = '".$seller_id."' AND ".$today_where;
				$today_invalid_cards = $db->query_first($sql);
				
				$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check = '".strval(CHECK_REFUND)."' AND card_sellerid = '".$seller_id."' AND ".$today_where;
				$today_refund_cards = $db->query_first($sql);
				
				$sql = "SELECT count(*), IFNULL(SUM(card_price + card_additionPrice), 0) AS income FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check <> '".strval(CHECK_REFUND)."' AND card_sellerid = '".$seller_id."' AND ".$today_where;
				$today_sold_income_cards = $db->query_first($sql);
				
				$sql = "SELECT count(*), IFNULL(SUM(card_price + card_additionPrice), 0) AS refund FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check = '".strval(CHECK_REFUND)."' AND card_sellerid = '".$seller_id."' AND ".$today_where;
				$today_refund_money_cards = $db->query_first($sql);

				
				
				
				$today_where = "(FROM_UNIXTIME(".TABLE_OTHERACCOUNTS.".otheraccount_buyTime, '%d') = '".date("d")."') AND (FROM_UNIXTIME(".TABLE_OTHERACCOUNTS.".otheraccount_buyTime, '%m') = '".date("m")."') AND (FROM_UNIXTIME(".TABLE_OTHERACCOUNTS.".otheraccount_buyTime, '%Y') = '".date("Y")."')";
				
				$sql = "SELECT count(*) FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid <> '0' AND otheraccount_sellerid = '".$seller_id."' AND ".$today_where;
				$today_sold_otheraccounts = $db->query_first($sql);
				
				$sql = "SELECT count(*) FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid <> '0' AND otheraccount_check = '".strval(CHECK_VALID)."' AND otheraccount_sellerid = '".$seller_id."' AND ".$today_where;
				$today_valid_otheraccounts = $db->query_first($sql);
				
				$sql = "SELECT count(*) FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid <> '0' AND otheraccount_check = '".strval(CHECK_INVALID)."' AND otheraccount_sellerid = '".$seller_id."' AND ".$today_where;
				$today_invalid_otheraccounts = $db->query_first($sql);
				
				$sql = "SELECT count(*) FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid <> '0' AND otheraccount_check = '".strval(CHECK_REFUND)."' AND otheraccount_sellerid = '".$seller_id."' AND ".$today_where;
				$today_refund_otheraccounts = $db->query_first($sql);
				
				$sql = "SELECT count(*), IFNULL(SUM(otheraccount_price + otheraccount_additionPrice), 0) AS income FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid <> '0' AND otheraccount_check <> '".strval(CHECK_REFUND)."' AND otheraccount_sellerid = '".$seller_id."' AND ".$today_where;
				$today_sold_income_otheraccounts = $db->query_first($sql);
				
				$sql = "SELECT count(*), IFNULL(SUM(otheraccount_price + otheraccount_additionPrice), 0) AS refund FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid <> '0' AND otheraccount_check = '".strval(CHECK_REFUND)."' AND otheraccount_sellerid = '".$seller_id."' AND ".$today_where;
				$today_refund_money_otheraccounts = $db->query_first($sql);
			} else {
			print_r($allSellers[$seller_id]);
				$errorMsg = "Cannot find this seller.";
			}
		} else {
			$errorMsg = "Please chose one seller to view statistic.";
		}
?>
					<div class="section_title">SELLER STATISTICS</div>
					<div class="section_title error"><?=$errorMsg?></div>
					<div id="admin_static">
						<table class="content_table" style="width:600px; margin: 0 auto;">
							<tbody class="left bold">
								<tr>
									<form action="" method="GETE">
									<input type="hidden" name="type" value="seller" />
									<td class="right">
										SELLER: <select name="lstSeller">
<?php
		if (is_array($allSellers) && count($allSellers) > 0) {
			foreach ($allSellers as $seller) {
				echo "<option value=\"".$seller['user_id']."\"".(($_GET["lstSeller"] == $seller['user_id'])?" selected":"").">".$seller['user_name']."</option>";
			}
		}
?>
										</select>
									</td>
									<td>
										<input type="submit" value="View Statistic" />
									</td>
									</form>
								</tr>
<?php
		if (count($allSellers[$seller_id]) > 0) {
?>
<?php
		}
?>
							</tbody>
						</table>
					</div>

					<div id="admin_static">
						<table class="content_table" style="width:600px; margin: 0 auto;">
							<tbody class="left bold">
								<tr>
									<td class="success">
										Total Cards: <?=$total_cards["count(*)"]?>
									</td>
									<td class="error">
										Unsold Cards: <?=$total_cards["count(*)"] - $sold_cards["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="success">
										Valid Sold Cards: <?=$valid_cards["count(*)"]?>
									</td>
									<td class="error">
										Invalid Sold Cards: <?=$invalid_cards["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="success">
										Sold Cards: <?=$sold_cards["count(*)"]?>
									</td>
									<td class="error">
										Uncheck Cards: <?=$uncheck_cards["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="success">
										Refund Cards: <?=$refund_cards["count(*)"]?>
									</td>
									<td class="error">
										Wait Refund Cards: <?=$wait_refund_cards["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="success">
										Total Accounts: <?=$total_otheraccounts["count(*)"]?>
									</td>
									<td class="error">
										Unsold Accounts: <?=$total_otheraccounts["count(*)"] - $sold_otheraccounts["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="success">
										Valid Sold Accounts: <?=$valid_otheraccounts["count(*)"]?>
									</td>
									<td class="error">
										Invalid Sold Accounts: <?=$invalid_otheraccounts["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="success">
										Sold Accounts: <?=$sold_otheraccounts["count(*)"]?>
									</td>
									<td class="error">
										Uncheck Accounts: <?=$uncheck_otheraccounts["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="success">
										Refund Accounts: <?=$refund_otheraccounts["count(*)"]?>
									</td>
									<td class="error">
										Wait Refund Accounts: <?=$wait_refund_otheraccounts["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="success">
										Sold Income: $<?=doubleval($sold_income_cards["income"] + $sold_income_otheraccounts["income"])?>
									</td>
									<td class="error">
										Refund Money: $<?=doubleval($sold_income_cards["refund"] + $sold_income_otheraccounts["refund"])?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="section_title">TODAY STATISTICS</div>
					<div id="admin_static">
						<table class="content_table" style="width:600px; margin: 0 auto;">
							<tbody class="left bold">
								<tr>
									<td class="success">
										Valid Sold Cards: <?=$today_valid_cards["count(*)"]?>
									</td>
									<td class="error">
										Invalid Sold Cards: <?=$today_invalid_cards["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="success">
										Sold Cards: <?=$today_sold_cards["count(*)"]?>
									</td>
									<td class="error">
										Refund Cards: <?=$today_refund_cards["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="success">
										Valid Sold Accounts: <?=$today_valid_otheraccounts["count(*)"]?>
									</td>
									<td class="error">
										Invalid Sold Accounts: <?=$today_invalid_otheraccounts["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="success">
										Sold Accounts: <?=$today_sold_otheraccounts["count(*)"]?>
									</td>
									<td class="error">
										Refund Accounts: <?=$today_refund_otheraccounts["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="success">
										Sold Income: $<?=doubleval($today_sold_income_cards["income"] + $today_sold_income_otheraccounts["income"])?>
									</td>
									<td class="error">
										Refund money: $<?=doubleval($today_sold_income_cards["refund"] + $today_sold_income_otheraccounts["refund"])?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
<?php
	} else {
		$sql = "SELECT count(*) FROM `".TABLE_USERS."`";
		$total_users = $db->query_first($sql);
		$sql = "SELECT count(*) FROM `".TABLE_USERS."` WHERE user_balance > 0";
		$balance_users = $db->query_first($sql);
		
		$sql = "SELECT count(*) FROM `".TABLE_CARDS."`";
		$total_cards = $db->query_first($sql);
		$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_userid <> '0'";
		$sold_cards = $db->query_first($sql);
		
		$sql = "SELECT count(*) FROM `".TABLE_OTHERACCOUNTS."`";
		$total_accounts = $db->query_first($sql);
		$sql = "SELECT count(*) FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid <> '0'";
		$sold_accounts = $db->query_first($sql);
		
		$sql = "SELECT count(*) FROM `".TABLE_DUMPS."`";
		$total_dumps = $db->query_first($sql);
		$sql = "SELECT count(*) FROM `".TABLE_DUMPS."` WHERE dump_userid <> '0'";
		$sold_dumps = $db->query_first($sql);
		
		$sql = "SELECT count(*), SUM(upgrade_price) FROM `".TABLE_UPGRADES."`";
		$total_upgrades = $db->query_first($sql);
		$sql = "SELECT count(*), SUM(deposit_amount) FROM `".TABLE_DEPOSITS."`";
		$total_deposits = $db->query_first($sql);
		$sql = "SELECT count(*), SUM(order_total) FROM `".TABLE_ORDERS."`";
		$total_orders = $db->query_first($sql);
		$sql = "SELECT count(*), SUM(check_fee) FROM `".TABLE_CHECKS."`";
		$total_checks = $db->query_first($sql);
		
		$sql = "SELECT count(*) FROM `".TABLE_USERS."` WHERE (FROM_UNIXTIME(".TABLE_USERS.".user_regdate, '%d') = '".date("d")."') AND (FROM_UNIXTIME(".TABLE_USERS.".user_regdate, '%m') = '".date("m")."') AND (FROM_UNIXTIME(".TABLE_USERS.".user_regdate, '%Y') = '".date("Y")."')";
		$today_users = $db->query_first($sql);
		$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE (FROM_UNIXTIME(".TABLE_CARDS.".card_buyTime, '%d') = '".date("d")."') AND (FROM_UNIXTIME(".TABLE_CARDS.".card_buyTime, '%m') = '".date("m")."') AND (FROM_UNIXTIME(".TABLE_CARDS.".card_buyTime, '%Y') = '".date("Y")."')";
		$today_cards = $db->query_first($sql);
		
		$sql = "SELECT count(*) FROM `".TABLE_OTHERACCOUNTS."` WHERE (FROM_UNIXTIME(".TABLE_OTHERACCOUNTS.".otheraccount_buyTime, '%d') = '".date("d")."') AND (FROM_UNIXTIME(".TABLE_OTHERACCOUNTS.".otheraccount_buyTime, '%m') = '".date("m")."') AND (FROM_UNIXTIME(".TABLE_OTHERACCOUNTS.".otheraccount_buyTime, '%Y') = '".date("Y")."')";
		$today_accounts = $db->query_first($sql);
		$sql = "SELECT count(*) FROM `".TABLE_DUMPS."` WHERE (FROM_UNIXTIME(".TABLE_DUMPS.".dump_buyTime, '%d') = '".date("d")."') AND (FROM_UNIXTIME(".TABLE_DUMPS.".dump_buyTime, '%m') = '".date("m")."') AND (FROM_UNIXTIME(".TABLE_DUMPS.".dump_buyTime, '%Y') = '".date("Y")."')";
		$today_dumps = $db->query_first($sql);
		
		$sql = "SELECT count(*), SUM(upgrade_price) FROM `".TABLE_UPGRADES."` WHERE (FROM_UNIXTIME(".TABLE_UPGRADES.".upgrade_time, '%d') = '".date("d")."') AND (FROM_UNIXTIME(".TABLE_UPGRADES.".upgrade_time, '%m') = '".date("m")."') AND (FROM_UNIXTIME(".TABLE_UPGRADES.".upgrade_time, '%Y') = '".date("Y")."')";
		$today_upgrades = $db->query_first($sql);
		$sql = "SELECT count(*), SUM(deposit_amount) FROM `".TABLE_DEPOSITS."` WHERE (FROM_UNIXTIME(".TABLE_DEPOSITS.".deposit_time, '%d') = '".date("d")."') AND (FROM_UNIXTIME(".TABLE_DEPOSITS.".deposit_time, '%m') = '".date("m")."') AND (FROM_UNIXTIME(".TABLE_DEPOSITS.".deposit_time, '%Y') = '".date("Y")."')";
		$today_deposits = $db->query_first($sql);
		$sql = "SELECT count(*), SUM(order_total) FROM `".TABLE_ORDERS."` WHERE (FROM_UNIXTIME(".TABLE_ORDERS.".order_time, '%d') = '".date("d")."') AND (FROM_UNIXTIME(".TABLE_ORDERS.".order_time, '%m') = '".date("m")."') AND (FROM_UNIXTIME(".TABLE_ORDERS.".order_time, '%Y') = '".date("Y")."')";
		$today_orders = $db->query_first($sql);
		$sql = "SELECT count(*), SUM(check_fee) FROM `".TABLE_CHECKS."` WHERE (FROM_UNIXTIME(".TABLE_CHECKS.".check_time, '%d') = '".date("d")."') AND (FROM_UNIXTIME(".TABLE_CHECKS.".check_time, '%m') = '".date("m")."') AND (FROM_UNIXTIME(".TABLE_CHECKS.".check_time, '%Y') = '".date("Y")."')";
		$today_checks = $db->query_first($sql);
?>
				<div id="config_manager">
					<div class="section_title">STORE STATISTICS</div>
					<div id="admin_static">
						<table class="borderstyle" style="width:600px; margin: 0 auto;">
							<tbody class="left bold">
								<tr>
									<td class="white">
										Total Users: <?=$total_users["count(*)"]?>
									</td>
									<td class="pink">
										Balance Users: <?=$balance_users["count(*)"]?>
									</td>
									<td class="red">
										Empty Users: <?=$total_users["count(*)"] - $balance_users["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="white">
										Total Cards: <?=$total_cards["count(*)"]?>
									</td>
									<td class="pink">
										Sold Cards: <?=$sold_cards["count(*)"]?>
									</td>
									<td class="red">
										Unsold Cards: <?=$total_cards["count(*)"] - $sold_cards["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="white">
										Total Accounts: <?=$total_accounts["count(*)"]?>
									</td>
									<td class="pink">
										Sold Accounts: <?=$sold_accounts["count(*)"]?>
									</td>
									<td class="red">
										Unsold Accounts: <?=$total_accounts["count(*)"] - $sold_accounts["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="white">
										Total Dumps: <?=$total_dumps["count(*)"]?>
									</td>
									<td class="pink">
										Sold Dumps: <?=$sold_dumps["count(*)"]?>
									</td>
									<td class="red">
										Unsold Dumps: <?=$total_dumps["count(*)"] - $sold_dumps["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="white">
										Upgrades Number: <?=$total_upgrades["count(*)"]?>
									</td>
									<td class="pink">
										Upgrades Money: $<?=number_format($total_upgrades["SUM(upgrade_price)"], 2, '.', '')?>
									</td>
								</tr>
								<tr>
									<td class="white">
										Deposits Number: <?=$total_deposits["count(*)"]?>
									</td>
									<td class="pink">
										Deposits Money: $<?=number_format($total_deposits["SUM(deposit_amount)"], 2, '.', '')?>
									</td>
								</tr>
								<tr>
									<td class="white">
										Orders Number: <?=$total_orders["count(*)"]?>
									</td>
									<td class="pink">
										Orders Money: $<?=number_format($total_orders["SUM(order_total)"], 2, '.', '')?>
									</td>
								</tr>
<?php
		if (ENABLE_CHECKER == true) {
?>
								<tr>
									<td class="white">
										Checks Number: <?=$total_checks["count(*)"]?>
									</td>
									<td class="pink">
										Checks Money: $<?=number_format($total_checks["SUM(check_fee)"], 2, '.', '')?>
									</td>
								</tr>
<?php
		}
?>
							</tbody>
						</table>
					</div>
					<div class="section_title">TODAY STATISTICS</div>
					<div id="admin_static">
						<table class="borderstyle" style="width:600px; margin: 0 auto;">
							<tbody class="left bold">
								<tr>
									<td class="white">
										New Users: <?=$today_users["count(*)"]?>
									</td>
									<td class="pink">
										Sold Cards: <?=$today_cards["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="white">
										Sold Accounts: <?=$today_accounts["count(*)"]?>
									</td>
									<td class="pink">
										Sold Dumps: <?=$today_dumps["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="white">
										Upgrades Number: <?=$today_upgrades["count(*)"]?>
									</td>
									<td class="pink">
										Upgrades Money: $<?=number_format($today_upgrades["SUM(upgrade_price)"], 2, '.', '')?>
									</td>
								</tr>
								<tr>
									<td class="white">
										Deposits Number: <?=$today_deposits["count(*)"]?>
									</td>
									<td class="pink">
										Deposits Money: $<?=number_format($today_deposits["SUM(deposit_amount)"], 2, '.', '')?>
									</td>
								</tr>
								<tr>
									<td class="white">
										Orders Number: <?=$today_orders["count(*)"]?>
									</td>
									<td class="pink">
										Orders Money: $<?=number_format($today_orders["SUM(order_total)"], 2, '.', '')?>
									</td>
								</tr>
<?php
		if (ENABLE_CHECKER == true) {
?>
								<tr>
									<td class="white">
										Checks Number: <?=$today_checks["count(*)"]?>
									</td>
									<td class="pink">
										Checks Money: $<?=number_format($today_checks["SUM(check_fee)"], 2, '.', '')?>
									</td>
								</tr>
<?php
		}
?>
							</tbody>
						</table>
					</div>
<?php
	}
}
else {
	require("./minilogin.php");
}
require("./footer.php");
?>