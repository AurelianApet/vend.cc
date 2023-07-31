<?php
require("./header.php");
if ($checkLogin) {
	if ($_GET["act"] == "add") {
?>
				<div id="tool_manager">
					<div class="section_title">ADD NEW TOOLS</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		if (isset($_POST["tool_add_save"])) {
			$tool_import["tool_name"] = $_POST["tool_name"];
			$tool_import["tool_url"] = $_POST["tool_url"];
			if ($errorMsg == "") {
				if($db->insert(TABLE_TOOLS, $tool_import)) {
					$errorMsg = "";
				}
				else {
					$errorMsg = "Add new  Tools error.";
				}
			}
			if ($errorMsg == "") {
?>
									<script type="text/javascript">setTimeout("window.location = './tools.php'", 1000);</script>
									<tr>
										<td colspan="3" class="centered">
											<span class="success">Add new Tools successfully.</span>
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
		}
?>
								<form method="POST" action="">
									<tr>
										<td class="formstyle centered">
											<strong>TOOLS NAME</strong>
										</td>
										<td class="formstyle centered">
											<strong>TOOLS URL</strong>
										</td>
									</tr>
									<tr>
										<td class="centered">
											<span><input class="formstyle" name="tool_name" type="text" value="<?=$tool_import['tool_name']?>" /></span>
										</td>
										<td class="centered">
											<span><input class="formstyle" name="tool_url" type="text" value="<?=$tool_import['tool_url']?>" size="100" /></span>
										</td>
									</tr>
									<tr>
										<td colspan="2" class="centered">
											<input type="submit" name="tool_add_save" value="Save" /><input onclick="window.location='./tools.php'"type="button" name="tool_edit_cancel" value="Cancel" />
										</td>
									</tr>
								</form>
							</tbody>
						</table>
					</div>
				</div>
<?php
	}
	else if ($_GET["act"] == "edit" && $_GET["tool_id"] != "") {
		$tool_id = $db->escape($_GET["tool_id"]);
?>
				<div id="tool_manager">
					<div class="section_title">TOOLS EDITOR</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		if (isset($_POST["tool_edit_save"])) {
			$tool_update["tool_name"] = $_POST["tool_name"];
			$tool_update["tool_url"] = $_POST["tool_url"];
			if ($errorMsg == "") {
				if($db->update(TABLE_TOOLS, $tool_update, "tool_id='".$tool_id."'")) {
					$errorMsg = "";
				}
				else {
					$errorMsg = "Update Tools error.";
				}
			}
			if ($errorMsg == "") {
?>
									<script type="text/javascript">setTimeout("window.location = './tools.php'", 1000);</script>
									<tr>
										<td colspan="5" class="centered">
											<span class="success">Update Tools successfully.</span>
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
		$sql = "SELECT * FROM `".TABLE_TOOLS."` WHERE tool_id = '".$tool_id."'";
		$records = $db->fetch_array($sql);
		if (count($records)>0) {
			$value = $records[0];
?>
								<form method="POST" action="">
								<tr>
									<td class="formstyle centered">
										<strong>TOOLS ID</strong>
									</td>
									<td class="formstyle centered">
										<strong>TOOLS NAME</strong>
									</td>
									<td class="formstyle centered">
										<strong>TOOLS URL</strong>
									</td>
								</tr>
									<tr>
										<td class="centered">
											<span><?=$value['tool_id']?></span>
										</td>
										<td class="centered">
											<span><input class="formstyle" name="tool_name" type="text" value="<?=$value['tool_name']?>" /></span>
										</td>
										<td class="centered">
											<span><input class="formstyle" name="tool_url" type="text" value="<?=$value['tool_url']?>" size="80"/></span>
										</td>
									</tr>
									<tr>
										<td colspan="3" class="centered">
											<input type="submit" name="tool_edit_save" value="Save" /><input onclick="window.location='./tools.php'"type="button" name="tool_edit_cancel" value="Cancel" />
										</td>
									</tr>
								</form>
<?php
		}
		else {
?>
								<tr>
									<td class="tool_title">
										<span class="error">Tools ID Invalid.</span>
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
	else if ($_GET["act"] == "delete" && $_GET["tool_id"] != "") {
		$tool_id = $db->escape($_GET["tool_id"]);
?>
				<div id="tool_manager">
					<div class="section_title">TOOL DELETE</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		$sql = "DELETE FROM `".TABLE_TOOLS."` WHERE tool_id='".$tool_id."'";
		if ($db->query($sql) && $db->affected_rows > 0) {
			$errorMsg = "";
		}
		else {
			$errorMsg = "Delete Tools error.";
		}
		if ($errorMsg == "") {
?>
								<script type="text/javascript">setTimeout("window.location = './tools.php'", 1000);</script>
								<tr>
									<td class="centered">
										<span class="success">Deleted Tools successfully.</span>
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
		$sql = "SELECT count(*) FROM `".TABLE_TOOLS."`";
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
		$sql = "SELECT * FROM `".TABLE_TOOLS."` ORDER BY tool_id ASC LIMIT ".(($page-1)*$perPage).",".$perPage;
		$allTools = $db->fetch_array($sql);
?>
				<div id="tool_manager">
					<div class="section_title">TOOLS MANAGER</div>
					<div class="section_title"><a class="bold" href="?act=add">Add new Tools</a></div>
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
										<strong>TOOLS ID</strong>
									</td>
									<td class="formstyle centered">
										<strong>TOOLS NAME</strong>
									</td>
									<td class="formstyle centered">
										<strong>TOOLS URL</strong>
									</td>
									<td class="formstyle centered">
										<strong>ACTION</strong>
									</td>
								</tr>
<?php
		if (count($allTools) > 0) {
			foreach ($allTools as $key=>$value) {
				if (!($value_groups = unserialize($value['tool_groupid']))) {
					$value_groups = array();
				}
?>
								<tr class="formstyle">
									<td class="centered">
										<span><?=$value['tool_id']?></span>
									</td>
									<td class="centered">
										<span><?=$value['tool_name']?></span>
									</td>
									<td class="centered">
										<span><a href="<?=$value['tool_url']?>"><?=$value['tool_url']?></a></span>
									</td>
									<td class="centered">
										<span><a href="?act=edit&tool_id=<?=$value['tool_id']?>">Edit</a></span>
										<span><a href="?act=delete&tool_id=<?=$value['tool_id']?>" onclick="return confirm('Are you sure you want to DELETE this Tool?');">Delete</a></span>
									</td>
								</tr>
<?php
			}
		}
		else {
?>
								<tr>
									<td colspan="4" class="error">
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