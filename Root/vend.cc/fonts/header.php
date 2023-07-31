<?php
session_start();
$_SESSION["user_id"] = $user_info["user_id"];
require("./includes/config.inc.php");
require("./includes/user.php");

$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];

$user = new user($db);

//If login attempt else check login session
$checkLogin = $user->valid_login();

//user info var
$user_info = $user->user_info;
?>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml" class=" js csstransforms3d js csstransforms3d js csstransforms3d">
	<head>
		<title>Vend</title>
		<meta content="index, follow" name="robots" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="icon" href="./favicon.ico" type="image/x-icon" />
		<link rel="stylesheet" type="text/css" href="./styles/bootstrap.css" />
		<link rel="stylesheet" type="text/css" href="./styles/font-awesome.min.css" />
		<link rel="stylesheet" type="text/css" href="./styles/entypo.css" />
		<link rel="stylesheet" type="text/css" href="./styles/main.css?time=<?php echo time(); ?>" />
		<link rel="stylesheet" type="text/css" href="./styles/superfish.css" />
		<!--<link rel="stylesheet" type="text/css" href="./styles/kendy.css" />-->
		<script type="text/javascript" src="./js/encrypt.js"></script>
		<script type="text/javascript" src="./js/jquery.1.10.2.min.js"></script>
		<script type="text/javascript" src="./js/jquery.popupWindow.js"></script>
		<script type="text/javascript" src="./js/bootstrap.js"></script>
		<script type="text/javascript" src="./js/main.js?time=<?php echo time(); ?>" ></script>
		<script type="text/javascript" src="./js/superfish.js"></script>
        <link rel="stylesheet" type="text/css" href="./styles/search.css" />
		<script type="text/javascript">
			// initialise plugins
			jQuery(function(){
				//jQuery('ul.sf-menu').superfish();
			});
		</script>

		<div id="livezilla_tracking" style="display:none"></div>


		<link href='./fonts/googlefonts.css' rel='stylesheet' type='text/css'>

	</head>

	<body class="<?php if ($checkLogin) echo 'login-checked'; ?>">
<?php	
if ($checkLogin) {
?>
		<nav class="main-header clearfix" role="navigation">
			<a class="navbar-brand" href="https://vend.cards/"><img alt="" src="images/logo.png" width="200" height="40" /></a>
		  <div class="navbar-content">
				<!--
				<a href="./paygates/btcn.php" class="btn btn-default">
					<span class="label bg-dark-cold-grey">$<?=number_format($user_info["user_balance"], 2, '.', '')?> balance</span>
				</a>
				-->
				<a href="javascript:;" id="close-leftSide" class="btn left-toggler" data-toggle="offcanvas" role="button">
					<i class="fa fa-bars"></i>
				</a>
				<div class="right-toggler pull-right">
					<ul class="nav navbar-nav">
							<li class="dropdown user user-menu">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">
											<i class="glyphicon glyphicon-user"></i>
											<span><?=$user_info["user_name"]?> <i class="caret"></i></span>
									</a>
									<ul class="dropdown-menu">
											<!-- User image -->
											<li class="user-header bg-light-blue">
													<img src="images/avatar.png" class="img-circle" alt="User Image">
													<p>
															<?=$user_info["user_name"]?>
													</p>
											</li>
											<!-- Menu Footer-->
											<li class="user-footer">
													<div class="pull-left">
															<a href="./myaccount.php" class="btn btn-default btn-flat">Profile</a>
													</div>
													<div class="pull-right">
															<a href="./logout.php" class="btn btn-default btn-flat" onclick="return confirm('Are you sure want to log out?');">Sign out</a>
													</div>
													<div class="clear"></div>
											</li>
									</ul>
							</li>
					</ul>
				</div> 	
        <div class="top-cart pull-right"><img src="./images/welcome_basket.png" width="18" height="18"> <a href="cart.php" class="itemview"> <span id="number_shopping_cards"><?=count($_SESSION["shopping_cards"]) + count($_SESSION["shopping_otheraccounts"]) + count($_SESSION["shopping_dumps"])?></span> Items (View Cart)</a></div>
    	</div>
		</nav>
<?php
} else {
?>		
		<div id="header">
				<div class="logo">
						<a href="https://vend.cards"><img alt="" src="images/logo.png" /></a>
				</div>
		</div>
<?php
}
?>		
		<div class="west-body">
			<div id="banner"><!--<img alt="" src="images/coollogo.png" />--></div>
			<div id="wraper" class="row-offcanvas row-offcanvas-left">

<?php

