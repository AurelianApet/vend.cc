<?php
if ($checkLogin) {
	if ($user_info["user_groupid"] >= intval(PER_UNACTIVATE)) {
		if ($_POST["btnUpgradeVip"] != "") {
			if ($db != NULL) {
				$user_balance = $user_info["user_balance"];
				if (doubleval($user_balance) >= doubleval($db_config["activate_fee"])) {
					$user_update["user_balance"] = doubleval($user_balance)-doubleval($db_config["activate_fee"])+doubleval($db_config["activate_balance"]);
					$user_update["user_groupid"] = PER_USER;
					if ($db->update(TABLE_USERS, $user_update, "user_id='".$db->escape($user_info["user_id"])."'")) {
						$user_info["user_balance"] = $user_update["user_balance"];
						$upgradeVipResult = "<script type=\"text/javascript\">setTimeout(\"window.location = './activate_success.php'\", 0);</script><span class=\"success\">Activate account successful, click here if browser not redirect.</span>";
					} else {
						$upgradeVipResult = "<span class=\"error\">Update Credit: SQL Error, please try again.</span>";
					}
				}
				else {
					$upgradeVipResult = "<span class=\"error\">You don't have enough balance, please deposit more balance to activate your account.</span>";
				}
			} else {
				header("Location: ./activate.php");
			}
		}
?>
				<div id="upgrade_vip">
					<div class="section_title">ACTIVATE ACCOUNT</div>
					<div class="section_title"><?=$upgradeVipResult?></div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<form name="upgrade_vip" method="POST" action="https://sci.libertyreserve.com/en">
									<tr>
										<td class="centered large">
											To become an activated member of our system, you has to pay <span class="bold red">$<?=$db_config["activate_fee"]?></span> to activate your account. After activate, your account will get <span class="bold red">$<?=$db_config["activate_balance"]?></span> on your account.
										</td>
									</tr>
									<tr>
										<td colspan="5" class="centered">
											<p>
												<label>
													<input type="hidden" name="lr_acc" value="<?=$db_config["lr_account"]?>">
													<input type="hidden" name="lr_store" value="<?=$db_config["lr_store_name"]?>">
													<input type="hidden" name="lr_amnt" value="<?=$db_config["activate_fee"]?>">
													<input type="hidden" name="lr_currency" value="LRUSD">
													<input type="hidden" name="lr_comments" value="Activate account: <?=$user_info["user_name"]?>">
													<input type="hidden" name="lr_success_url" value="<?=$db_config["site_url"]?>/mydeposits.php">
													<input type="hidden" name="lr_success_url_method" value="LINK">
													<input type="hidden" name="lr_fail_url" value="<?=$db_config["site_url"]?>/deposit.php">
													<input type="hidden" name="lr_fail_url_method" value="LINK">
													<input type="hidden" name="lr_status_url" value="<?=$db_config["site_url"]?>/paygates/lractivate.php">
													<input type="hidden" name="lr_status_url_method" value="POST">
													<input type="hidden" name="user_id" value="<?=$_SESSION["user_id"]?>">
													<input type="submit" id="btnSubmit" value="Activate"/>
												</label>
												<label>
													<input type="button" id="btnCancel" value="Cancel" onclick="window.location='./index.php'"/>
												</label>
											</p>
										</td>
									</tr>
								</form>
							</tbody>
						</table>
					</div>
				</div>
<?php
	} else{
?>
				<div id="upgrade_vip">
					<div class="section_title">ACTIVATE ACCOUNT</div>
					<div class="section_title error">Your account already activated.</div>
				</div>
<?php
	}
} else {
	require("./minilogin.php");
}
?>