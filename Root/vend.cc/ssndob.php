<?php
set_time_limit(0);
require("./header.php");
include('includes/simple_html_dom.php');

$search_price = 0.3;
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

function getURLSource($url_to_scan, $post="")
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
curl_setopt($cURL, CURLOPT_HEADER, 1);
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

//OLD FUNCTION
function ssndob($first_name, $last_name, $middle_name, $city, $state) {
	global $ssndoblist, $db_config;
	createLoad();
	$respond = load("http://findget.me/index.php?page=login", "username=".$db_config["ssndob_account"]."&password=".$db_config["ssndob_password"]);
	if (substr_count($respond, "Welcome")) {
		$respond = load("http://findget.me/index.php?page=search&act=personal_detail", "first_name=".$first_name."&last_name=".$last_name."&middle_name=".$middle_name."&city=".$city."&state=".$state."&act=personal_detail");
		if (substr_count($respond, "Search Result")) {
			$ssndoblist = getBetween($respond, "Search Result", "</table>");
			$ssndoblist = str_replace("<tr><td>First Name", "<td>First Name", $ssndoblist);
			$ssndoblist = str_replace("<td>First Name", "<tr><td>First Name", $ssndoblist);
			$ssndoblist = "<table class=\"ssndob_result bordered\"><tr><td class=\"input_param\">Input Param</td><td class=\"search_result\">Search Result</td></tr>".$ssndoblist."</table>";
			return 0;
		}
		else if (substr_count($respond, "Not found any record that meets your search criteria")) {
			return 2;
		}
		else if (substr_count($respond, "You're not having any credits")) {
			return 3;
		}
		else {
			//echo "<textarea>$respond</textarea>";
			return 4;
		}
	}
	else {
		return 1;
	}
	closeLoad();
}

//get token for submit
function getToken($content){
    $html = file_get_html1($content);
        
    $token = $html->find('input[type=hidden]',0)->value;
    if ($token !== "")
        return $token;
    return false;
}

//get history
function get_result_list($first_name, $last_name, $state){
    
    $url = "http://www.ssndob.so/search/results";
    $data = getURLSource($url);
    $table = str_replace("\t", "",str_replace("\r", "",str_replace("\n", "", getParsedData($data, "<tbody>", "</tbody"))));
    $tmp = explode("</tr>", $table);
        
    foreach($tmp as $row)
        $history[] = explode("</td>", $row);
    
    $status = "STILL SEARCHING";
    $href = "";
    
    foreach($history as $row)
    {
        if (stripos($row[1], $first_name)!==false && stripos($row[2], $last_name)!==false && stripos($row[3], $state)!==false)
        {
            $state = $row[4];
             
            if (stripos($state, "In quene")!==false || stripos($state, "In progress")!==false){
                $status = "STILL SEARCHING";
            }
            elseif(stripos($state, "Ready")!==false){
                $status = "FOUND";
                $href = getParsedData($row[5],"href=\"", "\"");
            }
            elseif(stripos($state, "Not found")!==false)
            $status = "NOT FOUND";
            else
                $status = "STRANGE CASE";
    
            break;
        }
    }
    
    $result[0] = $status;
    $result[1] = $href;
    
    return $result;
}

