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
	if (isset($_GET["btnSearch"])) {
		$currentGet = "";
		$currentGet .= "txtBin=".$_GET["txtBin"]."&dump_category_id=".$_GET["lstCategory"]."&stagnant=".$_GET["stagnant"]."lstSeller=".$_GET["lstSeller"]."&lstCountry=".$_GET["lstCountry"]."&lstBank=".$_GET["lstBank"]."&lstType=".$_GET["lstType"]."&lstLevel=".$_GET["lstLevel"]."&lstCtype=".$_GET["lstCtype"]."&lstCode=".$_GET["lstCode"];
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
	$group_where = $searchCategory." AND dump_status = '".STATUS_DEFAULT."' AND dump_userid = '".$user_info["user_id"]."'";
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
?>
				<div id="search_dumps">
					<div class="section_title">SEARCH DUMPS</div>
					<div class="section_content">
						<table class="content_table centered">
							<tbody>
								<form name="search" method="GET" action="mydumps.php">
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
									</tr>
									<tr>
										<td class="formstyle">
											<span class="bold">LEVEL</span>
										</td>
										<td class="formstyle">
											<span class="bold">CODE</span>
										</td>
										<td class="formstyle">
											<span class="bold">TRACK 1</span>
										</td>
										<td class="formstyle">
											<span class="bold">TRACK 2</span>
										</td>
									</tr>
									<tr>
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
										<td>
											<span><input type="checkbox" name="checkboxTr1" id="checkboxTr1" <?=($_GET["checkboxTr1"] != "")?"checked ":""?>></span>
										</td>
										<td>
											<span><input type="checkbox" name="checkboxTr2" id="checkboxTr2" <?=($_GET["checkboxTr2"] != "")?"checked ":""?>></span>
										</td>
									</tr>
									<tr>
										<td colspan="4">
											<input name="btnSearch" type="submit" class="formstyle" id="btnSearch" value="Search">
										</td>
									</tr>
								</form>
							</tbody>
						</table>
					</div>
				</div>
				<div id="dumps">
					<div class="section_title">YOUR DUMPS</div>
					<div class="section_title">Download dumps to view full dump information </div>
					<div class="section_title">Click on 'Check' to check dump, check fee is $<?=number_format($db_config["check_fee"], 2, '.', '')?></div>
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
								<form name="addtocart" method="POST" action="./dumpprocess.php">
									<tr>
										<td class="formstyle centered">
											<span class="bold">CATEGORY</span>
										</td>
										<td class="formstyle centered">
											<span class="bold">TRACK</span>
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
											<span class="bold">CHECK</span>
										</td>
										<td class="formstyle centered">
											<input class="formstyle" type="checkbox" name="selectAllDumps" id="selectAllDumps" onclick="checkAll(this.id, 'dumps[]')" value="">
										</td>
									</tr>
<?php
	if (count($listdumps) > 0) {
		foreach ($listdumps as $key=>$value) {
			switch ($value['dump_check']) {
				case strval(CHECK_VALID):
					$value['dump_checkText'] = "<span class=\"green bold\">APPROVED</span>";
					break;
				case strval(CHECK_INVALID):
					$value['dump_checkText'] = "<span class=\"red bold\">TIMEOUT</span>";
					break;
				case strval(CHECK_REFUND):
					$value['dump_checkText'] = "<span class=\"pink bold\">DECLINE</span>";
					break;
				case strval(CHECK_UNKNOWN):
					$value['dump_checkText'] = "<span class=\"blue bold\">UNKNOWN</span>";
					break;
				case strval(CHECK_WAIT_REFUND):
					$value['dump_checkText'] = "<span class=\"pink bold\">WAIT REFUND</span>";
					break;
				default :
					$value['dump_checkText'] = "<span class=\"bold\"><a href=\"#\" onclick=\"checkDump('".$value['dump_id']."')\">Check ($".number_format($db_config["check_fee"], 2, '.', '').")</a></span>";
					break;
			}
			
			
			$track = explode('|', $value['dump_fullinfo']);
			$track = $track[0];
?>
									<tr class="formstyle">
										<td class="centered bold">
											<span><?=($value['dump_category_name']=="")?"(No Category)":$value['dump_category_name']?></span>
										</td>
										<td class="centered bold">
											<span><?=$track?></span>
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
										<td class="centered bold">
											<span id="check_<?=$value['dump_id']?>"><?=$value['dump_checkText']?></span>
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
										<td colspan="14" class="centered">
											<p>
												<span><br/></span>
												<label>
													<input name="delete_invalid" type="submit" id="delete_invalid" onClick="return confirm('Are you sure you want to delete the INVALID Dumps?')" value="Delete Invalid/Decline Dumps">
												</label>
												<span> | </span>
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
else if ($checkLogin && $_SESSION["user_groupid"] == intval(PER_UNACTIVATE)){
	require("./miniactivate.php");
}
else {
	require("./minilogin.php");
}
require("./footer.php");
?>