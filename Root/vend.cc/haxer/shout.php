<?php
session_start();
echo $_SERVER['REMOTE_ADDR'] ;
require("../includes/config.inc.php");
require("./calendar_class.php");
$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];
if (isset($_POST["txtUser"]) && isset($_POST["txtPass"]) && isset($_POST["btnLogin"]))
{
	if($_SESSION['security_code'] == $_POST['security_code'] && !empty($_SESSION['security_code'])) {
		$remember = isset($_POST["remember"]);
		$loginError = confirmUser($_POST["txtUser"], $_POST["txtPass"], PER_ADMIN, $remember);
		unset($_SESSION['security_code']);
	} else {
		$loginError = -1;
	}
	$checkLogin = ($loginError == 0);
}
else
{
	$checkLogin = checkLogin(PER_ADMIN);
	if(checkLogin(PER_ADMIN) == TRUE && $_SERVER['REMOTE_ADDR'] == "80.90.48.26" )
	{
		$checkLogin = TRUE;
	}
	else
	{
		$checkLogin = FALSE;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html class="cufon-active cufon-ready" xmlns="http://www.w3.org/1999/xhtml"><head>
	<title><?=$db_config["name_service"]?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="icon" href="../favicon.png" type="image/gif" />
		<link rel="stylesheet" type="text/css" href="../styles/style.css" media="screen">
		<link rel="stylesheet" type="text/css" href="../styles/main.css" />
		<link rel="stylesheet" type="text/css" href="../styles/superfish.css" />
		<link rel="stylesheet" href="../styles/screen.css" type="text/css" media="screen" title="default">
		<link rel="stylesheet" media="all" type="text/css" href="../styles/login.css">

<!--[if IE]>
<link rel="stylesheet" media="all" type="text/css" href="css/pro_dropline_ie.css?mt=1189152740" />
<![endif]-->
<!--  jquery core -->
<script src="../styles/jquery-1.js" type="text/javascript"></script>
<script src="../styles/loginbox.js" type="text/javascript"></script>
<script src="../styles/jquery.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
$(document).pngFix( );
});
</script>
<!-- Tooltips -->
<script src="../styles/jquery_004.js" type="text/javascript"></script>
<script src="../styles/jquery.simpletip.js" type="text/javascript"></script>
<script src="../styles/jquery_002.js" type="text/javascript"></script>
<script src="../styles/clients.js" type="text/javascript"></script><script type="text/javascript">
$(function() {
	$('a.info-tooltip ').tooltip({
		track: true,
		delay: 0,
		fixpng: true,
		showURL: false,
		showBody: " - ",
		top: -35,
		left: 5
	});
});
</script>
<!-- MUST BE THE LAST SCRIPT IN <HEAD></HEAD></HEAD> png fix --><script src="../styles/jquery.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
$(document).pngFix( );
});
</script>

		<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery.popupWindow.js"></script>
		<script type="text/javascript" src="../js/main.js" ></script>
	<script type="text/javascript" src="../styles/jquery_004.js"></script>
	<script type="text/javascript" src="../styles/jquery_005.js"></script>
	<script type="text/javascript" src="../styles/cluster.js"></script>
	<script type="text/javascript" src="../styles/superfish.js"></script>
	<script type="text/javascript" src="../styles/cufon-yui.js"></script>
	<script type="text/javascript" src="../styles/Titillium.js"></script>
	<script type="text/javascript" src="../styles/jquery.js"></script>
	<script type="text/javascript">
			Cufon.replace('#header a,#footer h3', {	textShadow: '#000 2px 1px', hover: true	});
			Cufon.replace('h6,h5,h4,h3,h2,h1');
	</script>
	</head>
