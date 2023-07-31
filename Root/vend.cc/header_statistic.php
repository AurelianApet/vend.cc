<?php
exit;
session_start();
require("./includes/config.inc.php");
$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];
if (isset($_POST["txtUser"]) && isset($_POST["txtPass"]) && isset($_POST["btnLogin"]))
{
	if($_SESSION['security_code'] == $_POST['security_code'] && !empty($_SESSION['security_code'])) {
		$remember = isset($_POST["remember"]);
		$loginError = confirmUser($_POST["txtUser"], $_POST["txtPass"], PER_UNACTIVATE, $remember);
		unset($_SESSION['security_code']);
	} else {
		$loginError = -1;
	}
	$checkLogin = ($loginError == 0);
}
else
{
	$checkLogin = checkLogin(PER_UNACTIVATE);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Acura - CCStore v2</title>
		<meta content="index, follow" name="robots" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="icon" href="./favicon.ico" type="image/x-icon" />
		<link rel="stylesheet" type="text/css" href="./styles/bootstrap.css" />
		<link rel="stylesheet" type="text/css" href="./styles/main.css" />
		<link rel="stylesheet" type="text/css" href="./styles/superfish.css" />
		<script type="text/javascript" src="./js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="./js/jquery.popupWindow.js"></script>
		<script type="text/javascript" src="./js/bootstrap.js"></script>
		<script type="text/javascript" src="./js/main.js" ></script>
		<script type="text/javascript" src="./js/superfish.js"></script>
		<script type="text/javascript">

		// initialise plugins
		jQuery(function(){
			jQuery('ul.sf-menu').superfish();
		});

		</script>
	</head>

	<body>
		<div id="banner"></div>
		<div id="wraper">
<?php
if ($checkLogin) {
	$sql = "SELECT * FROM `".TABLE_USERS."` WHERE user_id = '".$db->escape($_SESSION["user_id"])."'";
	$user_info = $db->query_first($sql);
	if (!$user_info) {
		$getinfoError = "<span class=\"error\">Get user information error, please try again</span>";
	}
	$sql = "SELECT COUNT(*) FROM `".TABLE_MESSAGES."` WHERE message_toid = '".$db->escape($_SESSION["user_id"])."' AND message_status = '1'";
	$count_message = $db->query_first($sql);
	if ($count_message) {
		$count_message = $count_message["COUNT(*)"];
	} else {
		$count_message = 0;
	}
?>
			<div id="menubar">
				<ul class="sf-menu" style="width:950px;">
					<li class="current"><a href="./">Home</a></li>
					<li>
						<a href="./cards.php?category_id=&stagnant=">Buy Cards</a>
						<ul>
							<li>
								<a href="./cards.php?category_id=&stagnant=">(All Category)</a>
								<ul>
									<li><a href="./cards.php?category_id=&stagnant=false">Expire > <?=date("m/Y")?></a></li>
									<li><a href="./cards.php?category_id=&stagnant=true"><span class="pink">Expire <?=date("m/Y")?></span></a></li>
								</ul>
							</li>
<?php
	$sql = "SELECT * FROM `".TABLE_CATEGORYS."` WHERE category_sellerid = '0'";
	$categorys = $db->fetch_array($sql);
	if (is_array($categorys) && count($categorys) > 0) {
		foreach ($categorys as $value) {
?>
							<li>
								<a href="./cards.php?category_id=<?=$value["category_id"]?>&stagnant="><?=$value["category_name"]?></a>
								<ul>
									<li><a href="./cards.php?category_id=<?=$value["category_id"]?>&stagnant=false">Expire > <?=date("m/Y")?></a></li>
									<li><a href="./cards.php?category_id=<?=$value["category_id"]?>&stagnant=true"><span class="pink">Expire <?=date("m/Y")?></span></a></li>
								</ul>
							</li>
<?php
		}
	}
?>
							<li>
								<a href="./cards.php?category_id=0&stagnant=">(No Category)</a>
								<ul>
									<li><a href="./cards.php?category_id=0&stagnant=false">Expire > <?=date("m/Y")?></a></li>
									<li><a href="./cards.php?category_id=0&stagnant=true"><span class="pink">Expire <?=date("m/Y")?></span></a></li>
								</ul>
							</li>
						</ul>
					</li>
					<li><a href="./otheraccounts.php">Buy Accounts</a></li>
					<li><a href="./dumps.php">Buy Dumps</a></li>
					<li>
						<a href="./myaccount.php">User CP</a>
						<ul>
							<li><a href="./myaccount.php">Account Information</a></li>
<?php
	if (strval($_SESSION["user_groupid"]) <= strval(PER_SELLER)) {
?>
							<li><a href="./sellercp"><span class="pink">Seller CP</span></a></li>
<?php
	}
?>
							<? if ($user_info["user_groupid"] == PER_UNACTIVATE) { ?><li><a href="./activate.php"><span class="red">Activate Account</span></a></li><?}?>
							<? if ($user_info["user_vipexpire"] < time() && $user_info["user_groupid"] < PER_UNACTIVATE) { ?><li><a href="./upgrade.php"><span class="red">Upgrade VIP</span></a></li><?}?>
							<li><a href="./deposit.php"><span class="red">Deposit Money</span></a></li>
							<li><a href="./mycards.php">Bought Cards</a></li>
							<li><a href="./myotheraccounts.php">Bought Accounts</a></li>
							<li><a href="./mydumps.php">Bought Dumps</a></li>
							<li><a href="./mymessages.php">Message <?=($count_message > 0)?"<span class=\"green\">($count_message)</span>":""?></a></li>
							<li>
								<a href="./mydeposits.php">User's History</a>
								<ul>
									<li><a href="./myupgrades.php">Upgrades History</a></li>
									<li><a href="./mydeposits.php">Deposits History</a></li>
									<li><a href="./myorders.php">Orders History</a></li>
									<li><a href="./mychecks.php">Check History</a></li>
								</ul>
							</li>
						</ul>
					</li>
					<li>
						<a href="javascript:void(0);">Tools</a>
						<ul>
<?php
	$tool_sql = "SELECT * FROM `".TABLE_TOOLS."` ORDER BY tool_id";
	$tools = $db->fetch_array($tool_sql);
	if (is_array($tools) && count($tools) > 0) {
		foreach ($tools as $tool) {
?>
							<li><a href="<?=$tool["tool_url"]?>" target="_new"><?=$tool["tool_name"]?></a></li>
<?php
		}
	}
?>
						</ul>
					</li>
					<li><a href="./rules.php">Rules</a></li>
					<li>
						<a href="./support.php">Support</a>
					</li>
					<li class="end"><a href="./logout.php" onclick="return confirm('You want to log out?');">Logout</a></li>
					<div class="clear"></div>
				</ul>
				<div id="shopping_cart" class="notication">
					<p class="bold">Welcome, <span style="color:<?=$user_groups[$_SESSION["user_groupid"]]["group_color"]?>;"><?=$_SESSION["user_name"]?></span> | <a href="./mymessages.php">Messages <?=($count_message > 0)?"<span class=\"green\">($count_message)</span>":""?></a></p>
					<p>
<?php
	if ($user_info["user_vipexpire"] > time()) {
?>
					<class class="bold pink">VIP expire in <?=date("d/M/Y", $user_info['user_vipexpire'])?></class>
<?php
	} else {
?>
					<class class="bold red">VIP expired</class>
<?php
	}
?>
					<a href="./upgrade.php">(Renew)</a>
					</p>
					<p><a href="deposit.php" class="bold">Your Balance: $<?=number_format($user_info["user_balance"], 2, '.', '')?></a></p>
					<p>
						<div id="yourShoppingCart">
							<img src="./images/Shopping-Cart-Symbol-48.png" height="30px"/>
					<a href="cart.php" class="bold"> <?=count($_SESSION["shopping_cards"]) + count($_SESSION["shopping_otheraccounts"]) + count($_SESSION["shopping_dumps"])?> Items (View Cart)</a>
						</div>
					</p>
				</div>
				
<?php
		$sql = "SELECT count(*), SUM(upgrade_price) FROM `".TABLE_UPGRADES."` WHERE (FROM_UNIXTIME(".TABLE_UPGRADES.".upgrade_time, '%d') = '".date("d")."') AND (FROM_UNIXTIME(".TABLE_UPGRADES.".upgrade_time, '%m') = '".date("m")."') AND (FROM_UNIXTIME(".TABLE_UPGRADES.".upgrade_time, '%Y') = '".date("Y")."')";
		$today_upgrades = $db->query_first($sql);
		$sql = "SELECT count(*), SUM(deposit_amount) FROM `".TABLE_DEPOSITS."` WHERE (FROM_UNIXTIME(".TABLE_DEPOSITS.".deposit_time, '%d') = '".date("d")."') AND (FROM_UNIXTIME(".TABLE_DEPOSITS.".deposit_time, '%m') = '".date("m")."') AND (FROM_UNIXTIME(".TABLE_DEPOSITS.".deposit_time, '%Y') = '".date("Y")."')";
		$today_deposits = $db->query_first($sql);
		$sql = "SELECT count(*), SUM(order_total) FROM `".TABLE_ORDERS."` WHERE (FROM_UNIXTIME(".TABLE_ORDERS.".order_time, '%d') = '".date("d")."') AND (FROM_UNIXTIME(".TABLE_ORDERS.".order_time, '%m') = '".date("m")."') AND (FROM_UNIXTIME(".TABLE_ORDERS.".order_time, '%Y') = '".date("Y")."')";
		$today_orders = $db->query_first($sql);
		$sql = "SELECT count(*), SUM(check_fee) FROM `".TABLE_CHECKS."` WHERE (FROM_UNIXTIME(".TABLE_CHECKS.".check_time, '%d') = '".date("d")."') AND (FROM_UNIXTIME(".TABLE_CHECKS.".check_time, '%m') = '".date("m")."') AND (FROM_UNIXTIME(".TABLE_CHECKS.".check_time, '%Y') = '".date("Y")."')";
		$today_checks = $db->query_first($sql);
?>
				<div class="section_title">TODAY STATISTICS</div>
				<div id="admin_static">
					<table class="borderstyle" style="width:400px; margin: 0 auto;">
						<tbody class="left bold">
							<tr>
								<td class="white">
									Upgrades Number: <?=$today_upgrades["count(*)"]?>
								</td>
								<td class="white">
									Upgrades Money: $<?=number_format($today_upgrades["SUM(upgrade_price)"], 2, '.', '')?>
								</td>
							</tr>
							<tr>
								<td class="white">
									Deposits Number: <?=$today_deposits["count(*)"]?>
								</td>
								<td class="white">
									Deposits Money: $<?=number_format($today_deposits["SUM(deposit_amount)"], 2, '.', '')?>
								</td>
							</tr>
							<tr>
								<td class="white">
									Orders Number: <?=$today_orders["count(*)"]?>
								</td>
								<td class="white">
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
								<td class="white">
									Checks Money: $<?=number_format($today_checks["SUM(check_fee)"], 2, '.', '')?>
								</td>
							</tr>
<?php
		}
?>
						</tbody>
					</table>
				</div>
				<div class="clear"></div>
				<div id="getinfoError" class="error">
					<?=$changeInfoResult?>
				</div>
<?php
	if ($user_info["user_groupid"] == PER_UNACTIVATE) {
?>
				<div id="balance_notify">
					<a href="./activate.php" class="error">You account currently is hasn't activated yet, click here to activate your account.</a>
				</div>
<?php
	}
	else if ($checkLogin && $_SESSION["user_groupid"] == intval(PER_UNCONFIRM)){
?>
				<div id="balance_notify">
					<a href="./confirm.php" class="error">You account currently is hasn't confirmed yet, click here to confirm your email address.</a>
				</div>
<?php
	}
?>
<?php
	if ($user_info["user_balance"] <= 0) {
?>
				<div id="balance_notify">
					<a href="./deposit.php" class="error">
					You balance is empty, please deposit money to buy cards.</a>
				</div>
<?php
	}
?>
			</div>
<?php
}
?>
			<div id="main" class="wrapper_table">