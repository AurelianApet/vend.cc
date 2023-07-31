<?php

function perfectmoney_config(){

    $configarray = array(
     "FriendlyName" => array("Type" => "System", "Value"=>"Perfect Money"),
		 "perfectmoney_id" => array("FriendlyName" => "Perfect Money Payee Account", "Type" => "text", "Size" => "20", "Description" => "example: U1234567"),
		 "perfectmoney_pass" => array("FriendlyName" => "Perfect Money Alternate PassPhrase", "Type" => "text", "Size" => "20", "Description" => "Alternate PassPhrase can be found and set under Settings section in your PM account"),
		 "UsageNotes" => array("Type" => "System", "Value"=>"You must select USD or EUR in \"Convert To For Processing\" field in order to accept Perfect Money."),
    );
		return $configarray;

}

function perfectmoney_link($params) {

	global $_LANG;

	# Gateway Specific Variables
	$perfectmoney_id = $params['perfectmoney_id'];
	
	# Invoice Variables
	$invoiceid = $params['invoiceid'];
	$description = $params["description"];
  $amount = $params['amount']; # Format: ##.##
  $currency = $params['currency']; # Currency Code
 
	# System Variables
	$companyname = $params['companyname'];
	$systemurl = $params['systemurl'];
	$currency = $params['currency'];

	# Enter your code submit to the gateway...

	$code = '<form action="https://perfectmoney.is/api/step1.asp" method="post">
<input type="hidden" name="SUGGESTED_MEMO" value="'.$description.'">

<input type="hidden" name="PAYMENT_ID" value="'.$invoiceid.'" />
<input type="hidden" name="PAYMENT_AMOUNT" value="'.$amount.'" />
<input type="hidden" name="PAYEE_ACCOUNT" value="'.$perfectmoney_id.'" />
<input type="hidden" name="PAYMENT_UNITS" value="'.$currency.'" />
<input type="hidden" name="PAYEE_NAME" value="'.$companyname.'" />
<input type="hidden" name="PAYMENT_URL" value="'.$systemurl.'/viewinvoice.php?id='.$invoiceid.'" />
<input type="hidden" name="PAYMENT_URL_METHOD" value="LINK" />
<input type="hidden" name="NOPAYMENT_URL" value="'.$systemurl.'/viewinvoice.php?id='.$invoiceid.'" />
<input type="hidden" name="NOPAYMENT_URL_METHOD" value="LINK" />
<input type="hidden" name="STATUS_URL" value="'.$systemurl.'/modules/gateways/callback/perfectmoney.php" />
<input type="submit" value="'.$_LANG['invoicespaynow'].'" />
</form>';

	return $code;

}
?>