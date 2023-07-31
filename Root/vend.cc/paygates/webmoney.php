<?php

function GetApiResult(&$postfields)
{
	$url = ""; # URL to WHMCS API file
	$username = ""; # Admin username goes here
	$password = ""; # Admin password goes here

	$postfields["username"] = $username;
	$postfields["password"] = md5($password);

	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	$xmlstr = curl_exec($ch);
	curl_close($ch);

	$xml = new SimpleXMLElement($xmlstr);

	if ($xml->result != "success") return false; else return $xml;
}


function webmoney_config() 
{
	$configarray = Array();
	$configarray["FriendlyName"] = array("Type" => "System", "Value"=>"WebMoney", );

	//получаем список валют и создаем поля для ввода номеров кошельков для каждой валюты
	$postfields = array('action' => 'getcurrencies');		
	$xml = GetApiResult($postfields, );
	if (!$xml) die("ERROR");

	$currencies = $xml->xpath('currencies/currency');
	
	if ($currencies)
	{
		foreach ($currencies as $currency) 
		{
			$configarray["purse_".$currency->code] = array("FriendlyName" => "Кошелек ".$currency->code, "Type" => "text", "Size" => "13", "Description" => "Укажите номер кошелька в валюте ".$currency->code." (буква и 12 цифр)", );
			$configarray["secretkey_".$currency->code] = array("FriendlyName" => "Секретный код кошелька в ".$currency->code, "Type" => "text", "Size" => "30", "Description" => "Укажите секретный код, который вы указали в настройках WM Transfer для кошелька в ".$currency->code, );
		}	
	}
	else die();
	
	$configarray["apiurl"] = array("FriendlyName" => "API Url", "Type" => "text", "Size" => "130", "Description" => "Укажите URL обращения к API WHMCS", );
	$configarray["apilogin"] = array("FriendlyName" => "API логин", "Type" => "text", "Size" => "20", "Description" => "Укажите логин пользователя WHMCS, с правами обращения к APIRL обращения к API WHMCS", );
	$configarray["apipwd"] = array("FriendlyName" => "API пароль", "Type" => "text", "Size" => "20", "Description" => "Укажите пароль пользователя WHMCS, с правами обращения к APIRL обращения к API WHMCS", );
	$configarray["simmode"] = array("FriendlyName" => "Тестовый режим", "Type" => "dropdown", "Options" => "Выкл.,Успешные операции,Операции с ошибкой,Комбинированный", "Description" => "Выберите режим тестирования", );		
	return $configarray;
}

function webmoney_link($params) 
{

	$invoiceid = $params['invoiceid'];
	$description = $params["description"];
   $amount = $params['amount'];   
   $paycurrency = $params['currency']; 
   $purse = $params['purse_'.$paycurrency];
   
   if ($purse == "") //Если для выбранной пользователем валюты нет соответствующего кошелька WebMoney
   {
   	$MAmount = $amount; //сохраняем оригинальную сумму счета
   	$FExchangePurse = 1; //устанавливаем флаг смены валюты и суммы платежа
   	
   	//получаем список валют 
		$postfields = array('action' => 'getcurrencies');		
		$xml = GetApiResult($postfields);
		if (!$xml) die("ERROR");

		$BaseCurrency = $xml->xpath('currencies/currency[rate=1.00000]'); //Находим базовую валюту

		//Берем номер кошелька для базовой валюты
		if ($BaseCurrency && $params['purse_'.$BaseCurrency[0]->code] != "")
		{
			$purse = $params['purse_'.$BaseCurrency[0]->code]; 
		}
		else die("Не указан номер кошелька WebMoney для базовой валюты!");
		
		//Вычисляем размер оплаты в базовой валюте
		//Для этого берем курс валюты, выбранной пользователем
				
		$currency = $xml->xpath('currencies/currency[code="'.$paycurrency.'"]');
		$CurrencyRate = (float)$currency[0]->rate;

		//вычисляем размер оплаты в базовой валюте	
		if ($CurrencyRate) $amount = round($amount / $CurrencyRate, 0); else die("Не найден курс валюты ".$paycurrency);
	}   

	switch ($params['simmode'])
	{
		case "Выкл.":
			$simmode = 10;
			break;
		
		case "Успешные операции":
			$simmode = 0;			
			break;
		
		case "Операции с ошибкой":
			$simmode = 1;		
			break;
		
		case "Комбинированный":
			$simmode = 2;		
			break;
	}
   
   $PaymentDesc = base64_encode("Оплата заказа №".$invoiceid." Клиент: ".$params['clientdetails']['lastname']." ".$params['clientdetails']['firstname']);
	$code = '<form method="POST" action="https://merchant.webmoney.ru/lmi/payment.asp">';  
	//$code = '<form method="POST" action="http://xyberry.com/test.php">';  	
	$code .= '<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="'.$amount.'">';
	$code .= '<input type="hidden" name="LMI_PAYMENT_DESC_BASE64" value="'.$PaymentDesc.'">';
	$code .= '<input type="hidden" name="LMI_PAYMENT_NO" value="'.$invoiceid.'">';
	$code .= '<input type="hidden" name="LMI_PAYEE_PURSE" value="'.$purse.'">';
	if ($simmode != 10) 
	{
		$code .= '<input type="hidden" name="LMI_SIM_MODE" value="'.$simmode.'">';
		$code .= '<input type="hidden" name="M_SIM_MODE" value="1">';		
	}
	if ($FExchangePurse) 
	{
		$code .= '<input type="hidden" name="M_CURRENCY" value="'.$paycurrency.'">';
		$code .= '<input type="hidden" name="M_AMOUNT" value="'.$MAmount.'">';
	}
	$code .= '<input type="submit" value="Оплатить" />';
	$code .= '</form>';


	return $code;
}


?>
