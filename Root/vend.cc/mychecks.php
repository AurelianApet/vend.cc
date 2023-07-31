<?php
require("./header.php");
if ($checkLogin) {
	if ($_POST["clear_history"] != "") {
		$check_update["check_hide"] = "1";
		if ($db->update(TABLE_CHECKS, $check_update, "check_userid='".$db->escape($user_info["user_id"])."'")) {
			$clearHistoryResult = "<span class=\"success\">Clear check history successfully</span>";
		}
		else {
			$clearHistoryResult = "<span class=\"error\">Clear check history error</span>";
		}
	}
	$sql = "SELECT count(*) FROM `".TABLE_CHECKS."` JOIN `".TABLE_CARDS."` ON ".TABLE_CHECKS.".check_userid = ".$user_info["user_id"]." AND ".TABLE_CHECKS.".check_cardid = ".TABLE_CARDS.".card_id AND ".TABLE_CHECKS.".check_hide = '0' ORDER BY check_id DESC";
	$totalRecords = $db->query_first($sql);
	$totalRecords = $totalRecords["count(*)"];
	$perPage = 30;
	$totalPage = ceil($totalRecords/$perPage);
	if (isset($_GET["page"])) {
		$page = $db->escape($_GET["page"]);
		if ($page < 1) {
			$page = 1;
		}
		else if ($page > $totalPage) {
			$page = 1;
		}
	} else {
		$page = 1;
	}
	$sql = "SELECT `".TABLE_CHECKS."`.*, `".TABLE_CARDS."`.card_id, AES_DECRYPT(`".TABLE_CARDS."`.card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number FROM `".TABLE_CHECKS."` JOIN `".TABLE_CARDS."` ON ".TABLE_CHECKS.".check_userid = ".$user_info["user_id"]." AND ".TABLE_CHECKS.".check_cardid = ".TABLE_CARDS.".card_id AND ".TABLE_CHECKS.".check_hide = '0' ORDER BY check_id DESC LIMIT ".(($page-1)*$perPage).",".$perPage;
	$check_historys = $db->fetch_array($sql);
?>
				<div id="check_history">
					<div class="section_title">CHECKS HISTORY</div>
					<div class="section_page_bar">
<?php
	if ($totalRecords > 0) {
		echo "Page:";
		if ($page>1) {
			echo "<a href=\"?page=".($page-1)."\">&lt;</a>";
			echo "<a href=\"?page=1\">1</a>";
		}
		if ($page>3) {
			echo "...";
		}
		if (($page-1) > 1) {
			echo "<a href=\"?page=".($page-1)."\">".($page-1)."</a>";
		}
		echo "<input type=\"TEXT\" class=\"page_go\" value=\"".$page."\" onchange=\"window.location.href='?page='+this.value\"/>";
		if (($page+1) < $totalPage) {
			echo "<a href=\"?page=".($page+1)."\">".($page+1)."</a>";
		}
		if ($page < $totalPage-2) {
			echo "...";
		}
		if ($page<$totalPage) {
			echo "<a href=\"?page=".$totalPage."\">".$totalPage."</a>";
			echo "<a href=\"?page=".($page+1)."\">&gt;</a>";
		}
	}
?>
					</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<tr>
									<td class="formstyle centered">
										<strong>DATE</strong>
									</td>
									<td class="formstyle centered">
										<strong>CARD NUMBER</strong>
									</td>
									<td class="formstyle centered">
										<strong>CHECK RESULT</strong>
									</td>
								</tr>
<?php
	if (count($check_historys) > 0) {
		foreach ($check_historys as $key=>$value) {
?>
								<tr class="formstyle">
									<td class="centered">
										<span><?=date("H:i:s d/M/Y", $value['check_time'])?></span>
									</td>
									<td class="centered bold">
										<span><?=$value['card_number']?></span>
									</td>
									<td class="centered bold">
<?php
			switch ($value['check_result']) {
				case strval(CHECK_VALID):
					echo "<span class=\"green bold\">APPROVED</span>";
					break;
				case strval(CHECK_INVALID):
					echo "<span class=\"red bold\">TIMEOUT</span>";
					break;
				case strval(CHECK_REFUND):
					echo "<span class=\"pink bold\">DECLINE</span>";
					break;
				case strval(CHECK_UNKNOWN):
					echo "<span class=\"blue bold\">UNKNOWN</span>";
					break;
				case strval(CHECK_WAIT_REFUND):
					echo "<span class=\"pink bold\">WAIT REFUND</span>";
					break;
				default :
					echo "<span class=\"black bold\">UNCHECK</span>";
					break;
			}
?>
									</td>
								</tr>
<?php
		}
?>
								<tr>
									<td colspan="3" class="centered">
										<label>
											<form action="" method="POST">
												<input name="clear_history" type="submit" id="clear_history" value="Clear Check History" >
											</form>
										</label>
									</td>
								</tr>
<?php
	}
	else {
?>
								<tr>
									<td colspan="3" class="error">
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
<?php
}
else {
	require("./minilogin.php");
}
require("./footer.php");
?>