<?php
require_once("./header.php");
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
?>
				<div id="upgrade_vip">
					<div class="section_title">CONFIRM EMAIL ADDRESS</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<form name="upgrade_vip" method="POST" action="">
									<tr>
										<td class="centered error">
											Currently we don't allow to resend email confirmation, if you don't get the confirmation email please contact support to confirm it manually.
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