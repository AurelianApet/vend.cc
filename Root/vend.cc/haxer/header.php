<?php
session_start();
require_once("../includes/config.inc.php");
require_once("./calendar_class.php");
require_once("../includes/user.php");

$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];
$checkLogin = '';
$user = new user($db);

$grpid = $_SESSION["user_groupid"];

if ($grpid==1) {
$user->user_type = PER_ADMIN;
$checkLogin = $user->valid_login();
$user_info = $user->user_info;

if(checkLogin(PER_ADMIN) == TRUE && $_SERVER['REMOTE_ADDR'] == "213.83.153.160")
	{
		$checkLogin = TRUE;
	}
	else
	{
		$checkLogin = FALSE;
	}
}
//If login attempt else check login session
//$checkLogin = $user->valid_login();
//user info var


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Mahalik CCV </title>
		<meta content="index, follow" name="robots" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" href="../favicon.ico" type="image/x-icon" />
		<link rel="stylesheet" type="text/css" href="../styles/main.css" />
		<link rel="stylesheet" type="text/css" href="../styles/superfish.css" />
		<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery.popupWindow.js"></script>
		<script type="text/javascript" src="../js/main.js" ></script>
		<script type="text/javascript" src="../js/calendar.js" ></script>
		<script type="text/javascript" src="../js/superfish.js"></script>
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
	$count_message = $db->num_rows($sql);
?>
			<div id="menubar">
				<ul class="sf-menu" style="width: 1040px;">
					<li class="current">
						<a href="javascript:void(0);">News</a>
						<ul>
							<li><a href="./?act=add">Publish News</a></li>
							<li><a href="./">News Manager</a></li>
						</ul>
					</li>
					<li class="current">
						<a href="javascript:void(0);">Ads</a>
						<ul>
							<li><a href="./ads.php?act=add">Add New AD</a></li>
							<li><a href="./ads.php">AD Manager</a></li>
						</ul>
					</li>
					<li>
						<a href="javascript:void(0);">Categorys</a>
							<ul>
								<li>
									<li>
										<a href="./categorys.php">Card Categorys</a>
									</li>
									<li>
										<a href="./otheraccountcategorys.php">Account Categorys</a>
									</li>
									<li>
										<a href="./dumpcategorys.php">Dump Categorys</a>
									</li>
								</li>
							</ul>
					</li>
					<li>
						<a href="javascript:void(0);">Cards</a>
							<ul>
								<li>
									<li>
										<a href="./cards.php">Store Cards</a>
										<ul>
											<li><a href="./cards.php">Card Manager</a></li>
											<li><a href="./cards.php?act=import">Card Importer</a></li>
											<li><a href="./cards.php?act=export">Card Exporter</a></li-->
										</ul>
									</li>
									<li>
										<a href="./markets.php">Seller Cards</a>
										<ul>
											<li><a href="./markets.php">Card Manager</a></li>
											<li><a href="./markets.php?act=import">Card Importer</a></li>
											<li><a href="./markets.php?act=export">Card Exporter</a></li-->
										</ul>
									</li>
								</li>
							</ul>
					</li>
					<li>
						<a href="javascript:void(0);">Accounts</a>
							<ul>
								<li>
									<li>
										<a href="./otheraccounts.php">Store Accounts</a>
										<ul>
											<li><a href="./otheraccounts.php">Accounts Manager</a></li>
											<li><a href="./otheraccounts.php?act=import">Accounts Importer</a></li>
											<!--li><a href="./otheraccounts.php?act=export">Card Exporter</a></li-->
										</ul>
									</li>
									<li>
										<a href="./otheraccountmarkets.php">Seller Accounts</a>
										<ul>
											<li><a href="./otheraccountmarkets.php">Card Manager</a></li>
											<li><a href="./otheraccountmarkets.php?act=import">Card Importer</a></li>
											<!--li><a href="./otheraccountmarkets.php?act=export">Card Exporter</a></li-->
										</ul>
									</li>
								</li>
							</ul>
					</li>
					<li>
						<a href="javascript:void(0);">Dumps</a>
							<ul>
								<li><a href="./dumps.php">Dumps Manager</a></li>
								<li><a href="./dumps.php?act=import">Dumps Importer</a></li>
								<!--li><a href="./otheraccounts.php?act=export">Card Exporter</a></li-->
							</ul>
					</li>
					<li>
						<a href="javascript:void(0);">Users</a>
						<ul>
							<li><a href="./users.php?act=add">Add New User</a></li>
							<li><a href="./users.php">User Manager</a></li>
							<li><a href="./users.php?act=mail">Email All Users</a></li>
							<li><a href="./vouchers.php">Vouchers</a></li>
							<li><a href="./groups.php">Group Manager</a></li>
						</ul>
					</li>
					<li>
						<a href="./statistics.php">History</a>
						<ul>
							<li><a href="./statistics.php">Statistics</a></li>
							<li><a href="./statistics.php?type=seller">Seller Statistic</a></li>
							<li><a href="./upgrades.php">Upgrades History</a></li>
							<li><a href="./deposits.php">Deposit History</a></li>
							<li><a href="./orders.php">Order History</a></li>
<?php
	if (ENABLE_CHECKER == true) {
?>
							<li><a href="./checks.php">Check History</a></li>
<?php
	}
?>
						</ul>
					</li>
					<li>
						<a href="javascript:void(0);">Settings</a>
						<ul>
							<li><a href="./configs.php">Configs</a></li>
							<li><a href="./bonus.php">Bonus</a></li>
						</ul>
					</li>
					<li><a href="./tools.php">Tools</a></li>
					<li>
						<a href="./messages.php">Msg <?=($count_message > 0)?"<span class=\"red\">[$count_message]</span>":""?></a>
						<ul>
							<a href="./messages.php">My Message <?=($count_message > 0)?"<span class=\"red\">[$count_message]</span>":""?></a>
                            <li><a href="./messages.php?act=compose">Send Message</a></li>
						</ul>
					</li>
					<li class="end"><a href="./logout.php" onclick="return confirm('You want to log out?');">Logout</a></li>
					<div class="clear"></div>
				</ul>
				<div class="clear"></div>
			</div>
<?php
}
?>
			<div id="main">