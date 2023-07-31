<?php

require("./header.php");

$currentuserID=$db->escape($user_info['user_id']);

if ($checkLogin && $_SESSION["user_groupid"] < intval(PER_UNACTIVATE)) {
	$sql = "SELECT user_mail FROM `".TABLE_USERS."` WHERE user_id='".$db->escape($user_info['user_id'])."'";
	$user_mail = $db->query_first($sql);
	if ($user_mail) {
		$user_mail = $user_mail["user_mail"];
	}
?>
				<div id="balance">
					<div class="section_title">SEND SUPPORT MESSAGE</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<form action="" method="POST">
<?php

        $tsql = "SELECT messages.*, users.user_name AS message_fromuser, users.user_groupid AS message_fromgroup FROM `messages` LEFT JOIN `users` ON messages.message_fromid = users.user_id WHERE message_toid = '9233' AND message_todelete = '0' ORDER BY message_time DESC, message_id DESC LIMIT 0,25";
        $tvalue = $db->query_first($tsql);
        //var_dump($tvalue);

		if (isset($_POST["submit"])) {
			 if($_COOKIE['secure_code'] ==  hash("sha512", $_POST['security_code']."34hjhFDSFKj5g&uh34545") && !empty($_COOKIE['secure_code'])) {

			$message_touser = $_POST["message_touser"];
			$message_subject = htmlentities($_POST["message_subject"]);
			$message_message = $_POST["message_message"];
			if ($message_subject == "") {
				$errorMsg = "Please enter message subject";
			} else if ($message_message == "") {
				$errorMsg = "Please enter message body";
			} else {
				$sql = "SELECT user_id FROM `".TABLE_USERS."` WHERE user_groupid = '".intval(PER_ADMIN)."' ORDER BY user_id ASC";
				if ($value = $db->query_first($sql)) {
				//echo (' 2 From: '.$_SESSION["user_id"].'  TO: '.$value["user_id"]);

					$message_import["message_fromid"] = $currentuserID; //$_SESSION["user_id"];
					$message_import["message_toid"] = $value["user_id"];
					$message_import["message_subject"] = htmlentities($_POST["message_subject"]);
					$message_import["message_message"] = $_POST["message_message"];
					$message_import["message_time"] = time();
					$errorMsg = "";
					$message_toid = $message_import["message_toid"];
                    ?>
                    
                    <script type="text/javascript">
                    alert('Message sent');
                    </script>
                    
                    <?php
				} else {
					$errorMsg = "Cannot found any administrator to send support message";
				}
			}
			}
			else {
				$errorMsg = "Sorry, you have provided an invalid security code.";
			}
			if ($errorMsg == "") {
			foreach($message_import as $key=>$value)
				{
				$message_import[$key]=htmlspecialchars(addslashes($value));
				}
				if($db->insert(TABLE_MESSAGES, $message_import)) {
					$errorMsg = "";
				}
				else {
					$errorMsg = "Send new message error.";
				}
			}
			if ($errorMsg == "123123") {
?>
									<script type="text/javascript">setTimeout("window.location = './mymessages.php?act=sent'", 1000);</script>
									<tr>
										<td colspan="2">
											<span class="success">Send support message successful.</span>
										</td>
									</tr>
<?php
			} else {
?>
									<tr>
										<td colspan="2">
											<span class="error"><?=$errorMsg?></span>
										</td>
									</tr>
<?php
			}
		}
?>
									<tr>
										<td>
											Subject:
										</td>
										<td>
											<input id="subject-west" type="text" name="message_subject" value="<?=$message_subject?>" size="50"/>
										</td>
									</tr>
									<tr>
										<td>
											Message:
										</td>
										<td class="left message_message">
											<textarea name="message_message" class="message_message"><?=$message_message?></textarea>
										</td>
									</tr>
									<tr>
									<td>
									<img src="./captcha.php?width=100&height=40&characters=5" width="100px" height="30px" style="border-radius: 5px">
									</td>
									<td class="left bold">
									&nbsp;&nbsp;<input name="security_code" type="text" class="formstyle" id="security_code" maxlength="5" style="border-radius: 5px">
									</td>
									<tr>
										<td colspan="2">
											<input type="submit" name="submit" value="Send Support Message" /> | <input type="reset" name="reset" value="Reset" />
										</td>
									</tr>
								</form>
							</tbody>
						</table>
					</div>
				</div>
				<div id="balance">
					<div class="section_content">
						<table class=" content_table">
							<tbody>
<?php
	if ($db_config["support_yahoo1"] != "") {
?>
								<tr>
									<td class="support_title">
										<span>Yahoo support 1:</span>
									</td>
									<td class="support_content">
										<a href="ymsgr:sendIM?<?=$db_config["support_yahoo1"]?>""><?=$db_config["support_yahoo1"]?> <!--img src="http://opi.yahoo.com/online?u=<?//=$db_config["support_yahoo1"]?>&t=8" border="0" width="55px" height="40px;" VALIGN="MIDDLE"  /--></a>
									</td>
								</tr>
<?php
	}
	if ($db_config["support_yahoo2"] != "") {
?>
								<tr>
									<td class="support_title">
										<span>Yahoo support 2:</span>
									</td>
									<td class="support_content">
										<a href="ymsgr:sendIM?<?=$db_config["support_yahoo2"]?>""><?=$db_config["support_yahoo2"]?> <!--img src="http://opi.yahoo.com/online?u=<?//=$db_config["support_yahoo1"]?>&t=8" border="0" width="55px" height="40px;" VALIGN="MIDDLE"  /--></a>
									</td>>
								</tr>
<?php
	}
	if ($db_config["support_icq"] != "") {
?>
								<tr>
									<td class="support_title">
										<span>ICQ support:</span>
									</td>
									<td class="support_content">
										<a href="http://www.icq.com/people/cmd.php?uin=<?=$db_config["support_icq"]?>&action=message"><?=$db_config["support_icq"]?></a>
									</td>
								</tr>
<?php
	}
	if ($db_config["support_skype"] != "") {
?>
								<tr>
									<td class="support_title">
										<span>Skype support:</span>
									</td>
									<td class="support_content">
										<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
										<a href="skype:<?=$db_config["support_skype"]?>?call"?>?call"><img src="http://download.skype.com/share/skypebuttons/buttons/call_blue_white_124x52.png" style="border: none;" width="124" height="52" alt="Skype Me™!" /></a>
									</td>
								</tr>
<?php
	}
?>
							</tbody>
						</table>
					</div>
				</div>
<?php
}
else if ($checkLogin && $_SESSION["user_groupid"] == intval(PER_UNACTIVATE)){
	require("./miniactivate.php");
}
else {
	require("./minilogin.php");
}
require("./footer.php");
?>