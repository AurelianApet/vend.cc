<?
session_start();
require ("../includes/config.inc.php");
//require ("../includes/sms.inc.php");
$user_id = $user_info["user_id"];

// config wmz account
$WMID = "111028427943";
$PASS = "Contrax22!";
$WMZ  = "Z240886375109";

if (isset($_POST['amount'])) {
    $_SESSION['amount'] = doubleval($_POST['amount']);
	
    $length = 10;
    $_SESSION['description'] = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

if ($_POST['submit']) {
    $__LBD_VCT = $_POST['__LBD_VCT'];
    $__VIEWSTATE = $_POST['__VIEWSTATE'];
    $__EVENTVALIDATION = $_POST['__EVENTVALIDATION'];
    $_tmp = $_POST['_tmp'];
    $secutity_code = $_POST['secutity_code'];
    $url = "https://my.wmtransfer.com/login.aspx?ReturnUrl=%2foperations.aspx";
    $data = "__LASTFOCUS=&__EVENTTARGET=&__EVENTARGUMENT=&__LBD_VCT=$__LBD_VCT&__LBD_SGC_login_ctl00_cph_captcha_captcha=0&__VIEWSTATE=$__VIEWSTATE&__EVENTVALIDATION=$__EVENTVALIDATION&search=Find+information&ctl00%24cph%24ddlAuthMethod=Login+and+password&ctl00%24cph%24tbLogin=$WMID&ctl00%24cph%24tbPassword=$PASS&ctl00%24cph%24Captcha%24tbCaptcha=$secutity_code&ctl00%24cph%24btnSubmit=Sign+In";
    $page = _curl($url, $data, $sock, $_tmp);
    if (stristr($page, $_SESSION['description'])) {
		$pos = strpos($page, $_SESSION['description']);
		$temp = substr($page, $pos-412, 412);
        $amount = doubleval( preg_replace('/[^0-9.]/s', '', get_string_between($temp, 'm_operation_In">', 'wmz')) );
		//var_dump($temp);
		if($amount > 0) {
			$user_balance = $user_info["user_balance"];
			$credit_update["user_balance"] = doubleval($user_balance) + $amount;
			if ($db->update(TABLE_USERS, $credit_update, "user_id='" . $user_id . "'")) {
				if (intval($user_referenceid) == 0 || $db->update(TABLE_USERS, $reference_update,
					"user_id='" . $user_referenceid . "'")) {
					$msgBody .= 'Payment was verified and is successful.('.$amount.' wmz)\r\n
                    <div align="center">
                     <INPUT Type="BUTTON" VALUE="Return To Shop" ONCLICK="location.href=\'http://base-valid.cc/shop/\'">
                    </div>

';
					unset($_SESSION['description']);
					unset($_SESSION['amount']);
					die($msgBody);
				} else {
					$msgBody .= "Update Reference Credit: SQL Error.\r\n";
				}
			} else {
				$msgBody .= "Update Credit: SQL Error.\r\n";
			}
		}
		else {
			$msgBody .= "Error ocurred, contact support please.";
			//die($msgBody);
		}
    }
	else {
		$msgBody .= "Payment not found, try again later in 1-2 minutes.";
		//die($msgBody);
	}
}

$sock = str_replace("\n", "", $sock);
$cookie = tempnam('cookies', 'ApiNo1_' . rand(100000000000, 999999999999) .
    '.txt');

$url = "https://my.wmtransfer.com/login.aspx?ReturnUrl=%2foperations.aspx";
$page = _curl($url, "", $sock, $cookie);

$__LBD_VCT = urlencode(get_string_between($page, '__LBD_VCT" value="', '"'));
$__VIEWSTATE = urlencode(get_string_between($page, '__VIEWSTATE" value="', '"'));
$__EVENTVALIDATION = urlencode(get_string_between($page,
    '__EVENTVALIDATION" value="', '"'));

$delcaptcha = del_files('captcha/', 'png');
$t = get_string_between($page, 'captcha_captcha&t=', "'");
$url = "https://my.wmtransfer.com/LanapCaptcha.aspx?get=image&c=login_ctl00_cph_captcha_captcha&t=$t";

$imgdata = _curl($url, "", $sock, $cookie);
$imgfile = 'captcha/' . mt_rand() . '.png';
$fp = @fopen($imgfile, 'w');
@fwrite($fp, $imgdata);
@fclose($fp);

?>

<html>
<style type="text/css">
.4 {
	color: #0F0;
}
</style>

				<fieldset>
				

					<p align="center"><strong><br>
					  </strong><img src="webmoney.jpg" alt=".54" width="532" height="26" /></p>
                    
                    <div align="center">
                      <fieldset>
                        <legend><strong>Step 1:</strong></legend>
                        
                        <legend>Go to <a href="https://enter.webmoney.ru/addLP.aspx" target="_blank">WebMoney</a> =>Copy Details From Step 2 : Transfer <i>Exactly</i> To The Following Account:</legend>
                        
                      </fieldset>
                    </div>
                    
<div align="center">
                      <fieldset>
                        <legend><strong>Step 2:</strong></legend>
                        <table border="0" width="100%" cellspacing="1">
                          <tbody>
                            <tr style=" height: 35px; ">
                              <td colspan="3"><div align="center"><u style=" font-weight: bold; ">MAKE SURE MATCHES PAYMENT:</u></div></td>
                            </tr>
                            <tr style=" height: 35px; ">
                              <td width="32%"> </td>
                              <td width="34%"><div align="center"><b>Account:
                                <?= $WMZ ?>
                              </b></div></td>
                              <td width="34%"><div align="left"></div></td>
                            </tr>
                            <tr style=" height: 35px; ">
                              <td> </td>
                              <td><div align="center"><b>Amount:
                                <?= $_SESSION['amount'] ?>
                              WMZ</b></div></td>
                              <td><div align="left"></div></td>
                            </tr>
                            <tr style=" height: 35px; ">
                              <td> </td>
                              <td><div align="center"><span class="4"><b id="memomemo2">Description</b></span><b id="memomemo2">:</b><b id="memomemo">
                                <?= $_SESSION['description'] ?>
                              </b> </div></td>
                              <td><div align="left"><!-- <a id="copy-button" href="#">Copy</a> --></div></td>
                            </tr>
                            <tr>
                              <td colspan="3"><div align="center">(<i>Do not change anything</i>)</div></td>
                            </tr>
                            <tr>
                              <td colspan="3"><legend></legend>
                                <table border="0" cellspacing="1" width="100%">
                                  <tbody>
                                    <tr>
                                      <td><div align="center"></div></td>
                                    </tr>
                                  </tbody>
                                </table></td>
                            </tr>
                          </tbody>
                        </table>
                      </fieldset>
                      <legend></legend>
                      <fieldset>
                        <legend><strong>Step 3:</strong></legend>
                        <?php
						if(!empty($msgBody)) {
							echo "<p><b>NOTICE: ".$msgBody."</b></p>";
						}
						else {
                        	echo "<p>(<i>Please Wait 1-2 Minutes Then Move To Step 3</i>)</p>";
						}
						?>
                        <form method="post">
                          <div align="center">
                            <table>
                              <tbody>
                                <tr>
                                  <input type="hidden" name="__LBD_VCT" id="__LBD_VCT" value="<?= $__LBD_VCT ?>" />
                                  <input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="<?= $__VIEWSTATE ?>" />
                                  <input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="<?= $__EVENTVALIDATION ?>" />
                                  <input type="hidden" name="_tmp" id="_tmp" value="<?= $cookie ?>" />
                                </tr>
                                <tr>
                                  <td><img src="<?= $imgfile; ?>" alt=".1" height="30px" class="recaptcha_image" id="recaptcha_image" /></td>
                                  <td><input name="secutity_code" type="secutity_code" id="secutity_code" class="inputbox"  size="5" maxlength="5" />
                                    <input name="submit" type="submit" value="Confirm Payment"/></td>
                                </tr>
                                <tr></tr>
                              </tbody>
                            </table>
                          </div>
                        </form>
                        <p> </p>
                      </fieldset>
                    </div>

</html>

<?

function clear($str)
{
    $str = str_replace(array("\\\"", "\\'"), array("\"", "'"), $str);
    $str = str_replace("\r", "", $str);
    $str = str_replace("\n\n", "\n", $str);
    $str = str_replace(" ", "", $str);
    return $str;
}

function get_string_between($string, $start, $end)
{
    $string = " " . $string;
    $ini = strpos($string, $start);
    if ($ini == 0)
        return "";
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
function _curl($url, $post = "", $sock, $usecookie = false)
{
    $ch = curl_init();
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    if (!empty($sock)) {
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        curl_setopt($ch, CURLOPT_PROXY, $sock);
    }
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT,
        "Mozilla/6.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.7) Gecko/20050414 Firefox/1.0.3");
    if ($usecookie) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $usecookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $usecookie);
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
function get3Str($str1, $str2, $str3, $str)
{
    $s = explode($str1, $str);
    if (count($s) < 2)
        return $s[0];
    $s = explode($str2, $s[1]);
    if (count($s) < 2)
        return $s[0];
    $s = explode($str3, $s[1]);
    return $s[0];
}

function del_files($dir, $extension)
{
    foreach (glob($dir . '*.' . $extension) as $v) {
        unlink($v);
    }
}
?>