if ($checkLogin) {
	if (!$user_info) $getinfoError = "<span class=\"error\">Get user information error, please try again</span>";

	$count_message = $db->num_rows("SELECT * FROM `".TABLE_MESSAGES."` WHERE message_toid = ? AND message_status = '1'", $user_info["user_id"]);

	// count the number of cards
	$cardsCount = $db->num_rows("SELECT * FROM cards where card_userid = '0'");

	// count the number of otheraccounts
	$otheraccountsCount = $db->num_rows("SELECT * FROM otheraccounts where otheraccount_userid = 0");

	// count the number of dumps
	$dumpsCount = $db->num_rows("SELECT * FROM dumps where dump_userid = 0");
?>
			<div id="menubar" class="left-side sidebar-offcanvas">
				<div class="user-panel">
						<div class="pull-left image">
								<img src="images/avatar.png" class="img-circle" alt="User Image">
						</div>
						<div class="pull-left info">
								<p>Hello, <?=$user_info["user_name"]?></p>
								<a href="./paygates/btcn.php"><i class="fa fa-circle text-success"></i> Balance $<?=number_format($user_info["user_balance"], 2, '.', '')?></a>
						</div>
						<div class="clear"></div>
				</div>
				
				<ul class="sidebar-menu">
					<li class="active"><a href="./"><i class="fa fa-home"></i> Home</a></li>
					<li class="treeview">
						<a href="./ssndob.php"><i class="fa fa-tags"></i> SSN/DOB <i class="fa pull-right fa-angle-left"></i></a>
						<ul class="treeview-menu">
							<li><a href="./ssndob.php"><i class="fa fa-tag"></i> US SSN/DOB</a></li>
							<!--<li><a href="./ukdob.php"><i class="fa fa-globe"></i> UK DOB</a></li>-->
						</ul>
					</li>
					<li class="treeview">
						<a href="./cards.php?category_id=&stagnant="><i class="fa fa-star"></i> Buy Cards <small class="badge bg-red" ><?php echo($cardsCount);?></small> <i class="fa pull-right fa-angle-left"></i></a>
						<ul class="treeview-menu">
							<li>
								<a href="./cards.php?category_id=&stagnant="><i class="fa fa-star"></i> (All Category)</a>
							</li>
<?php
	$sql = "SELECT * FROM `".TABLE_CATEGORYS."` WHERE category_sellerid = '0'";
	$categorys = $db->fetch_array($sql);
	if (is_array($categorys) && count($categorys) > 0) {
		foreach ($categorys as $value) {
?>
							<li>
								<a href="./cards.php?category_id=<?=$value["category_id"]?>&stagnant="><i class="fa fa-star-half-o"></i> <?=$value["category_name"]?></a>
							</li>
<?php
		}
	}
?>
							<li>
								<a href="./cards.php?category_id=0&stagnant="><i class="fa fa-star-o"></i> (No Category)</a>
							</li>
						</ul>
					</li>
					<li><a href="./otheraccounts.php"><i class="fa fa-thumbs-up"></i> Buy Accounts <small class="badge bg-red" ><?php echo($otheraccountsCount);?></small></a></li>
					<li><a href="./dumps.php"><i class="fa fa-leaf"></i> Buy Dumps <small class="badge bg-red" ><?php echo($dumpsCount);?></small></a></li>
					<li class="treeview">
						<a href="./myaccount.php"><i class="fa fa-user"></i> Client <i class="fa pull-right fa-angle-left"></i></a>
						<ul class="treeview-menu">
							<li><a href="./myaccount.php"><i class="fa fa-user"></i> Account Information</a></li>
<?php
	if (strval($_SESSION["user_groupid"]) <= strval(PER_SELLER)) {
?>
							<li><a href="./sellercp"><span class="pink"><i class="fa fa-truck"></i> Seller</span></a></li>
<?php
	}
?>
							<?php if ($user_info["user_groupid"] == PER_UNACTIVATE) { ?><li><a href="./activate.php"><i class="fa fa-toggle-on"></i> <span class="red">Activate Account</span></a></li><?}?>
							<?php if ($user_info["user_vipexpire"] < time() && $user_info["user_groupid"] < PER_UNACTIVATE) { ?><!--<li><a href="./upgrade.php"><i class="fa fa-upload"></i> <span class="red">Upgrade VIP</span></a></li>--><?}?>
							<li><a href="./paygates/btcn.php"><i class="fa fa-money"></i> <span class="red">Deposit Money</span></a></li>
							<li><a href="./mycards.php"><i class="fa fa-calendar-o"></i> Bought Cards</a></li>
							<li><a href="./myotheraccounts.php"><i class="fa fa-users"></i> Bought Accounts</a></li>
							<li><a href="./mydumps.php"><i class="fa fa-square"></i> Bought Dumps</a></li>
							<li><a href="./mymessages.php"><i class="fa fa-envelope"></i> Message <?=($count_message > 0)?"<small class='badge bg-green' >($count_message)</small>":""?></a></li>
							<li class="treeview">
								<a href="./mydeposits.php"><i class="fa fa-sun-o"></i> User's History <i class="fa pull-right fa-angle-left"></i></a>
								<ul class="treeview-menu">
									<li><a href="./myupgrades.php"><i class="fa fa-upload"></i> Upgrades History</a></li>
									<li><a href="./mydeposits.php"><i class="fa fa-tint"></i> Deposits History</a></li>
									<li><a href="./myorders.php"><i class="fa fa-shopping-cart"></i> Orders History</a></li>
									<li><a href="./mychecks.php"><i class="fa fa-check-circle"></i> Check History</a></li>
								</ul>
							</li>
						</ul>
					</li>
					<li>
						<a href="./paygates/btcn.php"><i class="fa fa-money text-success"></i> Deposit</a>
					</li>
					<li><a href="./rules.php"><i class="fa fa-check-square-o"></i> Rules</a></li>
					<li>
						<a href="./support.php"><i class="fa fa-question-circle"></i> Support</a>
					</li>
					<!--<li class="end" style="width:67px"><a href="./logout.php" onclick="return confirm('You want to log out?');">Logout</a></li>-->
					<div class="clear"></div>
				</ul>
<!--
				<div id="shopping_cart" class="notication" style="line-height: 21px;">
					Welcome, <span style="color:#ff9c00"><?=$_SESSION["user_name"]?></span>
          <span style="display: block;">
						<a href="./mymessages.php">Messages <?=($count_message > 0)?"<span class=\"green\">($count_message)</span>":""?></a>
					</span>
<?php
	if ($user_info["user_vipexpire"] > time()) {
?>
					<class class="vip">VIP expire in <?=date("d/M/Y", $user_info['user_vipexpire'])?></class>
<?php
	} else {

	}
?>
					<p><a href="./paygates/btcn.php" class="urbal">Your Balance: <span class="amt">$<?=number_format($user_info["user_balance"], 2, '.', '')?></span></a></p>
						<div id="yourShoppingCart">
							<img src="./images/welcome_basket.png" height="30px"/>
							<a href="cart.php" class="itemview"> <?=count($_SESSION["shopping_cards"]) + count($_SESSION["shopping_otheraccounts"]) + count($_SESSION["shopping_dumps"])?> Items (View Cart)</a>
						</div>
				</div>
-->				
				<div class="clear"></div>
				<div id="getinfoError" class="error">
					<?=$changeInfoResult?>
				</div>
<!--				
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
					<a href="./paygates/btcn.php" class="error">
					You balance is empty, please deposit money to buy cards.</a>
				</div>
<?php
	}
