<?php
require("./header.php");
if ($checkLogin) {
	$sql = "SELECT count(*) FROM `".TABLE_DEPOSITS."` WHERE deposit_userid = '".$db->escape($user_info["user_id"])."' ORDER BY deposit_id DESC";
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
	$sql = "SELECT * FROM `".TABLE_DEPOSITS."` WHERE deposit_userid = '".$db->escape($user_info["user_id"])."' ORDER BY deposit_id DESC LIMIT ".(($page-1)*$perPage).",".$perPage;
	$list_deposits = $db->fetch_array($sql);
?>
				<div id="check_history">
					<div class="section_title">DESPOSITS HISTORY</div>
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
										<strong>DEPOSIT ID</strong>
									</td>
									<td class="formstyle centered">
										<strong>DATE</strong>
									</td>
									<td class="formstyle centered">
										<strong>METHOD</strong>
									</td>
									<td class="formstyle centered">
										<strong>AMOUNT</strong>
									</td>
									<td class="formstyle centered">
										<strong>BEFORE BALANCE</strong>
									</td>
									<td class="formstyle centered">
										<strong>AFTER BALANCE</strong>
									</td>
									<td class="formstyle centered">
										<strong>PAYGATE PROOF</strong>
									</td>
								</tr>
<?php
	if (count($list_deposits) > 0) {
		foreach ($list_deposits as $key=>$value) {
?>
								<tr class="formstyle">
									<td class="centered">
										<span><?=$value['deposit_id']?></span>
									</td>
									<td class="centered">
										<span><?=date("H:i:s d/M/Y", $value['deposit_time'])?></span>
									</td>
									<td class="centered">
										<span><?=$value['deposit_paygate']?></span>
									</td>
									<td class="bold centered">
										<span>$<?=$value['deposit_amount']?></span>
									</td>
									<td class="centered">
										<span>$<?=$value['deposit_before']?></span>
									</td>
									<td class="centered">
										<span>$<?=$value['deposit_before'] + $value['deposit_amount']?></span>
									</td>
									<td class="centered">
										<span><?=$value['deposit_proof']?></span>
									</td>
								</tr>
<?php
		}
	}
	else {
?>
								<tr>
									<td colspan="7" class="error">
										You don't have any deposit yet.
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