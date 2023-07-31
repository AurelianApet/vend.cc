<?php

# Required File Includes
include("../../../dbconnect.php");
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");

function WMGetApiResult(&$postfields, $apiurl)
{
	$postfields["password"] = md5($postfields["password"]);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $apiurl);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	$xmlstr = curl_exec($ch);
	curl_close($ch);

	$xml = new SimpleXMLElement($xmlstr);

	if ($xml->result != "success") return false; else return $xml;
}

$gatewaymodule = "webmoney"; 

$GATEWAY = getGatewayVariables($gatewaymodule);
if (!$GATEWAY["type"]) die("Module Not Activated"); 

//общие параметры запроса сохраняем в локальных переменных
$purse = $_REQUEST["LMI_PAYEE_PURSE"];
$amount = $_REQUEST["LMI_PAYMENT_AMOUNT"];
$simmode = $_REQUEST["LMI_MODE"];
$invDesc = $_REQUEST["LMI_PAYMENT_DESC"];
$PayerPurse = $_REQUEST["LMI_PAYER_PURSE"];
$PayerWMID = $_REQUEST["LMI_PAYER_WM"];
$invoiceid = $_REQUEST["LMI_PAYMENT_NO"];
$WMT_invoiceid = $_REQUEST['LMI_SYS_INVS_NO'];
$WMT_transactionid = $_REQUEST['LMI_SYS_TRANS_NO'];
$DateTime = $_REQUEST['LMI_SYS_TRANS_DATE'];
$hash = $_REQUEST['LMI_HASH'];

switch ($_REQUEST['step'])
{
	case "result":
		$FAmountCorrect = true; //Флаг корректности суммы платежа
		$FPurseCorrect = true; //Флаг корректности номера кошелька

		$invoiceid = checkCbInvoiceID($invoiceid,$GATEWAY["name"]); //Проверяем номер счета

		//получаем информацию о счете
		$postfields = array('action' => 'getinvoice', 'invoiceid' => $invoiceid,  'username' => $GATEWAY['apilogin'], 'password' => $GATEWAY['apipwd']);		
		$xml = WMGetApiResult($postfields, $GATEWAY['apiurl']);
		if (!$xml) die("ERROR");

		$inv_amount = (string)$xml->total;
		$my_amount = $inv_amount;

		//Проверяем соответствие суммы платежа и номер кошелька
		if (isset($_REQUEST["M_CURRENCY"])) //если менялась валюта платежа
		{
			//получаем список валют 
			$postfields = array('action' => 'getcurrencies', 'username' => $GATEWAY['apilogin'], 'password' => $GATEWAY['apipwd']);		
			$xml = WMGetApiResult($postfields, $GATEWAY['apiurl']);
			if (!$xml) die("ERROR");
	
			//получаем курс оригинальной валюты
			$currency = $xml->xpath('currencies/currency[code="'.$_REQUEST["M_CURRENCY"].'"]');
			$CurrencyRate = (float)$currency[0]->rate;

			$my_amount = 	round($my_amount / $CurrencyRate,0);
			$my_amount = number_format($my_amount, 2, ".", "");
	
			//проверям сумму платежа
			if ($inv_amount != $_REQUEST["M_AMOUNT"] || $my_amount != $amount) 
			{
				$FAmountCorrect = false;
				echo "Сумма платежа неверна";
			}
	
			//проверяем номер кошелька
			$BaseCurrency = $xml->xpath('currencies/currency[rate=1.00000]'); //Находим базовую валюту
			if ($GATEWAY['purse_'.$BaseCurrency[0]->code] != $purse) 
			{
				$FPurseCorrect = false;
				echo "Номер кошелька неверен";
			}
		}
		else
		{
			//проверяем сумму платежа
			if ($my_amount != $amount) 
			{
				$FAmountCorrect = false;
				echo "Сумма платежа неверна";
			}
			//проверяем номер кошелька
			if (!array_search($purse, $GATEWAY)) 
			{
				$FPurseCorrect = false;
				echo "Номер кошелька неверен";
			}
		}

		if ($_REQUEST["LMI_PREREQUEST"] == 1) 
		{
			//Предварительный запрос
			$trans_desc = "$gatewaymodule Поступил предварительный запрос об оплате: ";
			if ($FPurseCorrect && $FAmountCorrect) 
			{
				echo "YES";
				$trans_desc .= "Успешно";
			}
			else 	$trans_desc .= "Ошибка (неверный номер кошелька и/или сумма платежа)";
		}
		else 
		{
			//оповещение о платеже
			$trans_desc = "$gatewaymodule Поступило оповещение об оплате: ";	
			if ($FPurseCorrect && $FAmountCorrect) 
			{	
				if ($GATEWAY['simmode'] == "Выкл.") $mode = 0; else $mode = 1;
				$SecretKeyField = "secretkey_".substr(array_search($purse, $GATEWAY), -3, 3);
				$SecretKey = $GATEWAY[$SecretKeyField];
	
				$myhash = strtoupper(md5($purse.$my_amount.$invoiceid.$mode.$WMT_invoiceid.$WMT_transactionid.$DateTime.$SecretKey.$PayerPurse.$PayerWMID));

				if ($myhash == $hash)	$trans_desc .= "Успешно"; else	$trans_desc .= "Ошибка (неверная контрольная сумма)";
			}
			else $trans_desc .= "Ошибка (неверный номер кошелька и/или сумма платежа)";
		}
		logTransaction($GATEWAY["name"],$_REQUEST,$trans_desc);	
		break;
		
	case "success":
		//Платеж выполнен
		checkCbTransID($WMT_transactionid); 
		if ($_REQUEST['M_SIM_MODE'] == 0) addInvoicePayment($invoiceid,$WMT_transactionid,$my_amount,0,$gatewaymodule);
		$trans_desc = "$gatewaymodule Поступление оплаты: Успешно";	
		logTransaction($GATEWAY["name"],$_REQUEST,$trans_desc);	
		echo $page;
		break;	
		
	case "fail":
		//Платеж не выполнен
		$trans_desc = "$gatewaymodule Поступление оплаты: Ошибка";	
		logTransaction($GATEWAY["name"],$_REQUEST,$trans_desc);	
		echo $page;
		break;	
}

?>