<?php
session_start();
ini_set("display_errors",0);
require ("../includes/config.inc.php");
//User check
$user_id = $_SESSION['user_id'];
if(!is_numeric($user_id)) die("Please Logout And Login Again");
$user_info = $db->query_first("SELECT * FROM `users` WHERE `user_id` = '".$user_id."'");
if(empty($user_info)) die("Unknown user, please login before viewing this page!");
//Startup
$btc_amount = $_POST['btc_amount'];
$btc_amount = !is_numeric($btc_amount) ? '0' : ceil($btc_amount);
//Btc-e current price:
$json_data = json_decode(file_get_contents('https://www.bitstamp.net/api/ticker/'), true);
if(empty($json_data)||!isset($json_data['last'])||!is_numeric($json_data['last'])) die('<p style="color:#F00">There is an error getting the current BTC price, please contact us!</p>');
$btc = ceil($json_data['last']);
//jquery request
if(isset($_POST['juqery_current_price'])) die($btc);
//end startup
$guid = '2da92ff4-548b-4d37-a8ce-b9285a03ea50';
$main_password = 'Walkman22!';
$second_password = '';
$label = $user_info["user_name"];
if (!isset($_SESSION['address']) || $_SESSION["address"] == "" ) {
    $url = "https://localhost:3000/merchant/2da92ff4-548b-4d37-a8ce-b9285a03ea50/new_address?password=Walkman22!";
    
    //$page = _curl($url, '', '');
    /* And you can avoid using curl. ?? */
    $page = file_get_contents( $url ); /* Gets the json Array and decodes it to PHP */
    
    echo "address";
    
    $json = json_decode( $page );
    $address = $json->address;

    /* This code here works and gets the json request. You just get the addres from that. */

   // $address = get_string_between($page, 'address":"', '"'); 
   /* No need for this part */
    $_SESSION['address'] = $address;

}

