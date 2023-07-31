<?php
//session_start();

require_once("./header.php");
require_once("./includes/class.phpmailer.php");
define(TABLE_VOUCHERS, 'vouchers');

if (!$checkLogin) {
	function random_gen($length) {
		$random = "";
		srand((double)microtime()*1000000);
		$char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$char_list .= "abcdefghijklmnopqrstuvwxyz";
		$char_list .= "1234567890";
		for($i = 0; $i < $length; $i++) {    
			$random .= substr($char_list,(rand()%(strlen($char_list))), 1);  
		}  
		return $random;
	}
	$showForm = true;
	if (isset($_GET["r"]) && trim($_GET["r"]) != "") {
		$_POST["user_reference"] = trim($_GET["r"]);
	}
	if ($_POST["btnRegister"] != "") {
		$_POST['security_code'] = hash("sha512", $_POST['security_code']."34hjhFDSFKj5g&uh34545");
		//if($_COOKIE['secure_code'] == $_POST['security_code'] && !empty($_COOKIE['secure_code'])) {
			if ($db_config["enable_voucher"]) {
				if (isset($_POST["voucher"]) || trim($_POST["voucher"]) != "") {
					$sql = "SELECT * FROM `".TABLE_VOUCHERS."` WHERE voucher_code = ?";
					$user_vourcer = $db->query_first($sql, $_POST["voucher"]);
					if ($user_vourcer) {
						if ($user_vourcer["voucher_userid"] == "0") {
							$voucherError = "";
						} else {
							$voucherError = "This voucher were used by other account";
						}
					} else {
						$voucherError = "This voucher is incorrect";
					}
				} else {
					$voucherError = "Please enter voucher";
				}
			} else {
				$voucherError = "";
			}
			if ($db_config["enable_confirm"]) {
				$user_add["user_groupid"] = intval(PER_UNCONFIRM);
			} else {
				$user_add["user_groupid"] = intval(PER_USER);
			}
			switch (emailFaild($_POST["user_mail"])) {
				case 0:
					$emailError = "";
					$user_add["user_mail"] = $_POST["user_mail"];
					break;
				case 1:
					$emailError = "Invalid e-mail address.";
					break;
				case 2:
			}
			if ($emailError == "") {
				$sql = "SELECT count(*) FROM `".TABLE_USERS."` WHERE user_mail = ?";
				$user_mailCount = $db->query_first($sql, $_POST["user_mail"]);
				if ($user_mailCount) {
					if (intval($user_mailCount["count(*)"]) != intval(0)) {
						$emailError = "This email has been used.";
					}
				} else {
					$emailError = "Check email error, please try again";
				}
			}
			$user_add["user_yahoo"] = $_POST["user_yahoo"];
			$user_add["user_icq"] = $_POST["user_icq"];
			switch (passwordFaild($_POST["user_pass"], $_POST["user_pass_re"])) {
				case 0:
					$passwordError = "";
					$user_add["user_salt"] = rand(100,999);
					$user_add["user_pass"] = md5(md5($_POST["user_pass"]).$user_add["user_salt"]);
					break;
				case 1:
					$passwordError = "Password is too short.";
					break;
				case 2:
					$passwordError = "Password is too long.";
					break;
				case 3:
					$passwordError = "Password doesn't match.";
					break;
			}
			switch (usernameFaild($_POST["user_name"])) {
				case 0:
					$usernameError = "";
					$user_add["user_name"] = $_POST["user_name"];
					break;
				case 1:
					$usernameError = "Username is too short.";
					break;
				case 2:
					$usernameError = "Username is too long.";
					break;
				case 3:
					$usernameError = "Username is only accept digits, character and underscore.";
					break;
			}
			if ($_POST["user_reference"] != "") {
				$sql = "SELECT user_id FROM `".TABLE_USERS."` WHERE user_name = ?";
				$user_reference = $db->query_first($sql, $_POST["user_reference"]);
				if ($user_reference) {
					$user_add["user_referenceid"] = $user_reference["user_id"];
					$referenceError = "";
				} else {
					$referenceError = "This username doesn't exist.";
				}
			} else {
				$user_add["user_referenceid"] = "0";
				$referenceError = "";
			}
			if ($usernameError == "") {
				$sql = "SELECT count(*) FROM `".TABLE_USERS."` WHERE user_name = ?";
				$user_nameCount = $db->query_first($sql, $_POST["user_name"]);
				if ($user_nameCount) {
					if (intval($user_nameCount["count(*)"]) != intval(0)) {
						$usernameError = "This username has been used.";
					}
				} else {
					$usernameError = "Check username error, please try again";
				}
			}
			$user_add["user_balance"] = doubleval(DEFAULT_BALANCE);
			$user_add["user_activecode"] = random_gen(10);
			$user_add["user_regdate"] = time();
			if ($voucherError == "" && $emailError == "" && $passwordError == "" && $usernameError == "" && $referenceError == "") {
				if($db->insert(TABLE_USERS, $user_add)) {
					$voucher_update = array();
					$sql = "SELECT * FROM `".TABLE_USERS."` WHERE user_name = ?";
					$user_info = $db->query_first($sql, $user_add["user_name"]);
					if ($user_info) {
						$voucher_update["voucher_userid"] = $user_info["user_id"];
						$voucher_update["voucher_time"] = time();
						if (!$db_config["enable_voucher"] || $db->update(TABLE_VOUCHERS, $voucher_update, "voucher_code = ?", $_POST["voucher"])) {
							if ($db_config["enable_confirm"]) {
								$mail = new PHPMailer();
								$mail->IsSMTP();
								$mail->SMTPAuth = $smtp_auth;
								$mail->SMTPSecure = $smtp_secure;
								$mail->Host = $smtp_host;
								$mail->Port = $smtp_port;
								$mail->Username = $smtp_user;
								$mail->Password = $smtp_pass;
								$mail->From = $smtp_from;
								$mail->FromName = $smtp_alias;
								$mail->Subject = "Confirm email address for ".$user_info["user_name"]." at ".$db_config["site_url"];
								$mail->Body = "Hello ".$user_info["user_name"]."<br />Please <a href='".$db_config["site_url"]."/confirm.php?u=".$user_info["user_id"]."&c=".$user_info["user_activecode"]."'>click here</a> to confirm your email address or copy and pass the bellow url to your browser to confirm your email address<br /><br />".$db_config["site_url"]."/confirm.php?u=".$user_info["user_id"]."&c=".$user_info["user_activecode"]; //HTML Body
								$mail->MsgHTML($mail->Body);
								$mail->AddAddress($user_info["user_mail"]);
								$mail->IsHTML(true);
								if (!$mail->Send()) {
									$registerResult = "<span class=\"error\">Your account has been created successful but we can not send the email cofnirmation. Please send the bellow information to the administrator:<br/>".$mail->ErrorInfo."</span>";
								} else {
									$registerResult = "<span class=\"success\">Welcome [".$user_add["user_name"]."], we have sent a email with confirmation link, please check your email address to confirm it. <a href=\"./login.php\">After it, you can click here to login.</a></span>";
								}
							} else {
								$registerResult = "<script type=\"text/javascript\">setTimeout(\"window.location = './login.php'\", 1000);</script><span class=\"success\">Welcome [".$user_add["user_name"]."], click <a href=\"./login.php\">here</a> to login.</span>";
							}
							$showForm = false;
						} else {
							$registerResult = "<span class=\"error\">Register new user error.</span>";
						}
					} else {
						$registerResult = "<span class=\"error\">Register new user error.</span>";
					}
				}
				else {
					$registerResult = "<span class=\"error\">Register new user error.</span>";
				}
			}
			else {
				$registerResult = "<span class=\"error\">Please correct all information.</span>";
			}
			unset($_COOKIE['secure_code']);
		/*
		} else {
			$registerResult = "<span class=\"error\">Sorry, you have provided an invalid security code.</span>";
		}
		*/
	}
?>
				<div id="cards">
<?php
	if ($db_config["enable_voucher"]) {
?>
					<h1 style="text-align: center;">
			<u><strong><span style="color:#ff0000;">Registration is Closed.</span> <span style="color:#ff0000;">Obtain Your Voucher (Registration Code) From Support.</span></strong></u></h1>
		<h2 style="text-align: center;">
			<span style="color:#ffff00;">YH: UG.Ghost</span></h2>
		<h2 style="text-align: center;">
			<span style="color:#ffff00;">ICQ:&nbsp;624767951</span></h2>
		<h2 style="text-align: center;">
			<span style="color:#ffff00;">Jabber: Jabam@Jabber.org</span></h2>
	</body>
<?php
	}
?>
					<div class="box-title"><?=$registerResult?></div>
<?php
	if ($showForm) {
?>
								<form name="login" method="post" action="" autocomplete="off" class="form-register">
										<fieldset>
<?php
	if ($db_config["enable_voucher"]) {
?>
													<div class="form-group">
														<span class="input-icon">
															<input name="voucher" type="text" class="form-control itemformstyle" id="voucher" placeholder="Voucher/Register Code(*)" value="<?=$_POST["voucher"]?>" maxlength="24" size="24">
															<div class="error">
																<?=$voucherError?>
															</div>
														</span>	
													</div>
<?php
	}
?>
													<div class="form-group">
														<span class="input-icon">
															<input name="user_name" type="text" class="form-control itemformstyle" id="user_name" placeholder="Username" value="<?=$_POST["user_name"]?>" size="24">
															<i class="fa fa-user"></i>
															<div class="error">
																<?=$usernameError?>
															</div>
														</span>
													</div>
													<div class="form-group">
														<span class="input-icon">
															<input name="user_pass" type="password" class="form-control itemformstyle" placeholder="Password" id="user_pass" size="24">
															<i class="fa fa-lock"></i>
															<div class="error">
																<?=$passwordError?>
															</div>
														</span>	
													</div>	
													<div class="form-group">
														<span class="input-icon">
															<input name="user_pass_re" type="password" class="form-control itemformstyle" placeholder="Verify Password" id="user_pass_re" size="24">
															<i class="fa fa-lock"></i>
															<div class="error">
															</div>
														</span>	
													</div>
													<div class="form-group">
														<span class="input-icon">
															<input name="user_mail" type="text" class="form-control itemformstyle" placeholder="Email" id="user_mail" value="<?=$_POST["user_mail"]?>" size="24">
															<i class="fa fa-envelope"></i>
															<div class="error">
																<?=$emailError?>
															</div>	
													</div>
													<!--
													<div class="item-control">
														<span class="scaptcha"><img src="./captcha.php?width=100&height=40&characters=5" width="100px" height="40px" />
														</span>
															<input name="security_code" type="text" class="itemformstyle" id="security_code" maxlength="5" autocomplete="off" tabindex="3">
														<div class="error">
														</div>
													</div>
													-->
													<div class="form-actions item-control">
																<input name="btnRegister" type="submit" class="btn btn-default btn-primary pull-left btn-register" id="btnRegister" value="Register">
																<input name="btnCancel" type="button" class="btn btn-default btn-primary pull-right btn-cancel" id="btnCancel" value="Cancel" onclick="window.location='./'">
													</div>
										</fieldset>			
								</form>
<!--<table border="0" width="120%">
	<tr>
		<td><img class="footer-west" src="http://westsiders.net/demo/demo64/images/credit-cards-caae.png"></td>
	</tr>
</table>-->
<?php
	}
?>
				</div>
<?php
}
else {
?>
				<div class="box-register">
					<h1 class="box-title">USER REGISTER</H1>
									<div align="center">
										<span class="error">You have already logged with username [<?=$_SESSION["user_name"]?>], please logout to register new account or click <a href="./">here</a> to go back.</span>
									</div>
					</div>
<?php
}
require("./footer.php");
?>