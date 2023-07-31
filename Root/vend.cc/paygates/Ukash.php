<?php
session_start();
require ("../includes/config.inc.php");
//require ("../includes/sms.inc.php");
$user_id = $_SESSION["user_id"];//$user_info["user_id"];
$username = $_SESSION["user_name"];//$user_info["user_name"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <title>Ukash Paygate</title>
</head>

<body>
<script>
function makeid()
{
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 15; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    document.getElementById('batch_number').value=text;
	document.getElementById('batch_area').style.display="block";
	document.getElementById('msg_area').style.display="block";
	document.getElementById('batch_number').setAttribute("type","text");
}
</script>
<?php
if ($_POST['submit']=="Confirm Payment")
{
if ($_POST['e_code']!="" && $_POST['e_currency']!="" && $_POST['security_code']==$_SESSION['security_code'])
{
// The message
$message = "
Hi,\r\n
Payment from Ukash was sent. Please check and confirm.\r\n
E-Voucher : ".$_POST['e_code']."\r\n
E-Voucher Currency : ".$_POST['e_currency']."\r\n
E-Voucher Amount : ".$_POST['e_amount']."\r\n
Username : ".$_POST['e_username']."\r\n
Batch Number : ".$_POST['batch_number']."\r\n
Thank you so much,";

// In case any of our lines are larger than 70 characters, we should use wordwrap()
$message = wordwrap($message, 70, "\r\n");

// Send
$send_mail=mail('rex.hjulmand@live.dk', 'Payment Via Ukash', $message);
if ($send_mail==true)
{
  $msg = '
  <center><img src="./Perfect.jpg" /></center><br><br>
<div align="center">*We Have Received Your Request & We Will Process Very Soon
</div>
<div align="center">* For Fast Process Send Batch To Support OR Use Ticket, Thanks...
</div>
</p>
    <div align="center">
  <INPUT Type="BUTTON" VALUE="Return To Shop" ONCLICK="location.href=\'http://mahalik.cc/\'">
    </div>
  ';
}
}
}
if ($msg=="") {
?>
<form action="" method="post">
<table width="100%" cellspacing="0" cellpadding="5">
<tr>
<td colspan="2" style="border: 1px solid #404040;">
<center>
<img src="./ukash-logo.jpg" width="250"/>
<img src="./card_ukash.jpg" height="100"/>

</center>
<b>Step 1: </b><br />
Go to <a href="https://ukash.com/" target="_blank">Ukash</a> => Acquire your voucher
</tr>
<tr>
<td style="border-top: 1px solid #404040;border-left: 1px solid #404040;"><b>Step 2: </b><br />Enter E-Voucher :</td>
<td style="border-top: 1px solid #404040;border-right: 1px solid #404040;" align="left"><input type="text" name="e_code" size="25"/></td>
</tr>
<tr>
<td style="border-left: 1px solid #404040;" width="200">E-Voucher Currency :</td>
<td style="border-right: 1px solid #404040;" align="left"><select name="e_currency">
<option value="EUR">EUR</option>
<option value="GBP">GBP</option>
</select></td>
</tr>
<tr>
<td style="border-left: 1px solid #404040;">E-voucher Amount  :</td>
<td style="border-right: 1px solid #404040;" align="left"><input type="text" name="e_amount" size="25" value="<?php echo $_POST['amount'];?>" readonly="readonly"/></td>
</tr>
<tr>
<td style="border-left: 1px solid #404040;">Username :</td>
<td style="border-right: 1px solid #404040;" align="left"><input type="text" name="e_username" size="25" value="<?php echo $username;?>" readonly="readonly"/>
</td>
</tr>
<tr>
<td style="border-right: 1px solid #404040;border-left: 1px solid #404040;border-bottom: 1px solid #404040;" style="border-right: 1px solid #404040;" colspan="2">Now go to step 3</td>
</tr>


<tr>
<td colspan="2" style="border: 1px solid #404040;">
<b>Step 3:</b> <br />
CLICK HERE TO GENERATE TRANSACTION BATCH <br />
<div id="batch_area" style="display:none;">Batch Number: </div><input type="hidden" readonly="readonly" name="batch_number" value="" id="batch_number" size="30"/>
<button onclick="javascript:makeid();return false;">Create Batch Number</button><br />
<div id="msg_area" style="display:none;">PLEASE COPY AND SAVE YOUR BATCH NUMBER</div>
</td>
</tr>

<tr>
<td colspan="2" style="border: 1px solid #404040;">
<b>NOW FINAL STEP</b><br />
<center>
<img src="../captcha.php?width=100&height=40&characters=5" width="90px" height="26px" /> <br />
Captcha Code: <input name="security_code" type="text" id="security_code" maxlength="5" />
<input type="Submit" name="submit" value="Confirm Payment" />
</center><br /><br />
</td>
</tr>

</table><br />
</form>


<?php
}
else
{
echo $msg;
}
?>
</body>

</html>
