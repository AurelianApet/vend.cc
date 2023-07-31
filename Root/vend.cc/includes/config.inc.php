<?php

//if($_SERVER['REMOTE_ADDR']!='82.26.196.254') die("This site is being upgraded, please check again soon :)");

//Clean all inputs


function clean($input){
	
	if(is_array($input)){
		
		foreach($input as $key => $val) $input[$key] = clean($val);
		
		return $input;
	}
	
	//Single
	return trim(strip_tags($input));
	
	
	
}

$_POST=clean($_POST);
$_GET=clean($_GET);

stream_context_set_default(array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
    ),
));  

error_reporting(E_ALL ^ E_NOTICE);
//date_default_timezone_set('America/Los_Angeles');
/* Begin Configurations */
define("ENABLE_CHECKER", true);

define("SHOW_SQL_ERROR", true);

//database server
define('DB_SERVER', "localhost");

//database name
define('DB_DATABASE', "vend");
//database login name
define('DB_USER', "vend");
//database login password
define('DB_PASS', "ykhZ0B4y8LyctzdV");
define("API_AUTH","66a825dea03f879a9430af9c495557967e5109d2");
define("API_LOGIN","onimus");
define("API_GATE","2");

//CC Encrypt password
define('DB_ENCRYPT_PASS', "sdfsdf");

//Login time out for remeber me logins (in hours)
define('REMEMBER_ME_TIMEOUT', 48);

//config SMTP Mail
$smtp_host="smtp.gmail.com";
$smtp_port="465";
$smtp_auth=true; //enable SMTP authentication
$smtp_secure="ssl"; //smtp secure type
$smtp_user="widowrella@gmail.com"; //username of smtp login
$smtp_pass="contrax2"; //password of smtp login
$smtp_from="widowrella@gmail.com"; //email of the sender
$smtp_alias="FRESH CC SHOP"; //name of the sender

//table names
define('TABLE_ACTIVATES', "activates");
define('TABLE_ADS', "ads");
define('TABLE_BINS', "bins");
define('TABLE_BONUS', "bonus");
define('TABLE_CARDS', "cards");
define('TABLE_CATEGORYS', "categorys");
define('TABLE_CHECKS', "checks");
define('TABLE_CONFIGS', "configs");
define('TABLE_DEPOSITS', "deposits");
define('TABLE_DUMPS', "dumps");
define('TABLE_DUMP_CATEGORYS', "dump_categorys");
define('TABLE_DUMP_CHECKS', "dump_checks");
define('TABLE_GROUPS', "groups");
define('TABLE_MESSAGES', "messages");
define('TABLE_NEWS', "news");
define('TABLE_ORDERS', "orders");
define('TABLE_OTHERACCOUNTS', "otheraccounts");
define('TABLE_OTHER_CATEGORYS', "other_categorys");
define('TABLE_PLANS', "plans");
define('TABLE_TOOLS', "tools");
define('TABLE_TRANSFERS', "transfers");
define('TABLE_UPGRADES', "upgrades");
define('TABLE_USERS', "users");
define('TABLE_ZIPCODES', "zipcodes");

//permission
define('PER_UNCONFIRM', 5);
define('PER_UNACTIVATE', 4);
define('PER_USER', 3);
define('PER_SELLER', 2);
define('PER_ADMIN', 1);

//check result
define('CHECK_DEFAULT', 0);
define('CHECK_VALID', 1);
define('CHECK_INVALID', 2);
define('CHECK_REFUND', 3);
define('CHECK_UNKNOWN', 4);
define('CHECK_WAIT_REFUND', 5);

//card status
define('STATUS_DEFAULT', 0);
define('STATUS_DELETED', 1);

//card expire
define('EXPIRE_FUTURE', 0);
define('EXPIRE_STAGNANT', 1);
define('EXPIRE_EXPIRED', 2);

//config
define('DEFAULT_BALANCE', 0);

//config
define('TYPE_UNKNOWN', 1);
define('TYPE_CARD', 1);
define('TYPE_OTHERACCOUNT', 2);

/* End Configurations */

