<?php
// error_reporting(E_ALL);
// ini_set('display_startup_errors',1); 
// ini_set('display_errors',1);
// error_reporting(-1);
require("./header.php");

require("../checkers/checker.php");

if ($checkLogin) {

$sql = "SELECT full_name from `country` ";
$allCountries = $db->fetch_array($sql);
$select_country = "<select name='card_country'>";
foreach($allCountries as $country){
	$select_country .= "<option value='".$country['full_name']."'>".$country['full_name']."</option>";
}
$select_country .= "</select>";
?>
<script>
	//var select_country = "<?php echo $select_country; ?>";
</script>
<?
	if ($_GET["act"] == "import") {
?>
				<div id="cards">
					<div class="section_title">IMPORT CARDS</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
			if (isset($_POST["card_import_save"]) || isset($_POST["card_import_preview"])) {
				foreach ($_POST as &$temp) {
					if ($id == "card_spliter" && $temp != " ") {
						$temp = trim($temp);
					}
				}
				if ($_POST["card_content"] == "") {
					$errorMsg = "Please input card content";
				}
				else if ($_POST["card_categoryid"] == "" || $_POST["card_categoryid"] < 0) {
					$errorMsg = "Please input a valid card category";
				}
				else if ($_POST["card_price"] == "" || $_POST["card_price"] <= 0) {
					$errorMsg = "Please input a valid card price";
				}
				else if ($_POST["card_spliter"] == "") {
					$errorMsg = "Please input card spliter";
				}
				//else if ($_POST["card_number"] == "") {
				//	$errorMsg = "Please input card number position";
				//}
				else if ($_POST["card_month"] == "") {
					$errorMsg = "Please input card exp month position";
				}
				else if ($_POST["card_year"] == "") {
					$errorMsg = "Please input card exp year position";
				//} else if ($_POST["city_select_mode"] == "1" AND $_POST["card_zip"] == "") {
				//	$errorMsg = "Please input zip code position";
				//} else if ($_POST["city_select_mode"] == "2" AND ($_POST["card_city"] == "" || $_POST["card_state"] == "")) {
				//	$errorMsg = "Please input city and state position";
				}
				else {
					if (isset($_POST["card_import_preview"])) {
?>
										<tr>
											<td colspan="8" class="centered">
												<table style="width:786px;margin: 0 auto;">
													<tbody>
														<tr>
															<td class="formstyle centered">
																<span class="bold">CARD NUMBER</span>
															</td>
															<td class="formstyle centered">
																<span class="bold">EXPIRE</span>
															</td>
															<td class="formstyle centered">
																<span>CVV</span>
															</td>
															<td class="formstyle centered">
																<span>NAME</span>
															</td>
															<td class="formstyle centered">
																<span>ADDRESS</span>
															</td>
															<td class="formstyle centered">
																<span>CITY</span>
															</td>
															<td class="formstyle centered">
																<span>STATE</span>
															</td>
															<td class="formstyle centered">
																<span>ZIP</span>
															</td>
															<td class="formstyle centered">
																<span>COUNTRY</span>
															</td>
															<td class="formstyle centered">
																<span>PHONE</span>
															</td>
															<td class="formstyle centered">
																<span>SSN</span>
															</td>
															<td class="formstyle centered">
																<span>DOB</span>
															</td>
														</tr>
<?php
					}
					$_POST["card_content"] = str_replace("\r", "", $_POST["card_content"]);
					$_POST["card_content"] = str_replace(array(" ".$_POST["card_spliter"], $_POST["card_spliter"]." "), $_POST["card_spliter"], $_POST["card_content"]);
					while (substr_count($_POST["card_content"], "\n\n")) {
						$_POST["card_content"] = str_replace("\n\n", "\n", $_POST["card_content"]);
					}
					$card_content = explode("\n", $_POST["card_content"]);
					$card_import["card_categoryid"] = $_POST["card_categoryid"];
					$card_import["card_price"] = $_POST["card_price"];
					foreach ($card_content as $id=>$line) {
						$get_card_number_error = "";
						$card_expired = "";
						$get_zipcode_error = "";
						$get_country_error = "";
						if (strlen($line) > 10) {
							$lineField = explode($_POST["card_spliter"], $line);
							$card_import["card_fullinfo"] = $line;
							$cardNumber = "";
							$cardType = "";
							if (getCardNumber($line, $cardType, $cardNumber)) {
								$card_import["card_number"] = $cardNumber;
							} else {
								$import_get_card_number_error[] = $line." => Get card number error.";
								$get_card_number_error = <<<HTML
								<tr>
									<td colspan="12" class="centered">
										<span class="error">{$line} => Get card number error.</span>
									</td>
								</tr>
HTML;
							}
							$card_import["card_bin"] = substr($card_import["card_number"], 0, 6);
							if ($_POST["card_month"] == $_POST["card_year"]) {
								if (strlen($lineField[$_POST["card_month"] - 1]) == 3) {
									$card_import["card_month"] = substr($lineField[$_POST["card_month"] - 1], 0, 1);
									$card_import["card_year"] = substr($lineField[$_POST["card_month"] - 1], -2);
								} else {
									$card_import["card_month"] = substr($lineField[$_POST["card_month"] - 1], 0, 2);
									$card_import["card_year"] = substr($lineField[$_POST["card_month"] - 1], -2);
								}
							}
							else {
								$card_import["card_month"] = $lineField[$_POST["card_month"] - 1];
								$card_import["card_year"] = $lineField[$_POST["card_year"] - 1];
							}
							if (strlen($card_import["card_year"]) == 1) {
								$card_import["card_year"] = "200".$card_import["card_year"];
							} else if (strlen($card_import["card_year"]) == 2) {
								$card_import["card_year"] = "20".$card_import["card_year"];
							} else if (strlen($card_import["card_year"]) == 3) {
								$card_import["card_year"] = "2".$card_import["card_year"];
							}
							$card_import["card_month"] = intval($card_import["card_month"]);
							$card_import["card_year"] = intval($card_import["card_year"]);
							if ($card_import["card_year"] < date("Y") || ($card_import["card_year"] == date("Y") && $card_import["card_month"] < date("n"))) {
								$import_card_expired[] = $line." => Card expired.";
								$card_expired = <<<HTML
								<tr>
									<td colspan="12" class="centered">
										<span class="error">{$line} => Card expired.</span>
									</td>
								</tr>
HTML;
							}
							if ($_POST["card_cvv"] == "") {
								$card_import["card_cvv"] = "";
							}
							else {
								$card_import["card_cvv"] = $lineField[$_POST["card_cvv"] - 1];
							}
							if ($_POST["card_fname"] == $_POST["card_lname"] && $_POST["card_fname"] != "") {
								$card_import["card_name"] = $lineField[$_POST["card_fname"] - 1];
							}
							else {
								if ($_POST["card_fname"] != "" && $_POST["card_fname"] == $_POST["card_lname"]) {
									$card_import["card_name"] = trim($lineField[$_POST["card_fname"] - 1]);
								} else {
									if ($_POST["card_fname"] == "") {
										$card_fname = "";
									}
									else {
										$card_fname = explode(" ", $lineField[$_POST["card_fname"] - 1]);
										$card_fname = trim($card_fname[0]);
									}
									if ($_POST["card_lname"] == "") {
										$card_lname = "";
									}
									else {
										$card_lname = explode($card_fname, $lineField[$_POST["card_lname"] - 1]);
										$card_lname = $card_lname[count($card_lname)-1];
									}
									$card_import["card_name"] = $card_fname." ".$card_lname;
								}
							}
							if ($_POST["card_address"] == "") {
								$card_import["card_address"] = "";
							}
							else {
								$card_import["card_address"] = $lineField[$_POST["card_address"] - 1];
							}
							if ($_POST["city_select_mode"] == "0") {
								if ($_POST["card_city"] == "") {
									$card_import["card_city"] = "";
								}
								else {
									$card_import["card_city"] = $lineField[$_POST["card_city"] - 1];
								}
								if ($_POST["card_state"] == "") {
									$card_import["card_state"] = "";
								}
								else {
									$card_import["card_state"] = $lineField[$_POST["card_state"] - 1];
								}
								if ($_POST["card_zip"] == "") {
									$card_import["card_zip"] = "";
								}
								else {
									$card_import["card_zip"] = $lineField[$_POST["card_zip"] - 1];
								}
							} else if ($_POST["city_select_mode"] == "1") {
								if ($_POST["card_zip"] == "") {
									$card_import["card_city"] = "";
									$card_import["card_state"] = "";
									$card_import["card_zip"] = "";
								}
								else {
									$card_import["card_zip"] = $lineField[$_POST["card_zip"] - 1];
									$card_zip = $db->escape($card_import["card_zip"]);
									$sql = "SELECT CITY, REGION FROM `".TABLE_ZIPCODES."` WHERE ZIPCODE = '".$card_zip."'";
									$card_zip = $db->query_first($sql);
									if ($card_zip) {
										if (trim($card_zip["CITY"]) != "" AND trim($card_zip["CITY"]) != "-" AND trim($card_zip["REGION"]) != "" AND trim($card_zip["REGION"]) != "-") {
											$card_import["card_city"] = trim($card_zip["CITY"]);
											$card_import["card_state"] = trim($card_zip["REGION"]);
										} else {
											$import_get_zipcode_error1[] = $line." => Zipcode not found in database.";
											$get_zipcode_error = <<<HTML
									<tr>
										<td colspan="12" class="centered">
											<span class="error">{$line} => Zipcode not found in database.</span>
										</td>
									</tr>
HTML;
										}
									} else {
										$import_get_zipcode_error2[] = $line." => Get City, State error.";
										$get_zipcode_error = <<<HTML
									<tr>
										<td colspan="12" class="centered">
											<span class="error">{$line} => Get City, State error.</span>
										</td>
									</tr>
HTML;
									}
								}
							} else if ($_POST["city_select_mode"] == "2") {
								if ($_POST["card_city"] == "" || $_POST["card_state"] == "") {
									$card_import["card_city"] = "";
									$card_import["card_state"] = "";
									$card_import["card_zip"] = "";
								}
								else {
									$card_import["card_city"] = $lineField[$_POST["card_city"] - 1];
									$card_import["card_state"] = $lineField[$_POST["card_state"] - 1];
									$card_city = $db->escape($card_import["card_city"]);
									$card_state = $db->escape($card_import["card_state"]);
									$sql = "SELECT * FROM `".TABLE_ZIPCODES."` WHERE (CITY = '".$card_city."' AND REGION = '".$card_state."') OR (CITY = '".$card_city."')";
									$card_zip = $db->query_first($sql);
									if ($card_zip) {
										if (trim($card_zip["ZIPCODE"]) != "" && trim($card_zip["ZIPCODE"]) != "-") {
											$card_import["card_zip"] = trim($card_zip["ZIPCODE"]);
											$card_import["card_state"] = trim($card_zip["REGION"]);
										} else {
											$import_get_zipcode_error1[] = $line." => City, State not found in database.";
											$get_zipcode_error = <<<HTML
									<tr>
										<td colspan="12" class="centered">
											<span class="error">{$line} => City, State not found in database.</span>
										</td>
									</tr>
HTML;
										}
									} else {
										$import_get_zipcode_error2[] = $line." => Get zipcode error.";
										$get_zipcode_error = <<<HTML
									<tr>
										<td colspan="12" class="centered">
											<span class="error">{$line} => Get zipcode error.</span>
										</td>
									</tr>
HTML;
									}
								}
							}
							if ($_POST["card_country"] == "") {
								$card_import["card_country"] = "";
							}
							else if ($_POST["card_country"] == "AUTO BY BIN") {
								$cardBin = $db->escape(substr($card_import["card_number"], 0, 6));
								$sql = "SELECT card_country FROM `".TABLE_BINS."` WHERE card_bin = '".$cardBin."'";
								$card_country = $db->query_first($sql);
								if ($card_country) {
									if (trim($card_country["card_country"]) != "" AND trim($card_country["card_country"]) != "-") {
										$card_import["card_country"] = trim($card_country["card_country"]);
									} else {
										$import_get_country_error1[] = $line." => BIN not found in database.";
										$get_country_error = <<<HTML
									<tr>
										<td colspan="12" class="centered">
											<span class="error">{$line} => BIN not found in database.</span>
										</td>
									</tr>
HTML;
									}
								} else {
									$import_get_country_error2[] = $line." => Get country error.";
									$get_country_error = <<<HTML
									<tr>
										<td colspan="12" class="centered">
											<span class="error">{$line} => Get country error.</span>
										</td>
									</tr>
HTML;
								}
							}
							else {
								$card_import["card_country"] = $lineField[$_POST["card_country"] - 1];
							}
							if ($_POST["card_phone"] == "") {
								$card_import["card_phone"] = "";
							}
							else {
								$card_import["card_phone"] = $lineField[$_POST["card_phone"] - 1];
							}
							if ($_POST["card_ssn"] == "") {
								$card_import["card_ssn"] = "";
							}
							else {
								$card_import["card_ssn"] = $lineField[$_POST["card_ssn"] - 1];
							}
							if ($_POST["card_dob"] == "") {
								$card_import["card_dob"] = "";
							}
							else {
								$card_import["card_dob"] = $lineField[$_POST["card_dob"] - 1];
							}
							if (isset($_POST["card_import_save"])) {
								$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_number = AES_ENCRYPT('".$db->escape($card_import["card_number"])."', '".strval(DB_ENCRYPT_PASS)."')";
								$card_duplicate = $db->query_first($sql);
								if ($get_card_number_error != "") {
									echo $get_card_number_error;
								} else if ($card_expired != "") {
									echo $card_expired;
								} else if ($get_zipcode_error != "") {
									echo $get_zipcode_error;
								} else if ($get_country_error != "") {
									echo $get_country_error;
								} else if ($card_duplicate) {
									if (intval($card_duplicate["count(*)"]) == 0) {
										if ($_POST["checklive"] == "on") {
											$check = check($card_import["card_number"], $card_import["card_month"], $card_import["card_year"], $card_import["card_cvv"]);
										}
										else {
											$check = 1;
										}
										if ($check == 1) {
											$card_import["card_fullinfo"] = "AES_ENCRYPT('".$card_import["card_fullinfo"]."', '".strval(DB_ENCRYPT_PASS)."')";
											$card_import["card_number"] = "AES_ENCRYPT('".$card_import["card_number"]."', '".strval(DB_ENCRYPT_PASS)."')";
											if($db->insert(TABLE_CARDS, $card_import)) {
?>
										<tr>
											<td colspan="8" class="centered">
												<span class="success"><?=$line?> => Add Card successfully.</span>
											</td>
										</tr>
<?php
											}
											else {
?>
										<tr>
											<td colspan="8" class="centered">
												<span class="error"><?=$line?> => Add Card error.</span>
											</td>
										</tr>
<?php
											}
										}
										else if ($check == 2) {
?>
										<tr>
											<td colspan="8" class="centered">
												<span class="error"><?=$line?> => Card die.</span>
											</td>
										</tr>
<?php
										}
										else {
?>
										<tr>
											<td colspan="8" class="centered">
												<span class="error"><?=$line?> => Other Error.</span>
											</td>
										</tr>
<?php
										}
									}
									else {
?>
										<tr>
											<td colspan="8" class="centered">
												<span class="error"><?=$line?> => Duplicated in database.</span>
											</td>
										</tr>
<?php
									}
								}
								else {
?>
										<tr>
											<td colspan="8" class="centered">
												<span class="error"><?=$line?> => Check duplicate error.</span>
											</td>
										</tr>
<?php
								}
							} else {
								if ($get_card_number_error != "") {
									echo $get_card_number_error;
								} else if ($card_expired != "") {
									echo $card_expired;
								} else if ($get_zipcode_error != "") {
									echo $get_zipcode_error;
								} else if ($get_country_error != "") {
									echo $get_country_error;
								} else {
?>
														<tr class="formstyle">
															<td class="centered">
																<span><?=$card_import['card_number']?></span>
															</td>
															<td class="centered">
																<span><?=$card_import['card_month']?>/<?=$card_import['card_year']?></span>
															</td>
															<td class="centered">
																<span><?=$card_import['card_cvv']?></span>
															</td>
															<td class="centered">
																<span><?=$card_import['card_name']?></span>
															</td>
															<td class="centered">
																<span><?=$card_import['card_address']?></span>
															</td>
															<td class="centered">
																<span><?=$card_import['card_city']?></span>
															</td>
															<td class="centered">
																<span><?=$card_import['card_state']?></span>
															</td>
															<td class="centered">
																<span><?=$card_import['card_zip']?></span>
															</td>
															<td class="centered">
																<span><?=$card_import['card_country']?></span>
															</td>
															<td class="centered">
																<span><?=$card_import['card_phone']?></span>
															</td>
															<td class="centered">
																<span><?=$card_import['card_ssn']?></span>
															</td>
															<td class="centered">
																<span><?=$card_import['card_dob']?></span>
															</td>
														</tr>
<?php
								}
							}
						}
						flush();
					}
					if (isset($_POST["card_import_preview"])) {
?>
													</tbody>
												</table>
											</td>
										</tr>
<?php
					}
					if (count($import_get_card_number_error) > 0) {
?>
									<tr>
										<td colspan="8" class="error">
											GET CARD NUMBER ERROR
											<textarea class="card_content_error">
<?php
						foreach ($import_get_card_number_error as $lineError) {
							echo $lineError;
						}
?>
</textarea>
										</td>
									</tr>
<?php
					}
					if (count($import_card_expired) > 0) {
?>
									<tr>
										<td colspan="8" class="error">
											CARD EXPIRED
											<textarea class="card_content_error">
<?php
						foreach ($import_card_expired as $lineError) {
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
										<td colspan="8" class="error">
											GET ZIPCODE ERROR
											<textarea class="card_content_error">
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
										<td colspan="8" class="error">
											GET ZIPCODE ERROR
											<textarea class="card_content_error">
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
										<td colspan="8" class="error">
											GET COUNTRY ERROR
											<textarea class="card_content_error">
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
										<td colspan="8" class="error">
											GET COUNTRY ERROR
											<textarea class="card_content_error">
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
										<td colspan="8" class="centered">
											<span class="error"><?=$errorMsg?></span>
										</td>
									</tr>
								<form method="POST" action="">
									<tr>
										<td class="centered bold" colspan="8">
											Card Content:
											<textarea class="card_content_editor" name="card_content"><?=$_POST['card_content']?></textarea>
										</td>
									</tr>
									<tr>
										<td class="centered bold" colspan="8">
											Select City, State, Zipcode Mode: <select name="city_select_mode" onchange="change_city_select_mode();">
												<option value="1" <?php if ($_POST["city_select_mode"] == "1") echo "selected ";?>>Auto find City & State by Zipcode - Recomend for US, UK have Zipcode</option>
												<option value="2" <?php if ($_POST["city_select_mode"] == "2") echo "selected ";?>>Auto find Zipcode by City & State - Recomend for US, UK have City & State</option>
												<option value="0" <?php if ($_POST["city_select_mode"] == "0") echo "selected ";?>>Manual - Recomend for International or US, UK none City, State, Zipcode</option>
											</select>
										</td>
									</tr>
									<tr>
										<td class="centered bold" colspan="8">
											Select Country Mode: <select name="country_select_mode" onchange="change_country_select_mode();">
												<option value="0" <?php if ($_POST["country_select_mode"] == "0") echo "selected ";?>>Auto find Country by Card Number - Recomend</option>
												<option value="1" <?php if ($_POST["country_select_mode"] == "1") echo "selected ";?>>Manual position</option>
											</select>
										</td>
									</tr>
									<tr>
										<td class="centered bold formstyle">
											Category
										</td>
										<td class="centered bold formstyle">
											Card Price
										</td>
										<td class="centered bold formstyle">
											Spliter
										</td>
										<td class="centered bold formstyle">
											Exp month
										</td>
										<td class="centered bold formstyle">
											Exp year
										</td>
										<td class="centered formstyle">
											Card CVV2
										</td>
										<td class="centered formstyle">
											First Name
										</td>
										<td class="centered formstyle">
											Last Name
										</td>
									</tr>
									<tr>
										<td class="centered bold">
											<select name="card_categoryid">
												<option value="0">(No Category)</option>
<?php
			$sql = "SELECT * FROM `".TABLE_CATEGORYS."` WHERE category_sellerid = '0'";
			$records = $db->fetch_array($sql);
			if ($records && is_array($records) && count($records) > 0) {
				foreach($records as $value) {
?>
												<option value="<?=$value["category_id"]?>"<?=($value["category_id"] == $_POST["card_categoryid"])?" selected":""?>><?=$value["category_name"]?></option>
<?php
				}
			}
?>
											</select>
										</td>
										<td class="centered bold">
											<input name="card_price" type="text" size="4" value="<?=$_POST["card_price"]?>" />
										</td>
										<td class="centered bold">
											<input name="card_spliter" type="text" size="4" value="<?=$_POST["card_spliter"]?>" />
										</td>
										<td class="centered bold">
											<input name="card_month" type="text" size="4" value="<?=$_POST["card_month"]?>" />
										</td>
										<td class="centered bold">
											<input name="card_year" type="text" size="4" value="<?=$_POST["card_year"]?>" />
										</td>
										<td class="centered bold">
											<input name="card_cvv" type="text" size="4" value="<?=$_POST["card_cvv"]?>" />
										</td>
										<td class="centered bold">
											<input name="card_fname" type="text" size="4" value="<?=$_POST["card_fname"]?>" />
										</td>
										<td class="centered bold">
											<input name="card_lname" type="text" size="4" value="<?=$_POST["card_lname"]?>" />
										</td>
									</tr>
									<tr>
										<td class="centered formstyle">
											Address
										</td>
										<td id="card_city" class="centered formstyle">
											City
										</td>
										<td id="card_state" class="centered formstyle">
											State
										</td>
										<td id="card_zip" class="centered formstyle">
											Zipcode
										</td>
										<td class="centered formstyle">
											Country
										</td>
										<td class="centered formstyle">
											Phone
										</td>
										<td class="centered formstyle">
											SSN
										</td>
										<td class="centered formstyle">
											DOB
										</td>
									</tr>
									<tr>
										<td class="centered bold">
											<input name="card_address" type="text" size="4" value="<?=$_POST["card_address"]?>" />
										</td>
										<td class="centered bold">
											<input name="card_city" type="text" size="11" value="<?=$_POST["card_city"]?>" />
										</td>
										<td class="centered bold">
											<input name="card_state" type="text" size="11" value="<?=$_POST["card_state"]?>" />
										</td>
										<td class="centered bold">
											<input name="card_zip" type="text" size="12" value="<?=$_POST["card_zip"]?>" />
										</td>
										<td class="centered bold">
											<input name="card_country" type="text" size="11" value="<?=$_POST["card_country"]?>" />
										</td>
										<td class="centered bold">
											<input name="card_phone" type="text" size="4" value="<?=$_POST["card_phone"]?>" />
										</td>
										<td class="centered bold">
											<input name="card_ssn" type="text" size="4" value="<?=$_POST["card_ssn"]?>" />
										</td>
										<td class="centered bold">
											<input name="card_dob" type="text" size="4" value="<?=$_POST["card_dob"]?>" />
										</td>
									</tr>
									<tr>
										<td colspan="8" class="error">
											CHECK CARD LIVE BEFORE IMPORT: <input type="checkbox" name="checklive" />
										</td>
									</tr>
									<tr>
										<td colspan="8" class="centered">
											<input type="submit" name="card_import_preview" value="Preview" /><input type="submit" name="card_import_save" value="Import" /><input onclick="window.location='./cards.php'"type="button" name="card_import_cancel" value="Cancel" />
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
					<div class="section_title">EXPORT CARDS</div>
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
											<form method="POST" action="../cardprocess.php">
												<label>
													<input name="export_unsold" type="submit" class="bold" value="UnSold Cards">
												</label>
												<span> | </span>
												<label>
													<input name="export_sold" type="submit" class="bold" value="Sold Cards">
												</label>
												<span> | </span>
												<label>
													<input name="export_expired" type="submit" class="bold" value="Expired Cards">
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
	else if ($_GET["act"] == "edit" && $_GET["card_id"] != "") {
		$card_id = $db->escape($_GET["card_id"]);
?>
				<div id="cards">
					<div class="section_title">CARD EDITOR</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		if (isset($_POST["card_edit_save"])) {
			$card_update["card_fullinfo"] = "AES_ENCRYPT('".$_POST["card_fullinfo"]."', '".strval(DB_ENCRYPT_PASS)."')";
			$card_update["card_number"] = "AES_ENCRYPT('".$_POST["card_number"]."', '".strval(DB_ENCRYPT_PASS)."')";
			$card_update["card_categoryid"] = $_POST["card_categoryid"];
			$card_update["card_month"] = $_POST["card_month"];
			$card_update["card_year"] = $_POST["card_year"];
			$card_update["card_cvv"] = $_POST["card_cvv"];
			$card_update["card_name"] = $_POST["card_name"];
			$card_update["card_address"] = $_POST["card_address"];
			$card_update["card_city"] = $_POST["card_city"];
			$card_update["card_state"] = $_POST["card_state"];
			$card_update["card_zip"] = $_POST["card_zip"];
			$card_update["card_country"] = $_POST["card_country"];
			$card_update["card_ssn"] = $_POST["card_ssn"];
			$card_update["card_dob"] = $_POST["card_dob"];
			$card_update["card_phone"] = $_POST["card_phone"];
			$card_update["card_price"] = $_POST["card_price"];
			$card_update["card_userid"] = $_POST["card_userid"];
			$card_update["card_check"] = $_POST["card_check"];
			if($db->update(TABLE_CARDS, $card_update, "card_id='".$card_id."' AND card_sellerid = '0'")) {
?>
									<tr>
										<td colspan="4" class="centered">
											<span class="success">Update Card successfully.</span>
										</td>
									</tr>
<?php
			}
			else {
?>
									<tr>
										<td colspan="4" class="centered">
											<span class="error">Update Card error.</span>
										</td>
									</tr>
<?php
			}
		}
		$sql = "SELECT user_id, user_name from `".TABLE_USERS."` ORDER BY user_name";
		$allUsers = $db->fetch_array($sql);
		$sql = "SELECT *, AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number, AES_DECRYPT(card_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS card_fullinfo FROM `".TABLE_CARDS."` WHERE card_id = '".$card_id."' AND card_sellerid = '0'";
		$records = $db->fetch_array($sql);
		if (count($records)>0) {
			$value = $records[0];
?>
								<form method="POST" action="">
									<tr>
										<td colspan="4" class="centered">
											<textarea class="card_full_info" name="card_fullinfo" type="text" wrap="on";><?=$value['card_fullinfo']?></textarea>
										</td>
									</tr>
									<tr>
										<td class="card_editor">
											Card number:
										</td>
										<td>
											<input class="card_value_editor" name="card_number" type="text" value="<?=$value['card_number']?>" />
										</td>
										<td class="card_editor">
											Category:
										</td>
										<td>
											<select class="card_value_editor" name="card_categoryid">
<?php
			$sql = "SELECT * FROM `".TABLE_CATEGORYS."` WHERE category_sellerid = '0'";
			$categorys = $db->fetch_array($sql);
			if (is_array($categorys) && count($categorys) > 0) {
				foreach ($categorys as $category) {
?>
							<option value="<?=$category["category_id"]?>"<?=($category["category_id"]==$value["card_categoryid"])?" selected":""?>><?=$category["category_name"]?></option>
<?php
				}
			}
?>
												<option value="0"<?=(strval($value["card_categoryid"])=="0")?" selected":""?>>(No Category)</option>
											</select>
										</td>
									</tr>
									<tr>
										<td class="card_editor">
											Card month:
										</td>
										<td>
											<input class="card_value_editor" name="card_month" type="text" value="<?=$value['card_month']?>" />
										</td>
										<td class="card_editor">
											Card year:
										</td>
										<td>
											<input class="card_value_editor" name="card_year" type="text" value="<?=$value['card_year']?>" />
										</td>
									</tr>
									<tr>
										<td class="card_editor">
											Card CVV2:
										</td>
										<td>
											<input class="card_value_editor" name="card_cvv" type="text" value="<?=$value['card_cvv']?>" />
										</td>
										<td class="card_editor">
											Card Name:
										</td>
										<td>
											<input class="card_value_editor" name="card_name" type="text" value="<?=$value['card_name']?>" />
										</td>
									</tr>
									<tr>
										<td class="card_editor">
											Card Address:
										</td>
										<td>
											<input class="card_value_editor" name="card_address" type="text" value="<?=$value['card_address']?>" />
										</td>
										<td class="card_editor">
											Card City:
										</td>
										<td>
											<input class="card_value_editor" name="card_city" type="text" value="<?=$value['card_city']?>" />
										</td>
									</tr>
									<tr>
										<td class="card_editor">
											Card State:
										</td>
										<td>
											<input class="card_value_editor" name="card_state" type="text" value="<?=$value['card_state']?>" />
										</td>
										<td class="card_editor">
											Card Zipcode:
										</td>
										<td>
											<input class="card_value_editor" name="card_zip" type="text" value="<?=$value['card_zip']?>" />
										</td>
									</tr>
									<tr>
										<td class="card_editor">
											Card Country:
										</td>
										<td>
											<input class="card_value_editor" name="card_country" type="text" value="<?=$value['card_country']?>" />
										</td>
										<td class="card_editor">
											Card SSN:
										</td>
										<td>
											<input class="card_value_editor" name="card_ssn" type="text" value="<?=$value['card_ssn']?>" />
										</td>
									</tr>
									<tr>
										<td class="card_editor">
											Card DOB:
										</td>
										<td>
											<input class="card_value_editor" name="card_dob" type="text" value="<?=$value['card_dob']?>" />
										</td>
										<td class="card_editor">
											Card Phone:
										</td>
										<td>
											<input class="card_value_editor" name="card_phone" type="text" value="<?=$value['card_phone']?>" />
										</td>
									</tr>
									<tr>
										<td class="card_editor">
											Card Price:
										</td>
										<td>
											<input class="card_value_editor" name="card_price" type="text" value="<?=$value['card_price']?>" />
										</td>
										<td class="card_editor">
											Card Used by:
										</td>
										<td>
											<select class="card_value_editor" name="card_userid">
												<option value="0">--Unsold--</option>
<?php
			if (count($allUsers) > 0){
				foreach ($allUsers as $k=>$v) {
?>
												<option value="<?=$v["user_id"]?>" <?=($v["user_id"]==$value["card_userid"])?"selected ":""?>><?=$v["user_name"]?></option>
<?php
				}
			}
?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="card_editor">
											Card Check Status:
										</td>
										<td>
											<select class="card_value_editor" name="card_check">
												<option value="<?=strval(CHECK_DEFAULT)?>" <?=(strval(CHECK_DEFAULT)==$value["card_check"])?"selected ":""?>>UNCHECK</option>
												<option value="<?=strval(CHECK_INVALID)?>" <?=(strval(CHECK_INVALID)==$value["card_check"])?"selected ":""?>>INVALID</option>
												<option value="<?=strval(CHECK_VALID)?>" <?=(strval(CHECK_VALID)==$value["card_check"])?"selected ":""?>>APPROVED</option>
												<option value="<?=strval(CHECK_REFUND)?>" <?=(strval(CHECK_REFUND)==$value["card_check"])?"selected ":""?>>DECLINE</option>
												<option value="<?=strval(CHECK_UNKNOWN)?>" <?=(strval(CHECK_UNKNOWN)==$value["card_check"])?"selected ":""?>>UNKNOWN</option>
												<option value="<?=strval(CHECK_WAIT_REFUND)?>" <?=(strval(CHECK_WAIT_REFUND)==$value["card_check"])?"selected ":""?>>WAIT REFUND</option>
											</select>
										</td>
									</tr>
									<tr>
										<td colspan="4" class="centered">
											<input type="submit" name="card_edit_save" value="Save" /><input onclick="window.location='./cards.php'"type="button" name="card_edit_cancel" value="Cancel" />
										</td>
									</tr>
								</form>
<?php
		}
		else {
?>
								<tr>
									<td class="error">
										<span class="error">Card ID Invalid.</span>
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
	else if ($_POST["multi_edit"] != "" && $_POST["cards"] != "" && is_array($_POST["cards"])) {
		$cards_id = $_POST["cards"];
		$cards_sql = "";
		if (count($cards_id) > 0) {
			$cards_sql = "'".$db->escape($cards_id[count($cards_id) - 1])."'";
			unset($cards_id[count($cards_id)-1]);
			foreach ($cards_id as $v) {
				$cards_sql .= ", '".$db->escape($v)."'";
			}
		}
?>
				<div id="cards">
					<div class="section_title">MULTI CARDS EDITOR</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		if (isset($_POST["card_edit_save"])) {
			if ($_POST["card_country"] != "") {
				$card_update["card_country"] = $_POST["card_country"];
			}
			if ($_POST["card_categoryid"] != "") {
				$card_update["card_categoryid"] = $_POST["card_categoryid"];
			}
			if ($_POST["card_price"] != "") {
				$card_update["card_price"] = $_POST["card_price"];
			}
			if ($_POST["card_userid"] != "") {
				$card_update["card_userid"] = $_POST["card_userid"];
			}
			if ($_POST["card_check"] != "") {
				$card_update["card_check"] = $_POST["card_check"];
			}
			if (count($card_update) > 0) {
				if($db->update(TABLE_CARDS, $card_update, "card_id IN (".$cards_sql.") AND card_sellerid = '0'")) {
?>
									<tr>
										<td colspan="5" class="centered">
											<span class="success">Update Cards successfully.</span>
										</td>
									</tr>
<?php
				}
				else {
?>
									<tr>
										<td colspan="5" class="centered">
											<span class="error">Update Cards error.</span>
										</td>
									</tr>
<?php
				}
			}
		}
		$sql = "SELECT user_id, user_name from `".TABLE_USERS."` ORDER BY user_name";
		$allUsers = $db->fetch_array($sql);
		$sql = "SELECT *, AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number, AES_DECRYPT(card_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS card_fullinfo FROM `".TABLE_CARDS."` LEFT JOIN (`".TABLE_USERS."` LEFT JOIN `".TABLE_GROUPS."` ON ".TABLE_USERS.".user_groupid = ".TABLE_GROUPS.".group_id) ON ".TABLE_CARDS.".card_userid = ".TABLE_USERS.".user_id LEFT JOIN `".TABLE_CATEGORYS."` ON ".TABLE_CARDS.".card_categoryid = ".TABLE_CATEGORYS.".category_id WHERE card_id IN (".$cards_sql.") AND card_sellerid = '0'";
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
															<span>CARD NUMBER</span>
														</td>
														<td class="formstyle bold centered">
															<span>CATEGORY</span>
														</td>
														<td class="formstyle bold centered">
															<span>COUNTRY</span>
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
														<input type="HIDDEN" name="cards[]" value="<?=$value['card_id']?>" />
														<td class="centered">
															<span>
																<?=$value['card_number']?>
															</span>
														</td>
														<td class="centered bold">
															<span>
																<?=($value['category_name']=="")?"(No Category)":$value['category_name']?>
															</span>
														</td>
														<td class="centered bold">
															<span>
																<?=$value['card_country']?>
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
				switch ($value['card_check']) {
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
															<span><?=$value['card_price']?></span>
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
										<td class="card_editor">
											Card Country:
										</td>
										<td>
											<input class="card_value_editor" name="card_country" type="text" value="" />
										</td>
										<td class="card_editor">
											Category:
										</td>
										<td>
											<select class="card_value_editor" name="card_categoryid">
												<option value="">(No Change)</option>
<?php
			$sql = "SELECT * FROM `".TABLE_CATEGORYS."` WHERE category_sellerid = '0'";
			$categorys = $db->fetch_array($sql);
			if (is_array($categorys) && count($categorys) > 0) {
				foreach ($categorys as $value) {
?>
							<option value="<?=$value["category_id"]?>"<?=($value["category_id"]==$_POST["card_categoryid"])?" selected":""?>><?=$value["category_name"]?></option>
<?php
				}
			}
?>
												<option value="0"<?=(strval($_POST["card_categoryid"])=="0")?" selected":""?>>(No Category)</option>
											</select>
										</td>
									</tr>
									<tr>
										<td class="card_editor">
											Card Price:
										</td>
										<td>
											<input class="card_value_editor" name="card_price" type="text" value="" />
										</td>
										<td class="card_editor">
											Card Used by:
										</td>
										<td>
											<select class="card_value_editor" name="card_userid">
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
									</tr>
									<tr>
										<td class="card_editor">
											Card Check Status:
										</td>
										<td>
											<select class="card_value_editor" name="card_check">
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
											<input type="submit" name="card_edit_save" value="Save" /><input onclick="window.location='./cards.php'"type="button" name="card_edit_cancel" value="Cancel" />
										</td>
									</tr>
								</form>
<?php
		}
		else {
?>
								<tr>
									<td class="error">
										<span class="error">Cards ID Invalid.</span>
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
	else if ($_GET["act"] == "refund" && $_GET["card_id"] != "") {
		$card_id = $db->escape($_GET["card_id"]);
?>
				<div id="cards">
					<div class="section_title">CARD EDITOR</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		$sql = "SELECT card_check, card_userid, card_sellerid, card_price, card_additionPrice FROM `".TABLE_CARDS."` WHERE card_userid > 0 AND card_id = '".$card_id."' AND card_sellerid = '0'";
		$records = $db->fetch_array($sql);
		if (count($records)>0) {
			$value = $records[0];
			$sql = "SELECT user_id, user_balance from `".TABLE_USERS."` WHERE user_id = '".$value["card_userid"]."'";
			$card_user = $db->fetch_array($sql);
			if ($value["card_sellerid"] <> 0) {
				$sql = "SELECT user_id, user_balance from `".TABLE_USERS."` WHERE user_id = '".$value["card_sellerid"]."' AND user_groupid = '".strval(PER_SELLER)."'";
				$card_seller = $db->fetch_array($sql);
			}
			if (count($card_user)>0) {
				$card_user = $card_user[0];
				if ($value["card_sellerid"] == 0 || count($card_seller)>0) {
					if ($value["card_sellerid"] <> 0) {
						$card_seller = $card_seller[0];
					}
					if ($value["card_sellerid"] == 0 || doubleval($card_seller["user_balance"]) >= ((doubleval($value["card_price"])+doubleval($value["card_additionPrice"]))*(1-$db_config["commission"]))) {
						if ($value["card_check"] != strval(CHECK_REFUND)) {
							$user_update["user_balance"] = doubleval($card_user["user_balance"])+(doubleval($value["card_price"])+doubleval($value["card_additionPrice"]));
							if ($value["card_sellerid"] <> 0) {
								$seller_update["user_balance"] = doubleval($card_seller["user_balance"])-((doubleval($value["card_price"])+doubleval($value["card_additionPrice"]))*(1-$db_config["commission"]));
							}
							$card_update["card_check"] = strval(CHECK_REFUND);
							if($db->update(TABLE_CARDS, $card_update, "card_id='".$card_id."' AND card_sellerid = '0'") && $db->update(TABLE_USERS, $user_update, "user_id='".$value["card_userid"]."'") && ($value["card_sellerid"] == 0 || $db->update(TABLE_USERS, $seller_update, "user_id='".$value["card_sellerid"]."'"))) {
?>
								<tr>
									<td colspan="4" class="centered">
										<span class="success">Refund Card successfully.</span>
									</td>
								</tr>
<?php
							}
							else {
?>
								<tr>
									<td colspan="4" class="centered">
										<span class="error">Refund Card error.</span>
									</td>
								</tr>
<?php
							}
						}
						else {
	?>
								<tr>
									<td colspan="4" class="centered">
										<span class="error">This card is refunded.</span>
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
										<span class="error">Card ID Invalid.</span>
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
	else if ($_GET["act"] == "delete" && $_GET["card_id"] != "") {
		$card_id = $db->escape($_GET["card_id"]);
		$sql = "DELETE FROM `".TABLE_CARDS."` WHERE card_id = '".$card_id."' AND card_sellerid = '0'";
		if ($db->query($sql) && $db->affected_rows > 0) {
?>
				<script type="text/javascript">setTimeout("window.location = './cards.php'", 1000);</script>
				<div id="cards">
					<div class="section_title">CARD DELETE</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<tr class="centered">
									<td class="success">
										Delete Card ID <?=$card_id?> successfully.
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
					<div class="section_title">CARD DELETE</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<tr>
									<td class="error">
										Card ID Invalid.
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
			$sql = "DELETE FROM `".TABLE_CARDS."` WHERE card_year < ".date("Y")." OR (card_year = ".date("Y")." AND card_month < ".date("n").") AND card_sellerid = '0'";
			if ($db->query($sql)) {
				$deleteResult=<<<END
										<td colspan="13" class="success">
											Delete all expired cards successfully.
										</td>
END;
			}
			else {
				$deleteResult=<<<END
										<td colspan="13" class="error">
											Delete expired cards error.
										</td>
END;
			}
		} else if ($_POST["delete_invalid"] != "") {
			$sql = "DELETE FROM `".TABLE_CARDS."` WHERE card_check = '".strval(CHECK_INVALID)."' OR card_check = '".strval(CHECK_REFUND)."' AND card_sellerid = '0'";
			if ($db->query($sql)) {
				$deleteResult=<<<END
										<td colspan="13" class="success">
											Delete all invalid and refund cards successfully.
										</td>
END;
			}
			else {
				$deleteResult=<<<END
										<td colspan="13" class="error">
											Delete invalid and refund cards error.
										</td>
END;
			}
		} else if ($_POST["delete_select"] != "" && $_POST["cards"] != "" && is_array($_POST["cards"])) {
			$allCards = $_POST["cards"];
			$countDeleteRows = count($allCards);
			$lastCards = $db->escape($allCards[count($allCards)-1]);
			unset($allCards[count($allCards)-1]);
			$sql = "DELETE FROM `".TABLE_CARDS."` WHERE card_id IN (";
			if (count($allCards) > 0) {
				foreach ($allCards as $key=>$value) {
					$sql .= "'".$db->escape($value)."', ";
				}
			}
			$sql .= "'".$lastCards."') AND card_sellerid = '0'";
			if ($db->query($sql) && $db->affected_rows > 0) {
				if ($db->affected_rows == $countDeleteRows) {
					$deleteResult=<<<END
										<td colspan="13" class="success">
											Delete selected cards successfully.
										</td>
END;
				} else {
					$countDeletedRows = $countDeleteRows-$db->affected_rows;
					$deleteResult=<<<END
										<td colspan="13" class="error">
											Delete {$countDeletedRows} of {$countDeleteRows} selected cards error, please check again.
										</td>
END;
				}
			}
			else {
				$deleteResult=<<<END
										<td colspan="13" class="error">
											Delete selected cards error.
										</td>
END;
			}
		}
		
		if ($_GET["lstCategory"] != "") {
			$_GET["lstCategory"] = intval($_GET["lstCategory"]);
			if ($_GET["lstCategory"] > 0) {
				$searchCategory = "card_categoryid = '".$db->escape($_GET["lstCategory"])."'";
			} else if ($_GET["lstCategory"] == 0) {
				$searchCategory = "card_categoryid NOT IN (SELECT category_id FROM `".TABLE_CATEGORYS."` WHERE category_sellerid = '0')";
			} else {
				$searchCategory = "1";
				$_GET["lstCategory"] = "";
			}
		} else {
			$searchCategory = "1";
		}
		if (isset($_GET["btnSearch"])) {
			$currentGet = "";
			$currentGet .= "txtBin=".$_GET["txtBin"]."&lstCategory=".$_GET["lstCategory"]."&lstCountry=".$_GET["lstCountry"]."&lstState=".$_GET["lstState"]."&lstCity=".$_GET["lstCity"]."&txtZip=".$_GET["txtZip"]."&lstAvailable=".$_GET["lstAvailable"]."&lstExpire=".$_GET["lstExpire"]."&lstStatus=".$_GET["lstStatus"]."&lstCheck=".$_GET["lstCheck"];
			$currentGet .= ($_GET["boxDob"]!="")?"&boxDob=".$_GET["boxDob"]:"";
			$currentGet .= ($_GET["boxSSN"]!="")?"&boxSSN=".$_GET["boxSSN"]:"";
			$currentGet .= "&btnSearch=Search&";
		}
		$searchBin = $db->escape($_GET["txtBin"]);
		$searchCountry = $db->escape($_GET["lstCountry"]);
		$searchState = $db->escape($_GET["lstState"]);
		$searchCity = $db->escape($_GET["lstCity"]);
		$searchZip = $db->escape($_GET["txtZip"]);
		$searchSSN = ($_GET["boxSSN"] == "on")?" AND card_ssn <> ''":"";
		$searchDob = ($_GET["boxDob"] == "on")?" AND card_dob <> ''":"";
		switch ($_GET["lstAvailable"]) {
			case "unsold":
				$searchAvailable = "card_userid = '0'";
				break;
			case "sold":
				$searchAvailable = "card_userid <> '0'";
				break;
			default:
				if (intval($_GET["lstAvailable"]) > 0) {
					$searchAvailable = "card_userid = '".$db->escape(intval($_GET["lstAvailable"]))."'";
				} else {
					$searchAvailable = "1";
				}
				break;
		}
		switch ($_GET["lstExpire"]) {
			case strval(EXPIRE_FUTURE):
				$searchExpire = "(card_year > ".date("Y")." OR (card_year = ".date("Y")." AND card_month > ".date("n")."))";
				break;
			case strval(EXPIRE_STAGNANT):
				$searchExpire = "(card_year = ".date("Y")." AND card_month = ".date("n").")";
				break;
			case strval(EXPIRE_EXPIRED):
				$searchExpire = "(card_year < ".date("Y")." OR (card_year = ".date("Y")." AND card_month < ".date("n")."))";
				break;
			default:
				$searchExpire = "1";
				break;
		}
		switch ($_GET["lstStatus"]) {
			case strval(STATUS_DEFAULT):
				$searchStatus = "card_status = '".strval(STATUS_DEFAULT)."'";
				break;
			case strval(STATUS_DELETED):
				$searchStatus = "card_status = '".strval(STATUS_DELETED)."'";
				break;
			case strval(STATUS_STAGNANT):
				$searchStatus = "card_status = '".strval(STATUS_STAGNANT)."'";
				break;
			case strval(STATUS_EXPIRED):
				$searchStatus = "card_status = '".strval(STATUS_EXPIRED)."'";
				break;
			default:
				$searchStatus = "1";
				break;
		}
		switch ($_GET["lstCheck"]) {
			case strval(CHECK_DEFAULT):
				$searchCheck = "card_check = '".strval(CHECK_DEFAULT)."'";
				break;
			case strval(CHECK_VALID):
				$searchCheck = "card_check = '".strval(CHECK_VALID)."'";
				break;
			case strval(CHECK_INVALID):
				$searchCheck = "card_check = '".strval(CHECK_INVALID)."'";
				break;
			case strval(CHECK_REFUND):
				$searchCheck = "card_check = '".strval(CHECK_REFUND)."'";
				break;
			case strval(CHECK_UNKNOWN):
				$searchCheck = "card_check = '".strval(CHECK_UNKNOWN)."'";
				break;
			case strval(CHECK_WAIT_REFUND):
				$searchCheck = "card_check = '".strval(CHECK_WAIT_REFUND)."'";
				break;
			default:
				$searchCheck = "1";
				break;
		}
		$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE ".$searchCategory." AND ".$searchExpire." AND ".$searchStatus." AND ".$searchCheck." AND ".$searchAvailable." AND ('".$searchBin."'='' OR AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') LIKE '".$searchBin."%') AND ('".$searchCountry."'='' OR card_country = '".$searchCountry."') AND ('".$searchState."'='' OR card_state = '".$searchState."') AND ('".$searchCity."'='' OR card_city = '".$searchCity."') AND ('".$searchZip."'='' OR card_zip LIKE '".$searchZip."%')".$searchSSN.$searchDob." AND card_sellerid = '0'";
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
		$sql = "SELECT ".TABLE_CARDS.".*, AES_DECRYPT(".TABLE_CARDS.".card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number, ".TABLE_USERS.".user_name, ".TABLE_GROUPS.".group_color, ".TABLE_CATEGORYS.".* FROM `".TABLE_CARDS."` LEFT JOIN `".TABLE_USERS."` ON ".TABLE_CARDS.".card_userid = ".TABLE_USERS.".user_id LEFT JOIN `".TABLE_GROUPS."` ON ".TABLE_USERS.".user_groupid = ".TABLE_GROUPS.".group_id LEFT JOIN `".TABLE_CATEGORYS."` ON ".TABLE_CARDS.".card_categoryid = ".TABLE_CATEGORYS.".category_id WHERE ".$searchCategory." AND ".$searchExpire." AND ".$searchStatus." AND ".$searchCheck." AND ".$searchAvailable." AND ('".$searchBin."'='' OR AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') LIKE '".$searchBin."%') AND ('".$searchCountry."'='' OR card_country = '".$searchCountry."') AND ('".$searchState."'='' OR card_state = '".$searchState."') AND ('".$searchCity."'='' OR card_city = '".$searchCity."') AND ('".$searchZip."'='' OR card_zip LIKE '".$searchZip."%')".$searchSSN.$searchDob." AND card_sellerid = '0' ORDER BY card_id LIMIT ".(($page-1)*$perPage).",".$perPage;
		$listcards = $db->fetch_array($sql);
?>
				<div id="search_cards">
					<div class="section_title">SEARCH CARDS</div>
					<div class="section_content">
						<table class="content_table centered">
							<tbody>
								<form name="search" method="GET" action="cards.php">
									<tr>
										<td class="formstyle">
											<span class="bold">CARD NUMBER</span>
										</td>
										<td class="formstyle">
											<span class="bold">CATEGORY</span>
										</td>
										<td class="formstyle">
											<span class="bold">COUNTRY</span>
										</td>
										<td class="formstyle">
											<span class="bold">STATE</span>
										</td>
										<td class="formstyle">
											<span class="bold">CITY</span>
										</td>
										<td class="formstyle">
											<span class="bold">ZIP</span>
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
		$sql = "SELECT * FROM `".TABLE_CATEGORYS."` WHERE category_sellerid = '0'";
		$allCategory = $db->fetch_array($sql);
		if (count($allCategory) > 0) {
			foreach ($allCategory as $category) {
				echo "<option value=\"".$category['category_id']."\"".(($_GET["lstCategory"] == $category['category_id'])?" selected":"").">".$category['category_name']."</option>";
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
		$sql = "SELECT DISTINCT card_country FROM `".TABLE_CARDS."` WHERE card_sellerid = '0' ORDER BY card_country";
		$allCountry = $db->fetch_array($sql);
		if (count($allCountry) > 0) {
			foreach ($allCountry as $country) {
				echo "<option value=\"".$country['card_country']."\"".(($_GET["lstCountry"] == $country['card_country'])?" selected":"").">".$country['card_country']."</option>";
			}
		}
?>
											</select>
										</td>
										<td>
											<select name="lstState" class="formstyle" id="lstState">
												<option value="">All State</option>
<?php
		$sql = "SELECT DISTINCT card_state FROM `".TABLE_CARDS."` WHERE card_sellerid = '0' ORDER BY card_state";
		$allCountry = $db->fetch_array($sql);
		if (count($allCountry) > 0) {
			foreach ($allCountry as $country) {
				echo "<option value=\"".$country['card_state']."\"".(($_GET["lstState"] == $country['card_state'])?" selected":"").">".$country['card_state']."</option>";
			}
		}
?>
											</select>
										</td>
										<td>
											<select name="lstCity" class="formstyle" id="lstCity">
												<option value="">All City</option>
<?php
		$sql = "SELECT DISTINCT card_city FROM `".TABLE_CARDS."` WHERE card_sellerid = '0 ORDER BY card_city'";
		$allCountry = $db->fetch_array($sql);
		if (count($allCountry) > 0) {
			foreach ($allCountry as $country) {
				echo "<option value=\"".$country['card_city']."\"".(($_GET["lstCity"] == $country['card_city'])?" selected":"").">".$country['card_city']."</option>";
			}
		}
?>
											</select>
										</td>
										<td>
											<input name="txtZip" type="text" class="formstyle" id="txtZip" value="<?=$_GET["txtZip"]?>" size="12">
										</td>
									</tr>
									<tr>
										<td class="formstyle">
											<span class="bold">Have SSN</span>
										</td>
										<td class="formstyle">
											<span class="bold">Have DoB</span>
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
											<span><input type="checkbox" name="boxSSN" id="boxSSN" <?=($_GET["boxSSN"] != "")?"checked ":""?>></span>
										</td>
										<td>
											<span><input type="checkbox" name="boxDob" id="boxDob" <?=($_GET["boxDob"] != "")?"checked ":""?>></span>
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
				<div id="cards">
					<div class="section_title">AVAILABLE CARDS</div>
					<div class="section_title"><a href="?act=import">Import Cards</a></div>
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
											<span class="bold">CARD NUMBER</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">CATEGORY</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">EXPIRE</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">CVV</span>
										</td>
										<!--td class="formstyle centered">
											<span class="bold">CITY</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">STATE</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">ZIP</span>
										</td-->
										<td class="formstyle centered">
											<span class="bold">COUNTRY</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">SSN/DOB</span>
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
											<input class="formstyle" type="checkbox" name="selectAllCards" id="selectAllCards" onclick="checkAll(this.id, 'cards[]')" value="">
										</td>
									</tr>
<?php
		if (count($listcards) > 0) {
			foreach ($listcards as $key=>$value) {
?>
									<tr class="formstyle">
										<td class="centered bold">
											<span><?=$value['card_number']?></span>
										</td>
										<td class="centered bold">
											<span><?=($value['category_name']=="")?"(No Category)":$value['category_name']?></span>
										</td>
										<td class="centered">
											<span><?=$value['card_month']?>/<?=$value['card_year']?></span>
										</td>
										<td class="centered">
											<span><?=$value['card_cvv']?></span>
										</td>
										<!--td class="centered">
											<span><?//=$value['card_city']?></span>
										</td>
										<td class="centered">
											<span><?//=$value['card_state']?></span>
										</td>
										<td class="centered">
											<span><?//=$value['card_zip']?></span-->
										</td>
										<td class="centered">
											<span><?=$value['card_country']?></span>
										</td>
										<td class="centered">
											<span><?=($value['card_ssn'] == "")?"NO":"YES"?></span>/<span><?=($value['card_dob'] == "")?"NO":"YES"?></span>
										</td>
										<td class="centered">
<?php
				switch ($value['card_userid']) {
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
				switch ($value['card_check']) {
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
											<a href="?act=edit&card_id=<?=$value['card_id']?>">Edit</a>
											 | <a href="?act=delete&card_id=<?=$value['card_id']?>" onClick="return confirm('Are you sure you want to DELETE this Cards?')">Delete</a>
<?php
				if ($value['card_userid'] > 0 && $value['card_check'] != strval(CHECK_REFUND)) {
?>
											 | <a href="?act=refund&card_id=<?=$value['card_id']?>" onClick="return confirm('Are you sure you want to REFUND this Cards?')">Refund</a>
<?php
				}
?>
											</span>
										</td>
										<td class="centered">
											<input class="formstyle" type="checkbox" name="cards[]" value="<?=$value['card_id']?>">
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
													<input name="multi_edit" type="submit" id="multi_edit" onClick="" value="Edit Selected Cards">
												<span> | </span>
												</label>
												<label>
													<input name="delete_expired" type="submit" id="delete_expired" onClick="return confirm('Are you sure you want to delete the EXPIRED Cards?')" value="Delete Expired Cards">
												<span> | </span>
												</label>
												<label>
													<input name="delete_invalid" type="submit" id="delete_invalid" onClick="return confirm('Are you sure you want to delete the INVALID Cards?')" value="Delete Invalid/Refunded Cards">
												<span> | </span>
												</label>
												<label>
													<input name="delete_select" type="submit" id="delete_select" onClick="return confirm('Are you sure you want to delete the SELECTED Cards?')" value="Delete Selected Cards">
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