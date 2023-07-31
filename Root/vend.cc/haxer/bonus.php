<?php
require("./header.php");
if ($checkLogin) {
	if ($_GET["act"] == "add") {
?>
				<div id="bonus_manager">
					<div class="section_title">ADD NEW BONUS</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		if (isset($_POST["bonus_add_save"])) {
			$bonus_import["bonus_groupid"] = serialize($_POST["bonus_groupid"]);
			$bonus_import["bonus_start"] = $_POST["bonus_start"];
			$bonus_import["bonus_end"] = $_POST["bonus_end"];
			$bonus_import["bonus_value"] = $_POST["bonus_value"];
			$bonus_import["bonus_description"] = $_POST["bonus_description"];
			if ($errorMsg == "") {
				if($db->insert(TABLE_BONUS, $bonus_import)) {
					$errorMsg = "";
				}
				else {
					$errorMsg = "Add new  Bonus error.";
				}
			}
			if ($errorMsg == "") {
?>
									<script type="text/javascript">setTimeout("window.location = './bonus.php'", 1000);</script>
									<tr>
										<td colspan="3" class="centered">
											<span class="success">Add new Bonus successfully.</span>
										</td>
									</tr>
<?php
			}
			else {
?>
									<tr>
										<td colspan="3" class="centered">
											<span class="error"><?=$errorMsg?></span>
										</td>
									</tr>
<?php
			}
			if (!($value_groups = unserialize($bonus_import['bonus_groupid']))) {
				$value_groups = array();
			}
		}
?>
								<form method="POST" action="">
									<tr>
										<td class="formstyle centered">
											<strong>BONUS USER GROUPS</strong>
										</td>
										<td class="formstyle centered">
											<strong>PRICE RANGE</strong>
										</td>
										<td class="formstyle centered">
											<strong>BONUS PERCENT</strong>
										</td>
										<td class="formstyle centered">
											<strong>BONUS DESCRIPTION</strong>
										</td>
									</tr>
									<tr>
										<td class="bold centered">
<?php
		foreach ($user_groups as $user_group_value) {
?>
											<span style="color:<?=$user_group_value["group_color"]?>;"><?=$user_group_value["group_name"]?> <input class="formstyle" name="bonus_groupid[]" type="checkbox" value="<?=$user_group_value["group_id"]?>" <?=($_POST["bonus_add_save"]=="" || in_array($user_group_value["group_id"], $value_groups))?"checked":""?> /></span><br/>
<?php
		}
?>
										</td>
										<td class="centered">
											<span>Amount from <input class="formstyle" name="bonus_start" type="text" value="<?=$bonus_import['bonus_start']?>" size="3" /> to < <input class="formstyle" name="bonus_end" type="text" value="<?=$bonus_import['bonus_end']?>" size="3" /></span><br/>
											<i>(Amount to < 0 mean Amount to unlimited)</i>
										</td>
										<td class="centered">
											<span><input class="formstyle" name="bonus_value" type="text" value="<?=$bonus_import['bonus_value']?>" size="3" />%</span>
										</td>
										<td class="centered">
											<span><textarea class="formstyle" name="bonus_description"><?=$bonus_import['bonus_description']?></textarea></span>
										</td>
									</tr>
									<tr>
										<td colspan="4" class="centered">
											<input type="submit" name="bonus_add_save" value="Save" /><input onclick="window.location='./bonus.php'"type="button" name="bonus_edit_cancel" value="Cancel" />
										</td>
									</tr>
								</form>
							</tbody>
						</table>
					</div>
				</div>
<?php
	}
	else if ($_GET["act"] == "edit" && $_GET["bonus_id"] != "") {
		$bonus_id = $db->escape($_GET["bonus_id"]);
?>
				<div id="bonus_manager">
					<div class="section_title">BONUS EDITOR</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		if (isset($_POST["bonus_edit_save"])) {
			$bonus_update["bonus_groupid"] = serialize($_POST["bonus_groupid"]);
			$bonus_update["bonus_start"] = $_POST["bonus_start"];
			$bonus_update["bonus_end"] = $_POST["bonus_end"];
			$bonus_update["bonus_value"] = $_POST["bonus_value"];
			$bonus_update["bonus_description"] = $_POST["bonus_description"];
			if ($errorMsg == "") {
				if($db->update(TABLE_BONUS, $bonus_update, "bonus_id='".$bonus_id."'")) {
					$errorMsg = "";
				}
				else {
					$errorMsg = "Update Bonus error.";
				}
			}
			if ($errorMsg == "") {
?>
									<script type="text/javascript">setTimeout("window.location = './bonus.php'", 1000);</script>
									<tr>
										<td colspan="5" class="centered">
											<span class="success">Update Bonus successfully.</span>
										</td>
									</tr>
<?php
			}
			else {
?>
									<tr>
										<td colspan="5" class="centered">
											<span class="error"><?=$errorMsg?></span>
										</td>
									</tr>
<?php
			}
		}
?>
<?php
		$sql = "SELECT * FROM `".TABLE_BONUS."` WHERE bonus_id = '".$bonus_id."'";
		$records = $db->fetch_array($sql);
		if (count($records)>0) {
			$value = $records[0];
			if (!($value_groups = unserialize($value['bonus_groupid']))) {
				$value_groups = array();
			}
?>
								<form method="POST" action="">
								<tr>
									<td class="formstyle centered">
										<strong>BONUS ID</strong>
									</td>
									<td class="formstyle centered">
										<strong>BONUS USER GROUPS</strong>
									</td>
									<td class="formstyle centered">
										<strong>PRICE RANGE</strong>
									</td>
									<td class="formstyle centered">
										<strong>BONUS PERCENT</strong>
									</td>
									<td class="formstyle centered">
										<strong>BONUS DESCRIPTION</strong>
									</td>
								</tr>
									<tr>
										<td class="centered">
											<span><?=$value['bonus_id']?></span>
										</td>
										<td class="bold centered">
<?php
			foreach ($user_groups as $user_group_value) {
?>
											<span style="color:<?=$user_group_value["group_color"]?>;"><?=$user_group_value["group_name"]?> <input class="formstyle" name="bonus_groupid[]" type="checkbox" value="<?=$user_group_value["group_id"]?>" <?=(in_array($user_group_value["group_id"], $value_groups))?"checked":""?> /></span><br/>
<?php
			}
?>
										</td>
										<td class="centered">
											<span>Amount from <input class="formstyle" name="bonus_start" type="text" value="<?=$value['bonus_start']?>" size="3"/> to < <input class="formstyle" name="bonus_end" type="text" value="<?=$value['bonus_end']?>" size="3"/></span><br/>
											<i>(Amount to < 0 mean Amount to unlimited)</i>
										</td>
										<td class="centered">
											<span><input class="formstyle" name="bonus_value" type="text" value="<?=$value['bonus_value']?>" size="3"/>%</span>
										</td>
										<td class="centered">
											<span><textarea class="formstyle" name="bonus_description"><?=$value['bonus_description']?></textarea></span>
										</td>
									</tr>
									<tr>
										<td colspan="5" class="centered">
											<input type="submit" name="bonus_edit_save" value="Save" /><input onclick="window.location='./bonus.php'"type="button" name="bonus_edit_cancel" value="Cancel" />
										</td>
									</tr>
								</form>
<?php
		}
		else {
?>
								<tr>
									<td class="bonus_title">
										<span class="error">Bonus ID Invalid.</span>
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
	else if ($_GET["act"] == "delete" && $_GET["bonus_id"] != "") {
		$bonus_id = $db->escape($_GET["bonus_id"]);
?>
				<div id="tool_manager">
					<div class="section_title">BONUS DELETE</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		$sql = "DELETE FROM `".TABLE_BONUS."` WHERE bonus_id='".$bonus_id."'";
		if ($db->query($sql) && $db->affected_rows > 0) {
			$errorMsg = "";
		}
		else {
			$errorMsg = "Delete Bonus error.";
		}
		if ($errorMsg == "") {
?>
								<script type="text/javascript">setTimeout("window.location = './bonus.php'", 1000);</script>
								<tr>
									<td class="centered">
										<span class="success">Deleted Bonus successfully.</span>
									</td>
								</tr>
<?php
		}
		else {
?>
								<tr>
									<td class="centered">
										<span class="error"><?=$errorMsg?></span>
									</td>
								</tr>
<?php
		}
	}
	else {
		$sql = "SELECT count(*) FROM `".TABLE_BONUS."`";
		$totalRecords = $db->query_first($sql);
		$totalRecords = $totalRecords["count(*)"];
		$perPage = 50;
		$totalPage = ceil($totalRecords/$perPage);
		if (isset($_GET["page"])) {
			$page = $db->escape($_GET["page"]);
			if ($page < 1)
			{
				$page = 1;
			}
			else if ($page > $totalPage)
			{
				$page = 1;
			}
		}
		else
		{
			$page = 1;
		}
		$sql = "SELECT * FROM `".TABLE_BONUS."` ORDER BY bonus_id ASC LIMIT ".(($page-1)*$perPage).",".$perPage;
		$allBonus = $db->fetch_array($sql);
?>
				<div id="bonus_manager">
					<div class="section_title">BONUS MANAGER</div>
					<div class="section_title"><span class="bold large red">You can change Bonus for VIP in vip_discount value in Setting section</span></div>
					<div class="section_title"><a class="bold" href="?act=add">Add New Bonus</a></div>
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
										<strong>BONUS ID</strong>
									</td>
									<td class="formstyle centered">
										<strong>BONUS USER GROUPS</strong>
									</td>
									<td class="formstyle centered">
										<strong>PRICE RANGE</strong>
									</td>
									<td class="formstyle centered">
										<strong>BONUS PERCENT</strong>
									</td>
									<td class="formstyle centered">
										<strong>BONUS DESCRIPTION</strong>
									</td>
									<td class="formstyle centered">
										<strong>ACTION</strong>
									</td>
								</tr>
<?php
		if (count($allBonus) > 0) {
			foreach ($allBonus as $key=>$value) {
				if (!($value_groups = unserialize($value['bonus_groupid']))) {
					$value_groups = array();
				}
?>
								<tr class="formstyle">
									<td class="centered">
										<span><?=$value['bonus_id']?></span>
									</td>
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
										<span>All amount</span>
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
									<td class="centered">
										<span><a href="?act=edit&bonus_id=<?=$value['bonus_id']?>">Edit</a></span>
										<span><a href="?act=delete&bonus_id=<?=$value['bonus_id']?>" onclick="return confirm('Are you sure you want to DELETE this Bonus?');">Delete</a></span>
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
<?php
	}
?>
<?php
}
else {
	require("./minilogin.php");
}
require("./footer.php");
?>