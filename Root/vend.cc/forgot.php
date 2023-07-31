<?php
require("./header.php");
require_once("./includes/class.phpmailer.php");
function genPassword($length = 8) {
    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
    $string = "";    
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    return $string;
}
if (!$checkLogin) {
	if ($_POST["btnForgot"] != "") {
		switch (emailFaild($_POST["user_mail"])) {
			case 0:
				$emailError = "";
				$forgot_user_mail = $db->escape($_POST["user_mail"]);
				break;
			case 1:
				$emailError = "Invalid e-mail address.";
				break;
			case 2:
		}
		switch (usernameFaild($_POST["user_name"])) {
			case 0:
				$usernameError = "";
				$forgot_user_name = $db->escape($_POST["user_name"]);
				break;
			case 1:
				$usernameError = "Username is too short.";
				break;
			case 2:
				$usernameError = "Username is too long.";
				break;
		}
		if ($emailError == "" && $usernameError == "") {
			$new_password = genPassword();
			$user_update["user_salt"] = $user->new_salt();
			$user_update["user_pass"] = $user->password_crypt($new_password, $user_update["user_salt"]);
			if($db->update(TABLE_USERS, $user_update, "user_name= ? AND user_mail= ?", array($forgot_user_name, $forgot_user_mail))) {
				if($db->affected_rows == 1){
					
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
					$mail->Subject = "New password for [".$forgot_user_name."] at [".$db_config["site_url"]."]";
					$mail->Body = "Hello $forgot_user_name,<br/> Your new password is:$new_password"; //HTML Body
					$mail->MsgHTML($mail->Body);
					$mail->IsHTML(true);
					$mail->AddAddress($forgot_user_mail);
					

					if (!$mail->Send()) {
						$forgotResult = "<span class=\"error\">Cannot send email. <br/>".$mail->ErrorInfo."</span>";
					} else {
						$forgotResult = "<span class=\"success\">Your new password has been sent to your email address.</span>";
					}
					unset($mail);
				}
				else{
					$forgotResult = "<span class=\"error\">Wrong information.</span>";
				}
			}
			else {
				$forgotResult = "<span class=\"error\">Update User error.</span>";
			}
		}
		else {
			$forgotResult = "<span class=\"error\">Please correct all information.</span>";
		}
	}
?>
				<div class="box-login">
					<h1 class="box-title"><?=$forgotResult?></h1>
								<form name="login" method="post" action="" autocomplete="off" class="form-lostpass">
													<div class="item-control">
														<label>USERNAME:</label>
															<input name="user_name" type="text" class="itemformstyle" id="user_name" value="<?=$_POST["user_name"]?>">
														<div class="error">
															<?=$usernameError?>
														</div>
													</div>
													<div class="item-control">
														<label>EMAIL:</label>
															<input name="user_mail" type="text" class="itemformstyle" id="user_mail" value="<?=$_POST["user_mail"]?>">
														<div class="error">
															<?=$emailError?>
														</div>
													</div>
													<div class="item-control item-control-sub">
																<input name="btnForgot" type="submit" class="btn-reset" id="btnForgot" value="Reset Password">
																<input name="btnCancel" type="button" class="btn-cancel" id="btnCancel" value="Cancel" onclick="window.location='./'">
													</div>
								</form>

<!--<table border="0" width="120%">
	<tr>
		<td><img class="footer-west" src="http://westsiders.net/demo/demo64/images/credit-cards-caae.png"></td>
	</tr>
</table>-->
				</div>
<?php
}
else {
?>
				<div id="cards">
					<div class="section_title">USER FORGOT PASSWORD</div>
					<div class="section_content">
						<table class="content_table" style="border:none;">
							<tbody>
								<tr>
									<td align="center">
										<span class="error">You have already logged with username [<?=$_SESSION["user_name"]?>], click <a href="./">here</a> to go back.</span>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
<?php
}
require("./footer.php");
?>