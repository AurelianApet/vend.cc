<?php
require("./header.php");
if ($checkLogin) {
?>

<div class="section_title">> Read the following first before buying from us</div>
<ul>
<li>Vend CC Service - Please read rules before buying!</li>
<li>We accept payments made with Bitcoin (BTC) only - Our service DO NOT return money to wallet!</li>
<li>Follow instructions on <a href="https://vend.cc/paygates/btcn.php" target="_blank">DEPOSIT</a> page on how to topup balance</li>
<li>You will have exactly 15 minutes to 'CHECK' a card/dump and get refund, only if material was bad!</li>
<li>You pay only for checker if card was 'APPROVED', checking cost is $0.2</li>
<li>We are not responsible for 3D-Secure/Verified By Visa</li>
<li>If you are want to sell in our shop, then contact using the ticket system. Bulk orders welcome!</li>
<li>We will BAN you for suspicious behaviour!<br></li>

<br>
<i>thank you for business.</i>
<?php
}
else {
	require("./minilogin.php");
}
require("./footer.php");
?>