if (isset($_POST['check_result'])) {
    $url = "https://blockchain.info/q/addressbalance/".$_SESSION['address']."/?confirmations=0";
   // $page = _curl($url, '', '');
    $page = file_get_contents( $url );
    
    if ($page > 0) {
$amount = doubleval($page*($btc/100000000));
$totalBonus = 0;
$sql = "SELECT * FROM `".TABLE_BONUS."`";
$records = $db->fetch_array($sql);
if (count($records)>0) {
foreach ($records as $value) {
if ($value_groups = unserialize($value['bonus_groupid'])) {
if (in_array($_SESSION['user_groupid'], $value_groups)) {
if ((doubleval($value['bonus_start']) >= 0) && (doubleval($value['bonus_end']) == 0 || doubleval($value['bonus_start']) <= doubleval($value['bonus_end'])) && (doubleval($amount) >= doubleval($value['bonus_start'])) && (doubleval($value['bonus_end']) == 0 || doubleval($amount) < doubleval($value['bonus_end']))) {
$allBonus[] = $value;
$totalBonus += $value['bonus_value'];
}
}
}
}
}
$bonus = doubleval($amount*($totalBonus/100));
$user_balance = $user_info['user_balance'];
$user_deptime = $user_info['user_deptime'];
$user_depmoney = $user_info['user_depmoney'];
$credit_update['user_balance'] = doubleval($user_balance) + doubleval($amount + $bonus);
//$credit_update['user_deptime'] = intval($user_deptime) + 1; /* This field is missing
//$credit_update['user_balance'] = doubleval($user_depmoney) + doubleval($amounts);test now
$deposits_add['deposit_userid'] = $user_id;
$deposits_add['deposit_paygate'] = "bitcoin";
$deposits_add['deposit_amount'] = doubleval($amount);
$deposits_add['deposit_price'] = doubleval($amount);
$deposits_add['deposit_bonus'] = doubleval($bonus);
$deposits_add['deposit_before'] = doubleval($user_balance);
$deposits_add['deposit_proof'] = $_SESSION['address'];
$deposits_add['deposit_time'] = time();
$db->insert(TABLE_DEPOSITS, $deposits_add);
        if ($db->update(TABLE_USERS, $credit_update, "user_id='" . $user_info['user_id'] . "'"))      
	  {
         
                $msgBody = '
					<div align="center">
					 <img src="http://s7.postimg.org/r8pss76t3/success_icon.png" height="200" width="200" />
					 <br>
					 Payment was verified and is successful.<br />
                  <div align="center"> <button onclick="location.href=\'https://vend.cc\'"> Return to shop </button> </div>
                    </div>
';
                unset($_SESSION['address']);
                die($msgBody);
 
        } else die("SQL_ERROR");
    } else die("NDY");
	exit;
}
function _curl($url, $post = "", $sock, $usecookie = false)
{
    $ch = curl_init();
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    if (!empty($sock)) {
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        curl_setopt($ch, CURLOPT_PROXY, $sock);
    }
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT,
        "Mozilla/6.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.7) Gecko/20050414 Firefox/1.0.3");
    if ($usecookie) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $usecookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $usecookie);
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
function get_string_between($string, $start, $end)
{
    $string = " " . $string;
    $ini = strpos($string, $start);
    if ($ini == 0)
        return "";
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=shift_jis">

<title>Deposit</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<link rel="stylesheet" href="../styles/jquery-ui-1.10.3.custom.min.css">
<script src="../js/jquery-ui.js"></script>

<body>
<div class="head">
<div class="title"><center> Deposit </div></center>
</div>				

<script type="text/javascript">
	//Error and win control
	$(function(){
		$("#win").hide();
		$("#error").hide();
		var error = '<?php if(isset($error))echo $error?>';
		var win = '<?php if(isset($win))echo $win?>';
		//Check for if set
		if(win != ''){
			$("#win").html(win);
			$("#win").slideDown("slow");
		}
		if(error != ''){
			$("#error").html(error);
			$("#error").slideDown("slow");
		}
		//Detect clicks
		$("#win").click(function(){
			$("#win").slideUp('slow');
		});
		$("#error").click(function(){
			$("#error").slideUp('slow');
		});
	});
	//Live btc price
	$(function(){
		var timer=setInterval(function(){
			$.post(
			'btcn.php',
			{juqery_current_price:'yes'},
			function(result){
				$("#btc_price").html(result);
			});	
			//Update bitcoins needed
			cal_price($("#usd_input").val());
		}, 1500);
		cal_price(<?php echo $btc_amount?>);
	});
	//Check payment
	function check_payment() {
		$("#checking_payment").html('<center><img src="ajax-loader.gif"/><br />Checking payment...</center>');
		$("#checking_payment").dialog({
			autoOpen: true,
			height: 475,
			width: 650,
			show:'slide',
			hide:'slide',
			modal: true,
			close: function() {
			  $(this).dialog("close");
			}
		});
		//Timer
		var timer=setInterval(function(){
			$.post(
			'btcn.php',
			{check_result:'yes'},
			function(result){
				if(result=="SQL_ERROR"){
					alert("There was a problem with your request, please contact us!");
					return false;
				}	
				else if(result=="NDY"){
					$("#checking_payment").html('<center><img src="ajax-loader.gif"  /><br />Payment has not been confirmed yet and may take up to 1 hour.<br />This window will refresh every 5 seconds and check.</center>');
				} else {				
				//Display win result
				$("#checking_payment").html(result);
				clearInterval(timer);	
				}
				$("#checking_payment").dialog({
					autoOpen: true,
					height: 475,
					width: 650,
					show:'slide',
					hide:'slide',
					modal: true,
					buttons: {
						"close": function(){
							clearInterval(timer);
							$(this).dialog("close");
						}
					},
					close: function() {
					  clearInterval(timer);
					  $(this).dialog("close");
					}
				});
			});
		},5000);
	}
	//Convert function
	function cal_price(usd){
		if(isNaN(usd)){
			$("#btc_output").val('Price error');	
			return false;	
		}
		btc_price = $("#btc_price").html();
		if(isNaN(btc_price)){return false;}
		//Cal
		$("#btc_output").val(usd/btc_price);
	}
	</script>
	<style type="text/css">
<!--
* {
text-transform:none;
}
a {
color: #000;
text-decoration: none;
text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.8);
}
.paygate {
background:#FFF;
color:#000;
}
body {
padding-top:10px;
}
body {
font: 14px/20px 'Helvetica Neue', Helvetica, Arial, sans-serif;
background: #191919;
padding-top:10px;
padding-right:10px;
padding-bottom:10px;
padding-left:10px;
}
.logo {
padding-top: 5px;
font-family: 'Lato', Calibri, Arial, sans-serif;
color: #E6E9ED;
font-size: 40px;
text-align: left;
margin-left:20px;
font-weight: 500;
text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.8);
}
.title {
font-size: 20px;
text-align: left;
padding-top: 20px;
color: #2980b9;
margin-bottom:50px;
text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.8);
}
button  {
background: #1c222a;
border: 0;
line-height: 40px;
padding: 0px 40px;
-webkit-border-radius: 20px;
border-radius: 20px;
cursor: pointer;
-webkit-transition: all 0.3s ease;
-moz-transition: all 0.3s ease;
-o-transition: all 0.3s ease;
-ms-transition: all 0.3s ease;
transition: all 0.3s ease;
overflow: hidden;
outline: none;
font-family: 'Montserrat', sans-serif;
font-weight: 700;
font-size: 14px;
color: #72d2ff;
float:left;
}
input[type=text]:focus, input[type=secutity_code]:focus {
color: #ecf0f1;
background: rgba(0, 0, 0, 0.1);
outline: 0;
}
input[type=text], input[type=secutity_code] {
padding: 0 10px;
width: 150px;
height: 40px;
color: #bbb;
text-shadow: 1px 1px 1px black;
background: rgba(0, 0, 0, 0.16);
border: 0;
border-radius: 5px;
-webkit-box-shadow: inset 0 1px 4px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.06);
box-shadow: inset 0 1px 4px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.06);
text-align:center;
}
-->
</style>

