<?php
require("./header.php");
if ($checkLogin) {
	if ($getinfoError == "" && $_POST["btnChangePwd"] != "") {
		if ($_POST["user_pass"] == "") {
			$changeInfoResult = "<span class=\"error\">Please enter your current password</span>";
		}
		elseif($user->password_crypt($_POST["user_pass"],$user_info["user_salt"]) == $user_info["user_pass"]) {
			switch (passwordFaild($_POST["user_pass_new"], $_POST["user_pass_new_re"])) {
				case 0:
				
					$user_update["user_salt"] = $user->new_salt();
					$user_update["user_pass"] = $user->password_crypt($_POST["user_pass_new"],$user_update["user_salt"]);
					if($db->update(TABLE_USERS, $user_update, "user_id='".$db->escape($user_info["user_id"])."'")) {
						$changeInfoResult = "<span class=\"success\">Change password successfully.</span>";
						$user_info["user_salt"] = $user_update["user_salt"];
						$user_info["user_pass"] = $user_update["user_pass"];
						
					}
					else {
						$changeInfoResult = "<span class=\"error\">Update user information error, please try again.</span>";
					}
					break;
				case 1:
					$changeInfoResult = "<span class=\"error\">New Password is too short.</span>";
					break;
				case 2:
					$changeInfoResult = "<span class=\"error\">New Password is too long.</span>";
					break;
				case 3:
					$changeInfoResult = "<span class=\"error\">New Password doesn't match.</span>";
					break;
			}
		}
		else {
			$changeInfoResult = "<span class=\"error\">Wrong password, please try again</span>";
		}
	}
	if ($getinfoError == "" && $_POST["btnChangeEmail"] != "") {
		if ($_POST["user_pass"] == "") {
			$changeInfoResult = "<span class=\"error\">Please enter your current password</span>";
		}
		else if ($user->password_crypt($_POST["user_pass"],$user_info["user_salt"]) == $user_info["user_pass"]) {
			switch (emailFaild($_POST["user_mail"])) {
				case 0:
					$user_update["user_mail"] = $_POST["user_mail"];
					if($db->update(TABLE_USERS, $user_update, "user_id='".$db->escape($user_info["user_id"])."'")) {
						$changeInfoResult = "<span class=\"success\">Change email address successfully.</span>";
						$user_info["user_mail"] = $user_update["user_mail"];
					}
					else {
						$changeInfoResult = "<span class=\"error\">Update user information error, please try again.</span>";
					}
					break;
				case 1:
					$changeInfoResult = "<span class=\"error\">Invalid e-mail address.</span>";
					break;
			}
		}
		else {
			$changeInfoResult = "<span class=\"error\">Wrong password, please try again</span>";
		}
	}
	if ($getinfoError == "" && $_POST["btnChangeYahoo"] != "") {
		if ($_POST["user_pass"] == "") {
			$changeInfoResult = "<span class=\"error\">Please enter your current password</span>";
		}
		else if ($user->password_crypt($_POST["user_pass"],$user_info["user_salt"]) == $user_info["user_pass"]) {
			$user_update["user_yahoo"] = $_POST["user_yahoo"];
			if($db->update(TABLE_USERS, $user_update, "user_id='".$db->escape($user_info["user_id"])."'")) {
				$changeInfoResult = "<span class=\"success\">Change Yahoo id successfully.</span>";
				$user_info["user_yahoo"] = $user_update["user_yahoo"];
			}
			else {
				$changeInfoResult = "<span class=\"error\">Update user information error, please try again.</span>";
			}
		}
		else {
			$changeInfoResult = "<span class=\"error\">Wrong password, please try again</span>";
		}
	}
	if ($getinfoError == "" && $_POST["btnChangeICQ"] != "") {
		if ($_POST["user_pass"] == "") {
			$changeInfoResult = "<span class=\"error\">Please enter your current password</span>";
		}
		else if ($user->password_crypt($_POST["user_pass"],$user_info["user_salt"]) == $user_info["user_pass"]) {
			$user_update["user_icq"] = $_POST["user_icq"];
			if($db->update(TABLE_USERS, $user_update, "user_id='".$db->escape($user_info["user_id"])."'")) {
				$changeInfoResult = "<span class=\"success\">Change ICQ id successfully.</span>";
				$user_info["user_icq"] = $user_update["user_icq"];
			}
			else {
				$changeInfoResult = "<span class=\"error\">Update user information error, please try again.</span>";
			}
		}
		else {
			$changeInfoResult = "<span class=\"error\">Wrong password, please try again</span>";
		}
	}
?>
			<form action="" method="POST">
				<div id="myaccount">
					<div class="section_title">ACCOUNT INFORMATION</div>
					<div class="bg-danger"><?=$getinfoError?></div>
					<div class="bg-success"><?=$changeInfoResult?></div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<tr class="bold">
									<td class="centered">
										Username: <?=$_SESSION["user_name"]?>
									</td>
									<td class="myaccount_content centered">
										Account type: <span style="color:<?=$user_groups[$_SESSION["user_groupid"]]["group_color"]?>;"><?=$user_groups[$_SESSION["user_groupid"]]["group_name"]?></span>
									</td>
                                    
                               	</tr>
                                <tr class="bold"> 
									<td class="myaccount_content centered">
<?php
	if ($user_info["user_vipexpire"] > time()) {
?>
					<class class="bold pink">VIP expire in <?=date("H:i:s d/M/Y", $user_info['user_vipexpire'])?></class>
<?php
	} else {
?>
					<class class="bold red">VIP Expired <?=date("H:i:s d/M/Y", $user_info['user_vipexpire'])?></class>
<?php
	}
?>
										<a href="./upgrade.php">(Renew)</a>
									</td>
                                    <td class="centered">
										You will get <?=$db_config["commission"]*100?>% when your referencal deposit money.
									</td>
								</tr>
								<tr class="bold">
									<td class="centered" >
										Reference link:
                                     </td>
                                     <td>
                                      <input type="TEXT" size="30" value="<?=$db_config["site_url"]?>/register.php?r=<?=$_SESSION["user_name"]?>"/>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="section_content">
						<table class="content_table">
								<tbody>
                                <tr class="bold centered">
									<td>
										Current Password
                                    </td>    
                                    <td>
										<input name="user_pass" type="password" value="" />
									</td>
								</tr>
									<tr class="bold centered">
										<td>
											New Password
										</td>
										<td>
											<input name="user_pass_new" type="password" value="">
										</td>
                                      </tr>
                                      <tr class="bold centered">
										<td>
											Current Email Address:
										</td>
										<td>
											&nbsp&nbsp&nbsp<?=$user_info["user_mail"]?>
										</td>
									</tr>
									<tr class="bold centered">
										<td>
											Verify New Password
										</td>
										<td>
											<input name="user_pass_new_re" type="password" value="">
										</td>
                                      </tr>
                                      <tr class="bold centered">
										<td>
											New Email Address
										</td>
										<td>
											<input name="user_mail" type="text" value="">
										</td>
									</tr>
									<tr>
										<td class="centered">
											<input type="submit" name="btnChangePwd" value="Change Password" />
										</td>
										<td class="centered">
											<input type="submit" name="btnChangeEmail" value="Change Email Address" />
										</td>
									</tr>
								</tbody>
						</table>
					</div>
					<div class="section_content">
						<table class="content_table">
								<tbody>
									<tr class="bold centered">
										<td>
											Current Yahoo ID:
										</td>
										<td>
											<?=$user_info["user_yahoo"]?>
										</td>
                                       </tr>
                                       <tr class="bold centered">
										<td>
											Current ICQ ID:
										</td>
										<td>
											<?=$user_info["user_icq"]?>
										</td>
									</tr>
									<tr class="bold centered">
										<td>
											New Yahoo ID
										</td>
										<td>
											<input name="user_yahoo" type="text" value="">
										</td>
                                       </tr>
                                       <tr class="bold centered">
										<td>
											New ICQ ID
										</td>
										<td>
											<input name="user_icq" type="text" value="">
										</td>
									</tr>
									<tr>
										<td  class="centered">
											<input type="submit" name="btnChangeYahoo" value="Change Yahoo ID" />
										</td>
										<td class="centered">
											<input type="submit" name="btnChangeICQ" value="Change ICQ ID" />
										</td>
									</tr>
								</tbody>
						</table>
					</div>
				</div>
			</form>
<?php
}
else {
	require("./minilogin.php");
}
require("./footer.php");
?>