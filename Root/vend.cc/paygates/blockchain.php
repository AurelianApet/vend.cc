<?php

function blockchain_config() {
	return array(
		"FriendlyName" => array("Type" => "System", "Value" => "Blockchain.info"),
		"receiving_address" => array("FriendlyName" => "Bitcoin Address", "Type" => "text", "Size" => "64", "Description" => "Bitcoin address where received payments will be sent"),
		"shared_wallet" => array("FriendlyName" => "Shared Wallet", "Type" => "yesno", "Description" => "Tick to send payments through a shared wallet, anonymizing them. Transactions will take slightly longer to confirm. 0.5% fee."),
		"confirmations_required" => array("FriendlyName" => "Confirmations Required", "Type" => "text", "Size" => "4", "Description" => "Number of confirmations required before an invoice is marked 'Paid'. Has no effect if 'Shared Wallet' is ticked above."),
	);
}

function blockchain_link($params) {
	mysql_query("CREATE TABLE IF NOT EXISTS `blockchain_payments` (`invoice_id` int(11) NOT NULL, `amount` float(11,8) NOT NULL, `address` varchar(64) NOT NULL, `secret` varchar(64) NOT NULL, `confirmations` int(11) NOT NULL, `status` enum('unpaid','confirming','paid') NOT NULL, PRIMARY KEY (`invoice_id`))");
	
	$q = mysql_fetch_array(mysql_query("SELECT * FROM `blockchain_payments` WHERE invoice_id = '{$params['invoiceid']}'"));
	if($q['address']) {
		$amount = $q['amount'];
		$address = $q['address'];
		$confirmations = $q['confirmations'];
	}
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://blockchain.info/tobtc?currency={$params['currency']}&value={$params['amount']}");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$amount = curl_exec($ch);
	$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	
	if($status >= 300 || $amount < 0.001) { // Blockchain.info will only relay a transaction if it's 0.001 BTC or larger
		return "We're sorry, but you cannot use Bitcoin to pay for this transaction at this time.";
	}
	
	$shared = 'false';
	if($params['shared_wallet'] == 'on') {
		$shared = 'true';
	}
	
	$secret = '';
	$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	for($i = 0; $i < 64; $i++) {
		$secret .= substr($characters, rand(0, strlen($characters) - 1), 1);
	}
	
	$callback_url = urlencode($params['systemurl'] . "/modules/gateways/callback/blockchain.php?secret=$secret");
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://blockchain.info/api/receive?method=create&address={$params['receiving_address']}&shared=$shared&callback=$callback_url");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	
	if($status >= 300) {
		return "We're sorry, but you cannot use Bitcoin to pay for this transaction at this time.";
	}
	
	$response = json_decode($response);
	if(!$response->input_address) {
		return "We're sorry, but you cannot use Bitcoin to pay for this transaction at this time.";
	}
	
	mysql_query("INSERT INTO `blockchain_payments` SET invoice_id = '{$params['invoiceid']}', amount = '" . mysql_real_escape_string($amount) . "', address = '" . mysql_real_escape_string($response->input_address) . "', secret = '$secret', confirmations = '0', status = 'unpaid'");
	
	return "<iframe src='{$params['systemurl']}/modules/gateways/blockchain.php?invoice={$params['invoiceid']}' style='border:none; height:120px'>Your browser does not support frames.</iframe>";
}

if($_GET['invoice']) {
require('./../../dbconnect.php');
include("./../../includes/gatewayfunctions.php");
$gateway = getGatewayVariables('blockchain');
?>
<!doctype html>
<html>
	<head>
		<title>Blockchain.info Invoice Payment</title>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript">
		function checkStatus() {
			$.get("blockchain.php?checkinvoice=<?php echo $_GET['invoice']; ?>", function(data) {
				if(data == 'paid') {
					parent.location.href = '<?php echo $gateway['systemurl']; ?>/viewinvoice.php?id=<?php echo $_GET['invoice']; ?>';
				} else if(data == 'unpaid') {
					setTimeout(checkStatus, 5000);
				} else {
					$("#content").html("Transaction confirming... " + data + "/<?php echo $gateway['confirmations_required']; ?> confirmations");
					setTimeout(checkStatus, 10000);
				}
			});
		}
		</script>
		<style>
		body {
			font-family:Tahoma;
			font-size:12px;
			text-align:center;
		}
		a:link, a:visited {
			color:#08c;
			text-decoration:none;
		}
		a:hover {
			color:#005580;
			text-decoration:underline
		}
		</style>
	</head>
	<body onload="checkStatus()">
		<p id="content"><?php echo blockchain_get_frame(); ?></p>
	</body>
</html>
<?php
}

function blockchain_get_frame() {
	global $gateway;
	
	$q = mysql_fetch_array(mysql_query("SELECT * FROM `blockchain_payments` WHERE invoice_id = '" . mysql_real_escape_string($_GET['invoice']) . "'"));
	if(!$q['address']) {
		return "We're sorry, but you cannot use Bitcoin to pay for this transaction at this time.";
	}
	
	return "Please send <b><a href='bitcoin:{$q['address']}?amount={$q['amount']}&label=" . urlencode($gateway['companyname'] . ' Invoice #' . $q['invoice_id']) . "'>{$q['amount']} BTC</a></b> to address:<br /><br /><b><a href='https://blockchain.info/address/{$q['address']}' target='_blank'>{$q['address']}</a></b><br /><br /><img src='" . $gateway['systemurl'] . "/images/loading.gif' />";
}

if($_GET['checkinvoice']) {
	header('Content-type: text/plain');
	require('./../../dbconnect.php');
	$q = mysql_fetch_array(mysql_query("SELECT * FROM `blockchain_payments` WHERE invoice_id = '" . mysql_real_escape_string($_GET['checkinvoice']) . "'"));
	
	if($q['status'] == 'paid') {
		echo 'paid';
	} elseif($q['status'] == 'confirming') {
		echo $q['confirmations'];
	} else {
		echo 'unpaid';
	}
}

?>