</head>
<body>
<div id="error" style="display:none"></div>

<div id="win" style="display:none"></div>



<div id="checking_payment" style="display:none" title="Checking payment"></div>
<fieldset>
<div class="paygate">
<p align="center"><strong><br>
</strong> <img src="bitcoin-logo.png" alt=".54" width="532" height="26" /> </p>
<div align="center">
<fieldset>
<legend><strong>Step 1:</strong></legend>
<legend>Deposit into Vend shop</legend>
</fieldset>
</div>
<div align="center">
<fieldset>
<legend><strong>Step 2:</strong></legend>
<table border="0" width="100%" cellspacing="1">
<tbody>
<tr style=" height: 35px; ">
<td colspan="3"><div align="center"><u style=" font-weight: bold; ">Send any amount. No minimum</u></div></td>
</tr>
</tr>
<tr style=" height: 35px; ">
<td> </td>
<td><div align="center"><b>1 BTC = <?php echo $json_data["last"]; ?></b></div></td>
<td><div align="left"></div></td>
</tr>
<td><div align="left"></div></td>
</tr>

<tr style=" height: 35px; ">
<td> </td>
<td><div align="center"><span class="4"><b id="memomemo2">Account : <?php echo $user_info["user_name"]; ?></b></span></b> <br> <h3 style="padding:5px; background:#B2FFA8; color:#000;">

<p><?php echo $_SESSION['address']; ?></p>
</h3></td>
</tr>
<tr>
<td colspan="3"><legend></legend>
<table border="0" cellspacing="1" width="100%">
<tbody>
<tr>
<td><div align="center"></div></td>
</tr>
</tbody>
</table></td>
</tr>
</tbody>
</table>
</fieldset>
<legend></legend>
<fieldset>
<legend><strong>Step 3:</strong></legend>
<form method="post">
<div align="center">
<table>
<tbody>
<tr>

</tr>
<tr>
<td></td>
<td></td>
<td>  <button onclick="check_payment();return false;">Confirm payment</button>  </td>
</tr>
<tr></tr>
</tbody>
</table>
</div>
</form>
<p> </p>

</div>
</body>
</html>