<?if ($checkLogin) {?>
<body onLoad="init()">
  <div id="loading" class="loading">
    <span><img alt="Loader" height="22" src="../images/loader.gif" width="126" />Request Processing...</span>
  </div>
<script>
var ld=(document.all);

var ns4=document.layers;
var ns6=document.getElementById&&!document.all;
var ie4=document.all;

if (ns4)
	ld=document.loading;
else if (ns6)
	ld=document.getElementById("loading").style;
else if (ie4)
	ld=document.all.loading.style;

function init()
{
if(ns4){ld.visibility="hidden";}
else if (ns6||ie4) ld.display="none";
}
</script>
<body>



<!-- Start: header -->
<div class="header">

  <div class="w960 center">
    	<div class="logo floatleft"><a href="./index.php"><img src="../images/login/logo.png" alt="" /></a></div>
        <div class="floatright">

        </div>
        <div class="clear"></div>
    </div>
</div>
<!-- End : header -->



        
        
        
        

<!--  start nav-outer -->
<div class="nav">
				<ul class="menu" style="width: 960px;">
					<li><a href="../" class="home_icon">Home</a></li>
					<li class="current">
						<a href="javascript:void(0);" class="news_icon">News/Ads</a>
						<ul>
							<li>
								<a href="./">News</a>
								<ul>
									<li><a href="./">News Manager</a></li>
									<li><a href="./?act=add">Publish News</a></li>
								</ul>
							</li>
							<li>
								<a href="./ads.php">Ads</a>
								<ul>
									<li><a href="./ads.php">AD Manager</a></li>
									<li><a href="./ads.php?act=add">Add New AD</a></li>
								</ul>
							</li>
						</ul>
					</li>
					<li>
						<a href="./categorys.php" class="ca_icon">Categorys</a>
					</li>
					<li><a href="javascript:void(0);" class="service_icon">Service</a>
					<ul>
							<li>
								<a href="javascript:void(0);">Cards</a>
									<ul>
										
											<li>
												<a href="./cards.php">Store Cards</a>
												<ul>
													<li><a href="./cards.php">Card Manager</a></li>
													<li><a href="./cards.php?act=import">Card Importer</a></li>
													<li><a href="./cards.php?act=export">Card Exporter</a></li>
												</ul>
											</li>
											<li>
												<a href="./markets.php">Seller Cards</a>
												<ul>
													<li><a href="./markets.php">Card Manager</a></li>
													<li><a href="./markets.php?act=import">Card Importer</a></li>
													<!--li><a href="./markets.php?act=export">Card Exporter</a></li> -->
												</ul>
											</li>
									
									</ul>
								</li>
								<li><a href="./paypal.php">Paypal</a></li>
								<li><a href="./others.php">Account</a></li>
							</ul>
					</li>
					<li>
						<a href="./users.php" class="user_icon">Users</a>
						<ul>
							<li><a href="./users.php">User Manager</a></li>
							<li><a href="./users.php?act=add">Add New User</a></li>
							<li><a href="./users.php?act=mail">Email All Users</a></li>
							<li><a href="./groups.php">Group Manager</a></li>
						</ul>
					</li>
					<li>
						<a href="./statistics.php" class="his_icon">Selling History</a>
						<ul>
							<li><a href="./statistics.php">Store Statistics</a></li>
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
						<a href="./configs.php" class="setting_icon">Settings</a>
						<ul>
							<li><a href="./configs.php">Configs</a></li>
							<li><a href="./bonus.php">Bonus</a></li>
							<li><a href="./tools.php">Tools</a></li>
						</ul>
					</li>
					<li class="end"><a href="./logout.php" onClick="return confirm('You want to log out?');" class="log_icon">Logout</a></li>
					<div class="clear"></div>
				</ul>
				<div class="clear"></div>
		<!--  start nav -->

</div>
<div class="clear"></div>
<!--  start nav-outer -->
</div>
<!--  start nav-outer-no-repeat................................................... END -->

 <div class="clear"></div>
 
 
</div>

<!--  start nav-outer-no-repeat................................................... END -->
 <div class="clear"></div>
 
<div id="content-outer">
<!-- start content -->
<div id="content">
	<div id="wraper" align="center"><br>
		<?} else {?>
	<body id="login-bg"  onLoad="init()">
		<?}?><?php
