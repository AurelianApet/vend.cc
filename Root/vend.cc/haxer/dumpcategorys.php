<?php
require("./header.php");
if ($checkLogin) {
	if ($_GET["act"] == "add") {
?>
				<div id="dump_category_manager">
					<div class="section_title">ADD NEW CATEGORY</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<form method="POST" action="">
<?php
		if (isset($_POST["dump_category_add_save"])) {
			$dump_category_import["dump_category_name"] = $_POST["dump_category_name"];
			if ($errorMsg == "") {
				if($db->insert(TABLE_DUMP_CATEGORYS, $dump_category_import)) {
					$errorMsg = "";
				}
				else {
					$errorMsg = "Add new Category error.";
				}
			}
			if ($errorMsg == "") {
?>
									<script type="text/javascript">setTimeout("window.location = './dump_categorys.php'", 1000);</script>
									<tr>
										<td colspan="1">
											<span class="success">Add new Category successfully.</span>
										</td>
									</tr>
<?php
			}
			else {
?>
									<tr>
										<td colspan="1">
											<span class="error"><?=$errorMsg?></span>
										</td>
									</tr>
<?php
			}
		}
?>
									<tr>
										<td class="formstyle centered">
											<strong>CATEGORY NAME</strong>
										</td>
									</tr>
									<tr>
										<td class="centered">
											<span><input class="formstyle" name="dump_category_name" type="text" value="<?=$dump_category_import['dump_category_name']?>" /></span>
										</td>
									</tr>
									<tr>
										<td colspan="1" class="centered">
											<input type="submit" name="dump_category_add_save" value="Save" /><input onclick="window.location='./dump_categorys.php'"type="button" name="dump_category_edit_cancel" value="Cancel" />
										</td>
									</tr>
								</form>
							</tbody>
						</table>
					</div>
				</div>
<?php
	} else if ($_GET["act"] == "edit" && $_GET["dump_category_id"] != "") {
		$dump_category_id = $db->escape($_GET["dump_category_id"]);
?>
				<div id="dump_category_manager">
					<div class="section_title">CATEGORY EDITOR</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		if (isset($_POST["dump_category_edit_save"])) {
			$dump_category_update["dump_category_name"] = $_POST["dump_category_name"];
			if ($errorMsg == "") {
				if($db->update(TABLE_DUMP_CATEGORYS, $dump_category_update, "dump_category_id='".$dump_category_id."'")) {
					$errorMsg = "";
				}
				else {
					$errorMsg = "Update Category error.";
				}
			}
			if ($errorMsg == "") {
?>
									<script type="text/javascript">setTimeout("window.location = './dump_categorys.php'", 1000);</script>
									<tr>
										<td colspan="2" class="centered">
											<span class="success">Update Category successfully.</span>
										</td>
									</tr>
<?php
			}
			else {
?>
									<tr>
										<td colspan="2" class="centered">
											<span class="error"><?=$errorMsg?></span>
										</td>
									</tr>
<?php
			}
		}
?>
<?php
		$sql = "SELECT * FROM `".TABLE_DUMP_CATEGORYS."` WHERE dump_category_id = '".$dump_category_id."'";
		$records = $db->fetch_array($sql);
		if (count($records)>0) {
			$value = $records[0];
?>
								<form method="POST" action="">
								<tr>
									<td class="formstyle centered">
										<strong>CATEGORY ID</strong>
									</td>
									<td class="formstyle centered">
										<strong>CATEGORY NAME</strong>
									</td>
								</tr>
									<tr>
										<td class="centered">
											<span><?=$value['dump_category_id']?></span>
										</td>
										<td class="bold centered">
											<span><input class="formstyle bold" id="dump_category_name" name="dump_category_name" type="text" value="<?=$value['dump_category_name']?>" /></span>
										</td>
									</tr>
									<tr>
										<td colspan="2" class="centered">
											<input type="submit" name="dump_category_edit_save" value="Save" /><input onclick="window.location='./dump_categorys.php'"type="button" name="dump_category_edit_cancel" value="Cancel" />
										</td>
									</tr>
								</form>
<?php
		}
		else {
?>
								<tr>
									<td class="categorys_title">
										<span class="error">Category ID Invalid.</span>
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
	else if ($_GET["act"] == "delete" && $_GET["dump_category_id"] != "") {
		$dump_category_id = $db->escape($_GET["dump_category_id"]);
?>
				<div id="dump_category_manager">
					<div class="section_title">CATEGORY DELETE</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		$sql = "DELETE FROM `".TABLE_DUMP_CATEGORYS."` WHERE dump_category_id='".$dump_category_id."'";
		if ($db->query($sql) && $db->affected_rows > 0) {
			$errorMsg = "";
		}
		else {
			$errorMsg = "Delete Categorys error.";
		}
		if ($errorMsg == "") {
?>
								<script type="text/javascript">setTimeout("window.location = './dump_categorys.php'", 1000);</script>
								<tr>
									<td class="centered">
										<span class="success">Deleted Categorys successfully.</span>
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
		$sql = "SELECT count(*) FROM `".TABLE_DUMP_CATEGORYS."`";
		$totalRecords = $db->query_first($sql);
		$totalRecords = $totalRecords["count(*)"];
		$perPage = 10;
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
		$sql = "SELECT * FROM `".TABLE_DUMP_CATEGORYS."` WHERE dump_category_sellerid = '0' ORDER BY dump_category_id ASC LIMIT ".(($page-1)*$perPage).",".$perPage;
		$list_dump_categorys = $db->fetch_array($sql);
?>
				<div id="dump_category_manager">
					<div class="section_title">CATEGORYS MANAGER</div>
					<div class="section_title"><a class="bold" href="?act=add">Add New Category</a></div>
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
										<strong>CATEGORY ID</strong>
									</td>
									<td class="formstyle centered">
										<strong>CATEGORY NAME</strong>
									</td>
									<td class="formstyle centered">
										<strong>ACTION</strong>
									</td>
								</tr>
								<tr class="formstyle">
									<td class="centered">
										<span>0</span>
									</td>
									<td class="bold centered">
										<span>(No Category)</span>
									</td>
									<td class="centered">
										<span><a href="dumps.php?lstCategory=0&btnSearch=Search">View Cards</a>
									</td>
								</tr>
<?php
		if (count($list_dump_categorys) > 0) {
			foreach ($list_dump_categorys as $key=>$value) {
?>
								<tr class="formstyle">
									<td class="centered">
										<span><?=$value['dump_category_id']?></span>
									</td>
									<td class="bold centered">
										<span><?=$value['dump_category_name']?></span>
									</td>
									<td class="centered">
										<span><a href="dumps.php?lstCategory=<?=$value['dump_category_id']?>&btnSearch=Search">View Cards</a> | <a href="?act=edit&dump_category_id=<?=$value['dump_category_id']?>">Edit</a> | <a href="?act=delete&dump_category_id=<?=$value['dump_category_id']?>" onclick="return confirm('Are you sure you want to DELETE this Category?');">Delete</a></span>
									</td>
								</tr>
<?php
			}
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