?>
-->
			</div>
<?php
}
?>
			<div id="main" class="wrapper_table right-side">
<?php if ($checkLogin) {	?>		
				<div class="content-header">
				
<?php		
	$page_title = '';
	
	$page_title_ary = array(
			'ssndob' 					=> 'SSN/DOB',
			'ukdob' 					=> 'UK DOB',
			'cards' 					=> 'Buy Cards',
			'otheraccounts' 	=> 'Buy Accounts',
			'dumps' 					=> 'Buy Dumps',
			'myaccount' 			=> 'Account Information',
			'upgrade' 				=> 'Upgrade VIP',
			'deposit' 				=> 'Deposit Money',
			'mycards' 				=> 'Bought Cards',
			'myotheraccounts' => 'Bought Accounts',
			'mydumps' 				=> 'Bought Dumps',
			'mymessages' 			=> 'Message',
			'myupgrades' 			=> 'Upgrades History',
			'mydeposits' 			=> 'Deposits History',
			'myorders' 				=> 'Orders History',
			'mychecks' 				=> 'Check History',
			'rules' 					=> 'Rules',
			'support' 				=> 'Support'
	);
	
	$uri = $_SERVER['REQUEST_URI'];
	$tmp = str_replace("/", "", $uri);
	$tmp_ary = explode('.php', $tmp);

	if (isset($tmp_ary[0])) {	
		if (isset($page_title_ary[$tmp_ary[0]])) {
			$page_title = $page_title_ary[$tmp_ary[0]];
		}
	}
?>				
          <h1><?php echo $uri=='/'?'Home':$page_title?> &nbsp;</h1>
<?php if ($page_title != '') { ?>					
          <ol class="breadcrumb">
						<li><a href="/">Home</a></li>
						<li class="active"><?=$page_title?></li>
					</ol><!-- breadcrumbs -->
<?php } ?>					
        </div>
				
				<div class="main-container">
					<div class="box box-primary">
<?php }?>