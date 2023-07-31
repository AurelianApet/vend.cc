<?php
require("./header.php");
require("../checkers/checker.php");
if ($checkLogin) {
	if ($_GET["act"] == "import") {
?>
				<div id="cards">
					<div class="section_title">IMPORT OTHER ACCOUNTS</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
			$sql = "SELECT * FROM `".TABLE_OTHER_CATEGORYS."` WHERE other_category_sellerid='0'";
			$allCategorys = $db->fetch_array($sql);
			$newAllCategorys = array();
			foreach ($allCategorys as $category) {
				$newAllCategorys[$category["other_category_id"]] = $category;
			}
			$allCategorys = $newAllCategorys;
			unset($newAllCategorys);
			if (isset($_POST["otheraccount_import_save"]) || isset($_POST["otheraccount_import_preview"])) {
				foreach ($_POST as &$temp) {
					if ($id == "otheraccount_spliter" && $temp != " ") {
						$temp = trim($temp);
					}
				}
				if ($_POST["otheraccount_content"] == "") {
					$errorMsg = "Please input accounts full information";
				}
				else if ($_POST["otheraccount_info"] == "") {
					$errorMsg = "Please input accounts short information";
				}
				else if ($_POST["otheraccount_type"] == "") {
					$errorMsg = "Please input accounts type";
				}
				else if ($_POST["otheraccount_categoryid"] == "" || $_POST["otheraccount_categoryid"] < 0) {
					$errorMsg = "Please input a valid accounts category";
				}
				else if ($_POST["otheraccount_price"] == "" || $_POST["otheraccount_price"] <= 0) {
					$errorMsg = "Please input a valid accounts price";
				}
				else {
					if (isset($_POST["otheraccount_import_preview"])) {
?>
										<tr>
											<td colspan="8" class="centered">
												<table style="width:786px;margin: 0 auto;">
													<tbody>
														<tr>
															<td class="formstyle centered">
																<span class="bold">ACCOUNT TYPE</span>
															</td>
															<td class="formstyle centered">
																<span class="bold">ACCOUNT CATEGORY</span>
															</td>
															<td class="formstyle centered">
																<span class="bold">ACCOUNT FULL INFORMATION</span>
															</td>
															<td class="formstyle centered">
																<span class="bold">ACCOUNT INFORMATION</span>
															</td>
															<td class="formstyle centered">
																<span class="bold">PRICE</span>
															</td>
														</tr>
<?php
					}
					$_POST["otheraccount_content"] = str_replace("\r", "", $_POST["otheraccount_content"]);
					while (substr_count($_POST["otheraccount_content"], "\n\n")) {
						$_POST["otheraccount_content"] = str_replace("\n\n", "\n", $_POST["otheraccount_content"]);
					}
					$otheraccount_content = explode("\n", $_POST["otheraccount_content"]);
					$otheraccount_import["otheraccount_info"] = $_POST["otheraccount_info"];
					$otheraccount_import["otheraccount_type"] = $_POST["otheraccount_type"];
					$otheraccount_import["otheraccount_categoryid"] = $_POST["otheraccount_categoryid"];
					$otheraccount_import["otheraccount_price"] = $_POST["otheraccount_price"];
					foreach ($otheraccount_content as $id=>$line) {
						if (strlen($line) > 10) {
							$otheraccount_import["otheraccount_fullinfo"] = $line;
							if (isset($_POST["otheraccount_import_save"])) {
								$sql = "SELECT count(*) FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_fullinfo = '".$db->escape($otheraccount_import["otheraccount_fullinfo"])."'";
								$otheraccount_duplicate = $db->query_first($sql);
								if ($otheraccount_duplicate) {
									if (intval($otheraccount_duplicate["count(*)"]) == 0) {
										if ($_POST["checklive"] == "on") {
											$check = check_account($type, $otheraccount_import["otheraccount_fullinfo"]);
										}
										else {
											$check = 1;
										}
										if ($check == 1) {
											if($db->insert(TABLE_OTHERACCOUNTS, $otheraccount_import)) {
?>
										<tr>
											<td colspan="3" class="centered">
												<span class="success"><?=$line?> => Add Account successfully.</span>
											</td>
										</tr>
<?php
											}
											else {
?>
										<tr>
											<td colspan="3" class="centered">
												<span class="error"><?=$line?> => Add Account error.</span>
											</td>
										</tr>
<?php
											}
										}
										else if ($check == 2) {
?>
										<tr>
											<td colspan="3" class="centered">
												<span class="error"><?=$line?> => Account die.</span>
											</td>
										</tr>
<?php
										}
										else {
?>
										<tr>
											<td colspan="3" class="centered">
												<span class="error"><?=$line?> => Other Error.</span>
											</td>
										</tr>
<?php
										}
									}
									else {
?>
										<tr>
											<td colspan="3" class="centered">
												<span class="error"><?=$line?> => Duplicated in database.</span>
											</td>
										</tr>
<?php
									}
								}
								else {
?>
										<tr>
											<td colspan="3" class="centered">
												<span class="error"><?=$line?> => Check duplicate error.</span>
											</td>
										</tr>
<?php
								}
							} else {
?>
														<tr class="formstyle">
															<td class="centered">
																<span><?=$otheraccount_import['otheraccount_type']?></span>
															</td>
															<td class="centered">
																<span><?=($allCategorys[$otheraccount_import['otheraccount_categoryid']]['other_category_name']==0)?'(No Category)':$allCategorys[$otheraccount_import['otheraccount_categoryid']]['other_category_name']?></span>
															</td>
															<td class="centered">
																<span><?=$otheraccount_import['otheraccount_fullinfo']?></span>
															</td>
															<td class="centered">
																<span><?=$otheraccount_import['otheraccount_info']?></span>
															</td>
															<td class="centered">
																<span><?=$otheraccount_import['otheraccount_price']?></span>
															</td>
														</tr>
<?php
							}
						}
						flush();
					}
					if (isset($_POST["otheraccount_import_preview"])) {
?>
													</tbody>
												</table>
											</td>
										</tr>
<?php
					}
				}
			}
?>
									<tr>
										<td colspan="3" class="centered">
											<span class="error"><?=$errorMsg?></span>
										</td>
									</tr>
<?php
?>
								<form method="POST" action="">
									<tr>
										<td class="centered bold" colspan="3">
											Account Full Information:
											<textarea class="otheraccount_content_editor" name="otheraccount_content"><?=$_POST['otheraccount_content']?></textarea>
										</td>
									</tr>
									<tr>
										<td class="centered bold" colspan="3">
											Account Short Information<input name="otheraccount_info" type="text" size="128" value="<?=$_POST["otheraccount_info"]?>" />
										</td>
									</tr>
									<tr>
										<td class="centered bold formstyle">
											Account Type
										</td>
										<td class="centered bold formstyle">
											Category
										</td>
										<td class="centered bold formstyle">
											Account Price
										</td>
									</tr>
									<tr>
										<td class="centered bold">
											<input name="otheraccount_type" type="text" size="32" value="<?=$_POST["otheraccount_type"]?>" />
										</td>
										<td class="centered bold">
											<select name="otheraccount_categoryid">
												<option value="0">(No Category)</option>
<?php
			if ($allCategorys && is_array($allCategorys) && count($allCategorys) > 0) {
				foreach($allCategorys as $category) {
?>
												<option value="<?=$category["other_category_id"]?>"<?=($category["other_category_id"] == $_POST["otheraccount_categoryid"])?" selected":""?>><?=$category["other_category_name"]?></option>
<?php
				}
			}
?>
											</select>
										</td>
										<td class="centered bold">
											<input name="otheraccount_price" type="text" size="4" value="<?=$_POST["otheraccount_price"]?>" />
										</td>
									</tr>
									<!--tr>
										<td colspan="3" class="error">
											CHECK CARD LIVE BEFORE IMPORT: <input type="checkbox" name="checklive" />
										</td>
									</tr-->
									<tr>
										<td colspan="3" class="centered">
											<input type="submit" name="otheraccount_import_preview" value="Preview" /><input type="submit" name="otheraccount_import_save" value="Import" /><input onclick="window.location='./cards.php'"type="button" name="otheraccount_import_cancel" value="Cancel" />
										</td>
									</tr>
								</form>
							</tbody>
						</table>
					</div>
				</div>
<?php
	}
	else if ($_GET["act"] == "export") {
?>
				<div id="cards">
					<div class="section_title">EXPORT ACCOUNTS</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<tr>
									<td class="centered">
										<span class="error"><?=$errorMsg?></span>
									</td>
								</tr>
<?php
?>
								<tr>
									<td class="centered">
										<p>
											<form method="POST" action="../otheraccountprocess.php">
												<label>
													<input name="export_unsold" type="submit" class="bold" value="UnSold Accounts">
												</label>
												<span> | </span>
												<label>
													<input name="export_sold" type="submit" class="bold" value="Sold Accounts">
												</label>
											</form>
										</p>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
<?php
	}
	else if ($_GET["act"] == "edit" && $_GET["otheraccount_id"] != "") {
		$otheraccount_id = $db->escape($_GET["otheraccount_id"]);
?>
				<div id="cards">
					<div class="section_title">ACCOUNT EDITOR</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		if (isset($_POST["otheraccount_edit_save"])) {
			$otheraccount_update["otheraccount_userid"] = $_POST["otheraccount_userid"];
			$otheraccount_update["otheraccount_fullinfo"] = $_POST["otheraccount_fullinfo"];
			$otheraccount_update["otheraccount_info"] = $_POST["otheraccount_info"];
			$otheraccount_update["otheraccount_type"] = $_POST["otheraccount_type"];
			$otheraccount_update["otheraccount_categoryid"] = $_POST["otheraccount_categoryid"];
			$otheraccount_update["otheraccount_price"] = $_POST["otheraccount_price"];
			$otheraccount_update["otheraccount_check"] = $_POST["otheraccount_check"];
			if($db->update(TABLE_OTHERACCOUNTS, $otheraccount_update, "otheraccount_id='".$otheraccount_id."' AND otheraccount_sellerid='0'")) {
?>
									<tr>
										<td colspan="4" class="centered">
											<span class="success">Update Account successfully.</span>
										</td>
									</tr>
<?php
			}
			else {
?>
									<tr>
										<td colspan="4" class="centered">
											<span class="error">Update Account error.</span>
										</td>
									</tr>
<?php
			}
		}
		$sql = "SELECT user_id, user_name from `".TABLE_USERS."` ORDER BY user_name";
		$allUsers = $db->fetch_array($sql);
		$sql = "SELECT * FROM `".TABLE_OTHERACCOUNTS."` LEFT JOIN `".TABLE_USERS."` ON ".TABLE_OTHERACCOUNTS.".otheraccount_userid = ".TABLE_USERS.".user_id WHERE otheraccount_id = '".$otheraccount_id."' AND otheraccount_sellerid='0'";
		$records = $db->fetch_array($sql);
		if (count($records)>0) {
			$value = $records[0];
?>
								<form method="POST" action="">
									<tr>
										<td colspan="4" class="centered">
											<textarea class="otheraccount_full_info" name="otheraccount_fullinfo" type="text" wrap="on";><?=$value['otheraccount_fullinfo']?></textarea>
										</td>
									</tr>
									<tr>
										<td class="otheraccount_editor">
											Account Short Information:
										</td>
										<td>
											<input class="otheraccount_value_editor" name="otheraccount_info" type="text" value="<?=$value['otheraccount_info']?>" />
										</td>
										<td class="otheraccount_editor">
											Account Price:
										</td>
										<td>
											<input class="otheraccount_value_editor" name="otheraccount_price" type="text" value="<?=$value['otheraccount_price']?>" />
										</td>
									</tr>
									<tr>
										<td class="otheraccount_editor">
											Account Type:
										</td>
										<td>
											<select name="otheraccount_type" class="otheraccount_value_editor">
												<option value="">All Types</option>
<?php
	$sql = "SELECT * FROM `".TABLE_OTHERACCOUNTS."` GROUP BY otheraccount_type ORDER BY otheraccount_type";
	$allType = $db->fetch_array($sql);
	if (count($allType) > 0) {
		foreach ($allType as $type) {
			echo "<option value=\"".$type['otheraccount_type']."\"".(($value["otheraccount_type"] == $type['otheraccount_type'])?" selected":"").">".$type['otheraccount_type']."</option>";
		}
	}
?>
											</select>
										</td>
										<td class="otheraccount_editor">
											Account Category:
										</td>
										<td>
											<select class="otheraccount_value_editor" name="otheraccount_categoryid">
<?php
			$sql = "SELECT * FROM `".TABLE_OTHER_CATEGORYS."` WHERE other_category_sellerid = '0'";
			$categorys = $db->fetch_array($sql);
			if (is_array($categorys) && count($categorys) > 0) {
				foreach ($categorys as $category) {
?>
							<option value="<?=$category["other_category_id"]?>"<?=($category["other_category_id"]==$value["otheraccount_categoryid"])?" selected":""?>><?=$category["other_category_name"]?></option>
<?php
				}
			}
?>
												<option value="0"<?=(strval($value["otheraccount_categoryid"])=="0")?" selected":""?>>(No Category)</option>
											</select>
										</td>
									</tr>
									<tr>
										<td class="otheraccount_editor">
											Account Used by:
										</td>
										<td>
											<select class="otheraccount_value_editor" name="otheraccount_userid">
												<option value="0">--Unsold--</option>
<?php
			if (count($allUsers) > 0){
				foreach ($allUsers as $k=>$v) {
?>
												<option value="<?=$v["user_id"]?>" <?=($v["user_id"]==$value["otheraccount_userid"])?"selected ":""?>><?=$v["user_name"]?></option>
<?php
				}
			}
?>
											</select>
										</td>
										<td class="otheraccount_editor">
											Account Check Status:
										</td>
										<td class="left">
											<select class="otheraccount_value_editor" name="otheraccount_check">
												<option value="<?=strval(CHECK_DEFAULT)?>" <?=(strval(CHECK_DEFAULT)==$value["otheraccount_check"])?"selected ":""?>>UNCHECK</option>
												<option value="<?=strval(CHECK_INVALID)?>" <?=(strval(CHECK_INVALID)==$value["otheraccount_check"])?"selected ":""?>>INVALID</option>
												<option value="<?=strval(CHECK_VALID)?>" <?=(strval(CHECK_VALID)==$value["otheraccount_check"])?"selected ":""?>>APPROVED</option>
												<option value="<?=strval(CHECK_REFUND)?>" <?=(strval(CHECK_REFUND)==$value["otheraccount_check"])?"selected ":""?>>DECLINE</option>
												<option value="<?=strval(CHECK_UNKNOWN)?>" <?=(strval(CHECK_UNKNOWN)==$value["otheraccount_check"])?"selected ":""?>>UNKNOWN</option>
												<option value="<?=strval(CHECK_WAIT_REFUND)?>" <?=(strval(CHECK_WAIT_REFUND)==$value["otheraccount_check"])?"selected ":""?>>WAIT REFUND</option>
											</select>
										</td>
									</tr>
									<tr>
										<td colspan="4" class="centered">
											<input type="submit" name="otheraccount_edit_save" value="Save" /><input onclick="window.location='./otheraccounts.php'"type="button" name="otheraccount_edit_cancel" value="Cancel" />
										</td>
									</tr>
								</form>
<?php
		}
		else {
?>
								<tr>
									<td class="error">
										<span class="error">Account ID Invalid.</span>
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
	else if ($_POST["multi_edit"] != "" && $_POST["otheraccounts"] != "" && is_array($_POST["otheraccounts"])) {
		$otheraccounts_id = $_POST["otheraccounts"];
		$otheraccounts_sql = "";
		if (count($otheraccounts_id) > 0) {
			$otheraccounts_sql = "'".$db->escape($otheraccounts_id[count($otheraccounts_id) - 1])."'";
			unset($otheraccounts_id[count($otheraccounts_id)-1]);
			foreach ($otheraccounts_id as $v) {
				$otheraccounts_sql .= ", '".$db->escape($v)."'";
			}
		}
?>
				<div id="cards">
					<div class="section_title">MULTI ACCOUNTS EDITOR</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		if (isset($_POST["otheraccount_edit_save"])) {
			if ($_POST["otheraccount_userid"] != "") {
				$otheraccount_update["otheraccount_userid"] = $_POST["otheraccount_userid"];
			}
			if ($_POST["otheraccount_type"] != "") {
				$otheraccount_update["otheraccount_type"] = $_POST["otheraccount_type"];
			}
			if ($_POST["otheraccount_info"] != "") {
				$otheraccount_update["otheraccount_info"] = $_POST["otheraccount_info"];
			}
			if ($_POST["otheraccount_categoryid"] != "") {
				$otheraccount_update["otheraccount_categoryid"] = $_POST["otheraccount_categoryid"];
			}
			if ($_POST["otheraccount_price"] != "") {
				$otheraccount_update["otheraccount_price"] = $_POST["otheraccount_price"];
			}
			if ($_POST["otheraccount_check"] != "") {
				$otheraccount_update["otheraccount_check"] = $_POST["otheraccount_check"];
			}
			if (count($otheraccount_update) > 0) {
				if($db->update(TABLE_OTHERACCOUNTS, $otheraccount_update, "otheraccount_id IN (".$otheraccounts_sql.") AND otheraccount_sellerid='0'")) {
?>
									<tr>
										<td colspan="5" class="centered">
											<span class="success">Update Accounts successfully.</span>
										</td>
									</tr>
<?php
				}
				else {
?>
									<tr>
										<td colspan="5" class="centered">
											<span class="error">Update Accounts error.</span>
										</td>
									</tr>
<?php
				}
			}
		}
		$sql = "SELECT user_id, user_name from `".TABLE_USERS."` ORDER BY user_name";
		$allUsers = $db->fetch_array($sql);
		$sql = "SELECT * FROM `".TABLE_OTHERACCOUNTS."` LEFT JOIN (`".TABLE_USERS."` LEFT JOIN `".TABLE_GROUPS."` ON ".TABLE_USERS.".user_groupid = ".TABLE_GROUPS.".group_id) ON ".TABLE_OTHERACCOUNTS.".otheraccount_userid = ".TABLE_USERS.".user_id LEFT JOIN `".TABLE_OTHER_CATEGORYS."` ON ".TABLE_OTHERACCOUNTS.".otheraccount_categoryid = ".TABLE_OTHER_CATEGORYS.".other_category_id WHERE otheraccount_id IN (".$otheraccounts_sql.")";
		$records = $db->fetch_array($sql);
		if (count($records)>0) {
?>
								<form method="POST" action="">
									<input type="HIDDEN" name="multi_edit" value="true" />
									<tr>
										<td colspan="5" class="centered">
											<table style="width:786px;margin: 0 auto;">
												<tbody>
													<tr>
														<td class="formstyle bold centered">
															<span>ACCOUNT TYPE</span>
														</td>
														<td class="formstyle bold centered">
															<span>CATEGORY</span>
														</td>
														<td class="formstyle bold centered">
															<span>ACCOUNT SHORT INFORMATION</span>
														</td>
														<td class="formstyle bold centered">
															<span>SOLD TO</span>
														</td>
														<td class="formstyle bold centered">
															<span>CHECK</span>
														</td>
														<td class="formstyle bold centered">
															<span>PRICE</span>
														</td>
													</tr>
<?php
			foreach ($records as $value) {
?>
													<tr class="formstyle">
														<input type="HIDDEN" name="otheraccounts[]" value="<?=$value['otheraccount_id']?>" />
														<td class="centered">
															<span>
																<?=$value['otheraccount_type']?>
															</span>
														</td>
														<td class="centered bold">
															<span>
																<?=($value['other_category_name']=="")?"(No Category)":$value['other_category_name']?>
															</span>
														</td>
														<td class="centered bold">
															<span>
																<?=$value['otheraccount_info']?>
															</span>
														</td>
														<td class="centered bold">
<?php
				if ($value['user_name'] != "") {
?>
															<span style="color:<?=$value['group_color']?>;" >
																<?=$value['user_name']?>
															</span>
<?php
				} else {
?>
															<span>
																-
															</span>
<?php
				}
?>
														</td>
														<td class="centered">
															<span>
<?php
				switch ($value['otheraccount_check']) {
					case strval(CHECK_VALID):
						echo "<span class=\"centered green bold\">APPROVED</span>";
						break;
					case strval(CHECK_INVALID):
						echo "<span class=\"centered red bold\">TIMEOUT</span>";
						break;
					case strval(CHECK_REFUND):
						echo "<span class=\"centered pink bold\">DECLINE</span>";
						break;
					case strval(CHECK_UNKNOWN):
						echo "<span class=\"centered blue bold\">UNKNOWN</span>";
						break;
					case strval(CHECK_WAIT_REFUND):
						echo "<span class=\"centered pink bold\">WAIT REFUND</span>";
						break;
					default :
						echo "<span class=\"centered black bold\">UNCHECK</span>";
						break;
				}
?>
															</span>
														</td>
														<td class="centered bold">
															<span><?=$value['otheraccount_price']?></span>
														</td>
													</tr>
<?php
			}
?>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td class="otheraccount_editor">
											Accounts Short Information:
										</td>
										<td>
											<input class="otheraccount_value_editor" name="otheraccount_info" type="text" value="" />
										</td>
										<td class="otheraccount_editor">
											Category:
										</td>
										<td>
											<select class="otheraccount_value_editor" name="otheraccount_categoryid">
												<option value="">(No Change)</option>
<?php
			$sql = "SELECT * FROM `".TABLE_OTHER_CATEGORYS."` WHERE other_category_sellerid = '0'";
			$categorys = $db->fetch_array($sql);
			if (is_array($categorys) && count($categorys) > 0) {
				foreach ($categorys as $value) {
?>
							<option value="<?=$value["other_category_id"]?>"<?=($value["other_category_id"]==$_POST["otheraccount_categoryid"])?" selected":""?>><?=$value["other_category_name"]?></option>
<?php
				}
			}
?>
												<option value="0"<?=(strval($_POST["otheraccount_categoryid"])=="0")?" selected":""?>>(No Category)</option>
											</select>
										</td>
									</tr>
									<tr>
										<td class="otheraccount_editor">
											Accounts Price:
										</td>
										<td>
											<input class="otheraccount_value_editor" name="otheraccount_price" type="text" value="" />
										</td>
										<td class="otheraccount_editor">
											Accounts Check Status:
										</td>
										<td>
											<select class="otheraccount_value_editor" name="otheraccount_check">
												<option value="">(No Change)</option>
												<option value="<?=strval(CHECK_DEFAULT)?>">UNCHECK</option>
												<option value="<?=strval(CHECK_INVALID)?>">INVALID</option>
												<option value="<?=strval(CHECK_VALID)?>">APPROVED</option>
												<option value="<?=strval(CHECK_REFUND)?>">DECLINE</option>
												<option value="<?=strval(CHECK_UNKNOWN)?>">UNKNOWN</option>
												<option value="<?=strval(CHECK_WAIT_REFUND)?>">WAIT REFUND</option>
											</select>
										</td>
									</tr>
									<tr>
										<td class="otheraccount_editor">
											Account Used by:
										</td>
										<td>
											<select class="otheraccount_value_editor" name="otheraccount_userid">
												<option value="">(No Change)</option>
												<option value="0">--Unsold--</option>
<?php
			if (count($allUsers) > 0){
				foreach ($allUsers as $k=>$v) {
?>
												<option value="<?=$v["user_id"]?>" <?=($v["user_id"]==$value["otheraccount_userid"])?"selected ":""?>><?=$v["user_name"]?></option>
<?php
				}
			}
?>
											</select>
										</td>
									</tr>
									<tr>
										<td colspan="5" class="centered">
											<input type="submit" name="otheraccount_edit_save" value="Save" /><input onclick="window.location='./otheraccounts.php'"type="button" name="otheraccount_edit_cancel" value="Cancel" />
										</td>
									</tr>
								</form>
<?php
		}
		else {
?>
								<tr>
									<td class="error">
										<span class="error">Accounts ID Invalid.</span>
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
	else if ($_GET["act"] == "refund" && $_GET["otheraccount_id"] != "") {
		$otheraccount_id = $db->escape($_GET["otheraccount_id"]);
?>
				<div id="cards">
					<div class="section_title">ACCOUNT EDITOR</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		$sql = "SELECT otheraccount_check, otheraccount_userid, otheraccount_sellerid, otheraccount_price, otheraccount_additionPrice FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_userid > 0 AND otheraccount_id = '".$otheraccount_id."' AND otheraccount_sellerid='0'";
		$records = $db->fetch_array($sql);
		if (count($records)>0) {
			$value = $records[0];
			$sql = "SELECT user_id, user_balance from `".TABLE_USERS."` WHERE user_id = '".$value["otheraccount_userid"]."'";
			$otheraccount_user = $db->fetch_array($sql);
			if ($value["otheraccount_sellerid"] <> 0) {
				$sql = "SELECT user_id, user_balance from `".TABLE_USERS."` WHERE user_id = '".$value["otheraccount_sellerid"]."' AND user_groupid = '".strval(PER_SELLER)."'";
				$otheraccount_seller = $db->fetch_array($sql);
			}
			if (count($otheraccount_user)>0) {
				$otheraccount_user = $otheraccount_user[0];
				if ($value["otheraccount_sellerid"] == 0 || count($otheraccount_seller)>0) {
					if ($value["otheraccount_sellerid"] <> 0) {
						$otheraccount_seller = $otheraccount_seller[0];
					}
					if ($value["otheraccount_sellerid"] == 0 || doubleval($otheraccount_seller["user_balance"]) >= ((doubleval($value["otheraccount_price"])+doubleval($value["otheraccount_additionPrice"]))*(1-$db_config["commission"]))) {
						if ($value["otheraccount_check"] != strval(CHECK_REFUND)) {
							$user_update["user_balance"] = doubleval($otheraccount_user["user_balance"])+(doubleval($value["otheraccount_price"])+doubleval($value["otheraccount_additionPrice"]));
							if ($value["otheraccount_sellerid"] <> 0) {
								$seller_update["user_balance"] = doubleval($otheraccount_seller["user_balance"])-((doubleval($value["otheraccount_price"])+doubleval($value["otheraccount_additionPrice"]))*(1-$db_config["commission"]));
							}
							$otheraccount_update["otheraccount_check"] = strval(CHECK_REFUND);
							if($db->update(TABLE_OTHERACCOUNTS, $otheraccount_update, "otheraccount_id='".$otheraccount_id."' AND otheraccount_sellerid='0'") && $db->update(TABLE_USERS, $user_update, "user_id='".$value["otheraccount_userid"]."'") && ($value["otheraccount_sellerid"] == 0 || $db->update(TABLE_USERS, $seller_update, "user_id='".$value["otheraccount_sellerid"]."'"))) {
?>
									<tr>
										<td colspan="4" class="centered">
											<span class="success">Refund Account successfully.</span>
										</td>
									</tr>
<?php
							}
							else {
?>
									<tr>
										<td colspan="4" class="centered">
											<span class="error">Refund Account error.</span>
										</td>
									</tr>
<?php
							}
						}
						else {
?>
									<tr>
										<td colspan="4" class="centered">
											<span class="error">This account is refunded.</span>
										</td>
									</tr>
<?php
						}
					}
					else {
?>
									<tr>
										<td colspan="4" class="centered">
											<span class="error">This seller doesn't have enought money to refund.</span>
										</td>
									</tr>
<?php
					}
				}
				else {
?>
								<tr>
									<td colspan="4" class="centered">
										<span class="error">Cannot find seller to refund.</span>
									</td>
								</tr>
<?php
				}
			}
			else {
?>
								<tr>
									<td colspan="4" class="centered">
										<span class="error">Cannot find user to refund.</span>
									</td>
								</tr>
<?php
			}
		}
		else {
?>
								<tr>
									<td class="error">
										<span class="error">Account ID Invalid.</span>
									</td>
								</tr>
<?php
		}
?>
								<tr>
									<td class="bold centered">
										<span><a href="javascript:history.back(-1);">Go back</a></span>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
<?php
	}
	else if ($_GET["act"] == "delete" && $_GET["otheraccount_id"] != "") {
		$otheraccount_id = $db->escape($_GET["otheraccount_id"]);
		$sql = "DELETE FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_id = '".$otheraccount_id."' AND otheraccount_sellerid='0'";
		if ($db->query($sql) && $db->affected_rows > 0) {
?>
				<script type="text/javascript">setTimeout("window.location = './otheraccounts.php'", 1000);</script>
				<div id="cards">
					<div class="section_title">ACCOUNT DELETE</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<tr class="centered">
									<td class="success">
										Delete Account ID <?=$otheraccount_id?> successfully.
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
<?php
		}
		else {
?>
				<div id="cards">
					<div class="section_title">ACCOUNT DELETE</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<tr>
									<td class="error">
										Account ID Invalid.
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
<?php
		}
	}
	else {
		if ($_POST["delete_invalid"] != "") {
			$sql = "DELETE FROM `".TABLE_OTHERACCOUNTS."` WHERE (otheraccount_check = '".strval(CHECK_INVALID)."' OR otheraccount_check = '".strval(CHECK_REFUND)."') AND otheraccount_sellerid='0'";
			if ($db->query($sql)) {
				$deleteResult=<<<END
										<td colspan="13" class="success">
											Delete all invalid and refund accounts successfully.
										</td>
END;
			}
			else {
				$deleteResult=<<<END
										<td colspan="13" class="error">
											Delete invalid and refund accounts error.
										</td>
END;
			}
		} else if ($_POST["delete_select"] != "" && $_POST["otheraccounts"] != "" && is_array($_POST["otheraccounts"])) {
			$allOtheraccounts = $_POST["otheraccounts"];
			$countDeleteRows = count($allOtheraccounts);
			$lastOtheraccount = $db->escape($allOtheraccounts[count($allOtheraccounts)-1]);
			unset($allOtheraccounts[count($allOtheraccounts)-1]);
			$sql = "DELETE FROM `".TABLE_OTHERACCOUNTS."` WHERE otheraccount_id IN (";
			if (count($allOtheraccounts) > 0) {
				foreach ($allOtheraccounts as $key=>$value) {
					$sql .= "'".$db->escape($value)."', ";
				}
			}
			$sql .= "'".$lastOtheraccount."') AND otheraccount_sellerid='0'";
			if ($db->query($sql)) {
				if ($db->affected_rows == $countDeleteRows) {
					$deleteResult=<<<END
										<td colspan="13" class="success">
											Delete selected accounts successfully.
										</td>
END;
				} else {
					$countDeletedRows = $countDeleteRows-$db->affected_rows;
					$deleteResult=<<<END
										<td colspan="13" class="error">
											Delete {$countDeletedRows} of {$countDeleteRows} selected accounts error, please check again.
										</td>
END;
				}
			}
			else {
				$deleteResult=<<<END
										<td colspan="13" class="error">
											Delete selected accounts error.
										</td>
END;
			}
		}
		
		if ($_GET["lstCategory"] != "") {
			$_GET["lstCategory"] = intval($_GET["lstCategory"]);
			if ($_GET["lstCategory"] > 0) {
				$searchCategory = "otheraccount_categoryid = '".$db->escape($_GET["lstCategory"])."'";
			} else if ($_GET["lstCategory"] == 0) {
				$searchCategory = "otheraccount_categoryid NOT IN (SELECT other_category_id FROM `".TABLE_OTHER_CATEGORYS."` WHERE other_category_sellerid = '0')";
			} else {
				$searchCategory = "1";
				$_GET["lstCategory"] = "";
			}
		} else {
			$searchCategory = "1";
		}
		$currentGet = "lstCategory=".$_GET["lstCategory"]."&";
		$currentGet .= "txtInfo=".$_GET["txtInfo"]."&";
		$_GET["lstType"] = trim($_GET["lstType"]);
		if (isset($_GET["btnSearch"])) {
			$currentGet .= "lstType=".$_GET["lstType"];
			$currentGet .= "&lstCategory=".$_GET["lstCategory"]."&lstAvailable=".$_GET["lstAvailable"]."&lstStatus=".$_GET["lstStatus"]."&lstCheck=".$_GET["lstCheck"];
			$currentGet .= "&btnSearch=Search&";
		}
		if ($_GET["lstType"] == "") {
			$searchType = "1";
		} else {
			$searchType = "otheraccount_type = '".$db->escape($_GET["lstType"])."'";
		}
		$searchInfo = $db->escape($_GET["txtInfo"]);
		switch ($_GET["lstAvailable"]) {
			case "unsold":
				$searchAvailable = "otheraccount_userid = '0'";
				break;
			case "sold":
				$searchAvailable = "otheraccount_userid <> '0'";
				break;
			default:
				if (intval($_GET["lstAvailable"]) > 0) {
					$searchAvailable = "otheraccount_userid = '".$db->escape(intval($_GET["lstAvailable"]))."'";
				} else {
					$searchAvailable = "1";
				}
				break;
		}
		switch ($_GET["lstStatus"]) {
			case strval(STATUS_DEFAULT):
				$searchStatus = "otheraccount_status = '".strval(STATUS_DEFAULT)."'";
				break;
			case strval(STATUS_DELETED):
				$searchStatus = "otheraccount_status = '".strval(STATUS_DELETED)."'";
				break;
			case strval(STATUS_STAGNANT):
				$searchStatus = "otheraccount_status = '".strval(STATUS_STAGNANT)."'";
				break;
			case strval(STATUS_EXPIRED):
				$searchStatus = "otheraccount_status = '".strval(STATUS_EXPIRED)."'";
				break;
			default:
				$searchStatus = "1";
				break;
		}
		switch ($_GET["lstCheck"]) {
			case strval(CHECK_DEFAULT):
				$searchCheck = "otheraccount_check = '".strval(CHECK_DEFAULT)."'";
				break;
			case strval(CHECK_VALID):
				$searchCheck = "otheraccount_check = '".strval(CHECK_VALID)."'";
				break;
			case strval(CHECK_INVALID):
				$searchCheck = "otheraccount_check = '".strval(CHECK_INVALID)."'";
				break;
			case strval(CHECK_REFUND):
				$searchCheck = "otheraccount_check = '".strval(CHECK_REFUND)."'";
				break;
			case strval(CHECK_UNKNOWN):
				$searchCheck = "otheraccount_check = '".strval(CHECK_UNKNOWN)."'";
				break;
			case strval(CHECK_WAIT_REFUND):
				$searchCheck = "otheraccount_check = '".strval(CHECK_WAIT_REFUND)."'";
				break;
			default:
				$searchCheck = "1";
				break;
		}
		$group_where = $searchCategory." AND ".$searchType." AND otheraccount_sellerid='0' AND ".$searchStatus." AND ".$searchCheck." AND ".$searchAvailable;
		$search_where = "('".$searchInfo."'='' OR otheraccount_fullinfo LIKE '%".$searchInfo."%' OR otheraccount_fullinfo LIKE '".$searchInfo."%' OR otheraccount_fullinfo LIKE '%".$searchInfo."')";
		$sql = "SELECT count(*) FROM `".TABLE_OTHERACCOUNTS."` WHERE ".$group_where." AND ".$search_where;
		$totalRecords = $db->query_first($sql);
		$totalRecords = $totalRecords["count(*)"];
		$perPage = 20;
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
		$sql = "SELECT ".TABLE_OTHERACCOUNTS.".*, ".TABLE_USERS.".user_name, ".TABLE_GROUPS.".group_color, ".TABLE_OTHER_CATEGORYS.".* FROM `".TABLE_OTHERACCOUNTS."` LEFT JOIN `".TABLE_USERS."` ON ".TABLE_OTHERACCOUNTS.".otheraccount_userid = ".TABLE_USERS.".user_id LEFT JOIN `".TABLE_GROUPS."` ON ".TABLE_USERS.".user_groupid = ".TABLE_GROUPS.".group_id LEFT JOIN `".TABLE_OTHER_CATEGORYS."` ON ".TABLE_OTHERACCOUNTS.".otheraccount_categoryid = ".TABLE_OTHER_CATEGORYS.".other_category_id WHERE ".$group_where." AND ".$search_where." ORDER BY otheraccount_id LIMIT ".(($page-1)*$perPage).",".$perPage;
		$listotheraccount = $db->fetch_array($sql);
?>
				<div id="search_cards">
					<div class="section_title">SEARCH OTHER ACCOUNTS</div>
					<div class="section_content">
						<table class="content_table centered">
							<tbody>
								<form name="search" method="GET" action="otheraccounts.php">
									<tr>
										<td class="formstyle" colspan="5">
											<span class="bold">ACCOUNT FULL INFORMATION</span>
										</td>
									</tr>
									<tr>
										<td colspan="5">
											<input name="txtInfo" type="text" class="formstyle" id="txtInfo" value="<?=$_GET["txtInfo"]?>" size="128" maxlength="128">
										</td>
									</tr>
									<tr>
										<td class="formstyle">
											<span class="bold">ACCOUNT TYPE</span>
										</td>
										<td class="formstyle">
											<span class="bold">CATEGORY</span>
										</td>
										<td class="formstyle">
											<span class="bold">AVAILABLE</span>
										</td>
										<td class="formstyle">
											<span class="bold">STATUS</span>
										</td>
										<td class="formstyle">
											<span class="bold">CHECK</span>
										</td>
									</tr>
									<tr>
										<td>
											<select name="lstType" class="formstyle" id="lstType">
												<option value="">All Types</option>
<?php
	$sql = "SELECT * FROM `".TABLE_OTHERACCOUNTS."` GROUP BY otheraccount_type ORDER BY otheraccount_type";
	$allType = $db->fetch_array($sql);
	if (count($allType) > 0) {
		foreach ($allType as $type) {
			echo "<option value=\"".$type['otheraccount_type']."\"".(($_GET["lstType"] == $type['otheraccount_type'])?" selected":"").">".$type['otheraccount_type']."</option>";
		}
	}
?>
											</select>
										</td>
										<td>
											<select name="lstCategory" class="formstyle" id="lstCategory">
												<option value="">(All Category)</option>
<?php
	$sql = "SELECT * FROM `".TABLE_OTHER_CATEGORYS."` ORDER BY other_category_name";
	$allType = $db->fetch_array($sql);
	if (count($allType) > 0) {
		foreach ($allType as $type) {
			echo "<option value=\"".$type['other_category_id']."\"".((strval($_GET["lstCategory"]) == $type['other_category_id'])?" selected":"").">".$type['other_category_name']."</option>";
		}
	}
?>
												<option value="0" <?=((strval($_GET["lstCategory"]) == "0")?" selected":"")?>>(No Category)</option>
											</select>
										</td>
										<td>
											<select name="lstAvailable" class="formstyle" id="lstAvailable">
												<option value="" <?=(($_GET["lstAvailable"] == "")?" selected":"")?>>ALL</option>
												<option value="unsold" <?=(($_GET["lstAvailable"] == "unsold")?" selected":"")?>>UNSOLD</option>
												<option value="sold" <?=(($_GET["lstAvailable"] == "sold")?" selected":"")?>>SOLD</option>
											</select>
										</td>
										<td>
											<select name="lstStatus" class="formstyle" id="lstStatus">
												
												<option value="" <?=(($_GET["lstStatus"] == "")?" selected":"")?>>ALL</option>
												<option value="<?=strval(STATUS_DEFAULT)?>" <?=(($_GET["lstStatus"] == strval(STATUS_DEFAULT))?" selected":"")?>>USER DEFAULT</option>
												<option value="<?=strval(STATUS_DELETED)?>" <?=(($_GET["lstStatus"] == strval(STATUS_DELETED))?" selected":"")?>>USER DELETED</option>
											</select>
										</td>
										<td>
											<select name="lstCheck" class="formstyle" id="lstCheck">
												
												<option value="" <?=(($_GET["lstCheck"] == "")?" selected":"")?>>ALL</option>
												<option value="<?=strval(CHECK_DEFAULT)?>" <?=(($_GET["lstCheck"] == strval(CHECK_DEFAULT))?" selected":"")?>>UNCHECK</option>
												<option value="<?=strval(CHECK_VALID)?>" <?=(($_GET["lstCheck"] == strval(CHECK_VALID))?" selected":"")?>>APPROVED</option>
												<option value="<?=strval(CHECK_INVALID)?>" <?=(($_GET["lstCheck"] == strval(CHECK_INVALID))?" selected":"")?>>TIMEOUT</option>
												<option value="<?=strval(CHECK_REFUND)?>" <?=(($_GET["lstCheck"] == strval(CHECK_REFUND))?" selected":"")?>>DECLINE</option>
												<option value="<?=strval(CHECK_UNKNOWN)?>" <?=(($_GET["lstCheck"] == strval(CHECK_UNKNOWN))?" selected":"")?>>UNKNOWN</option>
												<option value="<?=strval(CHECK_WAIT_REFUND)?>" <?=(($_GET["lstCheck"] == strval(CHECK_WAIT_REFUND))?" selected":"")?>>WAIT REFUND</option>
											</select>
										</td>
									</tr>
									<tr>
										<td colspan="5">
											<input name="btnSearch" type="submit" class="formstyle" id="btnSearch" value="Search">
										</td>
									</tr>
								</form>
							</tbody>
						</table>
					</div>
				</div>
				<div id="cards">
					<div class="section_title">OTHER ACCOUNTS</div>
					<div class="section_title"><a href="?act=import">Import Accounts</a></div>
					<div class="section_page_bar">
<?php
		if ($totalRecords > 0) {
			echo "Page:";
			if ($page>1) {
				echo "<a href=\"?".$currentGet."page=".($page-1)."\">&lt;</a>";
				echo "<a href=\"?".$currentGet."page=1\">1</a>";
			}
			if ($page>3) {
				echo "...";
			}
			if (($page-1) > 1) {
				echo "<a href=\"?".$currentGet."page=".($page-1)."\">".($page-1)."</a>";
			}
			echo "<input type=\"TEXT\" class=\"page_go\" value=\"".$page."\" onchange=\"window.location.href='?".$currentGet."page='+this.value\"/>";
			if (($page+1) < $totalPage) {
				echo "<a href=\"?".$currentGet."page=".($page+1)."\">".($page+1)."</a>";
			}
			if ($page < $totalPage-2) {
				echo "...";
			}
			if ($page<$totalPage) {
				echo "<a href=\"?".$currentGet."page=".$totalPage."\">".$totalPage."</a>";
				echo "<a href=\"?".$currentGet."page=".($page+1)."\">&gt;</a>";
			}
		}
?>
					</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<form name="cards" method="POST" action="">
									<tr>
										<?=$deleteResult?>
									</tr>
									<tr>
										<td class="formstyle centered">
											<span class="bold">ACCOUNT TYPE</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">CATEGORY</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">ACCOUNT FULL INFORMATION</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">SOLD TO</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">CHECK</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">ACTION</span>
										</td>
										<td class="formstyle centered">
											<input class="formstyle" type="checkbox" name="selectallOtheraccounts" id="selectallOtheraccounts" onclick="checkAll(this.id, 'otheraccounts[]')" value="">
										</td>
									</tr>
<?php
		if (count($listotheraccount) > 0) {
			foreach ($listotheraccount as $key=>$value) {
?>
									<tr class="formstyle">
										<td class="centered bold">
											<span><?=$value['otheraccount_type']?></span>
										</td>
										<td class="centered bold">
											<span><?=($value['other_category_name']=="")?"(No Category)":$value['other_category_name']?></span>
										</td>
										<td class="centered">
											<span><?=$value['otheraccount_fullinfo']?></span>
										</td>
										<td class="centered">
<?php
				switch ($value['otheraccount_userid']) {
					case 0:
						echo "<span class=\"bold\"> - </span>";
						break;
					default :
						echo "<span class=\"bold\" style=\"color:".$value['group_color'].";\" >".$value['user_name']."</span>";
						break;
				}
?>
										</td>
										<td class="centered bold">
											<span>
<?php
				switch ($value['otheraccount_check']) {
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
											</span>
										</td>
										<td class="centered">
											<span>
											<a href="?act=edit&otheraccount_id=<?=$value['otheraccount_id']?>">Edit</a>
											 | <a href="?act=delete&otheraccount_id=<?=$value['otheraccount_id']?>" onClick="return confirm('Are you sure you want to DELETE this Accounts?')">Delete</a>
<?php
				if ($value['otheraccount_userid'] > 0 && $value['otheraccount_check'] != strval(CHECK_REFUND)) {
?>
											 | <a href="?act=refund&otheraccount_id=<?=$value['otheraccount_id']?>" onClick="return confirm('Are you sure you want to REFUND this Accounts?')">Refund</a>
<?php
				}
?>
											</span>
										</td>
										<td class="centered">
											<input class="formstyle" type="checkbox" name="otheraccounts[]" value="<?=$value['otheraccount_id']?>">
										</td>
									</tr>
<?php
			}
		}
?>
									<tr>
										<td colspan="13" class="centered">
											<p>
												<label>
													<input name="multi_edit" type="submit" id="multi_edit" onClick="" value="Edit Selected Accounts">
												<span> | </span>
												</label>
												<label>
													<input name="delete_invalid" type="submit" id="delete_invalid" onClick="return confirm('Are you sure you want to delete the INVALID Accounts?')" value="Delete Invalid/Refunded Accounts">
												<span> | </span>
												</label>
												<label>
													<input name="delete_select" type="submit" id="delete_select" onClick="return confirm('Are you sure you want to delete the SELECTED Accounts?')" value="Delete Selected Accounts">
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
}
else {
	require("./minilogin.php");
}
require("./footer.php");
?>