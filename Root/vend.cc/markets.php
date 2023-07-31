<?php
require("./header.php");
if ($checkLogin && $user_info["user_groupid"] < intval(PER_UNACTIVATE)) {
	if ($_GET["category_id"] != "") {
		$_GET["category_id"] = intval($_GET["category_id"]);
		if ($_GET["category_id"] > 0) {
			$searchCategory = "card_categoryid = '".$db->escape($_GET["category_id"])."'";
		} else if ($_GET["category_id"] == 0) {
			$searchCategory = "card_categoryid NOT IN (SELECT category_id FROM `".TABLE_CATEGORYS."`)";
		} else {
			$searchCategory = "1";
			$_GET["category_id"] = "";
		}
	} else {
		$searchCategory = "1";
	}
	/*
	if ($_GET["cvv"] != "true") {
		$_GET["cvv"] = "false";
	}
	*/
	if (strtolower($_GET["stagnant"]) == "true") {
		$_GET["stagnant"] = "true";
	} else if (strtolower($_GET["stagnant"]) == "false") {
		$_GET["stagnant"] = "false";
	} else {
		$_GET["stagnant"] = "";
	}
	$currentGet = "category_id=".$_GET["category_id"]."&";
	//$currentGet .= "cvv=".$_GET["cvv"]."&";
	$currentGet .= "stagnant=".$_GET["stagnant"]."&";
	$currentGet .= "lstSeller=".$_GET["lstSeller"]."&";
	$currentGet .= "txtBin=".$_GET["txtBin"]."&";
	if (isset($_GET["btnSearch"])) {
		$currentGet .= "lstCountry=".$_GET["lstCountry"]."&lstState=".$_GET["lstState"]."&lstCity=".$_GET["lstCity"]."&txtZip=".$_GET["txtZip"];
		$currentGet .= ($_GET["boxDob"]!="")?"&boxDob=".$_GET["boxDob"]:"";
		$currentGet .= ($_GET["boxSSN"]!="")?"&boxSSN=".$_GET["boxSSN"]:"";
		$currentGet .= "&btnSearch=Search&";
	}
	$searchCVV = 1;
	/*
	if ($_GET["cvv"] == "true") {
		$searchCVV = "(card_cvv IS NOT NULL AND card_cvv <> '')";
	} else {
		$searchCVV = "(card_cvv IS NULL OR card_cvv = '')";
	}
	*/
	if ($_GET["stagnant"] == "true") {
		$searchExpire = "(card_year = ".date("Y")." AND card_month = ".date("n").")";
	} else if ($_GET["stagnant"] == "false") {
		$searchExpire = "(card_year > ".date("Y")." OR (card_year = ".date("Y")." AND card_month > ".date("n")."))";
	} else {
		$searchExpire = "1";
	}
	if ($_GET["lstSeller"] != "") {
		$searchSeller = "card_sellerid = '".$db->escape($_GET["lstSeller"])."'";
	} else {
		$searchSeller = "card_sellerid > 0";
	}
	$searchBin = substr($db->escape($_GET["txtBin"]), 0, 6);
	$searchCountry = $db->escape($_GET["lstCountry"]);
	$searchState = $db->escape($_GET["lstState"]);
	$searchCity = $db->escape($_GET["lstCity"]);
	$searchZip = $db->escape($_GET["txtZip"]);
	$searchSSN = ($_GET["boxSSN"] == "on")?" AND card_ssn <> ''":"";
	$searchDob = ($_GET["boxDob"] == "on")?" AND card_dob <> ''":"";
	$group_where = $searchCategory." AND ".$searchCVV." AND ".$searchExpire." AND ".$searchSeller." AND card_status = '".STATUS_DEFAULT."' AND card_userid = '0'";
	$search_where = "('".$searchBin."'='' OR card_bin LIKE '".$searchBin."%') AND ('".$searchCountry."'='' OR card_country = '".$searchCountry."') AND ('".$searchState."'='' OR card_state = '".$searchState."') AND ('".$searchCity."'='' OR card_city = '".$searchCity."') AND ('".$searchZip."'='' OR card_zip LIKE '".$searchZip."%')".$searchSSN.$searchDob;
	$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE ".$group_where." AND ".$search_where;
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
	else {
		$page = 1;
	}
	$sql = "SELECT * FROM `".TABLE_CARDS."` LEFT JOIN `".TABLE_CATEGORYS."` ON ".TABLE_CARDS.".card_categoryid = ".TABLE_CATEGORYS.".category_id WHERE ".$group_where." AND ".$search_where." ORDER BY card_id DESC LIMIT ".(($page-1)*$perPage).",".$perPage;
	$listcards = $db->fetch_array($sql);
?>
				<div id="search_cards">
					<div class="section_title">SELLER INFORMATION</div>
					<div class="section_content">
<?php
	$sql = "SELECT * FROM `".TABLE_USERS."` WHERE user_id = '".$db->escape($_GET["lstSeller"])."'";
	$seller_info = $db->query_first($sql);
	
	$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_sellerid = '".$db->escape($_GET["lstSeller"])."'";
	$total_cards = $db->query_first($sql);
	
	$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_sellerid = '".$db->escape($_GET["lstSeller"])."'";
	$sold_cards = $db->query_first($sql);
	
	$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check = '".strval(CHECK_VALID)."' AND card_sellerid = '".$db->escape($_GET["lstSeller"])."'";
	$valid_cards = $db->query_first($sql);
	
	$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check = '".strval(CHECK_INVALID)."' AND card_sellerid = '".$db->escape($_GET["lstSeller"])."'";
	$invalid_cards = $db->query_first($sql);
	
	$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check = '".strval(CHECK_DEFAULT)."' AND card_sellerid = '".$db->escape($_SESSION["user_id"])."'";
	$uncheck_cards = $db->query_first($sql);
	
	$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check = '".strval(CHECK_REFUND)."' AND card_sellerid = '".$db->escape($_GET["lstSeller"])."'";
	$refund_cards = $db->query_first($sql);
	
	$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check = '".strval(CHECK_WAIT_REFUND)."' AND card_sellerid = '".$db->escape($_SESSION["user_id"])."'";
	$wait_refund_cards = $db->query_first($sql);
	
	$sql = "SELECT count(*), IFNULL(SUM(card_price + card_additionPrice), 0) AS income FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check <> '".strval(CHECK_REFUND)."' AND card_sellerid = '".$db->escape($_GET["lstSeller"])."'";
	$sold_income = $db->query_first($sql);
	
	$sql = "SELECT count(*), IFNULL(SUM(card_price + card_additionPrice), 0) AS refund FROM `".TABLE_CARDS."` WHERE card_userid <> '0' AND card_check = '".strval(CHECK_REFUND)."' AND card_sellerid = '".$db->escape($_GET["lstSeller"])."'";
	$refund_money = $db->query_first($sql);
?>
						<table class="content_table" style="width:600px; margin: 0 auto;">
							<tbody class="left bold">
								<tr>
									<td class="centered">
										User Name: <span style="color:<?=$user_groups[$seller_info["user_groupid"]]["group_color"]?>"><?=$seller_info["user_name"]?></span>
									</td>
									<td class="centered">
										User From: <?=date("d/M/Y", $seller_info['user_regdate'])?>
									</td>
								</tr>
								<tr>
									<td class="success">
										Total Cards: <?=$total_cards["count(*)"]?>
									</td>
									<td class="error">
										Unsold Cards: <?=$total_cards["count(*)"] - $sold_cards["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="success">
										Valid Sold Cards: <?=$valid_cards["count(*)"]?>
									</td>
									<td class="error">
										Invalid Sold Cards: <?=$invalid_cards["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="success">
										Sold Cards: <?=$sold_cards["count(*)"]?>
									</td>
									<td class="error">
										Uncheck Cards: <?=$uncheck_cards["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="success">
										Refund Cards: <?=$refund_cards["count(*)"]?>
									</td>
									<td class="error">
										Wait Refund Cards: <?=$wait_refund_cards["count(*)"]?>
									</td>
								</tr>
								<tr>
									<td class="success">
										Sold Income: $<?=$sold_income["income"]?>
									</td>
									<td class="error">
										Refund Money: $<?=$refund_money["refund"]?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div id="search_cards">
					<div class="section_title">SEARCH SELLER CARDS</div>
					<div class="section_content">
						<table class="content_table centered">
							<tbody>
								<form id="searchForm" name="searchForm" method="GET" action="markets.php">
									<input type="hidden" name="category_id" value="<?=$_GET["category_id"]?>" />
									<!--input type="hidden" name="cvv" value="<?//=$_GET["cvv"]?>" /-->
									<input type="hidden" name="stagnant" value="<?=$_GET["stagnant"]?>" /-->
									<tr>
										<td class="formstyle">
											<span class="bold">BIN (+$<?=number_format($db_config["binPrice"], 2)?>)</span>
										</td>
										<td class="formstyle">
											<span class="bold">COUNTRY (+$<?=number_format($db_config["countryPrice"], 2)?>)</td></span>
										</td>
										<td class="formstyle">
											<span class="bold">STATE (+$<?=number_format($db_config["statePrice"], 2)?>)</td></span>
										</td>
										<td class="formstyle">
											<span class="bold">CITY (+$<?=number_format($db_config["cityPrice"], 2)?>)</td></span>
										</td>
										<td class="formstyle">
											<span class="bold">ZIP (+$<?=number_format($db_config["zipPrice"], 2)?>)</td></span>
										</td>
									</tr>
									<tr>
										<td>
											<input name="txtBin" type="text" class="formstyle" id="txtBin" value="<?=$_GET["txtBin"]?>" size="12" maxlength="6">
										</td>
										<td>
											<select name="lstCountry" class="formstyle" id="lstCountry">
												<option value="">All Country</option>
<?php
	$sql = "SELECT card_country, count(*) FROM `".TABLE_CARDS."` WHERE ".$group_where." GROUP BY card_country ORDER BY card_country";
	$allCountry = $db->fetch_array($sql);
	if (count($allCountry) > 0) {
		foreach ($allCountry as $country) {
			echo "<option value=\"".$country['card_country']."\"".(($_GET["lstCountry"] == $country['card_country'])?" selected":"").">".$country['card_country']." (".$country['count(*)']." cards)</option>";
		}
	}
?>
											</select>
										</td>
										<td>
											<select name="lstState" class="formstyle" id="lstState">
												<option value="">All State</option>
<?php
	$sql = "SELECT DISTINCT card_state FROM `".TABLE_CARDS."` WHERE ".$group_where." ORDER BY card_state";
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
	$sql = "SELECT DISTINCT card_city FROM `".TABLE_CARDS."` WHERE ".$group_where." ORDER BY card_city";
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
										<td>
											<a href='#' onclick="$('#txtBin').val('');$('#searchForm').submit();"><img src="./images/all-icon.png" class="iconcc" /></a>
											<a href='#' onclick="$('#txtBin').val(3);$('#searchForm').submit();"><img src="./images/american-express-icon.png" class="iconcc" /></a>
											<a href='#' onclick="$('#txtBin').val(4);$('#searchForm').submit();"><img src="./images/visa-icon.png" class="iconcc" /></a>
											<a href='#' onclick="$('#txtBin').val(5);$('#searchForm').submit();"><img src="./images/mastercard-icon.png" class="iconcc" /></a>
											<a href='#' onclick="$('#txtBin').val(6);$('#searchForm').submit();"><img src="./images/discover-icon.png" class="iconcc" /></a>
										</td>
										<td>
											<span><input type="checkbox" name="boxSSN" id="boxSSN" <?=($_GET["boxSSN"] != "")?"checked ":""?>>Have SSN</span>
										</td>
										<td>
											<span><input type="checkbox" name="boxDob" id="boxDob" <?=($_GET["boxDob"] != "")?"checked ":""?>>Have DoB</span>
										</td>
										<td colspan="2">
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
								<form name="addtocart" method="POST" action="./cart.php">
									<tr>
										<td class="formstyle centered">
											<span class="bold">CARD TYPE</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">CARD NUMBER</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">CATEGORY</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">FIRST NAME</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">COUNTRY</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">STATE</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">CITY</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">ZIP</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">SSN</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">DOB</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">PRICE</span>
										</td>
										<td class="formstyle centered">
											<input class="formstyle" type="checkbox" name="selectAllCards" id="selectAllCards" onclick="checkAll(this.id, 'cards[]')" value="">
										</td>
									</tr>
<?php
	if (count($listcards) > 0) {
		foreach ($listcards as $key=>$value) {
			$card_firstname = explode(" ", $value['card_name']);
			$card_firstname = $card_firstname[0];
?>
									<tr class="formstyle">
										<td class="centered bold">
<?php
			switch (intval(substr($value['card_bin'], 0, 1))) {
				case 3:
					echo "<span><img src=\"./images/american-express-icon.png\" class=\"iconcc\" /></span>";
					break;
				case 4:
					echo "<span><img src=\"./images/visa-icon.png\" class=\"iconcc\" /></span>";
					break;
				case 5:
					echo "<span><img src=\"./images/mastercard-icon.png\" class=\"iconcc\" /></span>";
					break;
				case 6:
					echo "<span><img src=\"./images/discover-icon.png\" class=\"iconcc\" /></span>";
					break;
			}
?>
												
										</td>
										<td class="centered bold">
											<span><?=$value['card_bin']?>******</span>
										</td>
										<td class="centered bold">
											<span><?=($value['category_name']=="")?"(No Category)":$value['category_name']?></span>
										</td>
										<td class="centered">
											<span><?=$card_firstname?></span>
										</td>
										<td class="centered">
											<span><?=$value['card_country']?></span>
										</td>
										<td class="centered">
											<span><?=$value['card_state']?></span>
										</td>
										<td class="centered">
											<span><?=$value['card_city']?></span>
										</td>
										<td class="centered">
											<span><?=$value['card_zip']?></span>
										</td>
										<td class="centered">
											<span><?=($value['card_ssn'] == "")?"NO":"YES"?></span>
										</td>
										<td class="centered">
											<span><?=($value['card_dob'] == "")?"NO":"YES"?></span>
										</td>
										<td class="centered bold">
											<span>
<?php
			printf("$%.2f", $value['card_price']);
			if (strlen($_GET["txtBin"]) > 1 && $db_config["binPrice"] > 0) {
				printf(" + $%.2f", $db_config["binPrice"]);
			}
			if ($_GET["lstCountry"] != "" && $db_config["countryPrice"] > 0) {
				printf(" + $%.2f", $db_config["countryPrice"]);
			}
			if ($_GET["lstState"] != "" && $db_config["statePrice"] > 0) {
				printf(" + $%.2f", $db_config["statePrice"]);
			}
			if ($_GET["lstCity"] != "" && $db_config["cityPrice"] > 0) {
				printf(" + $%.2f", $db_config["cityPrice"]);
			}
			if ($_GET["txtZip"] != "" && $db_config["zipPrice"] > 0) {
				printf(" + $%.2f", $db_config["zipPrice"]);
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
										<td colspan="12" class="centered">
											<p>
												<label>
													<input name="txtBin" type="hidden" id="txtBin" value="<?=$_GET["txtBin"]?>" />
													<input name="txtCountry" type="hidden" id="txtCountry" value="<?=$_GET["lstCountry"]?>" />
													<input name="lstState" type="hidden" id="lstState" value="<?=$_GET["lstState"]?>" />
													<input name="lstCity" type="hidden" id="lstCity" value="<?=$_GET["lstCity"]?>" />
													<input name="txtZip" type="hidden" id="txtZip" value="<?=$_GET["txtZip"]?>" />
													<input name="addToCart" type="submit" class="bold" id="download_select" value="Add Selected Cards to Shopping Cart" />
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
else if ($checkLogin && $_SESSION["user_groupid"] == intval(PER_UNACTIVATE)){
	require("./miniactivate.php");
}
else {
	require("./minilogin.php");
}
require("./footer.php");
?>