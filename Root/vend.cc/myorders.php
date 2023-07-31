<?php
require("./header.php");
if ($checkLogin) {
	$sql = "SELECT count(*) FROM `".TABLE_ORDERS."` WHERE order_userid = '".$db->escape($_SESSION["user_id"])."' ORDER BY order_id DESC";
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
	$sql = "SELECT * FROM `".TABLE_ORDERS."` WHERE order_userid = '".$db->escape($_SESSION["user_id"])."' ORDER BY order_id DESC LIMIT ".(($page-1)*$perPage).",".$perPage;
	$order_historys = $db->fetch_array($sql);
?>
				<div id="check_history">
					<div class="section_title">ORDERS HISTORY</div>
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
										<strong>ORDER ID</strong>
									</td>
									<td class="formstyle centered">
										<strong>DATE</strong>
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
										<strong>ACTION</strong>
									</td>
								</tr>
<?php
	if (count($order_historys) > 0) {
		foreach ($order_historys as $key=>$value) {
?>

								<tr class="formstyle">
									<td class="centered">
										<span><?=$value['order_id']?></span>
									</td>
									<td class="centered">
										<span><?=date("H:i:s d/M/Y", $value['order_time'])?></span>
									</td>
									<td class="bold centered">
										<span>$<?=$value['order_total']?></span>
									</td>
									<td class="centered">
										<span>$<?=$value['order_before']?></span>
									</td>
									<td class="centered">
										<span>$<?=$value['order_before'] - $value['order_total']?></span>
									</td>
									<td class="bold centered">
										<span><a href="./showcart.php?order_id=<?=$value['order_id']?>" class="viewcard">View Shopping Cart</a></span>
									</td>
								</tr>
<?php
		}
	}
	else {
?>
								<tr>
									<td colspan="8" class="error">
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