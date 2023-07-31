<?php
require("./header.php");

if ($checkLogin && $_SESSION["user_groupid"] < intval(PER_UNACTIVATE)) {
	if ($_GET["lstCategory"] != "") {
		$_GET["lstCategory"] = intval($_GET["lstCategory"]);
		if ($_GET["lstCategory"] > 0) {
			$searchCategory = "dump_categoryid = '".$db->escape($_GET["lstCategory"])."'";
		} else if ($_GET["lstCategory"] == 0) {
			$searchCategory = "dump_categoryid NOT IN (SELECT dump_category_id FROM `".TABLE_DUMP_CATEGORYS."`)";
		} else {
			$searchCategory = "1";
			$_GET["lstCategory"] = "";
		}
	} else {
		$searchCategory = "1";
	}
	if (strtolower($_GET["stagnant"]) == "true") {
		$_GET["stagnant"] = "true";
	} else if (strtolower($_GET["stagnant"]) == "false") {
		$_GET["stagnant"] = "false";
	} else {
		$_GET["stagnant"] = "";
	}
	$_GET["lstSeller"] = trim($_GET["lstSeller"]);
	if (isset($_GET["btnSearch"])) {
		$currentGet = "";
		$currentGet .= "txtBin=".$_GET["txtBin"]."&dump_category_id=".$_GET["lstCategory"]."&stagnant=".$_GET["stagnant"]."lstSeller=".$_GET["lstSeller"]."&lstCountry=".$_GET["lstCountry"]."&lstBank=".$_GET["lstBank"]."&lstType=".$_GET["lstType"]."&lstLevel=".$_GET["lstLevel"]."&lstCtype=".$_GET["lstCtype"]."&lstCode=".$_GET["lstCode"];
		$currentGet .= (strtolower($_GET["checkboxTr1"])=="on")?"&checkboxTr1=1":"";
		$currentGet .= (strtolower($_GET["checkboxTr2"])=="on")?"&checkboxTr2=1":"";
		$currentGet .= "&btnSearch=Search&";
	}
	if ($_GET["stagnant"] == "true") {
		$searchExpire = "(dump_exp = '".date("ym")."')";
	} else if ($_GET["stagnant"] == "false") {
		$searchExpire = "(dump_exp > '".date("ym")."')";
	} else {
		$searchExpire = "1";
	}
	if ($_GET["lstSeller"] == "") {
		$searchSeller = "dump_sellerid >= 0";
	} else {
		$searchSeller = "dump_sellerid = '".$db->escape($_GET["lstSeller"])."'";
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
	$group_where = $searchCategory." AND ".$searchExpire." AND dump_status = '".STATUS_DEFAULT."' AND dump_userid = '0'";
	$search_where = "('".$searchBin."'='' OR AES_DECRYPT(dump_number, '".strval(DB_ENCRYPT_PASS)."') LIKE '".$searchBin."%') AND ('".$searchCountry."'='' OR dump_country = '".$searchCountry."') AND ('".$searchBank."'='' OR dump_bank = '".$searchBank."') AND ('".$searchType."'='' OR dump_type = '".$searchType."') AND ('".$searchLevel."'='' OR dump_level = '".$searchLevel."') AND ('".$searchCtype."'='' OR dump_ctype = '".$searchCtype."') AND ('".$searchCode."'='' OR dump_code = '".$searchCode."')".$searchTr1.$searchTr2;
	$sql = "SELECT count(*) FROM `".TABLE_DUMPS."` WHERE $group_where AND $search_where";
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
	$sql = "SELECT ".TABLE_DUMPS.".*, AES_DECRYPT(dump_fullinfo, '".strval(DB_ENCRYPT_PASS)."') AS dump_fullinfo, ".TABLE_USERS.".user_name, ".TABLE_GROUPS.".group_color, ".TABLE_DUMP_CATEGORYS.".* FROM `".TABLE_DUMPS."` LEFT JOIN `".TABLE_USERS."` ON ".TABLE_DUMPS.".dump_userid = ".TABLE_USERS.".user_id LEFT JOIN `".TABLE_GROUPS."` ON ".TABLE_USERS.".user_groupid = ".TABLE_GROUPS.".group_id LEFT JOIN `".TABLE_DUMP_CATEGORYS."` ON ".TABLE_DUMPS.".dump_categoryid = ".TABLE_DUMP_CATEGORYS.".dump_category_id WHERE $group_where AND $search_where ORDER BY dump_id LIMIT ".(($page-1)*$perPage).",".$perPage;
	$listdumps = $db->fetch_array($sql);

	$sql = "SELECT * FROM `".TABLE_USERS."`";
	$listusers = $db->fetch_array($sql);
	$newlistusers = array();
	foreach ($listusers as $user) {
		$newlistusers[$user["user_id"]] = $user;
	}
	$listusers = $newlistusers;
	unset($newlistusers);
?>
				<div id="search_dumps">
					<div class="section_title">SEARCH DUMPS</div>
					<div class="section_content">
						<div class="content_table centered">
								<form name="search" id="searchForm" method="GET" action="dumps.php">
									<div>
									<div style="float:left;">
									<div class="formstyle">
									<span class="bold">DUMP BIN +$<?=number_format($db_config["binPrice"], 2)?></span>
									</div>
									<div>
									<input name="txtBin" type="text" class="formstyle" id="txtBin" value="<?=$_GET["txtBin"]?>" size="15" maxlength="16">
									</div>
									</div>
									<div style="float:left;">
									<div class="formstyle">
									<span class="bold">CATEGORY</span>
									</div>
									<div>
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
									</div>
									</div>
									<div style="float:left;">
									<div class="formstyle">
									<span class="bold">COUNTRY</span>
									</div>
									<div>
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
									</div>
									</div>
									<div style="float:left;">
									<div class="formstyle">
									<span class="bold">BANK</span>
									</div>
									<div>
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
									</div>
									</div>
									<div style="float:left;">
									<div class="formstyle">
									<a href='#' onclick="$('#txtBin').val('');$('#searchForm').submit();"><img src="./images/all-icon.png" class="iconcc" /></a>
											<a href='#' onclick="$('#txtBin').val(3);$('#searchForm').submit();"><img src="./images/american-express-icon.png" class="iconcc" /></a>
											<a href='#' onclick="$('#txtBin').val(4);$('#searchForm').submit();"><img src="./images/visa-icon.png" class="iconcc" /></a>
											<a href='#' onclick="$('#txtBin').val(5);$('#searchForm').submit();"><img src="./images/mastercard-icon.png" class="iconcc" /></a>
											<a href='#' onclick="$('#txtBin').val(6);$('#searchForm').submit();"><img src="./images/discover-icon.png" class="iconcc" /></a>
									</div>
									<div>
									</div>
									</div>
									<div style="float:left;">
									<div class="formstyle">
									<span class="bold">LEVEL</span>
									</div>
									<div>
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
									</div>
									</div>
									<div style="float:left;">
									<div class="formstyle">
									<span class="bold">CODE</span>
									</div>
									<div>
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
									</div>
									</div>
									<div style="float:left;">
									<div class="formstyle">
									<span class="bold">TRACK</span>
									</div>
									<div>&nbsp &nbsp
									<span>TR 1<input type="checkbox" name="checkboxTr1" id="checkboxTr1" <?=($_GET["checkboxTr1"] != "")?"checked ":""?>></span>
											<span>TR 2<input type="checkbox" name="checkboxTr2" id="checkboxTr2" <?=($_GET["checkboxTr2"] != "")?"checked ":""?>></span>
									</div>
									</div>
								</div>
									<div style="float:none; clear:both;"></div>
									<div>
										<div >
											<input name="btnSearch" type="submit" class="search_but" id="btnSearch" value="Search">
										</div>
									</div>
								</form>
							</div>
					</div>
				</div>
				<br/>
				<div style="float:none; clear:both;"></div>
				<div id="dumps">
					<div class="section_title">AVAILABLE DUMPS</div>
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
											<span class="bold">DUMP TYPE</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">TRACK</span>
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
											<span class="bold">PRICE</span>
										</td>
										<td class="formstyle centered">
											<input class="formstyle" type="checkbox" name="selectAllDumps" id="selectAllDumps" onclick="checkAll(this.id, 'dumps[]')" value="">
										</td>
									</tr>
<?php
	if (count($listdumps) > 0) {
		foreach ($listdumps as $key=>$value) {
		
			$sql="SELECT mini_name FROM `country` where full_name='".$value['dump_country']."'";
      $flag=$db->query_first($sql);	
	
?>
									<tr class="formstyle">
										<td class="centered bold">
<?php
			switch (intval(substr($value['dump_bin'], 0, 1))) {
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
											<span><?=$value['dump_bin']?>******</span>
										</td>
										<td class="centered bold">
											<span><?=($value['dump_category_name']=="")?"(No Category)":$value['dump_category_name']?></span>
										</td>
										<td class="centered">
											<span><?=$value['dump_exp']?></span>
										</td>
										<td class="centered">
											<span><img src="images/flag/<?=$flag['mini_name'].'.png';?>" /></span>
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
										<td class="centered bold">
											<span>
<?php
			printf("$%.2f", $value['dump_price']);
			if (strlen($_GET["txtBin"]) > 1 && $db_config["binPrice"] > 0) {
				printf(" + $%.2f", $db_config["binPrice"]);
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
										<td colspan="12" class="centered">
											<p>
												<label>
													<input name="txtBin" type="hidden" id="txtBin" value="<?=$_GET["txtBin"]?>" />
													<input name="addToCart" type="submit" class="bold" id="download_select" value="Add Selected Dumps to Shopping Cart" />
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