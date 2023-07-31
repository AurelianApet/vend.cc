<?php
require("./header.php");
require("../checkers/checker.php");
if ($checkLogin) {
	if ($_GET["act"] == "import") {
?>
				<div id="dumps">
					<div class="section_title">IMPORT DUMPS</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
			if (isset($_POST["dump_import_save"]) || isset($_POST["dump_import_preview"])) {
				foreach ($_POST as &$temp) {
					if ($id == "dump_spliter" && $temp != " ") {
						$temp = trim($temp);
					}
				}
				if ($_POST["dump_content"] == "") {
					$errorMsg = "Please input dump content";
				}
				else if ($_POST["dump_categoryid"] == "" || $_POST["dump_categoryid"] < 0) {
					$errorMsg = "Please input a valid dump category";
				}
				else if ($_POST["dump_price"] == "" || $_POST["dump_price"] <= 0) {
					$errorMsg = "Please input a valid dump price";
				}
				else if ($_POST["dump_spliter"] == "") {
					$errorMsg = "Please input dump spliter";
				}
				else {
					if (isset($_POST["dump_import_preview"])) {
?>
										<tr>
											<td colspan="10" class="centered">
												<table style="width:786px;margin: 0 auto;">
													<tbody>
														<tr>
															<td class="formstyle centered">
																<span class="bold">DUMP NUMBER</span>
															</td>
															<td class="formstyle centered">
																<span class="bold">EXPIRE</span>
															</td>
															<td class="formstyle centered">
																<span>COUNTRY</span>
															</td>
															<td class="formstyle centered">
																<span>BANK</span>
															</td>
															<td class="formstyle centered">
																<span>TYPE</span>
															</td>
															<td class="formstyle centered">
																<span>LEVEL</span>
															</td>
															<td class="formstyle centered">
																<span>CREDIT TYPE</span>
															</td>
															<td class="formstyle centered">
																<span>CODE</span>
															</td>
															<td class="formstyle centered">
																<span>TRACK 1</span>
															</td>
															<td class="formstyle centered">
																<span>TRACK 2</span>
															</td>
														</tr>
<?php
					}
					$_POST["dump_content"] = str_replace("\r", "", $_POST["dump_content"]);
					$_POST["dump_content"] = str_replace(array(" ".$_POST["dump_spliter"], $_POST["dump_spliter"]." "), $_POST["dump_spliter"], $_POST["dump_content"]);
					while (substr_count($_POST["dump_content"], "\n\n")) {
						$_POST["dump_content"] = str_replace("\n\n", "\n", $_POST["dump_content"]);
					}
					$dump_content = explode("\n", $_POST["dump_content"]);
					$dump_import["dump_categoryid"] = $_POST["dump_categoryid"];
					$dump_import["dump_price"] = $_POST["dump_price"];
					foreach ($dump_content as $id=>$line) {
						$get_dump_number_error = "";
						$dump_expired = "";
						$get_zipcode_error = "";
						$get_country_error = "";
						if (strlen($line) > 10) {
							$lineField = explode($_POST["dump_spliter"], $line);
							$dump_import["dump_fullinfo"] = $line;
							$dumpNumber = "";
							$dumpType = "";
							$dumpExp = "";
							if (getDumpNumber($line, $dumpType, $dumpNumber, $dumpExp)) {
								$dump_import["dump_type"] = $dumpType;
								$dump_import["dump_number"] = $dumpNumber;
								$dump_import["dump_exp"] = $dumpExp;
							} else {
								$import_get_dump_number_error[] = $line." => Get dump number error.";
								$get_dump_number_error = <<<HTML
								<tr>
									<td colspan="10" class="centered">
										<span class="error">{$line} => Get dump number error.</span>
									</td>
								</tr>
HTML;
							}
							$dump_import["dump_bin"] = substr($dump_import["dump_number"], 0, 6);
							if ($dump_import["dump_exp"] < date("ym")) {
								$import_dump_expired[] = $line." => Dump expired.";
								$dump_expired = <<<HTML
								<tr>
									<td colspan="10" class="centered">
										<span class="error">{$line} => Dump expired.</span>
									</td>
								</tr>
HTML;
							}
							if ($_POST["dump_country"] == "") {
								$dump_import["dump_country"] = "";
							}
							else {
								$dump_import["dump_country"] = $lineField[$_POST["dump_country"] - 1];
							}
							if ($_POST["dump_bank"] == "") {
								$dump_import["dump_bank"] = "";
							}
							else {
								$dump_import["dump_bank"] = $lineField[$_POST["dump_bank"] - 1];
							}
							if ($_POST["dump_level"] == "") {
								$dump_import["dump_level"] = "";
							}
							else {
								$dump_import["dump_level"] = $lineField[$_POST["dump_level"] - 1];
							}
							if ($_POST["dump_ctype"] == "") {
								$dump_import["dump_ctype"] = "";
							}
							else {
								$dump_import["dump_ctype"] = $lineField[$_POST["dump_ctype"] - 1];
							}
							if ($_POST["dump_code"] == "") {
								$dump_import["dump_code"] = "";
							}
							else {
								$dump_import["dump_code"] = $lineField[$_POST["dump_code"] - 1];
							}
							if (strtolower($_POST["dump_tr1"]) == "on") {
								$dump_import["dump_tr1"] = 1;
							}
							else {
								$dump_import["dump_tr1"] = 0;
							}
							if (strtolower($_POST["dump_tr2"]) == "on") {
								$dump_import["dump_tr2"] = 1;
							}
							else {
								$dump_import["dump_tr2"] = 0;
							}
							if ($_POST["dump_additional"] == "") {
								$dump_import["dump_additional"] = "";
							}
							else {
								$dump_import["dump_additional"] = $db->escape($_POST["dump_additional"]);
							}
							if ($dump_import["dump_country"] == "") $dump_import["dump_country"] = " - ";
							if ($dump_import["dump_bank"] == "") $dump_import["dump_bank"] = " - ";
							if ($dump_import["dump_level"] == "") $dump_import["dump_level"] = " - ";
							if ($dump_import["dump_ctype"] == "") $dump_import["dump_ctype"] = " - ";
							if ($dump_import["dump_code"] == "") $dump_import["dump_code"] = " - ";
							if (isset($_POST["dump_import_save"])) {
								$sql = "SELECT count(*) FROM `".TABLE_DUMPS."` WHERE dump_number = AES_ENCRYPT('".$db->escape($dump_import["dump_number"])."', '".strval(DB_ENCRYPT_PASS)."')";
								$dump_duplicate = $db->query_first($sql);
								if ($get_dump_number_error != "") {
									echo $get_dump_number_error;
								} else if ($dump_expired != "") {
									echo $dump_expired;
								} else if ($get_zipcode_error != "") {
									echo $get_zipcode_error;
								} else if ($get_country_error != "") {
									echo $get_country_error;
								} else if ($dump_duplicate) {
									if (intval($dump_duplicate["count(*)"]) == 0) {
									
										if ($_POST["checklive"] == "on") {
										
											$check = check_dump($dump_import["dump_number"], $dump_import["dump_exp"]);
										}
										else {
										
											$check = 1;
										}
										if ($check == 1) {
										
											$dump_import["dump_fullinfo"] = "AES_ENCRYPT('".$dump_import["dump_fullinfo"]."', '".strval(DB_ENCRYPT_PASS)."')";
											$dump_import["dump_number"] = "AES_ENCRYPT('".$dump_import["dump_number"]."', '".strval(DB_ENCRYPT_PASS)."')";
											if($db->insert(TABLE_DUMPS, $dump_import)) {
?>
										<tr>
											<td colspan="10" class="centered">
												<span class="success"><?=$line?> => Add Dump successfully.</span>
											</td>
										</tr>
<?php
											}
											else {
?>
										<tr>
											<td colspan="10" class="centered">
												<span class="error"><?=$line?> => Add Dump error.</span>
											</td>
										</tr>
<?php
											}
										}
										else if ($check == 2) {
?>
										<tr>
											<td colspan="10" class="centered">
												<span class="error"><?=$line?> => Dump die.</span>
											</td>
										</tr>
<?php
										}
										else {
?>
										<tr>
											<td colspan="10" class="centered">
												<span class="error"><?=$line?> => Other Error.</span>
											</td>
										</tr>
<?php
										}
									}
									else {
?>
										<tr>
											<td colspan="10" class="centered">
												<span class="error"><?=$line?> => Duplicated in database.</span>
											</td>
										</tr>
<?php
									}
								}
								else {
?>
										<tr>
											<td colspan="10" class="centered">
												<span class="error"><?=$line?> => Check duplicate error.</span>
											</td>
										</tr>
<?php
								}
							} else {
								if ($get_dump_number_error != "") {
									echo $get_dump_number_error;
								} else if ($dump_expired != "") {
									echo $dump_expired;
								} else if ($get_zipcode_error != "") {
									echo $get_zipcode_error;
								} else if ($get_country_error != "") {
									echo $get_country_error;
								} else {
?>
														<tr class="formstyle">
															<td class="centered">
																<span><?=$dump_import['dump_number']?></span>
															</td>
															<td class="centered">
																<span><?=$dump_import['dump_exp']?></span>
															</td>
															<td class="centered">
																<span><?=$dump_import['dump_country']?></span>
															</td>
															<td class="centered">
																<span><?=$dump_import['dump_bank']?></span>
															</td>
															<td class="centered">
																<span><?=$dump_import['dump_type']?></span>
															</td>
															<td class="centered">
																<span><?=$dump_import['dump_level']?></span>
															</td>
															<td class="centered">
																<span><?=$dump_import['dump_ctype']?></span>
															</td>
															<td class="centered">
																<span><?=$dump_import['dump_code']?></span>
															</td>
															<td class="centered">
																<span><?=$dump_import['dump_tr1']?></span>
															</td>
															<td class="centered">
																<span><?=$dump_import['dump_tr2']?></span>
															</td>
														</tr>
<?php
								}
							}
						}
						flush();
					}
					if (isset($_POST["dump_import_preview"])) {
?>
													</tbody>
												</table>
											</td>
										</tr>
<?php
					}
					if (count($import_get_dump_number_error) > 0) {
?>
									<tr>
										<td colspan="10" class="error">
											GET DUMP NUMBER ERROR
											<textarea class="dump_content_error">
<?php
						foreach ($import_get_dump_number_error as $lineError) {
							echo $lineError;
						}
?>
</textarea>
										</td>
									</tr>
<?php
					}
					if (count($import_dump_expired) > 0) {
?>
									<tr>
										<td colspan="10" class="error">
											DUMP EXPIRED
											<textarea class="dump_content_error">
<?php
						foreach ($import_dump_expired as $lineError) {
							echo $lineError;
						}
?>
</textarea>
										</td>
									</tr>
<?php
					}
					if (count($import_get_zipcode_error1) > 0) {
?>
									<tr>
										<td colspan="10" class="error">
											GET ZIPCODE ERROR
											<textarea class="dump_content_error">
<?php
						foreach ($import_get_zipcode_error1 as $lineError) {
							echo $lineError;
						}
?>
</textarea>
										</td>
									</tr>
<?php
					}
					if (count($import_get_zipcode_error2) > 0) {
?>
									<tr>
										<td colspan="10" class="error">
											GET ZIPCODE ERROR
											<textarea class="dump_content_error">
<?php
						foreach ($import_get_zipcode_error2 as $lineError) {
							echo $lineError;
						}
?>
</textarea>
										</td>
									</tr>
<?php
					}
					if (count($import_get_country_error1) > 0) {
?>
									<tr>
										<td colspan="10" class="error">
											GET COUNTRY ERROR
											<textarea class="dump_content_error">
<?php
						foreach ($import_get_country_error1 as $lineError) {
							echo $lineError."\n";
						}
?>
</textarea>
										</td>
									</tr>
<?php
					}
					if (count($import_get_country_error2) > 0) {
?>
									<tr>
										<td colspan="10" class="error">
											GET COUNTRY ERROR
											<textarea class="dump_content_error">
<?php
						foreach ($import_get_country_error2 as $lineError) {
							echo $lineError."\n";
						}
?>
</textarea>
										</td>
									</tr>
<?php
					}
				}
			}
?>
									<tr>
										<td colspan="10" class="centered">
											<span class="error"><?=$errorMsg?></span>
										</td>
									</tr>
<?php
?>
								<form method="POST" action="">
									<tr>
										<td class="centered bold" colspan="10">
											Dump Content:
											<textarea class="dump_content_editor" name="dump_content"><?=$_POST['dump_content']?></textarea>
										</td>
									</tr>
									<tr>
										<td class="centered bold formstyle">
											Category
										</td>
										<td class="centered bold formstyle">
											Dump Price
										</td>
										<td class="centered bold formstyle">
											Spliter
										</td>
										<td class="centered bold formstyle">
											Country
										</td>
										<td class="centered bold formstyle">
											Bank
										</td>
										<td class="centered formstyle">
											Level
										</td>
										<td class="centered formstyle">
											Credit Type
										</td>
										<td class="centered formstyle">
											Code
										</td>
										<td class="centered formstyle">
											Track 1
										</td>
										<td class="centered formstyle">
											Track 2
										</td>
									</tr>
									<tr>
										<td class="centered bold">
											<select name="dump_categoryid">
												<option value="0">(No Category)</option>
<?php
			$sql = "SELECT * FROM `".TABLE_DUMP_CATEGORYS."` WHERE dump_category_sellerid = '0'";
			$records = $db->fetch_array($sql);
			if ($records && is_array($records) && count($records) > 0) {
				foreach($records as $value) {
?>
												<option value="<?=$value["dump_category_id"]?>"<?=($value["dump_category_id"] == $_POST["dump_categoryid"])?" selected":""?>><?=$value["dump_category_name"]?></option>
<?php
				}
			}
?>
											</select>
										</td>
										<td class="centered bold">
											<input name="dump_price" type="text" size="4" value="<?=$_POST["dump_price"]?>" />
										</td>
										<td class="centered bold">
											<input name="dump_spliter" type="text" size="4" value="<?=$_POST["dump_spliter"]?>" />
										</td>
										<td class="centered bold">
											<input name="dump_country" type="text" size="11" value="<?=$_POST["dump_country"]?>" />
										</td>
										<td class="centered bold">
											<input name="dump_bank" type="text" size="11" value="<?=$_POST["dump_bank"]?>" />
										</td>
										<td class="centered bold">
											<input name="dump_level" type="text" size="11" value="<?=$_POST["dump_level"]?>" />
										</td>
										<td class="centered bold">
											<input name="dump_ctype" type="text" size="11" value="<?=$_POST["dump_ctype"]?>" />
										</td>
										<td class="centered bold">
											<input name="dump_code" type="text" size="11" value="<?=$_POST["dump_code"]?>" />
										</td>
										<td class="centered bold">
											<input name="dump_tr1" type="checkbox" <?=($value['dump_tr1']==1)?"checked ":""?>/>
										</td>
										<td class="centered bold">
											<input name="dump_tr2" type="checkbox" <?=($value['dump_tr2']==1)?"checked ":""?>/>
										</td>
									</tr>
									<tr>
										<td class="centered bold" colspan="10">
											Dump Additional:
											<textarea class="dump_content_editor" name="dump_additional"><?=$_POST['dump_additional']?></textarea>
										</td>
									</tr>
									<tr>
										<td colspan="10" class="error">
											CHECK DUMP LIVE BEFORE IMPORT: <input type="checkbox" name="checklive" />
										</td>
									</tr>
									<tr>
										<td colspan="10" class="centered">
											<input type="submit" name="dump_import_preview" value="Preview" /><input type="submit" name="dump_import_save" value="Import" /><input onclick="window.location='./dumps.php'"type="button" name="dump_import_cancel" value="Cancel" />
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
				<div id="dumps">
					<div class="section_title">EXPORT DUMPS</div>
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
											<form method="POST" action="../dumpprocess.php">
												<label>
													<input name="export_unsold" type="submit" class="bold" value="UnSold Dumps">
												</label>
												<span> | </span>
												<label>
													<input name="export_sold" type="submit" class="bold" value="Sold Dumps">
												</label>
												<span> | </span>
												<label>
													<input name="export_expired" type="submit" class="bold" value="Expired Dumps">
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
	else if ($_GET["act"] == "edit" && $_GET["dump_id"] != "") {
		$dump_id = $db->escape($_GET["dump_id"]);
?>
				<div id="dumps">
					<div class="section_title">DUMP EDITOR</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		if (isset($_POST["dump_edit_save"])) {
			$dump_update["dump_fullinfo"] = "AES_ENCRYPT('".$_POST["dump_fullinfo"]."', '".strval(DB_ENCRYPT_PASS)."')";
			$dump_update["dump_number"] = "AES_ENCRYPT('".$_POST["dump_number"]."', '".strval(DB_ENCRYPT_PASS)."')";
			$dump_update["dump_categoryid"] = $_POST["dump_categoryid"];
			$dump_update["dump_exp"] = $_POST["dump_exp"];
			$dump_update["dump_country"] = $_POST["dump_country"];
			$dump_update["dump_bank"] = $_POST["dump_bank"];
			$dump_update["dump_type"] = $_POST["dump_type"];
			$dump_update["dump_level"] = $_POST["dump_level"];
			$dump_update["dump_ctype"] = $_POST["dump_ctype"];
			$dump_update["dump_code"] = $_POST["dump_code"];
			$dump_update["dump_tr1"] = (strtolower($_POST["dump_tr1"]) == "on")?1:0;
			$dump_update["dump_tr2"] = (strtolower($_POST["dump_tr2"]) == "on")?1:0;
			$dump_update["dump_price"] = $_POST["dump_price"];
			$dump_update["dump_userid"] = $_POST["dump_userid"];
			$dump_update["dump_check"] = $_POST["dump_check"];
			if($db->update(TABLE_DUMPS, $dump_update, "dump_id='".$dump_id."' AND dump_sellerid = '0'")) {
?>
									<tr>
										<td colspan="4" class="centered">
											<span class="success">Update Dump successfully.</span>
										</td>
									</tr>
<?php
			}
			else {
?>
									<tr>
										<td colspan="4" class="centered">
											<span class="error">Update Dump error.</span>
										</td>
									</tr>
<?php
			}
		}
		$sql = "SELECT user_id, user_name from `".TABLE_USERS."` ORDER BY user_name";
		$allUsers = $db->fetch_array($sql);
		$sql = "SELECT *, AES_DECRYPT(dump_number, '".strval(DB_ENCRYPT_PASS)."') AS dump_number, AES_DECRYPT(dump_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS dump_fullinfo FROM `".TABLE_DUMPS."` WHERE dump_id = '".$dump_id."' AND dump_sellerid = '0'";
		$records = $db->fetch_array($sql);
		if (count($records)>0) {
			$value = $records[0];
?>
								<form method="POST" action="">
									<tr>
										<td colspan="4" class="centered">
											<textarea class="dump_full_info" name="dump_fullinfo" type="text" wrap="on";><?=$value['dump_fullinfo']?></textarea>
										</td>
									</tr>
									<tr>
										<td class="dump_editor">
											Dump number:
										</td>
										<td>
											<input class="dump_value_editor" name="dump_number" type="text" value="<?=$value['dump_number']?>" />
										</td>
										<td class="dump_editor">
											Category:
										</td>
										<td>
											<select class="dump_value_editor" name="dump_categoryid">
<?php
			$sql = "SELECT * FROM `".TABLE_DUMP_CATEGORYS."` WHERE dump_category_sellerid = '0'";
			$categorys = $db->fetch_array($sql);
			if (is_array($categorys) && count($categorys) > 0) {
				foreach ($categorys as $category) {
?>
							<option value="<?=$category["dump_category_id"]?>"<?=($category["dump_category_id"]==$value["dump_categoryid"])?" selected":""?>><?=$category["dump_category_name"]?></option>
<?php
				}
			}
?>
												<option value="0"<?=(strval($value["dump_categoryid"])=="0")?" selected":""?>>(No Category)</option>
											</select>
										</td>
									</tr>
									<tr>
										<td class="dump_editor">
											Dump Exp:
										</td>
										<td>
											<input class="dump_value_editor" name="dump_exp" type="text" value="<?=$value['dump_exp']?>" />
										</td>
										<td class="dump_editor">
											Dump Type:
										</td>
										<td>
											<input class="dump_value_editor" name="dump_type" type="text" value="<?=$value['dump_type']?>" />
										</td>
									</tr>
									<tr>
										<td class="dump_editor">
											Dump Country:
										</td>
										<td>
											<input class="dump_value_editor" name="dump_country" type="text" value="<?=$value['dump_country']?>" />
										</td>
										<td class="dump_editor">
											Dump Bank:
										</td>
										<td>
											<input class="dump_value_editor" name="dump_bank" type="text" value="<?=$value['dump_bank']?>" />
										</td>
									</tr>
									<tr>
										<td class="dump_editor">
											Dump Level:
										</td>
										<td>
											<input class="dump_value_editor" name="dump_level" type="text" value="<?=$value['dump_level']?>" />
										</td>
										<td class="dump_editor">
											Dump Credit Type:
										</td>
										<td>
											<input class="dump_value_editor" name="dump_ctype" type="text" value="<?=$value['dump_ctype']?>" />
										</td>
									</tr>
									<tr>
										<td class="dump_editor">
											Dump Track 1:
										</td>
										<td>
											<input class="dump_value_editor" name="dump_tr1" type="checkbox" <?=($value['dump_tr1']==1)?"checked ":""?>/>
										</td>
										<td class="dump_editor">
											Dump Track 2:
										</td>
										<td>
											<input class="dump_value_editor" name="dump_tr2" type="checkbox" <?=($value['dump_tr2']==1)?"checked ":""?>/>
										</td>
									</tr>
									<tr>
										<td class="dump_editor">
											Dump Price:
										</td>
										<td>
											<input class="dump_value_editor" name="dump_price" type="text" value="<?=$value['dump_price']?>" />
										</td>
										<td class="dump_editor">
											Dump Code:
										</td>
										<td>
											<input class="dump_value_editor" name="dump_code" type="text" value="<?=$value['dump_code']?>" />
										</td>
									</tr>
									<tr>
										<td class="dump_editor">
											Dump Used by:
										</td>
										<td>
											<select class="dump_value_editor" name="dump_userid">
												<option value="0">--Unsold--</option>
<?php
			if (count($allUsers) > 0){
				foreach ($allUsers as $k=>$v) {
?>
												<option value="<?=$v["user_id"]?>" <?=($v["user_id"]==$value["dump_userid"])?"selected ":""?>><?=$v["user_name"]?></option>
<?php
				}
			}
?>
											</select>
										</td>
										<td class="dump_editor">
											Dump Check Status:
										</td>
										<td>
											<select class="dump_value_editor" name="dump_check">
												<option value="<?=strval(CHECK_DEFAULT)?>" <?=(strval(CHECK_DEFAULT)==$value["dump_check"])?"selected ":""?>>UNCHECK</option>
												<option value="<?=strval(CHECK_INVALID)?>" <?=(strval(CHECK_INVALID)==$value["dump_check"])?"selected ":""?>>INVALID</option>
												<option value="<?=strval(CHECK_VALID)?>" <?=(strval(CHECK_VALID)==$value["dump_check"])?"selected ":""?>>APPROVED</option>
												<option value="<?=strval(CHECK_REFUND)?>" <?=(strval(CHECK_REFUND)==$value["dump_check"])?"selected ":""?>>DECLINE</option>
												<option value="<?=strval(CHECK_UNKNOWN)?>" <?=(strval(CHECK_UNKNOWN)==$value["dump_check"])?"selected ":""?>>UNKNOWN</option>
												<option value="<?=strval(CHECK_WAIT_REFUND)?>" <?=(strval(CHECK_WAIT_REFUND)==$value["dump_check"])?"selected ":""?>>WAIT REFUND</option>
											</select>
										</td>
									</tr>
									<tr>
										<td colspan="4" class="centered">
											<input type="submit" name="dump_edit_save" value="Save" /><input onclick="window.location='./dumps.php'"type="button" name="dump_edit_cancel" value="Cancel" />
										</td>
									</tr>
								</form>
<?php
		}
		else {
?>
								<tr>
									<td class="error">
										<span class="error">Dump ID Invalid.</span>
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
	else if ($_POST["multi_edit"] != "" && $_POST["dumps"] != "" && is_array($_POST["dumps"])) {
		$dumps_id = $_POST["dumps"];
		$dumps_sql = "";
		if (count($dumps_id) > 0) {
			$dumps_sql = "'".$db->escape($dumps_id[count($dumps_id) - 1])."'";
			unset($dumps_id[count($dumps_id)-1]);
			foreach ($dumps_id as $v) {
				$dumps_sql .= ", '".$db->escape($v)."'";
			}
		}
?>
				<div id="dumps">
					<div class="section_title">MULTI DUMPS EDITOR</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		if (isset($_POST["dump_edit_save"])) {
			if ($_POST["dump_categoryid"] != "") {
				$dump_update["dump_categoryid"] = $_POST["dump_categoryid"];
			}
			if ($_POST["dump_type"] != "") {
				$dump_update["dump_type"] = $_POST["dump_type"];
			}
			if ($_POST["dump_country"] != "") {
				$dump_update["dump_country"] = $_POST["dump_country"];
			}
			if ($_POST["dump_bank"] != "") {
				$dump_update["dump_bank"] = $_POST["dump_bank"];
			}
			if ($_POST["dump_level"] != "") {
				$dump_update["dump_level"] = $_POST["dump_level"];
			}
			if ($_POST["dump_ctype"] != "") {
				$dump_update["dump_ctype"] = $_POST["dump_ctype"];
			}
			if ($_POST["dump_code"] != "") {
				$dump_update["dump_code"] = $_POST["dump_code"];
			}
			if ($_POST["dump_price"] != "") {
				$dump_update["dump_price"] = $_POST["dump_price"];
			}
			if ($_POST["dump_tr1"] != "") {
				$dump_update["dump_tr1"] = $_POST["dump_tr1"];
			}
			if ($_POST["dump_tr2"] != "") {
				$dump_update["dump_tr2"] = $_POST["dump_tr2"];
			}
			if ($_POST["dump_userid"] != "") {
				$dump_update["dump_userid"] = $_POST["dump_userid"];
			}
			if ($_POST["dump_check"] != "") {
				$dump_update["dump_check"] = $_POST["dump_check"];
			}
			if (count($dump_update) > 0) {
				if($db->update(TABLE_DUMPS, $dump_update, "dump_id IN (".$dumps_sql.") AND dump_sellerid = '0'")) {
?>
									<tr>
										<td colspan="5" class="centered">
											<span class="success">Update Dumps successfully.</span>
										</td>
									</tr>
<?php
				}
				else {
?>
									<tr>
										<td colspan="5" class="centered">
											<span class="error">Update Dumps error.</span>
										</td>
									</tr>
<?php
				}
			}
		}
		$sql = "SELECT user_id, user_name from `".TABLE_USERS."` ORDER BY user_name";
		$allUsers = $db->fetch_array($sql);
		$sql = "SELECT *, AES_DECRYPT(dump_number, '".strval(DB_ENCRYPT_PASS)."') AS dump_number, AES_DECRYPT(dump_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS dump_fullinfo FROM `".TABLE_DUMPS."` LEFT JOIN (`".TABLE_USERS."` LEFT JOIN `".TABLE_GROUPS."` ON ".TABLE_USERS.".user_groupid = ".TABLE_GROUPS.".group_id) ON ".TABLE_DUMPS.".dump_userid = ".TABLE_USERS.".user_id LEFT JOIN `".TABLE_DUMP_CATEGORYS."` ON ".TABLE_DUMPS.".dump_categoryid = ".TABLE_DUMP_CATEGORYS.".dump_category_id WHERE dump_id IN (".$dumps_sql.") AND dump_sellerid = '0'";
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
															<span>DUMP NUMBER</span>
														</td>
														<td class="formstyle bold centered">
															<span>CATEGORY</span>
														</td>
														<td class="formstyle bold centered">
															<span>TYPE</span>
														</td>
														<td class="formstyle bold centered">
															<span>COUNTRY</span>
														</td>
														<td class="formstyle bold centered">
															<span>BANK</span>
														</td>
														<td class="formstyle bold centered">
															<span>LEVEL</span>
														</td>
														<td class="formstyle bold centered">
															<span>CREDIT TYPE</span>
														</td>
														<td class="formstyle bold centered">
															<span>CODE</span>
														</td>
														<td class="formstyle bold centered">
															<span>TRACK</span>
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
														<input type="HIDDEN" name="dumps[]" value="<?=$value['dump_id']?>" />
														<td class="centered">
															<span>
																<?=$value['dump_number']?>
															</span>
														</td>
														<td class="centered bold">
															<span>
																<?=($value['dump_category_name']=="")?"(No Category)":$value['dump_category_name']?>
															</span>
														</td>
														<td class="centered bold">
															<span>
																<?=$value['dump_type']?>
															</span>
														</td>
														<td class="centered bold">
															<span>
																<?=$value['dump_country']?>
															</span>
														</td>
														<td class="centered bold">
															<span>
																<?=$value['dump_bank']?>
															</span>
														</td>
														<td class="centered bold">
															<span>
																<?=$value['dump_level']?>
															</span>
														</td>
														<td class="centered bold">
															<span>
																<?=$value['dump_ctype']?>
															</span>
														</td>
														<td class="centered bold">
															<span>
																<?=$value['dump_code']?>
															</span>
														</td>
														<td class="centered bold">
															<span>
																<?=($value['dump_tr1'] == '1')?"TR1":""?></span><?=($value['dump_tr1'] == 1 && $value['dump_tr2'] == 1)?"+":""?><?=($value['dump_tr1'] == 0 && $value['dump_tr2'] == 0)?" - ":""?><span><?=($value['dump_tr2'] == 1)?"TR2":""?>
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
				switch ($value['dump_check']) {
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
															<span><?=$value['dump_price']?></span>
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
										<td class="dump_editor">
											Category:
										</td>
										<td>
											<select class="dump_value_editor" name="dump_categoryid">
												<option value="">(No Change)</option>
<?php
			$sql = "SELECT * FROM `".TABLE_DUMP_CATEGORYS."` WHERE dump_category_sellerid = '0'";
			$categorys = $db->fetch_array($sql);
			if (is_array($categorys) && count($categorys) > 0) {
				foreach ($categorys as $value) {
?>
							<option value="<?=$value["dump_category_id"]?>"<?=($value["dump_category_id"]==$_POST["dump_categoryid"])?" selected":""?>><?=$value["dump_category_name"]?></option>
<?php
				}
			}
?>
												<option value="0"<?=(strval($_POST["dump_categoryid"])=="0")?" selected":""?>>(No Category)</option>
											</select>
										</td>
										<td class="dump_editor">
											Dump Type:
										</td>
										<td>
											<input class="dump_value_editor" name="dump_type" type="text" value="" />
										</td>
									</tr>
									<tr>
										<td class="dump_editor">
											Dump Country:
										</td>
										<td>
											<input class="dump_value_editor" name="dump_country" type="text" value="" />
										</td>
										<td class="dump_editor">
											Dump Bank:
										</td>
										<td>
											<input class="dump_value_editor" name="dump_bank" type="text" value="" />
										</td>
									</tr>
									<tr>
										<td class="dump_editor">
											Dump Level:
										</td>
										<td>
											<input class="dump_value_editor" name="dump_level" type="text" value="" />
										</td>
										<td class="dump_editor">
											Dump Credit Type:
										</td>
										<td>
											<input class="dump_value_editor" name="dump_ctype" type="text" value="" />
										</td>
									</tr>
									<tr>
										<td class="dump_editor">
											Dump Code:
										</td>
										<td>
											<input class="dump_value_editor" name="dump_code" type="text" value="" />
										</td>
										<td class="dump_editor">
											Dump Price:
										</td>
										<td>
											<input class="dump_value_editor" name="dump_price" type="text" value="" />
										</td>
									</tr>
									<tr>
										<td class="dump_editor">
											Dump Track 1:
										</td>
										<td>
											<select class="dump_value_editor" name="dump_tr1">
												<option value="">(No Change)</option>
												<option value="1">Yes</option>
												<option value="0">No</option>
											</select>
										</td>
										<td class="dump_editor">
											Dump Track 2:
										</td>
										<td>
											<select class="dump_value_editor" name="dump_tr2">
												<option value="">(No Change)</option>
												<option value="1">Yes</option>
												<option value="0">No</option>
											</select>
										</td>
									</tr>
									<tr>
										<td class="dump_editor">
											Dump Used by:
										</td>
										<td>
											<select class="dump_value_editor" name="dump_userid">
												<option value="">(No Change)</option>
												<option value="0">--Unsold--</option>
<?php
			if (count($allUsers) > 0){
				foreach ($allUsers as $k=>$v) {
?>
												<option value="<?=$v["user_id"]?>"><?=$v["user_name"]?></option>
<?php
				}
			}
?>
											</select>
										</td>
										<td class="dump_editor">
											Dump Check Status:
										</td>
										<td>
											<select class="dump_value_editor" name="dump_check">
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
										<td colspan="5" class="centered">
											<input type="submit" name="dump_edit_save" value="Save" /><input onclick="window.location='./dumps.php'"type="button" name="dump_edit_cancel" value="Cancel" />
										</td>
									</tr>
								</form>
<?php
		}
		else {
?>
								<tr>
									<td class="error">
										<span class="error">Dumps ID Invalid.</span>
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
	else if ($_GET["act"] == "refund" && $_GET["dump_id"] != "") {
		$dump_id = $db->escape($_GET["dump_id"]);
?>
				<div id="dumps">
					<div class="section_title">DUMP EDITOR</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		$sql = "SELECT dump_check, dump_userid, dump_sellerid, dump_price FROM `".TABLE_DUMPS."` WHERE dump_userid > 0 AND dump_id = '".$dump_id."' AND dump_sellerid = '0'";
		$records = $db->fetch_array($sql);
		if (count($records)>0) {
			$value = $records[0];
			$sql = "SELECT user_id, user_balance from `".TABLE_USERS."` WHERE user_id = '".$value["dump_userid"]."'";
			$dump_user = $db->fetch_array($sql);
			if ($value["dump_sellerid"] <> 0) {
				$sql = "SELECT user_id, user_balance from `".TABLE_USERS."` WHERE user_id = '".$value["dump_sellerid"]."' AND user_groupid = '".strval(PER_SELLER)."'";
				$dump_seller = $db->fetch_array($sql);
			}
			if (count($dump_user)>0) {
				$dump_user = $dump_user[0];
				if ($value["dump_sellerid"] == 0 || count($dump_seller)>0) {
					if ($value["dump_sellerid"] <> 0) {
						$dump_seller = $dump_seller[0];
					}
					if ($value["dump_sellerid"] == 0 || doubleval($dump_seller["user_balance"]) >= (doubleval($value["dump_price"])*(1-$db_config["commission"]))) {
						if ($value["dump_check"] != strval(CHECK_REFUND)) {
							$user_update["user_balance"] = doubleval($dump_user["user_balance"])+doubleval($value["dump_price"]);
							if ($value["dump_sellerid"] <> 0) {
								$seller_update["user_balance"] = doubleval($dump_seller["user_balance"])-(doubleval($value["dump_price"])*(1-$db_config["commission"]));
							}
							$dump_update["dump_check"] = strval(CHECK_REFUND);
							if($db->update(TABLE_DUMPS, $dump_update, "dump_id='".$dump_id."' AND dump_sellerid = '0'") && $db->update(TABLE_USERS, $user_update, "user_id='".$value["dump_userid"]."'") && ($value["dump_sellerid"] == 0 || $db->update(TABLE_USERS, $seller_update, "user_id='".$value["dump_sellerid"]."'"))) {
?>
								<tr>
									<td colspan="4" class="centered">
										<span class="success">Refund Dump successfully.</span>
									</td>
								</tr>
<?php
							}
							else {
?>
								<tr>
									<td colspan="4" class="centered">
										<span class="error">Refund Dump error.</span>
									</td>
								</tr>
<?php
							}
						}
						else {
	?>
								<tr>
									<td colspan="4" class="centered">
										<span class="error">This dump is refunded.</span>
									</td>
								</tr>
<?php
						}
					}
					else {
	?>
								<tr>
									<td colspan="4" class="centered">
										<span class="error">This seller doesn't has enought money to refund.</span>
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
										<span class="error">Dump ID Invalid.</span>
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
	else if ($_GET["act"] == "delete" && $_GET["dump_id"] != "") {
		$dump_id = $db->escape($_GET["dump_id"]);
		$sql = "DELETE FROM `".TABLE_DUMPS."` WHERE dump_id = '".$dump_id."' AND dump_sellerid = '0'";
		if ($db->query($sql) && $db->affected_rows > 0) {
?>
				<script type="text/javascript">setTimeout("window.location = './dumps.php'", 1000);</script>
				<div id="dumps">
					<div class="section_title">DUMP DELETE</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<tr class="centered">
									<td class="success">
										Delete Dump ID <?=$dump_id?> successfully.
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
				<div id="dumps">
					<div class="section_title">DUMP DELETE</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<tr>
									<td class="error">
										Dump ID Invalid.
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
		if ($_POST["delete_expired"] != "") {
			$sql = "DELETE FROM `".TABLE_DUMPS."` WHERE dump_year < ".date("ym")." dump_sellerid = '0'";
			if ($db->query($sql)) {
				$deleteResult=<<<END
										<td colspan="13" class="success">
											Delete all expired dumps successfully.
										</td>
END;
			}
			else {
				$deleteResult=<<<END
										<td colspan="13" class="error">
											Delete expired dumps error.
										</td>
END;
			}
		} else if ($_POST["delete_invalid"] != "") {
			$sql = "DELETE FROM `".TABLE_DUMPS."` WHERE dump_check = '".strval(CHECK_INVALID)."' OR dump_check = '".strval(CHECK_REFUND)."' AND dump_sellerid = '0'";
			if ($db->query($sql)) {
				$deleteResult=<<<END
										<td colspan="13" class="success">
											Delete all invalid and refund dumps successfully.
										</td>
END;
			}
			else {
				$deleteResult=<<<END
										<td colspan="13" class="error">
											Delete invalid and refund dumps error.
										</td>
END;
			}
		} else if ($_POST["delete_select"] != "" && $_POST["dumps"] != "" && is_array($_POST["dumps"])) {
			$allDumps = $_POST["dumps"];
			$countDeleteRows = count($allDumps);
			$lastDumps = $db->escape($allDumps[count($allDumps)-1]);
			unset($allDumps[count($allDumps)-1]);
			$sql = "DELETE FROM `".TABLE_DUMPS."` WHERE dump_id IN (";
			if (count($allDumps) > 0) {
				foreach ($allDumps as $key=>$value) {
					$sql .= "'".$db->escape($value)."', ";
				}
			}
			$sql .= "'".$lastDumps."') AND dump_sellerid = '0'";
			if ($db->query($sql) && $db->affected_rows > 0) {
				if ($db->affected_rows == $countDeleteRows) {
					$deleteResult=<<<END
										<td colspan="13" class="success">
											Delete selected dumps successfully.
										</td>
END;
				} else {
					$countDeletedRows = $countDeleteRows-$db->affected_rows;
					$deleteResult=<<<END
										<td colspan="13" class="error">
											Delete {$countDeletedRows} of {$countDeleteRows} selected dumps error, please check again.
										</td>
END;
				}
			}
			else {
				$deleteResult=<<<END
										<td colspan="13" class="error">
											Delete selected dumps error.
										</td>
END;
			}
		}
		
		if ($_GET["lstCategory"] != "") {
			$_GET["lstCategory"] = intval($_GET["lstCategory"]);
			if ($_GET["lstCategory"] > 0) {
				$searchCategory = "dump_categoryid = '".$db->escape($_GET["lstCategory"])."'";
			} else if ($_GET["lstCategory"] == 0) {
				$searchCategory = "dump_categoryid NOT IN (SELECT dump_category_id FROM `".TABLE_DUMP_CATEGORYS."` WHERE dump_category_sellerid = '0')";
			} else {
				$searchCategory = "1";
				$_GET["lstCategory"] = "";
			}
		} else {
			$searchCategory = "1";
		}
		if (isset($_GET["btnSearch"])) {
			$currentGet = "";
			$currentGet .= "txtBin=".$_GET["txtBin"]."&lstCategory=".$_GET["lstCategory"]."&lstCountry=".$_GET["lstCountry"]."&lstBank=".$_GET["lstBank"]."&lstType=".$_GET["lstType"]."&lstLevel=".$_GET["lstLevel"]."&lstCtype=".$_GET["lstCtype"]."&lstCode=".$_GET["lstCode"]."&lstExpire=".$_GET["lstExpire"]."&lstStatus=".$_GET["lstStatus"]."&lstCheck=".$_GET["lstCheck"];
			$currentGet .= (strtolower($_GET["checkboxTr1"])=="on")?"&checkboxTr1=1":"";
			$currentGet .= (strtolower($_GET["checkboxTr2"])=="on")?"&checkboxTr2=1":"";
			$currentGet .= "&btnSearch=Search&";
		}
		$searchBin = $db->escape($_GET["txtBin"]);
		$searchCountry = $db->escape($_GET["lstCountry"]);
		$searchBank = $db->escape($_GET["lstBank"]);
		$searchType = $db->escape($_GET["lstType"]);
		$searchLevel = $db->escape($_GET["lstLevel"]);
		$searchCtype = $db->escape($_GET["lstCtype"]);
		$searchCode = $db->escape($_GET["lstCode"]);
		$searchTr1 = ($_GET["checkboxTr1"] == "on")?" AND dump_tr1 = '1'":"";
		$searchTr2 = ($_GET["checkboxTr2"] == "on")?" AND dump_tr2 = '1'":"";
		switch ($_GET["lstAvailable"]) {
			case "unsold":
				$searchAvailable = "dump_userid = '0'";
				break;
			case "sold":
				$searchAvailable = "dump_userid <> '0'";
				break;
			default:
				if (intval($_GET["lstAvailable"]) > 0) {
					$searchAvailable = "dump_userid = '".$db->escape(intval($_GET["lstAvailable"]))."'";
				} else {
					$searchAvailable = "1";
				}
				break;
		}
		switch ($_GET["lstExpire"]) {
			case strval(EXPIRE_FUTURE):
				$searchExpire = "(dump_exp > '".date("ym")."')";
				break;
			case strval(EXPIRE_STAGNANT):
				$searchExpire = "(dump_exp = '".date("ym")."')";
				break;
			case strval(EXPIRE_EXPIRED):
				$searchExpire = "(dump_exp < '".date("ym")."')";
				break;
			default:
				$searchExpire = "1";
				break;
		}
		switch ($_GET["lstStatus"]) {
			case strval(STATUS_DEFAULT):
				$searchStatus = "dump_status = '".strval(STATUS_DEFAULT)."'";
				break;
			case strval(STATUS_DELETED):
				$searchStatus = "dump_status = '".strval(STATUS_DELETED)."'";
				break;
			case strval(STATUS_STAGNANT):
				$searchStatus = "dump_status = '".strval(STATUS_STAGNANT)."'";
				break;
			case strval(STATUS_EXPIRED):
				$searchStatus = "dump_status = '".strval(STATUS_EXPIRED)."'";
				break;
			default:
				$searchStatus = "1";
				break;
		}
		switch ($_GET["lstCheck"]) {
			case strval(CHECK_DEFAULT):
				$searchCheck = "dump_check = '".strval(CHECK_DEFAULT)."'";
				break;
			case strval(CHECK_VALID):
				$searchCheck = "dump_check = '".strval(CHECK_VALID)."'";
				break;
			case strval(CHECK_INVALID):
				$searchCheck = "dump_check = '".strval(CHECK_INVALID)."'";
				break;
			case strval(CHECK_REFUND):
				$searchCheck = "dump_check = '".strval(CHECK_REFUND)."'";
				break;
			case strval(CHECK_UNKNOWN):
				$searchCheck = "dump_check = '".strval(CHECK_UNKNOWN)."'";
				break;
			case strval(CHECK_WAIT_REFUND):
				$searchCheck = "dump_check = '".strval(CHECK_WAIT_REFUND)."'";
				break;
			default:
				$searchCheck = "1";
				break;
		}
		$sql = "SELECT count(*) FROM `".TABLE_DUMPS."` WHERE ".$searchCategory." AND ".$searchExpire." AND ".$searchStatus." AND ".$searchCheck." AND ".$searchAvailable." AND ('".$searchBin."'='' OR AES_DECRYPT(dump_number, '".strval(DB_ENCRYPT_PASS)."') LIKE '".$searchBin."%') AND ('".$searchCountry."'='' OR dump_country = '".$searchCountry."') AND ('".$searchBank."'='' OR dump_bank = '".$searchBank."') AND ('".$searchType."'='' OR dump_type = '".$searchType."') AND ('".$searchLevel."'='' OR dump_level = '".$searchLevel."') AND ('".$searchCtype."'='' OR dump_ctype = '".$searchCtype."') AND ('".$searchCode."'='' OR dump_code = '".$searchCode."')".$searchTr1.$searchTr2." AND dump_sellerid = '0'";
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
		$sql = "SELECT ".TABLE_DUMPS.".*, AES_DECRYPT(".TABLE_DUMPS.".dump_number, '".strval(DB_ENCRYPT_PASS)."') AS dump_number, ".TABLE_USERS.".user_name, ".TABLE_GROUPS.".group_color, ".TABLE_DUMP_CATEGORYS.".* FROM `".TABLE_DUMPS."` LEFT JOIN `".TABLE_USERS."` ON ".TABLE_DUMPS.".dump_userid = ".TABLE_USERS.".user_id LEFT JOIN `".TABLE_GROUPS."` ON ".TABLE_USERS.".user_groupid = ".TABLE_GROUPS.".group_id LEFT JOIN `".TABLE_DUMP_CATEGORYS."` ON ".TABLE_DUMPS.".dump_categoryid = ".TABLE_DUMP_CATEGORYS.".dump_category_id WHERE ".$searchCategory." AND ".$searchExpire." AND ".$searchStatus." AND ".$searchCheck." AND ".$searchAvailable." AND ('".$searchBin."'='' OR AES_DECRYPT(dump_number, '".strval(DB_ENCRYPT_PASS)."') LIKE '".$searchBin."%') AND ('".$searchCountry."'='' OR dump_country = '".$searchCountry."') AND ('".$searchBank."'='' OR dump_bank = '".$searchBank."') AND ('".$searchType."'='' OR dump_type = '".$searchType."') AND ('".$searchLevel."'='' OR dump_level = '".$searchLevel."') AND ('".$searchCtype."'='' OR dump_ctype = '".$searchCtype."') AND ('".$searchCode."'='' OR dump_code = '".$searchCode."')".$searchTr1.$searchTr2." AND dump_sellerid = '0' ORDER BY dump_id LIMIT ".(($page-1)*$perPage).",".$perPage;
		$listdumps = $db->fetch_array($sql);
?>
				<div id="search_dumps">
					<div class="section_title">SEARCH DUMPS</div>
					<div class="section_content">
						<table class="content_table centered">
							<tbody>
								<form name="search" method="GET" action="dumps.php">
									<tr>
										<td class="formstyle">
											<span class="bold">DUMP NUMBER</span>
										</td>
										<td class="formstyle">
											<span class="bold">CATEGORY</span>
										</td>
										<td class="formstyle">
											<span class="bold">COUNTRY</span>
										</td>
										<td class="formstyle">
											<span class="bold">BANK</span>
										</td>
										<td class="formstyle">
											<span class="bold">LEVEL</span>
										</td>
										<td class="formstyle">
											<span class="bold">CODE</span>
										</td>
									</tr>
									<tr>
										<td>
											<input name="txtBin" type="text" class="formstyle" id="txtBin" value="<?=$_GET["txtBin"]?>" size="15" maxlength="16">
										</td>
										<td>
											<select name="lstCategory" class="formstyle" id="lstCategory">
												<option value="">(All Category)</option>
<?php
		$sql = "SELECT * FROM `".TABLE_DUMP_CATEGORYS."` WHERE dump_category_sellerid = '0'";
		$allCategory = $db->fetch_array($sql);
		if (count($allCategory) > 0) {
			foreach ($allCategory as $category) {
				echo "<option value=\"".$category['dump_category_id']."\"".(($_GET["lstCategory"] == $category['dump_category_id'])?" selected":"").">".$category['dump_category_name']."</option>";
			}
		}
?>
												<option value="0" <?=(strval($_GET["lstCategory"]) == "0")?" selected":""?>>(No Category)</option>
											</select>
										</td>
										<td>
											<select name="lstCountry" class="formstyle" id="lstCountry">
												<option value="">All Country</option>
<?php
		$sql = "SELECT DISTINCT dump_country FROM `".TABLE_DUMPS."` WHERE dump_sellerid = '0' ORDER BY dump_country";
		$allCountry = $db->fetch_array($sql);
		if (count($allCountry) > 0) {
			foreach ($allCountry as $country) {
				echo "<option value=\"".$country['dump_country']."\"".(($_GET["lstCountry"] == $country['dump_country'])?" selected":"").">".$country['dump_country']."</option>";
			}
		}
?>
											</select>
										</td>
										<td>
											<select name="lstBank" class="formstyle" id="lstBank">
												<option value="">All Bank</option>
<?php
		$sql = "SELECT DISTINCT dump_bank FROM `".TABLE_DUMPS."` WHERE dump_sellerid = '0' ORDER BY dump_bank";
		$allCountry = $db->fetch_array($sql);
		if (count($allCountry) > 0) {
			foreach ($allCountry as $country) {
				echo "<option value=\"".$country['dump_bank']."\"".(($_GET["lstBank"] == $country['dump_bank'])?" selected":"").">".$country['dump_bank']."</option>";
			}
		}
?>
											</select>
										</td>
										<td>
											<select name="lstLevel" class="formstyle" id="lstLevel">
												<option value="">All Level</option>
<?php
		$sql = "SELECT DISTINCT dump_level FROM `".TABLE_DUMPS."` WHERE dump_sellerid = '0 ORDER BY dump_level'";
		$allCountry = $db->fetch_array($sql);
		if (count($allCountry) > 0) {
			foreach ($allCountry as $country) {
				echo "<option value=\"".$country['dump_level']."\"".(($_GET["lstLevel"] == $country['dump_level'])?" selected":"").">".$country['dump_level']."</option>";
			}
		}
?>
											</select>
										</td>
										<td>
											<select name="lstCode" class="formstyle" id="lstCode">
												<option value="">All Code</option>
<?php
		$sql = "SELECT DISTINCT dump_code FROM `".TABLE_DUMPS."` WHERE dump_sellerid = '0 ORDER BY dump_code'";
		$allCountry = $db->fetch_array($sql);
		if (count($allCountry) > 0) {
			foreach ($allCountry as $country) {
				echo "<option value=\"".$country['dump_code']."\"".(($_GET["lstCode"] == $country['dump_code'])?" selected":"").">".$country['dump_code']."</option>";
			}
		}
?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="formstyle">
											<span class="bold">TRACK 1</span>
										</td>
										<td class="formstyle">
											<span class="bold">TRACK 2</span>
										</td>
										<td class="formstyle">
											<span class="bold">AVAILABLE</span>
										</td>
										<td class="formstyle">
											<span class="bold">EXPIRE TYPE</span>
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
											<span><input type="checkbox" name="checkboxTr1" id="checkboxTr1" <?=($_GET["checkboxTr1"] != "")?"checked ":""?>></span>
										</td>
										<td>
											<span><input type="checkbox" name="checkboxTr2" id="checkboxTr2" <?=($_GET["checkboxTr2"] != "")?"checked ":""?>></span>
										</td>
										<td>
											<select name="lstAvailable" class="formstyle" id="lstAvailable">
												<option value="" <?=(($_GET["lstAvailable"] == "")?" selected":"")?>>ALL</option>
												<option value="unsold" <?=(($_GET["lstAvailable"] == "unsold")?" selected":"")?>>UNSOLD</option>
												<option value="sold" <?=(($_GET["lstAvailable"] == "sold")?" selected":"")?>>SOLD</option>
											</select>
										</td>
										<td>
											<select name="lstExpire" class="formstyle" id="lstExpire">
												
												<option value="" <?=(($_GET["lstExpire"] == "")?" selected":"")?>>ALL</option>
												<option value="<?=strval(EXPIRE_FUTURE)?>" <?=(($_GET["lstExpire"] == strval(EXPIRE_FUTURE))?" selected":"")?>>FUTURE</option>
												<option value="<?=strval(EXPIRE_STAGNANT)?>" <?=(($_GET["lstExpire"] == strval(EXPIRE_STAGNANT))?" selected":"")?>>THIS MONTH</option>
												<option value="<?=strval(EXPIRE_EXPIRED)?>" <?=(($_GET["lstExpire"] == strval(EXPIRE_EXPIRED))?" selected":"")?>>EXPIRED</option>
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
												<option value="<?=strval(CHECK_VALID)?>" <?=(($_GET["lstCheck"] == strval(CHECK_VALID))?" selected":"")?>>VALID</option>
												<option value="<?=strval(CHECK_INVALID)?>" <?=(($_GET["lstCheck"] == strval(CHECK_INVALID))?" selected":"")?>>TIMEOUT</option>
												<option value="<?=strval(CHECK_REFUND)?>" <?=(($_GET["lstCheck"] == strval(CHECK_REFUND))?" selected":"")?>>DECLINE</option>
												<option value="<?=strval(CHECK_UNKNOWN)?>" <?=(($_GET["lstCheck"] == strval(CHECK_UNKNOWN))?" selected":"")?>>UNKNOWN</option>
												<option value="<?=strval(CHECK_WAIT_REFUND)?>" <?=(($_GET["lstCheck"] == strval(CHECK_WAIT_REFUND))?" selected":"")?>>WAIT REFUND</option>
											</select>
										</td>
									</tr>
									<tr>
										<td colspan="6">
											<input name="btnSearch" type="submit" class="formstyle" id="btnSearch" value="Search">
										</td>
									</tr>
								</form>
							</tbody>
						</table>
					</div>
				</div>
				<div id="dumps">
					<div class="section_title">AVAILABLE DUMPS</div>
					<div class="section_title"><a href="?act=import">Import Dumps</a></div>
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
								<form name="dumps" method="POST" action="">
									<tr>
										<?=$deleteResult?>
									</tr>
									<tr>
										<td class="formstyle centered">
											<span class="bold">DUMP NUMBER</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">CATEGORY</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">EXPIRE</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">COUNTRY</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">BANK</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">LEVEL</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">CREDIT TYPE</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">CODE</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">TRACK</span>
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
											<input class="formstyle" type="checkbox" name="selectAllDumps" id="selectAllDumps" onclick="checkAll(this.id, 'dumps[]')" value="">
										</td>
									</tr>
<?php
		if (count($listdumps) > 0) {
			foreach ($listdumps as $key=>$value) {
?>
									<tr class="formstyle">
										<td class="centered bold">
											<span><?=$value['dump_number']?></span>
										</td>
										<td class="centered bold">
											<span><?=($value['dump_category_name']=="")?"(No Category)":$value['dump_category_name']?></span>
										</td>
										<td class="centered">
											<span><?=$value['dump_exp']?></span>
										</td>
										<td class="centered">
											<span><?=$value['dump_country']?></span>
										</td>
										<td class="centered">
											<span><?=$value['dump_bank']?></span>
										</td>
										<td class="centered">
											<span><?=$value['dump_level']?></span>
										</td>
										<td class="centered">
											<span><?=$value['dump_ctype']?></span>
										</td>
										<td class="centered">
											<span><?=$value['dump_code']?></span>
										</td>
										<td class="centered">
											<span><?=($value['dump_tr1'] == '1')?"TR1":""?></span><?=($value['dump_tr1'] == 1 && $value['dump_tr2'] == 1)?"+":""?><?=($value['dump_tr1'] == 0 && $value['dump_tr2'] == 0)?" - ":""?><span><?=($value['dump_tr2'] == 1)?"TR2":""?></span>
										</td>
										<td class="centered">
<?php
				switch ($value['dump_userid']) {
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
				switch ($value['dump_check']) {
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
											<a href="?act=edit&dump_id=<?=$value['dump_id']?>">Edit</a>
											 | <a href="?act=delete&dump_id=<?=$value['dump_id']?>" onClick="return confirm('Are you sure you want to DELETE this Dumps?')">Delete</a>
<?php
				if ($value['dump_userid'] > 0 && $value['dump_check'] != strval(CHECK_REFUND)) {
?>
											 | <a href="?act=refund&dump_id=<?=$value['dump_id']?>" onClick="return confirm('Are you sure you want to REFUND this Dumps?')">Refund</a>
<?php
				}
?>
											</span>
										</td>
										<td class="centered">
											<input class="formstyle" type="checkbox" name="dumps[]" value="<?=$value['dump_id']?>">
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
													<input name="multi_edit" type="submit" id="multi_edit" onClick="" value="Edit Selected Dumps">
												<span> | </span>
												</label>
												<label>
													<input name="delete_expired" type="submit" id="delete_expired" onClick="return confirm('Are you sure you want to delete the EXPIRED Dumps?')" value="Delete Expired Dumps">
												<span> | </span>
												</label>
												<label>
													<input name="delete_invalid" type="submit" id="delete_invalid" onClick="return confirm('Are you sure you want to delete the INVALID Dumps?')" value="Delete Invalid/Refunded Dumps">
												<span> | </span>
												</label>
												<label>
													<input name="delete_select" type="submit" id="delete_select" onClick="return confirm('Are you sure you want to delete the SELECTED Dumps?')" value="Delete Selected Dumps">
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