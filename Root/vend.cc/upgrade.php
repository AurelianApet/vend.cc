<?php
require("./header.php");
if ($checkLogin && $user_info["user_groupid"] < intval(PER_UNACTIVATE)) {
	if ($user_info['user_vipexpire'] < time()) {
		$user_vipexpire = time();
	} else {
		$user_vipexpire = $user_info['user_vipexpire'];
	}
	$sql = "SELECT * FROM `".TABLE_PLANS."` ORDER BY plan_id";
	$plans = $db->fetch_array($sql);
	$allPlans = array();
	if (is_array($plans) && count($plans) > 0) {
		foreach ($plans as $plan) {
			$allPlans[$plan["plan_id"]] = $plan;
		}
	}
	if ($_POST["btnUpgradeVip"] != "") {
		$plan_id = $db->escape($_POST["plan_id"]);
		if (is_numeric($plan_id)&&$plan_id > 0) {
			if (is_array($allPlans[$plan_id])) {
				$plan_info = $allPlans[$plan_id];
				$user_balance = $user_info["user_balance"];
				if (doubleval($user_balance) >= doubleval($plan_info["plan_price"])) {
					$upgrades_add["upgrade_planid"] = $plan_id;
					$upgrades_add["upgrade_userid"] = $_SESSION["user_id"];
					$upgrades_add["upgrade_price"] = doubleval($plan_info["plan_price"]);
					$upgrades_add["upgrade_time"] = time();
					$user_update["user_balance"] = doubleval($user_balance)-doubleval($plan_info["plan_price"]);
					$user_update["user_vipexpire"] = $user_vipexpire+$plan_info["plan_perior"]*86400;
					if ($db->insert(TABLE_UPGRADES, $upgrades_add)) {
						if ($db->update(TABLE_USERS, $user_update, "user_id='".$db->escape($user_info["user_id"])."'")) {
							$user_info["user_balance"] = $user_update["user_balance"];
							$upgradeVipResult = "<script type=\"text/javascript\">setTimeout(\"window.location = './myaccount.php'\", 1000);</script><span class=\"success\">Upgrade to VIP member successful.</span>";
						} else {
							$upgradeVipResult = "<span class=\"error\">Update Credit: SQL Error, please try again.</span>";
						}
					}
					else {
						$upgradeVipResult = "<span class=\"error\">Insert Order Record: SQL Error, please try again.</span>";
					}
				}
				else {
					$upgradeVipResult = "<span class=\"error\">You don't have enough balance, please deposit more balance to upgrade.</span>";
				}
			}
			else {
				$upgradeVipResult = "<span class=\"error\">Please chose a valid plan.</span>";
			}
		}
		else {
			$upgradeVipResult = "<span class=\"error\">Please chose plan you want to upgrade.</span>";
		}
	}
?>
				<div id="upgrade_vip">
					<div class="section_title">UPGRADE TO VIP MEMBER</div>
					<div class="section_title">
<?php
	if ($user_info["user_vipexpire"] > time()) {
?>
					<class class="bold pink">Your VIP Perior is expire in <?=date("H:i:s d/M/Y", $user_info['user_vipexpire'])?></class>
<?php
	} else {
?>
					<class class="bold red">Your VIP Perior is Expired</class>
<?php
	}
?>
					</div>
					<div class="bg-success"><?=$upgradeVipResult?></div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<form name="upgrade_vip" method="POST" action="">
									<tr>
										<td class="formstyle centered">
											<strong>PLAN NAME</strong>
										</td>
										<td class="formstyle centered">
											<strong>PLAN PERIOR</strong>
										</td>
										<td class="formstyle centered">
											<strong>EXPIRE</strong>
										</td>
										<td class="formstyle centered">
											<strong>PLAN PRICE</strong>
										</td>
										<td class="formstyle centered">
										</td>
									</tr>
<?php
	if (is_array($allPlans) && count($allPlans) > 0) {
		foreach ($allPlans as $plan) {
?>
									<tr class="formstyle">
										<td class="centered bold">
											<span><?=$plan["plan_name"]?></span>
										</td>
										<td class="centered bold">
											<span><?=$plan["plan_perior"]?> Day(s)</span>
										</td>
										<td class="centered bold">
											<span><?=date("d/M/Y", $user_vipexpire+$plan["plan_perior"]*86400)?></span>
										</td>
										<td class="centered bold">
											<span><?=number_format($plan["plan_price"], 2)?></span>
										</td>
										<td class="centered bold">
											<input type="radio" name="plan_id" value="<?=$plan["plan_id"]?>"<?=($plan["plan_id"]==$_POST["plan_id"])?"checked":""?>/>
										</td>
									</tr>
<?php
		}
	}
?>
									<tr>
										<td colspan="5" class="centered">
											<p>
												<label>
													<input name="btnUpgradeVip" type="submit" class="bold" id="btnUpgradeVip" value="Purchase" />
												</label>
												<label>
													<input name="btnCancel" type="button" id="btnCancel" value="Cancel" onclick="window.location='./myaccount.php'"/>
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
}
else if ($checkLogin && $_SESSION["user_groupid"] == intval(PER_UNACTIVATE)){
	require("./miniactivate.php");
}
else {
	require("./minilogin.php");
}
require("./footer.php");
?>