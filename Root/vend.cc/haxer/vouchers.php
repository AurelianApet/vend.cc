<?php
require("./header.php");
define(TABLE_VOUCHERS, 'vouchers');
if ($checkLogin) {
	if ($_GET["act"] == "add") {
?>
				<div id="voucher_manager">
					<div class="section_title">ADD NEW VOUCHERS</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		function rand_str($length = 24, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
		{
			$chars_length = (strlen($chars) - 1);
			$string = $chars{rand(0, $chars_length)};
			for ($i = 1; $i < $length; $i = strlen($string))
			{
				$r = $chars{rand(0, $chars_length)};
				if ($r != $string{$i - 1}) $string .=  $r;
			}
			return $string;
		}
		if (isset($_POST["voucher_add_save"])) {
			$voucher_number = intval($_POST["voucher_number"]);
			if ($voucher_number < 1) {
				$errorMsg = "Number voucher must greater than 0.";
			}
			if ($errorMsg == "") {
				for ($i = 0; $i < $voucher_number; $i++) {
					$count_new_voucherMsg = "";
					do {
						$new_voucher = rand_str();
						$sql = "SELECT COUNT(*) FROM `".TABLE_VOUCHERS."` WHERE voucher_code = '$new_voucher'";
						$count_new_voucher = $db->query_first($sql);
						if ($count_new_voucher) {
							if ($count_new_voucher["COUNT(*)"] < 1) {
								break;
							}
						} else {
							$count_new_voucherMsg = "<font color='red'>Check duplicate voucher error.</font>";
							break;
						}
					} while (true);
					if ($count_new_voucherMsg == "") {
						$voucher_import = array();
						$voucher_import['voucher_code'] = $new_voucher;
						if($db->insert(TABLE_VOUCHERS, $voucher_import)) {
							$count_new_voucherMsg = "<font color='green'>Create Voucher <b>$new_voucher</b> successful!</font>";
						} else {
							$count_new_voucherMsg = "<font color='red'>Create new Vouchers error.</font>";
						}
					} else {
						break;
					}
					echo $count_new_voucherMsg."<br />";
					flush();
				}
			}
			if ($errorMsg == "") {
?>
									<tr>
										<td class="centered">
											<a href="./vouchers.php"><span class="success">Finished vouchers creation, click here to go back.</span></a>
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
?>
								<form method="POST" action="">
									<tr>
										<td class="centered">
											<strong>Number voucher want to create</strong>: <span><input class="formstyle" name="voucher_number" type="text" value="<?=$voucher_import['voucher_number']?>" /></span>
										</td>
									</tr>
									<tr>
										<td class="centered">
											<input type="submit" name="voucher_add_save" value="Save" /><input onclick="window.location='./vouchers.php'"type="button" name="voucher_edit_cancel" value="Cancel" />
										</td>
									</tr>
								</form>
							</tbody>
						</table>
					</div>
				</div>
<?php
	}
	else if ($_GET["act"] == "edit" && $_GET["voucher_id"] != "") {
		$voucher_id = $db->escape($_GET["voucher_id"]);
?>
				<div id="voucher_manager">
					<div class="section_title">VOUCHERS EDITOR</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		if (isset($_POST["voucher_edit_save"])) {
			$voucher_update["voucher_userid"] = $_POST["voucher_userid"];
			if (strval($_POST["voucher_userid"]) != '0') {
				$voucher_update["voucher_time"] = time();
			} else {
				$voucher_update["voucher_time"] = '0';
			}
			if ($errorMsg == "") {
				if($db->update(TABLE_VOUCHERS, $voucher_update, "voucher_id='".$voucher_id."'")) {
					$errorMsg = "";
				}
				else {
					$errorMsg = "Update Vouchers error.";
				}
			}
			if ($errorMsg == "") {
?>
									<script type="text/javascript">setTimeout("window.location = './vouchers.php'", 1000);</script>
									<tr>
										<td colspan="5" class="centered">
											<span class="success">Update Vouchers successfully.</span>
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
		$sql = "SELECT * FROM `".TABLE_USERS."` ORDER BY user_id";
		$allUsers = $db->fetch_array($sql);
		if ($allUsers && is_array($allUsers) && count($allUsers) > 0) {
			$allUsers_temp = array();
			foreach ($allUsers as $user) {
				$allUsers_temp[$user['user_id']] = $user;
			}
			$allUsers = $allUsers_temp;
			unset($allUsers_temp);
		}
		$sql = "SELECT * FROM `".TABLE_VOUCHERS."` WHERE voucher_id = '".$voucher_id."'";
		$records = $db->fetch_array($sql);
		if (count($records)>0) {
			$value = $records[0];
?>
								<form method="POST" action="">
								<tr>
									<td class="formstyle centered">
										<strong>VOUCHERS ID</strong>
									</td>
									<td class="formstyle centered">
										<strong>VOUCHERS CODE</strong>
									</td>
									<td class="formstyle centered">
										<strong>VOUCHERS USED BY</strong>
									</td>
								</tr>
									<tr>
										<td class="centered">
											<span><?=$value['voucher_id']?></span>
										</td>
										<td class="centered">
											<span><?=$value['voucher_code']?></span>
										</td>
										<td class="centered">
											<select class="card_value_editor" name="voucher_userid">
												<option value="0">--Unused--</option>
<?php
			if (is_array($allUsers) && count($allUsers) > 0){
				foreach ($allUsers as $user) {
?>
												<option value="<?=$user["user_id"]?>" <?=($user["user_id"]==$value["voucher_userid"])?"selected ":""?>><?=$user["user_name"]?></option>
<?php
				}
			}
?>
											</select>
										</td>
									</tr>
									<tr>
										<td colspan="3" class="centered">
											<input type="submit" name="voucher_edit_save" value="Save" /><input onclick="window.location='./vouchers.php'"type="button" name="voucher_edit_cancel" value="Cancel" />
										</td>
									</tr>
								</form>
<?php
		}
		else {
?>
								<tr>
									<td class="voucher_title">
										<span class="error">Vouchers ID Invalid.</span>
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
	else if ($_GET["act"] == "delete" && $_GET["voucher_id"] != "") {
		$voucher_id = $db->escape($_GET["voucher_id"]);
?>
				<div id="voucher_manager">
					<div class="section_title">VOUCHER DELETE</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		$sql = "DELETE FROM `".TABLE_VOUCHERS."` WHERE voucher_id='".$voucher_id."'";
		if ($db->query($sql) && $db->affected_rows > 0) {
			$errorMsg = "";
		}
		else {
			$errorMsg = "Delete Vouchers error.";
		}
		if ($errorMsg == "") {
?>
								<script type="text/javascript">setTimeout("window.location = './vouchers.php'", 1000);</script>
								<tr>
									<td class="centered">
										<span class="success">Deleted Vouchers successfully.</span>
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
		$sql = "SELECT count(*) FROM `".TABLE_VOUCHERS."`";
		$totalRecords = $db->query_first($sql);
		$totalRecords = $totalRecords["count(*)"];
		$perPage = 100;
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
		$sql = "SELECT * FROM `".TABLE_USERS."` ORDER BY user_id";
		$allUsers = $db->fetch_array($sql);
		if ($allUsers && is_array($allUsers) && count($allUsers) > 0) {
			$allUsers_temp = array();
			foreach ($allUsers as $user) {
				$allUsers_temp[$user['user_id']] = $user;
			}
			$allUsers = $allUsers_temp;
			unset($allUsers_temp);
		}
		$sql = "SELECT * FROM `".TABLE_VOUCHERS."` ORDER BY voucher_id ASC LIMIT ".(($page-1)*$perPage).",".$perPage;
		$allVouchers = $db->fetch_array($sql);
?>
				<div id="voucher_manager">
					<div class="section_title">VOUCHERS MANAGER</div>
					<div class="section_title"><a class="bold" href="?act=add">Add new Vouchers</a></div>
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
										<strong>VOUCHERS ID</strong>
									</td>
									<td class="formstyle centered">
										<strong>VOUCHERS CODE</strong>
									</td>
									<td class="formstyle centered">
										<strong>VOUCHERS USED BY</strong>
									</td>
									<td class="formstyle centered">
										<strong>VOUCHERS USED TIME</strong>
									</td>
									<td class="formstyle centered">
										<strong>ACTION</strong>
									</td>
								</tr>
<?php
		if (is_array($allVouchers) && count($allVouchers) > 0) {
			foreach ($allVouchers as $key=>$value) {
?>
								<tr class="formstyle">
									<td class="centered">
										<span><?=$value['voucher_id']?></span>
									</td>
									<td class="centered">
										<span><?=$value['voucher_code']?></span>
									</td>
									<td class="centered">
										<span><?=(is_array($allUsers[$value['voucher_userid']]))?$allUsers[$value['voucher_userid']]['user_name']:"-"?></span>
									</td>
									<td class="centered">
										<span><?=(strval($value['voucher_userid']) == "0")?"N/A":date("H:i:s d/M/Y", $value['voucher_time'])?></span>
									</td>
									<td class="centered">
										<span><a href="?act=edit&voucher_id=<?=$value['voucher_id']?>">Edit</a></span>
										<span><a href="?act=delete&voucher_id=<?=$value['voucher_id']?>" onclick="return confirm('Are you sure you want to DELETE this Voucher?');">Delete</a></span>
									</td>
								</tr>
<?php
			}
		}
		else {
?>
								<tr>
									<td colspan="5" class="error">
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