function ssndob2($first_name, $last_name, $middle_name='',$city='', $state, $zip='') {
	global $ssndoblist, $db_config;
	createLoad();
	/*
	$first_name = "Jack";
	$last_name = "McDonald";
	$state = "CA";
	$city = "";
	$zip = "";
	*/
	$data = getURLSource("http://www.ssndob.so/news");
	if (stripos($data, "Location: /login")!==false)	{ /*printos("DOING LOGIN"); */ 
	    $data = getURLSource("http://www.ssndob.so/login","login=lariza&password=walkman2");}
	//else printos("ALREADY LOGGED IN");
	
	if (stripos($data, "<h2>Login</h2>")!==false)
	    return 1;	
	//$ssndoblist = "<tr><td class=\"input_param\">Input Param</td><td class=\"search_result\">Search Result</td></tr>".$ssndoblist."</table>";
	
	$data = getURLSource("http://www.ssndob.so/search");
	$token = getToken($data);
	//echo "Token:".$token."<br/>";
	
	//DO SEARCH
	$post = "firstname=".$first_name."&lastname=".$last_name."&state=".$state."&token=".$token."&city=".$city."&zip=".$zip;
	$data = getURLSource("http://www.ssndob.so/search",$post);
		
	//LOOP TO WAIT FOR RESULTS
	$status = "STILL SEARCHING";
	$max_loops = 0;
	
	flush();
	ob_flush();
	
	sleep(15);
	
	while ($status == "STILL SEARCHING" && $max_loops < 40)
	{
		$history_result = get_result_list($first_name, $last_name, $state);
				
		$status = $history_result[0];
		$href = $history_result[1];
		
		//printos("LOOP NO (STATUS:".$status."):".$max_loops);
		if ($max_loops==0) echo "Loading: ...";
		else echo "...";
		
		if($status == "STILL SEARCHING")
		    sleep(10);
		
		flush();
		ob_flush();
		$max_loops++;
	}
		
	if ($status=="STILL SEARCHING")
	    return 4;
	if ($status == "NOT FOUND")
	    return 2;
	if ($status == "FOUND")	
	{
		$data = getURLSource("http://www.ssndob.so".$href);
		$table = getParsedData($data, "<table class=\"table table-condensed\">", "</table>");
		$tmp = explode("</tr>", $table);
		foreach($tmp as &$row)	
		{
			$id = getParsedData($row, "data-id=\"", "\"");
			$row = str_replace("<a>Buy</a>", "<a href=\"javascript:unhide(".$id.");\">Buy</a>", $row);
		}
		$table = implode("</tr>", $tmp)."</tr>";
		$ssndoblist = "<table class=\"ssndob_result bordered\">".$table."</table>";
		return 0;
	}
	return 4;
	
}

