<?php
require("./header.php");
if ($checkLogin) {
	if ($_POST["amount"] != "" && doubleval($_POST["amount"]) >= doubleval($db_config["paygate_minimum"]) && ($_POST["btnReview"] == "Liberty Reserve" || $_POST["btnReview"] == "Perfect Money")) {
		if ($_POST["btnReview"] == "Liberty Reserve") {
			$paygateName = "Liberty Reserve";
			$paygateUrl = "https://sci.libertyreserve.com/en";
		} else if ($_POST["btnReview"] == "Liberty Reserve") {
			$paygateName = "Perfect Money";
			$paygateUrl = "https://perfectmoney.com/api/step1.asp";
		}
		$totalBonus = 0;
		$sql = "SELECT * FROM `".TABLE_BONUS."`";
		$records = $db->fetch_array($sql);
		if (count($records)>0) {
			foreach ($records as $value) {
				if ($value_groups = unserialize($value['bonus_groupid'])) {
					if (in_array($_SESSION["user_groupid"], $value_groups)) {
						if ((doubleval($value['bonus_start']) >= 0) && (doubleval($value['bonus_end']) == 0 || doubleval($value['bonus_start']) <= doubleval($value['bonus_end'])) && (doubleval($_POST["amount"]) >= doubleval($value['bonus_start'])) && (doubleval($value['bonus_end']) == 0 || doubleval($_POST['amount']) < doubleval($value['bonus_end']))) {
							$allBonus[] = $value;
							$totalBonus += $value["bonus_value"];
						}
					}
				}
			}
		}
		if ($user_info["user_vipexpire"] > time()) {
			$allBonus[] = array("bonus_description"=>"Discount for VIP member", "bonus_value"=>$db_config["vip_discount"]*100);
			$totalBonus += $db_config["vip_discount"]*100;
		}
		$realAmount = doubleval($_POST["amount"])*(1+$totalBonus/100);
?>
				<div id="balance">
					<div class="section_title">REVIEW YOUR ORDER</div>
					<div class="section_title">Pay by <?=$paygateName?></div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<tr class="bold">
									<td class="formstyle centered" style="width:300px;">DEPOSIT MONEY</td>
									<td class="formstyle centered">DISCOUNTS</td>
									<td class="formstyle centered">PRICE</td>
								</tr>
								<tr>
									<td class="centered">
										<form id="updateForm" action="" method="POST">
											<input type="hidden" name="btnReview" value="<?=$paygateName?>" />
											<?=$paygateName?> Amount: $<input type="text" name="amount" value="<?=$_POST["amount"]?>" size="10" /> <input type="submit" name="update" value="Update"/>
										</form>
									</td>
									<td class="centered">
<?php
			if (is_array($allBonus) && $allBonus > 0) {
				foreach ($allBonus as $value) {
?>
											<p><?=$value["bonus_description"]?> : <?=$value["bonus_value"]?>%</p>
<?php
				}
			}
?>
									</td>
									<td class="centered">
										You will get $<?=$realAmount?>
									</td>
								</tr>
								<tr>
									<td>
									</td>
									<td class="centered centered">
										Total Bonus: <?=$totalBonus?>%
									</td>
									<td class="centered centered">
										<input type="button" value="Checkout" onclick="getElementById('paymentForm').submit();"/>
									</td>
								</tr>
								<form id="paymentForm" method="POST" action="<?=$paygateUrl?>">
<?php
		if ($_POST["btnReview"] == "Liberty Reserve") {
?>
									<input type="hidden" name="lr_acc" value="<?=$db_config["lr_account"]?>">
									<input type="hidden" name="lr_store" value="<?=$db_config["lr_store_name"]?>">
									<input type="hidden" name="lr_amnt" value="<?=$_POST["amount"]?>">
									<input type="hidden" name="lr_currency" value="LRUSD">
									<input type="hidden" name="lr_comments" value="Adding $<?=$_POST["amount"]?> = $<?=$realAmount?> balance to user account: <?=$_SESSION["user_name"]?>">
									<input type="hidden" name="lr_success_url" value="<?=$db_config["site_url"]?>/mydeposits.php">
									<input type="hidden" name="lr_success_url_method" value="LINK">
									<input type="hidden" name="lr_fail_url" value="<?=$db_config["site_url"]?>/deposit.php">
									<input type="hidden" name="lr_fail_url_method" value="LINK">
									<input type="hidden" name="user_id" value="<?=$_SESSION["user_id"]?>">
<?php
		} else if ($_POST["btnReview"] == "Perfect Money") {
?>
									<input type="hidden" name="PAYEE_ACCOUNT" value="<?=$db_config["pm_account"]?>">
									<input type="hidden" name="PAYEE_NAME" value="<?=$db_config["pm_payee_name"]?>">
									<input type="hidden" name="PAYMENT_AMOUNT" value="<?=$_POST["amount"]?>">
									<input type="hidden" name="PAYMENT_UNITS" value="USD">
									<input type="hidden" name="STATUS_URL" value="<?=$db_config["site_url"]?>/paygates/pmstatus.php">
									<input type="hidden" name="PAYMENT_URL" value="<?=$db_config["site_url"]?>/mydeposits.php">
									<input type="hidden" name="PAYMENT_URL_METHOD" value="LINK">
									<input type="hidden" name="NOPAYMENT_URL" value="<?=$db_config["site_url"]?>/deposit.php">
									<input type="hidden" name="NOPAYMENT_URL_METHOD" value="LINK">
									<input type="hidden" name="SUGGESTED_MEMO" value="Adding $<?=$_POST["amount"]?> = $<?=$realAmount?> balance to user account: <?=$_SESSION["user_name"]?>">
									<input type="hidden" name="BAGGAGE_FIELDS" value="user_id">
									<input type="hidden" name="user_id" value="<?=$_SESSION["user_id"]?>">
<?php
		}
?>
								</form>
							</tbody>
						</table>
					</div>
				</div>
<?php
	}
	else {
?>
				<script type="text/javascript">setTimeout("window.location = './deposit.php'", 1000);</script>
<?php
	}
}
else {
	require("./minilogin.php");
}
require("./footer.php");
?>