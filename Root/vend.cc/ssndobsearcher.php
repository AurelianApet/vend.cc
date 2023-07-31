<?php
set_time_limit(0);
session_start();
require("includes/config.inc.php");

if (checkLogin(PER_USER)) {


function getURLSource($url_to_scan, $post="", $use_header = true)
{

//sleep(rand(2,8));
	
//$url_to_scan = getRandomFrogFile()."?url=".$url_to_scan;

/***** GET_FILE_CONTENTS CALL (IF CURL DOESNT WORK) *****/
//echo $url_to_scan;
//$data = file_get_contents("http://www.triona.si/frog.php?url=".urlencode($url_to_scan));
//$data = file_get_contents("http://www.pakistanauction.com/frog.php?url=".urlencode($url_to_scan));
//$data = file_get_contents("http://www.coinvac.com/frog.php?url=".urlencode($url_to_scan));
//$data = file_get_contents("http://ssndatabase.org/frog.php?url=".urlencode($url_to_scan));

/***** CURL METHOD ******/

$cURL = curl_init();
curl_setopt($cURL, CURLOPT_URL, $url_to_scan);
if ($use_header) curl_setopt($cURL, CURLOPT_HEADER, 1);
curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
@curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, 1);

curl_setopt($cURL, CURLOPT_REFERER, "http://www.ssndob.so/login");
curl_setopt($cURL,CURLOPT_CONNECTTIMEOUT,20);
curl_setopt($cURL,CURLOPT_TIMEOUT,30);

curl_setopt($cURL, CURLOPT_USERAGENT, getRandomUserAgent);

curl_setopt($cURL,CURLOPT_COOKIEFILE,"ssn.txt");
curl_setopt($cURL,CURLOPT_COOKIEJAR,"ssn.txt");

if (strlen($post)>0)
{
	curl_setopt($cURL, CURLOPT_POST, 1); 
	curl_setopt($cURL, CURLOPT_POSTFIELDS, $post); 
}

$data = curl_exec($cURL);
curl_close($cURL);

/***** CURL METHOD ******/

return $data;

}
function printos($arr, $bDontEcho = false)
{
	$p = "<blockquote style=\"background-color: #d5d5d5;text-align:left;\">";
	$txt = str_replace("\n","<br/>",print_r($arr,true));
	$txt = str_replace(" ","&nbsp;",$txt);
	$p .= $txt;
	$p .= "</blockquote>";
	
	if ($bDontEcho) return $p;
	else echo $p;
}
function getParsedData($strData, $start_searchString, $end_searchString, &$pos_from=1, $skip_howManyend_searchStrings=0)
{
	if ($pos_from >= strlen($strData)) $pos_from = strlen($strData) - strlen($start_searchString);
	
	$pos_start = @strpos($strData, $start_searchString, $pos_from);
	if ($pos_start===false) return "";
	
	$skip_numberOfChars = strlen($start_searchString);
	$pos_end = strpos($strData, $end_searchString, $pos_start + $skip_numberOfChars);
	
	while ($skip_howManyend_searchStrings>0)
	{
		$pos_end = strpos($strData, $end_searchString, $pos_end + $skip_numberOfChars);
		$skip_howManyend_searchStrings--;
		//echo ":" . $pos_start . "-->" . $skip_numberOfChars . "-->" . $pos_end;
	}
	
	if ($pos_end > $pos_start)
	{
		$returnData = trim(substr($strData, $pos_start + $skip_numberOfChars, $pos_end - $pos_start - $skip_numberOfChars));
	}
	if (!strlen($returnData)) 	$returnData = ""; 
	$pos_from = $pos_start + $skip_numberOfChars;
	return $returnData;
}


	function createLoad()
	{
		global $ch,$cookie_jar_path,$cookie_file_path;
		preg_match("/(^.+\/)(.*)/",$_SERVER['SCRIPT_FILENAME'],$linkfolder);
		$string=md5(time().rand(0,999));
		$cookie_jar_path=$linkfolder[1].'/cookie/'.$linkfolder[2]."_".$string.'_jar.txt';
		$fp=fopen($cookie_jar_path,'wb');
		fclose($fp);
		$cookie_file_path=$linkfolder[1].'/cookie/'.$linkfolder[2]."_".$string.'_file.txt';
		$fp=fopen($cookie_file_path,'wb');
		fclose($fp);
		$ch=curl_init();
	}
	function load($url,$post='',$socks='',$h=false,$nobody=false,$referer='',$timeout=30,$ua='Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13 ( .NET CLR 3.5.30729)')
	{
		global $ch,$cookie_jar_path,$cookie_file_path,$error;
		curl_setopt($ch, CURLOPT_URL, $url);
		if ($h != false)
		{
			curl_setopt($ch, CURLOPT_HEADER, TRUE);
			//curl_setopt($ch, CURLOPT_HTTPHEADER, $h);
		}
		else
		{
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
		}
		curl_setopt($ch, CURLOPT_NOBODY, $nobody);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar_path);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		if ($ua) curl_setopt($ch, CURLOPT_USERAGENT, $ua);
		if ($referer) curl_setopt($ch, CURLOPT_REFERER,$referer);
		if (strncmp($url,"https",6))
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}
		if ($socks)
		{
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
			curl_setopt($ch, CURLOPT_PROXY, $socks);
			if ($type == 3)
			{
				curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
			}
			else if ($type == 4)
			{
				curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
			}
			else
			{
				curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
			}
		}
		if ($post)
		{
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		$data=curl_exec($ch);
		$error=curl_error($ch);
		if ($data) Return $data;
		//else Return $error;
		else Return false;
	}
	function closeLoad()
	{
		global $ch,$cookie_jar_path,$cookie_file_path;
		@curl_close($ch);
		@ob_end_clean();
		@unlink($cookie_jar_path);
		@unlink($cookie_file_path);
	}
	function ssndob_log($errorMsg)
	{
		$filename = "ssndob_log.txt";
		if ($handle = fopen($filename, 'a+')) {
			if (fwrite($handle, $errorMsg) === FALSE) {
				echo "Cannot write to file ($filename)";
			}
		}
		else {
			echo "Cannot open file ($filename)";
		}
		fclose($handle);
	}
	
	if (isset($_GET["id"])) {
		$sql = "SELECT * FROM `".TABLE_USERS."` WHERE user_id = '".$db->escape($_SESSION["user_id"])."'";
		$record = $db->query_first($sql);
		$user_balance = $record["user_balance"];
		if (doubleval($user_balance) >= doubleval($db_config["ssndob_fee"])) {
			createLoad();
			//INITIAL LOGIN
			$data = getURLSource("http://www.ssndob.so/news");
			if (stripos($data, "Location: /login")!==false)	{ $data = getURLSource("http://www.ssndob.so/login","login=lariza&password=walkman2");}
			if (stripos($data, "<h2>Login</h2>")!==false)	{ echo "<span class=\"red bold centered\">(Cannot login to SSN/DOB server, please contact administrator.)</span>"; exit(0); }
			//GETTING THE DATA
			//echo $data;
			
			$data = '{
    "result": true,
    "id": "25473495",
    "ssn": [
        "067285953"
    ],
    "dob": [
        "19350825",
        "19730219",
        "19350800"
    ]
}'; 
				$data = getURLSource("http://www.ssndob.so/search/ajax", "action=buyResult&id=".$_GET["id"], false);
				$result = json_decode ($data, true);
				if ($result["ssn"]!="" || $result["dob"]!="")
				{
					$credit_update["user_balance"] = doubleval($user_balance)-doubleval($db_config["ssndob_fee"]);
					if (!$db->update(TABLE_USERS, $credit_update, "user_id='".$db->escape($_SESSION["user_id"])."'")) {
						$respond = "<span class=\"red bold\">Update credit error, please try again</span>";
					}
					echo implode("<br />",$result["ssn"])."|".implode("<br />",$result["dob"]);
				}
				else {	echo "<span class=\"red bold centered\">Contact admin:<br />".$result["reason"]."</span>"; exit(0); }		
		} else {
			echo "<span class=\"red bold\">(Need $".number_format($db_config["ssndob_fee"], 2, '.', '')." to search)</span>";
		}
	}
	
	//OLD FUNCTION
	/*
	if (isset($_GET["id"]) && isset($_GET["param"])) {
		$sql = "SELECT * FROM `".TABLE_USERS."` WHERE user_id = '".$db->escape($_SESSION["user_id"])."'";
		$record = $db->query_first($sql);
		$user_balance = $record["user_balance"];
		if (doubleval($user_balance) >= doubleval($db_config["ssndob_fee"])) {
			createLoad();
			$respond = load("http://findget.me/index.php?page=login", "username=".$db_config["ssndob_account"]."&password=".$db_config["ssndob_password"]);
			if (substr_count($respond, "Welcome")) {
				$respond = load("http://findget.me/index.php?page=search&act=unhide&id=".$_GET["id"]."&param=".$_GET["param"]);
				if (preg_match("@(\d{3}-\d{2}-\d{4}|\d{2}-\d{2}-\d{4})@", $respond)) {
					$credit_update["user_balance"] = doubleval($user_balance)-doubleval($db_config["ssndob_fee"]);
					if (!$db->update(TABLE_USERS, $credit_update, "user_id='".$db->escape($_SESSION["user_id"])."'")) {
						$respond = "<span class=\"red bold\">Update credit error, please try again</span>";
					}
					echo $respond;
				}
				else if (substr_count($respond, "Not found any record that meets your search criteria") || substr_count($respond, "Invalid request")) {
					echo "<span class=\"red bold centered\">(Not found any record that meets your search criteria.)</span>";
				}
				else if (substr_count($respond, "You're not having any credits")) {
					echo "<span class=\"red bold centered\">(SSN/DOB credit is over, please contact administrator.)</span>";
				}
				else {
					ssndob_log($respond);
					echo "<span class=\"red bold centered\">Other error</span>";
				}
			}
			else {
				echo "<span class=\"red bold centered\">(Cannot login to SSN/DOB server, please contact administrator.)</span>";
			}
			closeLoad();
		} else {
			echo "<span class=\"red bold\">(Need $".number_format($db_config["ssndob_fee"], 2, '.', '')." to search)</span>";
		}
	}
	*/
}
else {
	header("Location: login.php");
}
exit(0);
?>