<?php
require_once("./header.php");
require_once("./includes/class.phpmailer.php");
if (!$checkLogin) {
	if ($_GET["u"] != "" && $_GET["c"] != "") {
		$sql = "SELECT * FROM `".TABLE_USERS."` WHERE user_id = ?";
		$user_info = $db->query_first($sql, $_GET["u"]);
		if (!$user_info) {
			$upgradeVipResult = "<span class=\"error\">This user is doesn't exist, please recheck your information</span>";
		} else {
			$user_activecode = $user_info["user_activecode"];
			if ($user_activecode == $db->escape($_GET["c"])) {
				$user_update["user_groupid"] = PER_UNACTIVATE;
				if ($db->update(TABLE_USERS, $user_update, "user_id=? AND user_groupid='".PER_UNCONFIRM."'", $_GET["u"]) && $db->affected_rows > 0) {
					$upgradeVipResult = "<script type=\"text/javascript\">setTimeout(\"window.location = './confirm_success.php'\", 0);</script><span class=\"success\">Confirm email address successful, click here if browser not redirect.</span>";
				} else if ($user_info["user_activecode"] < PER_UNCONFIRM) {
					$upgradeVipResult = "<span class=\"error\">You account already confirmed.</span>";
				} else {
					$upgradeVipResult = "<span class=\"error\">Cannot active your account, please recheck your confirmation code or request a new confirmation email.</span>";
				}
			}
			else {
				$upgradeVipResult = "<span class=\"error\">Wrong activate code, please recheck your confirmation code or request a new confirmation email.</span>";
			}
		}
?>
				<div id="upgrade_vip">
					<div class="section_title">CONFIRM EMAIL ADDRESS</div>
					<div class="section_title"><?=$upgradeVipResult?></div>
				</div>
<?php
	} else {
		if (isset($_POST["btnUpgradeVip"])) {
			if (trim($_POST['user_name']) == "") {
				$upgradeVipResult = "<span class=\"error\">Please enter username.</span>";
			} elseif ($_SESSION['security_code'] == $_POST['security_code'] && !empty($_SESSION['security_code'])) {
				/*if (!isset($_COOKIE["confirm_count"])) {
					setcookie("confirm_count", 1, time() + 1800);
				} else {
					setcookie("confirm_count", $_COOKIE["confirm_count"]+1, time() + 1800);
				}
				if ($_COOKIE["confirm_count"] < 4) {*/
					$sql = "SELECT * FROM `".TABLE_USERS."` WHERE user_name = ?";
					$user_info = $db->query_first($sql, $_POST["user_name"]);
					if (!$user_info) {
						$upgradeVipResult = "<span class=\"error\">This user is doesn't exist, please recheck your information</span>";
					} else if ($user_info["user_groupid"] != PER_UNCONFIRM){
						$upgradeVipResult = "<span class=\"error\">This account doesn't need to confirm email address.</span>";
					} else {
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
							$upgradeVipResult = "<span class=\"error\">".$mail->ErrorInfo."</span>";
						} else {
							$upgradeVipResult = "<span class=\"success\">An activate code have been sent to your email.</span>";
						}
					}
				//} else {
				//	$upgradeVipResult = "<span class=\"error\">You have tried to confirmed too many time, please contact support to reconfirm your account.</span>";
				//}
			} else {
				$upgradeVipResult = "<span class=\"error\">Sorry, you have provided an invalid security code.</span>";
			}
		}
?>
				<div id="upgrade_vip">
					<div class="section_title">CONFIRM EMAIL ADDRESS</div>
					<div class="section_title"><?=$upgradeVipResult?></div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<form name="upgrade_vip" method="POST" action="">
									<tr>
										<td class="centered">
											To become an confirm member of our system, you has to confirm your email address.
										</td>
									</tr>
									<tr>
										<td>
											<table style="width:200px;margin: 0 auto;">
												<tbody>
													<tr>
														<td class="centered">
															Username:
														</td>
														<td class="centered">
															<input name="user_name" type="text" class="formstyle" id="user_name" value="<?=$_POST["user_name"]?>" size="24">
														</td>
														<td class="error">
															<?=$referenceError?>
														</td>
													</tr>
													<tr>
														<td class="centered">
															<img src="./captcha.php?width=100&height=40&characters=5" width="100px" height="40px" />
														</td>
														<td class="centered">
															<input name="security_code" type="text" class="formstyle" id="security_code" maxlength="5" size="24">
														</td>
														<td class="error">
														</td>
													</tr>
													<tr>
														<td colspan="3" class="centered">
															<input name="btnUpgradeVip" type="submit" class="bold" id="btnUpgradeVip" value="Resend confirmation code" />
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
								</form>
							</tbody>
						</table>
					</div>
				</div>
<?php
	}
} else {
?>
				<div id="upgrade_vip">
					<div class="section_title">CONFIRM EMAIL ADDRESS</div>
					<div class="section_title error"><a href="./login.php">Your account already confirmed. Click here to login.</a></div>
				</div>
<?php
}
require("./footer.php");
?>