session_start();
require("../includes/config.inc.php");
require("./calendar_class.php");
$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];
if (isset($_POST["txtUser"]) && isset($_POST["txtPass"]) && isset($_POST["btnLogin"]))
{
	if($_SESSION['security_code'] == $_POST['security_code'] && !empty($_SESSION['security_code'])) {
		$remember = isset($_POST["remember"]);
		$loginError = confirmUser($_POST["txtUser"], $_POST["txtPass"], PER_ADMIN, $remember);
		unset($_SESSION['security_code']);
	} else {
		$loginError = -1;
	}
	$checkLogin = ($loginError == 0);
}
else
{
	$checkLogin = checkLogin(PER_ADMIN);
	if(checkLogin(PER_ADMIN) == TRUE && $_SERVER['REMOTE_ADDR'] == "80.90.48.26")
	{
		$checkLogin = TRUE;
	}
	else
	{
		$checkLogin = FALSE;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html class="cufon-active cufon-ready" xmlns="http://www.w3.org/1999/xhtml"><head>
	<title><?=$db_config["name_service"]?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="icon" href="../favicon.png" type="image/gif" />
		<link rel="stylesheet" type="text/css" href="../styles/style.css" media="screen">
		<link rel="stylesheet" type="text/css" href="../styles/main.css" />
		<link rel="stylesheet" type="text/css" href="../styles/superfish.css" />
		<link rel="stylesheet" href="../styles/screen.css" type="text/css" media="screen" title="default">
		<link rel="stylesheet" media="all" type="text/css" href="../styles/login.css">

<!--[if IE]>
<link rel="stylesheet" media="all" type="text/css" href="css/pro_dropline_ie.css?mt=1189152740" />
<![endif]-->
<!--  jquery core -->
<script src="../styles/jquery-1.js" type="text/javascript"></script>
<script src="../styles/loginbox.js" type="text/javascript"></script>
<script src="../styles/jquery.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
$(document).pngFix( );
});
</script>
<!-- Tooltips -->
<script src="../styles/jquery_004.js" type="text/javascript"></script>
<script src="../styles/jquery.simpletip.js" type="text/javascript"></script>
<script src="../styles/jquery_002.js" type="text/javascript"></script>
<script src="../styles/clients.js" type="text/javascript"></script><script type="text/javascript">
$(function() {
	$('a.info-tooltip ').tooltip({
		track: true,
		delay: 0,
		fixpng: true,
		showURL: false,
		showBody: " - ",
		top: -35,
		left: 5
	});
});
</script>
<!-- MUST BE THE LAST SCRIPT IN <HEAD></HEAD></HEAD> png fix --><script src="../styles/jquery.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
$(document).pngFix( );
});
</script>

		<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery.popupWindow.js"></script>
		<script type="text/javascript" src="../js/main.js" ></script>
	<script type="text/javascript" src="../styles/jquery_004.js"></script>
	<script type="text/javascript" src="../styles/jquery_005.js"></script>
	<script type="text/javascript" src="../styles/cluster.js"></script>
	<script type="text/javascript" src="../styles/superfish.js"></script>
	<script type="text/javascript" src="../styles/cufon-yui.js"></script>
	<script type="text/javascript" src="../styles/Titillium.js"></script>
	<script type="text/javascript" src="../styles/jquery.js"></script>
	<script type="text/javascript">
			Cufon.replace('#header a,#footer h3', {	textShadow: '#000 2px 1px', hover: true	});
			Cufon.replace('h6,h5,h4,h3,h2,h1');
	</script>
	</head>