function checkLogin($per_user){
	
	global $db;
	
	require_once $_SERVER['DOCUMENT_ROOT'].'/includes/user.php';	
	
	$user = new user($db);

	//If login attempt else check login session
	$checkLogin = $user->valid_login();
	//user info var
	$user_info = $user->user_info;
	
	return array($checkLogin, $user_info);
}


function usernameFaild($username) {
	if (strlen($username) < 4) {
		return 1;
	}
	else if (strlen($username) > 32) {
		return 2;
	} else if (preg_match("@^\w+$@i", $username) != 1) {
		return 3;
	}
	else {
		return 0;
	}
}
function passwordFaild($password, $repassword) {
	if (strlen($password) < 6) {
		return 1;
	}
	else if (strlen($password) > 32) {
		return 2;
	}
	else if ($password != $repassword) {
		return 3;
	}
	else {
		return 0;
	}
}
function emailFaild($email) {
	if (!preg_match("/^([a-zA-Z0-9_.])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/", $email)) {
		return 1;
	}
	else {
		return 0;
	}
}
function getDumpNumber($cardFullInfo, &$cardType, &$cardNumber, &$cardExp) {
	if (preg_match_all("@(?:^|\D+)((?:4903\d{2}|4905\d{2}|4911\d{2}|4936\d{2}|564182|633110|6333\d{2}|6759\d{2})(?:\d{10}|\d{12,13}))=(\d{17,})@", $cardFullInfo, $cardNumber)) {
		$cardType = "SWITCH";
	} else if (preg_match_all("@(?:^|\D+)((?:40550[1-4]{1}|40555[0-4]{1}|415928|424604|427533|4288\d{2}|443085|448[4-6]{1}\d{2}|471[5-6]{1}\d{2}|4804\d{2})\d{10})=(\d{17,})@", $cardFullInfo, $cardNumber)) {
		$cardType = "VISA";
	} else if (preg_match_all("@(?:^|\D+)((?:6334|6767)(?:\d{12}|\d{14,15}))=(\d{17,})@", $cardFullInfo, $cardNumber)) {
		$cardType = "SOLO";
	} else if (preg_match_all("@(?:^|\D+)((?:2014|2149|30[0-5]{1}|36\d{1}|38\d{1})\d{11})=(\d{17,})@", $cardFullInfo, $cardNumber)) {
		$cardType = "DINER";
	} else if (preg_match_all("@(?:^|\D+)((?:6011|622[1-9]{1}|64[4-9]{1}\d{1}|65\d{2})\d{12})=(\d{17,})@", $cardFullInfo, $cardNumber)) {
		$cardType = "DISCOVER";
	} else if (preg_match_all("@(?:^|\D+)((?:2131|1800|352[89]{1}\d{1}|35[3-8]{1}\d{2})\d{11})=(\d{17,})@", $cardFullInfo, $cardNumber)) {
		$cardType = "JCB";
	} else if (preg_match_all("@(?:^|\D+)((?:(?:50\d{2}|5[6-8]{1}\d{2}|6\d{3}|6304|6759|6761|6763)\d{8-15}|(?:6706|6771|6709)\d{12-15}))=(\d{17,})@", $cardFullInfo, $cardNumber)) {
		$cardType = "MAESTRO";
	} else if (preg_match_all("@(?:^|\D+)(3[47]{1}\d{13})=(\d{17,})@", $cardFullInfo, $cardNumber)) {
		$cardType = "AMEX";
	} else if (preg_match_all("@(?:^|\D+)(4\d{12,15})=(\d{17,})@", $cardFullInfo, $cardNumber)) {
		$cardType = "VISA";
	} else if (preg_match_all("@(?:^|\D+)(5[1-5]{1}\d{14})=(\d{17,})@", $cardFullInfo, $cardNumber)) {
		$cardType = "MASTERCARD";
	} else {
		$cardType = "";
	}
	if (is_array($cardNumber) && count($cardNumber) > 0) {
		$cardExp = substr($cardNumber[2][0], 0, 4);
		$cardNumber = $cardNumber[1][0];
	} else {
		$cardExp = "";
		$cardNumber = "";
	}
	return ($cardType != "");
}
function getCardNumber($cardFullInfo, &$cardType, &$cardNumber) {
	if (preg_match_all("@(?:^|\D+)((?:4903\d{2}|4905\d{2}|4911\d{2}|4936\d{2}|564182|633110|6333\d{2}|6759\d{2})(?:\d{10}|\d{12,13}))(?:$|\D+)@", $cardFullInfo, $cardNumber)) {
		$cardType = "SWITCH";
	} else if (preg_match_all("@(?:^|\D+)((?:40550[1-4]{1}|40555[0-4]{1}|415928|424604|427533|4288\d{2}|443085|448[4-6]{1}\d{2}|471[5-6]{1}\d{2}|4804\d{2})\d{10})(?:$|\D+)@", $cardFullInfo, $cardNumber)) {
		$cardType = "VISA";
	} else if (preg_match_all("@(?:^|\D+)((?:6334|6767)(?:\d{12}|\d{14,15}))(?:$|\D+)@", $cardFullInfo, $cardNumber)) {
		$cardType = "SOLO";
	} else if (preg_match_all("@(?:^|\D+)((?:2014|2149|30[0-5]{1}|36\d{1}|38\d{1})\d{11})(?:$|\D+)@", $cardFullInfo, $cardNumber)) {
		$cardType = "DINER";
	} else if (preg_match_all("@(?:^|\D+)((?:6011|622[1-9]{1}|64[4-9]{1}\d{1}|65\d{2})\d{12})(?:$|\D+)@", $cardFullInfo, $cardNumber)) {
		$cardType = "DISCOVER";
	} else if (preg_match_all("@(?:^|\D+)((?:2131|1800|352[89]{1}\d{1}|35[3-8]{1}\d{2})\d{11})(?:$|\D+)@", $cardFullInfo, $cardNumber)) {
		$cardType = "JCB";
	} else if (preg_match_all("@(?:^|\D+)((?:(?:50\d{2}|5[6-8]{1}\d{2}|6\d{3}|6304|6759|6761|6763)\d{8-15}|(?:6706|6771|6709)\d{12-15}))(?:$|\D+)@", $cardFullInfo, $cardNumber)) {
		$cardType = "MAESTRO";
	} else if (preg_match_all("@(?:^|\D+)(3[47]{1}\d{13})(?:$|\D+)@", $cardFullInfo, $cardNumber)) {
		$cardType = "AMEX";
	} else if (preg_match_all("@(?:^|\D+)(4\d{12,15})(?:$|\D+)@", $cardFullInfo, $cardNumber)) {
		$cardType = "VISA";
	} else if (preg_match_all("@(?:^|\D+)(5[1-5]{1}\d{14})(?:$|\D+)@", $cardFullInfo, $cardNumber)) {
		$cardType = "MASTERCARD";
	} else {
		$cardType = "";
	}
	if (is_array($cardNumber) && count($cardNumber) > 0) {
		$cardNumber = $cardNumber[1][0];
	} else {
		$cardNumber = "";
	}
	return ($cardType != "");
}
/* End Global Functions */

/* Begin Load Class MySQL */
require("mysql.class.php");
/* End Load Class MySQL */

/* Begin Connect MySQL */
$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$db->debug = SHOW_SQL_ERROR;
$db->connect();

/* End Connect MySQL */

/* Begin load db configuration */
$sql = "SELECT * FROM `".TABLE_CONFIGS."` ORDER BY config_name";
$db_config_temp = $db->fetch_array($sql);
foreach ($db_config_temp as $key => $value) {
	$db_config[$value["config_name"]] = $value["config_value"];
}

unset($db_config_temp);
$sql = "SELECT * FROM `".TABLE_GROUPS."` ORDER BY group_id";
$user_groups_temp = $db->fetch_array($sql);
foreach ($user_groups_temp as $key => $value) {
	$user_groups[$value["group_id"]] = array("group_id"=>$value["group_id"], "group_name"=>$value["group_name"], "group_color"=>$value["group_color"]);
}
/* End load db configuration */

?>