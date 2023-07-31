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
        <link rel="stylesheet" type="text/css" href="./styles/search.css?v=1" />
<style type="text/css">
    ul{
				padding:0;
        list-style: none;
    }
    ul li{
        display: inline-block;
        position: relative;
    }
    ul li a{
        display: block;
        text-decoration: none;
    }
    ul li a:hover{
    }
    ul li ul.treeview-menu{
        display: none;
        position: absolute;
        z-index: 5;
        left: 0;
    }
    ul li:hover ul.treeview-menu{
        display: block;	/* Display the treeview-menu */
    }
    ul li ul.treeview-menu li{
        display: block;
    }
</style>
		<script type="text/javascript">
			// initialise plugins
			jQuery(function(){

				var colors = new Array(
				  [62,35,255],
				  [60,255,60],
				  [255,35,98],
				  [45,175,230],
				  [255,0,255],
				  [255,128,0]);

				var step = 0;
				//color table indices for: 
				// current color left
				// next color left
				// current color right
				// next color right
				var colorIndices = [0,1,2,3];

				//transition speed
				var gradientSpeed = 0.002;

				function updateGradient()
				{
				  
				  if ( $===undefined ) return;
				  
				var c0_0 = colors[colorIndices[0]];
				var c0_1 = colors[colorIndices[1]];
				var c1_0 = colors[colorIndices[2]];
				var c1_1 = colors[colorIndices[3]];

				var istep = 1 - step;
				var r1 = Math.round(istep * c0_0[0] + step * c0_1[0]);
				var g1 = Math.round(istep * c0_0[1] + step * c0_1[1]);
				var b1 = Math.round(istep * c0_0[2] + step * c0_1[2]);
				var color1 = "rgb("+r1+","+g1+","+b1+")";

				var r2 = Math.round(istep * c1_0[0] + step * c1_1[0]);
				var g2 = Math.round(istep * c1_0[1] + step * c1_1[1]);
				var b2 = Math.round(istep * c1_0[2] + step * c1_1[2]);
				var color2 = "rgb("+r2+","+g2+","+b2+")";

				 $('#gradient').css({
				   background: "-webkit-gradient(linear, left top, right top, from("+color1+"), to("+color2+"))"}).css({
				    background: "-moz-linear-gradient(left, "+color1+" 0%, "+color2+" 100%)"});
				  
				  step += gradientSpeed;
				  if ( step >= 1 )
				  {
				    step %= 1;
				    colorIndices[0] = colorIndices[1];
				    colorIndices[2] = colorIndices[3];
				    
				    //pick two new target color indices
				    //do not pick the same as the current one
				    colorIndices[1] = ( colorIndices[1] + Math.floor( 1 + Math.random() * (colors.length - 1))) % colors.length;
				    colorIndices[3] = ( colorIndices[3] + Math.floor( 1 + Math.random() * (colors.length - 1))) % colors.length;
				    
				  }
				}

				setInterval(updateGradient,10);
			});
		</script>

		<div id="livezilla_tracking" style="display:none"></div>


		<link href='./fonts/googlefonts.css' rel='stylesheet' type='text/css'>

	</head>

	<body class="<?php if ($checkLogin) echo 'login-checked'; else echo 'login' ?>">
		<?php if (!$checkLogin) echo '<div id="gradient"></div>'; ?>


<?php	
if ($checkLogin) {
?>
		<nav class="main-header clearfix" role="navigation">
			<!--<a class="navbar-brand" href="https://vend.cc/"><img alt="" src="images/logo.png" width="200" height="40" /></a>-->
		  <div class="navbar-content">

<?php		  	if ($checkLogin) {
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
					<li><a href="./"><i class="fa fa-home"></i> Home</a></li>
					<li class="treeview">
						<a href="./ssndob.php"><i class="fa fa-tags"></i> Buy <i class="fa pull-right fa-angle-left"></i></a>
							<ul class="treeview-menu">
								<li><a href="./cards.php?category_id=&stagnant="><i class="fa fa-star"></i> Cards <small class="badge bg-red" ><?php echo($cardsCount);?></small> <i class="fa pull-right fa-angle-left"></i></a></li>
								<li><a href="./dumps.php"><i class="fa fa-leaf"></i> Dumps <small class="badge bg-red" ><?php echo($dumpsCount);?></small></a></li>
								<li><a href="./otheraccounts.php"><i class="fa fa-thumbs-up"></i> Accounts <small class="badge bg-red" ><?php echo($otheraccountsCount);?></small></a></li>
								<li><a href="./ssndob.php"><i class="fa fa-tag"></i> SSN/DOB</a></li>
							</ul>
					</li>
					
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
							<?php if ($user_info["user_groupid"] == PER_UNACTIVATE) { ?><li><a href="./activate.php"><i class="fa fa-toggle-on"></i> <span class="red">Activate Account</span></a></li><?php } ?>
							<?php if ($user_info["user_vipexpire"] < time() && $user_info["user_groupid"] < PER_UNACTIVATE) { ?><!--<li><a href="./upgrade.php"><i class="fa fa-upload"></i> <span class="red">Upgrade VIP</span></a></li>--><?php } ?>
							<li><a href="./paygates/btcn.php"><i class="fa fa-money"></i> Deposit Money</a></li>
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
						<a href="./paygates/btcn.php"><i class="fa fa-money"></i> Deposit</a>
					</li>
					<li><a href="./rules.php"><i class="fa fa-check-square-o"></i> Rules</a></li>
					<li>
						<a href="./support.php"><i class="fa fa-question-circle"></i> Support</a>
					</li>
					
					<div class="clear"></div>
				</ul>

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
        <div class="top-cart pull-right">
        <?php
		    $total = 0;
		    $shoppingCards = $_SESSION["shopping_card_items"];
		    $shoppingOther = $_SESSION["shopping_otheraccount_items"];
		    $shoppingDumps = $_SESSION["shopping_dump_items"];
		    
		    foreach ($shoppingCards as $key=>$value){
		        $total += $value['cardPrice'] + $value['binPrice'] + $value['countryPrice'] + $value['statePrice'] + $value['cityPrice'] + $value['zipPrice'];
		    }
		    
		    foreach ($shoppingOther as $key=>$value){
		        $total += $value['otherPrice'] + $value['additionPrice'];
		    }
		    
		    foreach ($shoppingDumps as $key=>$value){
		        $total += $value['dumpPrice'] + $value['binPrice'];
		    }
		    
		    //var_dump($_SESSION["shopping_card_items"]);
		?>
            <img src="./images/welcome_basket.png" width="18" height="18">
            <a href="cart.php" class="itemview" style="color:#F9F9F9 !important">
                <span id="number_shopping_cards">
                <?=count($_SESSION["shopping_cards"]) + count($_SESSION["shopping_otheraccounts"]) + count($_SESSION["shopping_dumps"])?>
                </span> 
                Items |
                $<span id="price_shopping_cards"><?=number_format($total, 2);?></span>
								
                (View Cart)</a>
        </div>

			</div>
<?php
}
?>
				
				
    	</div>
		</nav>
<?php
} else {
?>		
		<div id="header" style="height:auto;">
				<div class="logo">
						<a href="https://vend.cc"><img alt="" src="images/logo_login.png" /></a>
				</div>
		</div>
<?php
}
?>		
		<div class="west-body">
			<div id="banner"><!--<img alt="" src="images/coollogo.png" />--></div>
			<div id="wraper" class="row-offcanvas row-offcanvas-left">


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