<?if ($checkLogin) {?>
<body onLoad="init()">
  <div id="loading" class="loading">
    <span><img alt="Loader" height="22" src="../images/loader.gif" width="126" />Request Processing...</span>
  </div>
<script>
var ld=(document.all);

var ns4=document.layers;
var ns6=document.getElementById&&!document.all;
var ie4=document.all;

if (ns4)
	ld=document.loading;
else if (ns6)
	ld=document.getElementById("loading").style;
else if (ie4)
	ld=document.all.loading.style;

function init()
{
if(ns4){ld.visibility="hidden";}
else if (ns6||ie4) ld.display="none";
}
</script>
<body>



<!-- Start: header -->
<div class="header">

  <div class="w960 center">
    	<div class="logo floatleft"><a href="./index.php"><img src="../images/login/logo.png" alt="" /></a></div>
        <div class="floatright">

        </div>
        <div class="clear"></div>
    </div>
</div>
<!-- End : header -->



        
        
        
        

<!--  start nav-outer -->
<div class="nav">
				<ul class="menu" style="width: 960px;">
					<li><a href="../" class="home_icon">Home</a></li>
					<li class="current">
						<a href="javascript:void(0);" class="news_icon">News/Ads</a>
						<ul>
							<li>
								<a href="./">News</a>
								<ul>
									<li><a href="./">News Manager</a></li>
									<li><a href="./?act=add">Publish News</a></li>
								</ul>
							</li>
							<li>
								<a href="./ads.php">Ads</a>
								<ul>
									<li><a href="./ads.php">AD Manager</a></li>
									<li><a href="./ads.php?act=add">Add New AD</a></li>
								</ul>
							</li>
						</ul>
					</li>
					<li>
						<a href="./categorys.php" class="ca_icon">Categorys</a>
					</li>
					<li><a href="javascript:void(0);" class="service_icon">Service</a>
					<ul>
							<li>
								<a href="javascript:void(0);">Cards</a>
									<ul>
										
											<li>
												<a href="./cards.php">Store Cards</a>
												<ul>
													<li><a href="./cards.php">Card Manager</a></li>
													<li><a href="./cards.php?act=import">Card Importer</a></li>
													<li><a href="./cards.php?act=export">Card Exporter</a></li>
												</ul>
											</li>
											<li>
												<a href="./markets.php">Seller Cards</a>
												<ul>
													<li><a href="./markets.php">Card Manager</a></li>
													<li><a href="./markets.php?act=import">Card Importer</a></li>
													<!--li><a href="./markets.php?act=export">Card Exporter</a></li> -->
												</ul>
											</li>
									
									</ul>
								</li>
								<li><a href="./paypal.php">Paypal</a></li>
								<li><a href="./others.php">Account</a></li>
							</ul>
					</li>
					<li>
						<a href="./users.php" class="user_icon">Users</a>
						<ul>
							<li><a href="./users.php">User Manager</a></li>
							<li><a href="./users.php?act=add">Add New User</a></li>
							<li><a href="./users.php?act=mail">Email All Users</a></li>
							<li><a href="./groups.php">Group Manager</a></li>
						</ul>
					</li>
					<li>
						<a href="./statistics.php" class="his_icon">Selling History</a>
						<ul>
							<li><a href="./statistics.php">Store Statistics</a></li>
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
						<a href="./configs.php" class="setting_icon">Settings</a>
						<ul>
							<li><a href="./configs.php">Configs</a></li>
							<li><a href="./bonus.php">Bonus</a></li>
							<li><a href="./tools.php">Tools</a></li>
						</ul>
					</li>
					<li class="end"><a href="./logout.php" onClick="return confirm('You want to log out?');" class="log_icon">Logout</a></li>
					<div class="clear"></div>
				</ul>
				<div class="clear"></div>
		<!--  start nav -->

</div>
<div class="clear"></div>
<!--  start nav-outer -->
</div>
<!--  start nav-outer-no-repeat................................................... END -->

 <div class="clear"></div>
 
 
</div>

<!--  start nav-outer-no-repeat................................................... END -->
 <div class="clear"></div>
 
<div id="content-outer">
<!-- start content -->
<div id="content">
	<div id="wraper" align="center"><br>
		<?} else {?>
	<body id="login-bg"  onLoad="init()">
		<?}?>