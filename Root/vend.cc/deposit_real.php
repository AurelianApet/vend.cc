<?php
require("./header.php");
if ($checkLogin) {
?>
				<div id="balance">
					<div class="section_title">DEPOSIT DISCOUNT & BONUS</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<tr class="bold">
									<td class="formstyle centered">APPLY FOR GROUP</td>
									<td class="formstyle centered">APPLY FOR AMOUNT</td>
									<td class="formstyle centered">DISCOUNT PERCENT</td>
									<td class="formstyle centered">DESCRIPTION</td>
								</tr>
								<tr class="formstyle">
									<td class="bold centered">VIP</td>
									<td class="centered">All Amount</td>
									<td class="centered"><?=$db_config["vip_discount"]?>%</td>
									<td class="centered">Discount for VIP member</td>
								</tr>
<?php
		$sql = "SELECT * FROM `".TABLE_BONUS."` WHERE bonus_value > 0 ORDER BY bonus_groupid, bonus_value";
		$allBonus = $db->fetch_array($sql);
		if (count($allBonus) > 0) {
			foreach ($allBonus as $key=>$value) {
				if (!($value_groups = unserialize($value['bonus_groupid']))) {
					$value_groups = array();
				}
?>
								<tr class="formstyle">
									<td class="bold centered">
<?php
				if (count($value_groups) > 0) {
					foreach ($value_groups as $value_group) {
?>
										<span style="color:<?=$user_groups[$value_group]["group_color"]?>;"><?=$user_groups[$value_group]["group_name"]?></span>
<?php
					}
				}
?>
									</td>
									<td class="centered">
<?php
				if ($value['bonus_start'] == 0 && $value['bonus_end'] == 0) {
?>
										<span>All Amount</span>
<?php
				} else {
?>
										<span>Amount from <?=$value['bonus_start']?> to < <?=$value['bonus_end']?></span>
<?php
				}
?>
									</td>
									<td class="centered">
										<span><?=$value['bonus_value']?>%</span>
									</td>
									<td class="centered">
										<span><?=$value['bonus_description']?></span>
									</td>
								</tr>
<?php
			}
		}
		else {
?>
								<tr>
									<td colspan="6" class="error">
										No record found.
									</td>
								</tr>
<?php
		}
?>
							</tbody>
						</table>
					</div>
				</div>
				<div id="balance">
					<div class="section_title">DEPOSIT MONEY</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<form name="libertyreserve" method="post" action="./reviews.php">
									<tr>
										<td class="paygate_title">
											<p>MINIMUM PAYMENT: <span class="bold">$<?=number_format($db_config['paygate_minimum'], 2, '.', '')?></span></p>
											<p>
												<strong>AMOUNT: $</strong>
												<input name="amount" type="text" size="15" value="<?=$db_config["paygate_minimum"]?>">
											</p>
										</td>
										<td class="paygate_content">
											<p>
												<input type="submit" name="btnReview" value="Liberty Reserve">
											</p>
<?php
	if ($db_config['pm_account'] != "") {
?>
											<p>
												<input type="submit" name="btnReview" value="Perfect Money">
											</p>
<?php
	}
?>
										</td>
									</tr>
								</form>
							</tbody>
						</table>
					</div>
				</div>
<?php
	$transferResult = "";
	$amountError = "";
	$userError = "";
	if (isset($_POST["btnTransfer"])) {
		$transfer_user = trim($_POST["transfer_user"]);
		$transfer_amount= trim($_POST["transfer_amount"]);
		if ($transfer_user != "") {
				if ($transfer_amount != "") {
					$transfer_amount = doubleval($_POST["transfer_amount"]);
					if ($transfer_amount > 0.01) {
						$user_balance = $user_info["user_balance"];
						if (doubleval($user_balance) >= $transfer_amount) {
							$sql = "SELECT * FROM `".TABLE_USERS."` WHERE user_name = ?";
							$receive_transfer = $db->query_first($sql, $transfer_user);
							if ($receive_transfer && $user_info['user_id']!=$receive_transfer["user_id"]) {
								$transfers_add["transfer_senderid"] = $_SESSION["user_id"];
								$transfers_add["transfer_receiverid"] = $receive_transfer["user_id"];
								$transfers_add["transfer_amount"] = $transfer_amount;
								$transfers_add["transfer_time"] = time();
								$sender_update["user_balance"] = doubleval($user_balance)-doubleval($transfer_amount);
								$receiver_update["user_balance"] = doubleval($receive_transfer["user_balance"])+doubleval($transfer_amount);
								if($db->insert(TABLE_TRANSFERS, $transfers_add) && $db->update(TABLE_USERS, $sender_update, "user_id='".$user_info["user_id"]."'") && $db->update(TABLE_USERS, $receiver_update, "user_id=?", $receive_transfer["user_id"])) {
									$transferResult = "<span class=\"success\">Transfer money successful.</span>";

								}
								else {
									$transferResult = "<span class=\"error\">Transfer money error.</span>";

								}
							} else {
								$userError = "<span class=\"error\">This username doesn't exist or duplicate username.</span>";


							}
						} else {
							$amountError = "<span class=\"error\">You don't have enough money to send.</span>";


						}
					} else {
						$amountError = "<span class=\"error\">Amount is must greater than $0.01.</span>";


					}
				} else {
					$transferResult = "<span class=\"error\">Please enter amount of money want to transfer.</span>";


				}
		} else {
			$transferResult = "<span class=\"error\">Please enter username you want to transfer money to.</span>";


		}
	}
?>
				<div id="balance">
					<div name="transfer" id="transfer" class="section_title">TRANSFER MONEY</div>
					<div class="section_title"><?=$transferResult?></div>
					<div class="section_content">
						<table class="transfer_table">
							<tbody>
								<form name="transfermoney" method="post" action="#transfer">
									<tr>
										<td class="bold" width="150px">
											TRANSFER TO USER:
										</td>
										<td width="100px">
											<input name="transfer_user" type="text" size="15" value="<?=$_POST["transfer_user"]?>">
										</td>
										<td class="error">
											<?=$userError?>
										</td>
									</tr>
									<tr>
										<td class="bold">
											AMOUNT (Min $0.01): $
										</td>
										<td>
											<input name="transfer_amount" type="text" size="15" value="<?=$transfer_amount?>">
										</td>
										<td class="error left">
											<?=$amountError?>
										</td>
									</tr>
									<tr>
										<td colspan="3">
											<input type="submit" name="btnTransfer" value="Transfer">
										</td>
									</tr>
								</form>
							</tbody>
						</table>
					</div>
				</div>
<?php
}
else {
	require("./minilogin.php");
}
require("./footer.php");
?>