if ($checkLogin && $_SESSION["user_groupid"] < intval(PER_UNACTIVATE)) {
	if ($_POST["btnGetInfo"] != "") {
		$first_name = $_POST["first_name"];
		$last_name = $_POST["last_name"];
		$middle_name = $_POST["middle_name"];
		$city = $_POST["city"];
		$state = $_POST["state"];
		//if (doubleval($user_info["user_balance"]) >= $search_price) {
			if ($first_name == "") {
				$first_name_error = "First Name are requires.";
			}
			if ($last_name == "") {
				$last_name_error = "Last Name are requires.";
			}
			if ($state == "") {
				$state_error = "State are requires.";
			}
			if ($first_name_error == "" && $last_name_error == "" && $state_error == "") {
				switch (ssndob2($first_name, $last_name, $middle_name, $city, $state)) {
					case 0:
					    /*
						$credit_update["user_balance"] = doubleval($user_info["user_balance"])-doubleval($search_price);
						if (!$db->update(TABLE_USERS, $credit_update, "user_id='".$db->escape($user_info["user_id"])."'")) {
							$ssndobSearchError = "<span class=\"red bold\">Update credit error, please try again</span>";
						} else {
							$ssndobSearchError = "";
						}
						*/
						break;
					case 1:
						$ssndobSearchError = "<span class=\"red bold centered\">Cannot login to SSN/DOB server, please contact administrator.</span>";
						break;
					case 2:
						$ssndobSearchError = "<span class=\"red bold centered\">Not found any record that meets your search criteria.</span>";
						break;
					case 3:
						$ssndobSearchError = "<span class=\"red bold centered\">SSN/DOB credit is over, please contact administrator.</span>";
						break;
					case 4:
						$ssndobSearchError = "<span class=\"red bold centered\">Request Sent & but Timeout..,please try again</span>";
						break;
				}
			}
			else {
				$ssndobSearchError = "<span class=\"red bold centered\">Please fill all required information.</span>";
			}
		//}
		//else {
		//	$ssndobSearchError = "<span class=\"red bold centered\">Need $".number_format($db_config["ssndob_fee"], 2, '.', '')." to search</span>";
		//}
	}
?>
				<div id="myaccount">
					<div class="section_title">SEARCH: FREE | PRICE: $5</div>
					<div class="section_title"><?=$ssndobSearchError?></div>
					<div class="section_content">
						<table class="content_table bordered">
							<tbody>
								<form action="" method="POST">
									<tr>
										<td class="paygate_title">
											First Name <span class="red">(*)</span>
										</td>
										<td class="ssndob_content">
											<input name="first_name" type="text" value="<?=$_POST["first_name"]?>">
										</td>
										<td class="ssndob_content red bold">
											<?=$first_name_error?>
										</td>
									</tr>
									<tr>
										<td class="paygate_title">
											Last Name <span class="red">(*)</span>
										</td>
										<td class="ssndob_content">
											<input name="last_name" type="text" value="<?=$_POST["last_name"]?>">
										</td>
										<td class="red bold">
											<?=$last_name_error?>
										</td>
									</tr>
									<!--
									<tr>
										<td class="paygate_title">
											Middle Name
										</td>
										<td class="ssndob_content">
											<input name="middle_name" type="text" value="<?=$_POST["middle_name"]?>">
										</td>
										<td class="red bold">
										</td>
									</tr>
									-->
									<tr>
										<td class="paygate_title">
											City
										</td>
										<td class="ssndob_content">
											<input name="city" type="text" value="<?=$_POST["city"]?>">
										</td>
										<td class="red bold">
										</td>
									</tr>
									<tr>
										<td class="paygate_title">
											State <span class="red">(*)</span>
										</td>
										<td class="ssndob_content">
											<select name="state" size="1">
												<option value="AL"<?php if ($_POST["state"] == "AL") {echo " selected";}?>>Alabama</option>
												<option value="AK"<?php if ($_POST["state"] == "AK") {echo " selected";}?>>Alaska</option>
												<option value="AB"<?php if ($_POST["state"] == "AB") {echo " selected";}?>>Alberta</option>
												<option value="AS"<?php if ($_POST["state"] == "AS") {echo " selected";}?>>American Samoa</option>
												<option value="AZ"<?php if ($_POST["state"] == "AZ") {echo " selected";}?>>Arizona</option>
												<option value="AR"<?php if ($_POST["state"] == "AR") {echo " selected";}?>>Arkansas</option>
												<option value="CA"<?php if ($_POST["state"] == "CA") {echo " selected";}?>>California</option>
												<option value="CO"<?php if ($_POST["state"] == "CO") {echo " selected";}?>>Colorado</option>
												<option value="CT"<?php if ($_POST["state"] == "CT") {echo " selected";}?>>Connecticut</option>
												<option value="DC"<?php if ($_POST["state"] == "DC") {echo " selected";}?>>DIST OF COL</option>
												<option value="DE"<?php if ($_POST["state"] == "DE") {echo " selected";}?>>Delaware</option>
												<option value="FL"<?php if ($_POST["state"] == "FL") {echo " selected";}?>>Florida</option>
												<option value="GA"<?php if ($_POST["state"] == "GA") {echo " selected";}?>>Georgia</option>
												<option value="GU"<?php if ($_POST["state"] == "GU") {echo " selected";}?>>Guam</option>
												<option value="HI"<?php if ($_POST["state"] == "HI") {echo " selected";}?>>Hawaii</option>
												<option value="IA"<?php if ($_POST["state"] == "IA") {echo " selected";}?>>Iowa</option>
												<option value="ID"<?php if ($_POST["state"] == "ID") {echo " selected";}?>>Idaho</option>
												<option value="IL"<?php if ($_POST["state"] == "IL") {echo " selected";}?>>Illinois</option>
												<option value="IN"<?php if ($_POST["state"] == "IN") {echo " selected";}?>>Indiana</option>
												<option value="KS"<?php if ($_POST["state"] == "KS") {echo " selected";}?>>Kansas</option>
												<option value="KY"<?php if ($_POST["state"] == "KY") {echo " selected";}?>>Kentucky</option>
												<option value="LA"<?php if ($_POST["state"] == "LA") {echo " selected";}?>>Louisiana</option>
												<option value="MA"<?php if ($_POST["state"] == "MA") {echo " selected";}?>>Massachusetts</option>
												<option value="MD"<?php if ($_POST["state"] == "MD") {echo " selected";}?>>Maryland</option>
												<option value="ME"<?php if ($_POST["state"] == "ME") {echo " selected";}?>>Maine</option>
												<option value="MI"<?php if ($_POST["state"] == "MI") {echo " selected";}?>>Michigan</option>
												<option value="MN"<?php if ($_POST["state"] == "MN") {echo " selected";}?>>Minnesota</option>
												<option value="MO"<?php if ($_POST["state"] == "MO") {echo " selected";}?>>Missouri</option>
												<option value="MS"<?php if ($_POST["state"] == "MS") {echo " selected";}?>>Mississippi</option>
												<option value="MT"<?php if ($_POST["state"] == "MT") {echo " selected";}?>>Montana</option>
												<option value="NE"<?php if ($_POST["state"] == "NE") {echo " selected";}?>>Nebraska</option>
												<option value="NV"<?php if ($_POST["state"] == "NV") {echo " selected";}?>>Nevada</option>
												<option value="NH"<?php if ($_POST["state"] == "NH") {echo " selected";}?>>New Hampshire</option>
												<option value="NJ"<?php if ($_POST["state"] == "NJ") {echo " selected";}?>>New Jersey</option>
												<option value="NM"<?php if ($_POST["state"] == "NM") {echo " selected";}?>>New Mexico</option>
												<option value="NY"<?php if ($_POST["state"] == "NY") {echo " selected";}?>>New York</option>
												<option value="NC"<?php if ($_POST["state"] == "NC") {echo " selected";}?>>North Carolina</option>
												<option value="ND"<?php if ($_POST["state"] == "ND") {echo " selected";}?>>North Dakota</option>
												<option value="OH"<?php if ($_POST["state"] == "OH") {echo " selected";}?>>Ohio</option>
												<option value="OK"<?php if ($_POST["state"] == "OK") {echo " selected";}?>>Oklahoma</option>
												<option value="OR"<?php if ($_POST["state"] == "OR") {echo " selected";}?>>Oregon</option>
												<option value="PA"<?php if ($_POST["state"] == "PA") {echo " selected";}?>>Pennsylvania</option>
												<option value="PR"<?php if ($_POST["state"] == "PR") {echo " selected";}?>>Puerto Rico</option>
												<option value="RI"<?php if ($_POST["state"] == "RI") {echo " selected";}?>>Rhode Island</option>
												<option value="SK"<?php if ($_POST["state"] == "SK") {echo " selected";}?>>Saskatchewan</option>
												<option value="SC"<?php if ($_POST["state"] == "SC") {echo " selected";}?>>South Carolina</option>
												<option value="SD"<?php if ($_POST["state"] == "SD") {echo " selected";}?>>South Dakota</option>
												<option value="TN"<?php if ($_POST["state"] == "TN") {echo " selected";}?>>Tennessee</option>
												<option value="TX"<?php if ($_POST["state"] == "TX") {echo " selected";}?>>Texas</option>
												<option value="UT"<?php if ($_POST["state"] == "UT") {echo " selected";}?>>Utah</option>
												<option value="VT"<?php if ($_POST["state"] == "VT") {echo " selected";}?>>Vermont</option>
												<option value="VI"<?php if ($_POST["state"] == "VI") {echo " selected";}?>>Virgin Islands</option>
												<option value="VA"<?php if ($_POST["state"] == "VA") {echo " selected";}?>>Virginia</option>
												<option value="WA"<?php if ($_POST["state"] == "WA") {echo " selected";}?>>Washington</option>
												<option value="WV"<?php if ($_POST["state"] == "WV") {echo " selected";}?>>West Virginia</option>
												<option value="WI"<?php if ($_POST["state"] == "WI") {echo " selected";}?>>Wisconsin</option>
												<option value="WY"<?php if ($_POST["state"] == "WY") {echo " selected";}?>>Wyoming</option>
												<option value="AA"<?php if ($_POST["state"] == "AA") {echo " selected";}?>>AA-APO/FPO Military</option>
												<option value="AE"<?php if ($_POST["state"] == "AE") {echo " selected";}?>>AE-APO/FPO Military</option>
												<option value="AP"<?php if ($_POST["state"] == "AP") {echo " selected";}?>>AP-APO/FPO Military</option>
											</select>
										</td>
										<td class="red bold">
											<?=$state_error?>
										</td>
									</tr>
									<tr>
										<td colspan="3" class="centered">
											<input type="submit" name="btnGetInfo" value="Search SSN/DOB"/>
											<input type="button" name="btnCancel" value="Cancel" onclick="window.location='./'" />
										</td>
									</tr>
								</form>
							</tbody>
						</table>
					</div>
				</div>
<?php
	if ($ssndoblist != "") {
		echo "$ssndoblist";
	}
}
else if ($checkLogin && $_SESSION["user_groupid"] == intval(PER_UNACTIVATE)){
	require("./miniactivate.php");
}
else {
	require("./minilogin.php");
}
require("./footer.php");
?>