<?php
include("../../../dbconnect.php");
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");

$gatewaymodule = "perfectmoney";
$GATEWAY = getGatewayVariables($gatewaymodule);
if (!$GATEWAY["type"]) die("Module Not Activated"); 

function processPOST($hash, $amount, $payee, $cur){

		$_POST['whmcs_hash_compare']='CAME: '.$_POST['V2_HASH'].'; WE HAVE: '.$hash;
		$_POST['whmcs_amount_compare']='CAME: '.$_POST['PAYMENT_AMOUNT'].'; WE HAVE: '.$amount;
		$_POST['whmcs_payee_compare']='CAME: '.$_POST['PAYEE_ACCOUNT'].'; WE HAVE: '.$payee;
		$_POST['whmcs_currency_compare']='CAME: '.$_POST['PAYMENT_UNITS'].'; WE HAVE: '.$cur;

}

$string=
      $_POST['PAYMENT_ID'].':'.$_POST['PAYEE_ACCOUNT'].':'.
      $_POST['PAYMENT_AMOUNT'].':'.$_POST['PAYMENT_UNITS'].':'.
      $_POST['PAYMENT_BATCH_NUM'].':'.
      $_POST['PAYER_ACCOUNT'].':'.strtoupper(md5($GATEWAY["perfectmoney_pass"])).':'.
      $_POST['TIMESTAMPGMT'];


$hash=strtoupper(md5($string));

if($hash=$_POST['V2_HASH']){ // proccessing payment if only hash is valid

	$invoiceid = (int)$_POST["PAYMENT_ID"];
	$invoiceid = checkCbInvoiceID($invoiceid,$GATEWAY["name"]); # Checks invoice ID is a valid invoice number or ends processing
	checkCbTransID($_POST['PAYMENT_BATCH_NUM']); # Checks transaction number isn't already in the database and ends processing if it does

	
	$qry=mysql_query("SELECT tblinvoices.total AS total, tblcurrencies.code AS currency_code, tblcurrencies.id AS currency_id FROM tblinvoices, tblclients, tblcurrencies WHERE tblinvoices.paymentmethod='$gatewaymodule' AND tblinvoices.id=$invoiceid AND tblinvoices.userid=tblclients.id AND tblclients.currency=tblcurrencies.id");

	if(!$qry){ logTransaction($GATEWAY["name"], array_merge($_POST, array('SQL query'=>$qry)), "SQL query error"); die(); }
	if(mysql_num_rows($qry)!=1){ logTransaction($GATEWAY["name"], $_POST, "SQL returned invalid data"); die(); }
	$data=mysql_fetch_array($qry);
print_r(	$data);

	$order_amount=$data['total'];

	if(!empty($GATEWAY['convertto'])) if($data['currency_id']!=$GATEWAY['convertto']){	// need to convert to another currency
		$data['total'] = convertCurrency($data['total'],$data['currency_id'],$GATEWAY['convertto']);
		$_POST['PAYMENT_AMOUNT']=$_POST['PAYMENT_AMOUNT'];
		$qry0=mysql_query("SELECT code FROM tblcurrencies WHERE id=".$GATEWAY['convertto']);
		$data0=mysql_fetch_array($qry0);
		$data['currency_code']=$data0['code'];
	}
	

	if($_POST['PAYMENT_AMOUNT']==$data['total'] && $_POST['PAYEE_ACCOUNT']==$GATEWAY['perfectmoney_id'] && $_POST['PAYMENT_UNITS']==$data['currency_code']){

		addInvoicePayment($invoiceid,$_POST['PAYMENT_BATCH_NUM'],$order_amount,$fee,$gatewaymodule); # Apply Payment to Invoice: invoiceid, transactionid, amount paid, fees, modulename
		processPOST($hash, $data['total'], $GATEWAY['perfectmoney_id'], $data['currency_code']);
		logTransaction($GATEWAY["name"],$_POST,"Successful"); # Save to Gateway Log: name, data array, status

   }else{ // you can also save invalid payments for debug purposes

		 processPOST($hash, $data['total'], $GATEWAY['perfectmoney_id'], $data['currency_code']);
     logTransaction($GATEWAY["name"],$_POST,"Fake Data");

   }

}else{
		
	processPOST($hash, 'not defined', $GATEWAY['perfectmoney_id'], $data['currency_code']);
	logTransaction($GATEWAY["name"],$_POST,"Unsuccessful"); # Save to Gateway Log: name, data array, status

}
?>