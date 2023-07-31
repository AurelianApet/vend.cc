<?php
require("./header.php");
if ($checkLogin && $user_info["user_groupid"] < intval(PER_UNACTIVATE)) {
	if ($_GET["other_category_id"] != "") {
		$_GET["other_category_id"] = intval($_GET["other_category_id"]);
		if (is_numeric($_GET["other_category_id"])&&$_GET["other_category_id"] > 0) {
			$searchCategory = "otheraccount_categoryid = '".$db->escape($_GET["other_category_id"])."'";
		} else if (is_numeric($_GET["other_category_id"])&&strval($_GET["other_category_id"]) == "0") {
			$searchCategory = "otheraccount_categoryid NOT IN (SELECT other_category_id FROM `".TABLE_OTHER_CATEGORYS."`)";
		} else { 
			$searchCategory = "1";
			$_GET["other_category_id"] = "";
		}
	} else {
		$searchCategory = "1";
	}
	$currentGet = "other_category_id=".$_GET["other_category_id"]."&";
	$currentGet .= "txtInfo=".$_GET["txtInfo"]."&";
	$_GET["lstType"] = trim($_GET["lstType"]);
	$_GET["lstSeller"] = trim($_GET["lstSeller"]);
	if (isset($_GET["btnSearch"])) {
		$currentGet .= "lstType=".$_GET["lstType"];
		$currentGet .= "lstSeller=".$_GET["lstSeller"];
		$currentGet .= "&btnSearch=Search&";
	}
	if ($_GET["lstType"] == "") {
		$searchType = "1";
	} else {
		$searchType = "otheraccount_type = '".$db->escape($_GET["lstType"])."'";
	}
	if ($_GET["lstSeller"] == "") {
		$searchSeller = "otheraccount_sellerid >= 0";
	} else {
		$searchSeller = "otheraccount_sellerid = '".$db->escape($_GET["lstSeller"])."'";
	}
	$searchInfo = $db->escape($_GET["txtInfo"]);
	$group_where = $searchCategory." AND ".$searchType." AND ".$searchSeller." AND otheraccount_status = '".STATUS_DEFAULT."' AND otheraccount_userid = '0'";
	$search_where = "('".$searchInfo."'='' OR otheraccount_info LIKE '%".$searchInfo."%' OR otheraccount_info LIKE '".$searchInfo."%' OR otheraccount_info LIKE '%".$searchInfo."')";
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
	else {
		$page = 1;
	}
	$sql = "SELECT * FROM `".TABLE_OTHERACCOUNTS."` LEFT JOIN `".TABLE_OTHER_CATEGORYS."` ON ".TABLE_OTHERACCOUNTS.".otheraccount_categoryid = ".TABLE_OTHER_CATEGORYS.".other_category_id WHERE ".$group_where." AND ".$search_where." ORDER BY otheraccount_id DESC LIMIT ".(($page-1)*$perPage).",".$perPage;
	$listotheraccounts = $db->fetch_array($sql);
	$sql = "SELECT * FROM `".TABLE_USERS."`";
	$listusers = $db->fetch_array($sql);
	$newlistusers = array();
	foreach ($listusers as $user) {
		$newlistusers[$user["user_id"]] = $user;
	}
	$listusers = $newlistusers;
	unset($newlistusers);
?>
				<div id="search_cards">
					<div class="section_title">SEARCH OTHER ACCOUNTS</div>
					<div class="section_content">
						<table class="content_table centered">
							<tbody>
								<form id="searchForm" name="searchForm" method="GET" action="otheraccounts.php">
									<input type="hidden" name="other_category_id" value="<?=$_GET["other_category_id"]?>" />
									<input type="hidden" name="stagnant" value="<?=$_GET["stagnant"]?>" /-->
									<tr>
										<td>
											<span class="bold">ACCOUNT TYPE</td></span>
										</td>
										<td>
											<span class="bold">CATEGORY</td></span>
										</td>
										<td>
											<span class="bold">SELLER</span>
										</td>
										<td>
											<span class="bold">ACOUNT INFORMATION</span>
										</td>
										<td>
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
											<select name="other_category_id" class="formstyle" id="other_category_id">
												<option value="">(All Category)</option>
<?php
	$sql = "SELECT * FROM `".TABLE_OTHER_CATEGORYS."` ORDER BY other_category_name";
	$allType = $db->fetch_array($sql);
	if (count($allType) > 0) {
		foreach ($allType as $type) {
			echo "TEST [$_GET[other_category_id] == $type[other_category_id]]<br/>";
			echo "<option value=\"".$type['other_category_id']."\"".((strval($_GET["other_category_id"]) == $type['other_category_id'])?" selected":"").">".$type['other_category_name']."</option>";
		}
	}
?>
												<option value="0" <?=((strval($_GET["other_category_id"]) == "0")?" selected":"")?>>(No Category)</option>
											</select>
										</td>
										<td>
											<select name="lstSeller" class="formstyle" id="lstSeller">
												<option value="">All Seller</option>
												<option style="color:red;" value="0" <?=(strval($_GET["lstSeller"]) == "0")?" selected":""?>>SYSTEM</option>
<?php
	$sql = "SELECT * FROM `".TABLE_USERS."` WHERE user_groupid = '".PER_SELLER."' ORDER BY user_name";
	$allSeller = $db->fetch_array($sql);
	if (count($allSeller) > 0) {
		foreach ($allSeller as $seller) {
			echo "<option style=\"color:".$user_groups[$seller['user_groupid']]["group_color"].";\" value=\"".$seller['user_id']."\"".(($_GET["lstSeller"] == $seller['user_id'])?" selected":"").">".$seller['user_name']."</option>";
		}
	}
?>
											</select>
										</td>
										<td>
											<input name="txtInfo" type="text" class="formstyle" id="txtInfo" value="<?=$_GET["txtInfo"]?>" size="100" maxlength="128">
										</td>
										<td>
											<input name="btnSearch" type="submit" class="search_but" id="btnSearch" value="Search" />
										</td>
									</tr>
								</form>
							</tbody>
						</table>
					</div>
				</div>
				<div id="cards">
					<div class="section_title">AVAILABLE OTHER ACCOUNTS</div>
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
										<td class="centered">
											<span class="bold">ACCOUNT TYPE</span>
										</td>
										<td class="centered">
											<span class="bold">CATEGORY</span>
										</td>
										<td class="centered">
											<span class="bold">ACCOUNT INFORMATION</span>
										</td>
										<td class="centered">
											<span class="bold">SELLER</span>
										</td>
										<td class="centered">
											<span class="bold">PRICE</span>
										</td>
										<td class="centered">
											<input class="formstyle" type="checkbox" name="selectAllOtherAccounts" id="selectAllOtherAccounts" onclick="checkAll(this.id, 'otheraccounts[]')" value="">
										</td>
									</tr>
<?php
	if (count($listotheraccounts) > 0) {
		foreach ($listotheraccounts as $key=>$value) {
?>
									<tr class="formstyle">
										<td class="centered bold">
											<span><?=$value['otheraccount_type']?></span>
										</td>
										<td class="centered bold">
											<span><?=($value['other_category_name']=="")?"(No Category)":$value['other_category_name']?></span>
										</td>
										<td class="centered bold">
											<span><?=$value['otheraccount_info']?></span>
										</td>
										<td class="centered bold">
											<?php if ($value['otheraccount_sellerid'] == 0) { echo "<a href=\"?lstSeller=".$value['otheraccount_sellerid']."\"><span class=\"red\">SYSTEM</span></a>"; } else { echo "<a href=\"?lstSeller=".$value['otheraccount_sellerid']."\"><span style=\"color:".$user_groups[$listusers[$value['otheraccount_sellerid']]["user_groupid"]]["group_color"].";\">".$listusers[$value['otheraccount_sellerid']]["user_name"]."</span></a>"; }?>
										</td>
										<td class="centered bold">
											<span>
<?php
			printf("$%.2f", $value['otheraccount_price']);
			if (strlen($_GET["txtInfo"]) > 1 && $db_config["binPrice"] > 0) {
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
											<input class="formstyle" type="checkbox" name="otheraccounts[]" value="<?=$value['otheraccount_id']?>">
										</td>
									</tr>
<?php
		}
	}
?>
									<tr>
										<td colspan="6" class="centered">
											<p>
												<label>
													<input name="txtInfo" type="hidden" id="txtInfo" value="<?=$_GET["txtInfo"]?>" />
													<input name="txtCountry" type="hidden" id="txtCountry" value="<?=$_GET["lstCountry"]?>" />
													<input name="lstState" type="hidden" id="lstState" value="<?=$_GET["lstState"]?>" />
													<input name="lstCity" type="hidden" id="lstCity" value="<?=$_GET["lstCity"]?>" />
													<input name="txtZip" type="hidden" id="txtZip" value="<?=$_GET["txtZip"]?>" />
													<input name="addToCart" type="submit" class="bold" id="download_select" value="Add Selected Accounts to Shopping Cart" />
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