<?php
$max_search_bin = 100;
require("./header.php");

/*
category_id=&stagnant=&txtBin=&lstCity=&lstState=&txtZip=&lstCountry=&lstSeller=&btnSearch=Search
 $sql = "SELECT `".TABLE_CARDS."`.*, `".TABLE_CATEGORYS."`.*, `".TABLE_BINS."`.card_bank, `".TABLE_BINS."`.card_type, `".TABLE_BINS."`.card_level FROM (`".TABLE_CARDS."` LEFT JOIN `".TABLE_CATEGORYS."` ON ".TABLE_CARDS.".card_categoryid = ".TABLE_CATEGORYS.".category_id) LEFT JOIN `".TABLE_BINS."` ON ".TABLE_CARDS.".card_bin = ".TABLE_BINS.".card_bin ".$searchwhere2." LIMIT ".(($page-1)*$perPage).",".$perPage;

   if value not empty
   create its where clause
   add it to the searchwhere2

   if ($_GET["btnSearch"] == "Search") {

   }

W
*/

if ($checkLogin && $_SESSION["user_groupid"] < intval(PER_UNACTIVATE)) {
	if ($_GET["category_id"] != "") {
		$_GET["category_id"] = intval($_GET["category_id"]);
		if ($_GET["category_id"] > 0) {
			$searchCategory = "card_categoryid = '".$_GET["category_id"]."'";
		} else if ($_GET["category_id"] == 0) {
			$searchCategory = "card_categoryid NOT IN (SELECT category_id FROM `".TABLE_CATEGORYS."`)";
		} else {
			$searchCategory = "1";
			$_GET["category_id"] = "";
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
	$currentGet = "category_id=".$_GET["category_id"]."&";
	$currentGet .= "stagnant=".$_GET["stagnant"]."&";
	$currentGet .= "txtBin=".$_GET["txtBin"]."&";
	$_GET["lstSeller"] = trim($_GET["lstSeller"]);
	if (isset($_GET["btnSearch"])) {
		$currentGet .= "lstSeller=".$_GET["lstSeller"]."lstCountry=".$_GET["lstCountry"]."&lstState=".$_GET["lstState"]."&lstCity=".$_GET["lstCity"]."&txtZip=".$_GET["txtZip"];
		$currentGet .= ($_GET["boxDob"]!="")?"&boxDob=".$_GET["boxDob"]:"";
		$currentGet .= ($_GET["boxSSN"]!="")?"&boxSSN=".$_GET["boxSSN"]:"";
		$currentGet .= "&btnSearch=Search&";
	}
	$searchCVV = 1;
	if ($_GET["stagnant"] == "true") {
		$searchExpire = "(card_year = ".date("Y")." AND card_month = ".date("n").")";
	} else if ($_GET["stagnant"] == "false") {
		$searchExpire = "(card_year > ".date("Y")." OR (card_year = ".date("Y")." AND card_month > ".date("n")."))";
	} else {
		$searchExpire = "1";
	}

	$searchSeller = !is_numeric($_GET["lstSeller"])||$_GET["lstSeller"] == "" ? $searchSeller = "card_sellerid >= 0" : $searchSeller = "card_sellerid = ".$_GET["lstSeller"];

	$search_where_data = array(trim($_GET["txtBin"]));

	if(empty($_GET["txtBin"])){
		$search_where_data = array();
		$searchBinWhere = "('' = ''";
	} else {
		$search_where_data = array(trim($_GET["txtBin"]));
		$searchBinWhere = "(? = ''";
	}

	$searchBins = explode("\n", $_GET["txtBin"]);
	if (is_array($searchBins) && count($searchBins) > 0) {
		$count_bin = 0;
		foreach ($searchBins as &$searchBin) {
			if ($count_bin > $max_search_bin) {
				break;
			} else {
				if (strlen($searchBin) > 0) {
					$search_where_data[] = $db->escape(substr(trim($searchBin), 0, 6))."%";
					$searchBinWhere .= " OR ".TABLE_CARDS.".card_bin LIKE ?";
				}
				$count_bin++;
			}
		}
	}

	$search_where = $searchBinWhere.")";

	if(!empty($_GET["lstCountry"])) {
		array_push($search_where_data, $_GET["lstCountry"]);
		$search_where .= " AND ".TABLE_CARDS.".card_country = ?";
	}
	if(!empty($_GET["lstState"])) {
		array_push($search_where_data, $_GET["lstState"]);
		$search_where .= "  AND ".TABLE_CARDS.".card_state = ?";
	}
	if(!empty($_GET["lstCity"])) {
		array_push($search_where_data, $_GET["lstCity"]);
		$search_where .= " AND ".TABLE_CARDS.".card_city = ?";
	}
	if(!empty($_GET["txtZip"])) {
		array_push($search_where_data, $_GET["txtZip"]."%");
		$search_where .= " AND ".TABLE_CARDS.".card_zip LIKE ?";
	}

	$searchSSN = ($_GET["boxSSN"] == "on")?" AND card_ssn <> ''":"";
	$searchDob = ($_GET["boxDob"] == "on")?" AND card_dob <> ''":"";

	$group_where_data=array($searchCategory, $searchCVV, $searchExpire);

//$group_where = "? AND ? AND ? AND ".$searchSeller." AND card_status = '".STATUS_DEFAULT."' AND card_userid = '0'";
//	$group_where = $searchSeller." AND card_status = '".STATUS_DEFAULT."' AND card_userid = '0'";

	$search_where .=$searchSSN.$searchDob;

	//$search_where = "(".$searchBinWhere.") AND ('".$searchCountry."'='' OR ".TABLE_CARDS.".card_country = '".$searchCountry."') AND ('".$searchState."'='' OR ".TABLE_CARDS.".card_state = '".$searchState."') AND ('".$searchCity."'='' OR ".TABLE_CARDS.".card_city = '".$searchCity."') AND ('".$searchZip."'='' OR ".TABLE_CARDS.".card_zip LIKE '".$searchZip."%')".$searchSSN.$searchDob;

	//echo("SELECT count(*) FROM `".TABLE_CARDS."` WHERE 1=1 AND ".$group_where." AND ".$search_where);

	//$sql = "SELECT count(*) FROM `".TABLE_CARDS."` WHERE 1=1 AND ".$group_where." AND ".$search_where;
	//$sql = "SELECT count(*) FROM  `cards` WHERE  `card_categoryid`=14";
	//echo $sql;

	$sql_data=array_merge($group_where_data, $search_where_data);

	$totalRecords = $db->num_rows($sql, $sql_data);

	$perPage = 20;
	$totalPage = ceil($totalRecords/$perPage);

	$page = is_numeric($_GET["page"]) ? ceil($_GET["page"]) : 1;
	if ($page > $totalPage)$page = 1;

/*$sql = "SELECT `".TABLE_CARDS."`.*, `".TABLE_CATEGORYS."`.*, `".TABLE_BINS."`.card_bank, `".TABLE_BINS."`.card_type, `".TABLE_BINS."`.card_level FROM (`".TABLE_CARDS."` LEFT JOIN `".TABLE_CATEGORYS."` ON ".TABLE_CARDS.".card_categoryid = ".TABLE_CATEGORYS.".category_id) LEFT JOIN `".TABLE_BINS."` ON ".TABLE_CARDS.".card_bin = ".TABLE_BINS.".card_bin WHERE ".$group_where." AND ".$search_where." LIMIT ".(($page-1)*$perPage).",".$perPage;*/

	$where2="";
	if ($_GET["category_id"] > 0) {
	   $where2="WHERE card_categoryid =".$_GET["category_id"]." AND card_status = '".STATUS_DEFAULT."' AND card_userid = '0'";
	}
	else
	{
	   $where2="WHERE card_status = '".STATUS_DEFAULT."' AND card_userid = '0'";
	}


$searchwhere2="Where ".TABLE_CARDS.".card_bin LIKE ".$_GET["txtBin"];


if($_GET["btnSearch"] == 'Search')
{
echo(' ifsearch =');
	$sql = "SELECT `".TABLE_CARDS."`.*, `".TABLE_CATEGORYS."`.*, `".TABLE_BINS."`.card_bank, `".TABLE_BINS."`.card_type, `".TABLE_BINS."`.card_level FROM (`".TABLE_CARDS."` LEFT JOIN `".TABLE_CATEGORYS."` ON ".TABLE_CARDS.".card_categoryid = ".TABLE_CATEGORYS.".category_id) LEFT JOIN `".TABLE_BINS."` ON ".TABLE_CARDS.".card_bin = ".TABLE_BINS.".card_bin ".$searchwhere2." LIMIT ".(($page-1)*$perPage).",".$perPage;
echo(' ifsearch ='.$sql);
}
else
{	
echo(' elsesearch =');
	$sql = "SELECT `".TABLE_CARDS."`.*, `".TABLE_CATEGORYS."`.*, `".TABLE_BINS."`.card_bank, `".TABLE_BINS."`.card_type, `".TABLE_BINS."`.card_level FROM (`".TABLE_CARDS."` LEFT JOIN `".TABLE_CATEGORYS."` ON ".TABLE_CARDS.".card_categoryid = ".TABLE_CATEGORYS.".category_id) LEFT JOIN `".TABLE_BINS."` ON ".TABLE_CARDS.".card_bin = ".TABLE_BINS.".card_bin ".$where2." LIMIT ".(($page-1)*$perPage).",".$perPage;

echo(' elsesearch ='.$sql);
}


	$listcards = $db->fetch_array($sql, $sql_data);

	$sql = "SELECT * FROM `".TABLE_USERS."`";
	$listusers = $db->fetch_array($sql);
	$newlistusers = array();
	foreach ($listusers as $user) $newlistusers[$user["user_id"]] = $user;

	$listusers = $newlistusers;
	unset($newlistusers);
?>
				<div id="search_cards">
					<div class="section_title"><font color="white">SEARCH CARDS</font></div>
					<div class="section_content">
						<div>
								<form id="searchForm" name="searchForm" method="GET" action="testcards.php">
									<input type="hidden" name="category_id" value="<?=$_GET["category_id"]?>" />
									<input type="hidden" name="stagnant" value="<?=$_GET["stagnant"]?>" /-->
									<div>

                                    <div style="float:left;height:66px;">
									<div>
									<span class="bold">BIN +$<?=number_format($db_config["binPrice"], 2)?></span>
									</div>
									<div>
									<textarea name="txtBin" class="formstyle" id="txtBin" style="height:20px;"><?=$_GET["txtBin"]?></textarea>
									</div>
									</div>
                                    <div style="float:left;height:66px;">
									<div>
									<span class="bold">CITY +$<?=number_format($db_config["cityPrice"], 2)?></span>
									</div>
									<div>
                            		<select name="lstCity" class="formstyle" id="lstCity">
												<option value="">All City</option>

<?php
	//$sql = "SELECT card_city, count(*) FROM `".TABLE_CARDS."` WHERE ".$group_where." GROUP BY card_city ORDER BY card_city";
  $sql1 = "SELECT DISTINCT card_city FROM cards ";

        $sqlltewst=mysql_query($sql1);
        $test=mysql_fetch_array($sqlltewst);
            print_r( $test);
	$allCountry = $db->fetch_array($sql1, $group_where_data);
	if (count($allCountry) > 0) {
		foreach ($allCountry as $country)
echo "<option value=\"".$country['card_city']."\"".(($_GET["lstCity"] == $country['card_city'])?" selected":"") .">".$country['card_city']."</option>";

	}
?>
											</select>
									</div>
									</div>
                                    <div style="float:left;height:66px;">
									<div>
									<span class="bold">STATE +$<?=number_format($db_config["statePrice"], 2)?></span>
									</div>
									<div>
									<select name="lstState" class="formstyle" id="lstState">
												<option value="">All State</option>
<?php
   //	$sql = "SELECT DISTINCT card_state FROM `".TABLE_CARDS."` WHERE ".$group_where." ORDER BY card_state";
    $sql1 = "SELECT DISTINCT card_state FROM cards ";
        $sqlltewst=mysql_query($sql1);
        $test=mysql_fetch_array($sqlltewst);
            print_r( $test);
	$allCountry = $db->fetch_array($sql1, $group_where_data);
	if (count($allCountry) > 0) {
		foreach ($allCountry as $country)
		   echo "<option value=\"".$country['card_state']."\"".(($_GET["lstState"] == $country['card_state'])?" selected":"").">".$country['card_state']."</option>";

	}
?>
											</select>
									</div>
									</div>
                                    <div style="float:left;height:66px;">
									<div>
									<span class="bold">ZIP +$<?=number_format($db_config["zipPrice"], 2)?></span>
									</div>
									<div>
									<input name="txtZip" type="text" class="formstyle" id="txtZip" value="<?=$_GET["txtZip"]?>" size="12">
									</div>
									</div>
                                    <div style="float:left;height:66px;">
									<div>
									<span class="bold">COUNTRY +$<?=number_format($db_config["countryPrice"], 2)?></span>
									</div>
									<div>
									<select name="lstCountry" class="formstyle" id="lstCountry">
												<option value="">All Country</option>
<?php
	$sql = "SELECT card_country, count(*) FROM `".TABLE_CARDS."` WHERE ".$group_where." GROUP BY card_country ORDER BY card_country";
	$allCountry = $db->fetch_array($sql, $group_where_data);
	if (count($allCountry) > 0) {
		foreach ($allCountry as $country)
			echo "<option value=\"".$country['card_country']."\"".(($_GET["lstCountry"] == $country['card_country'])?" selected":"").">".$country['card_country']." (".$country['count(*)'].")</option>";

	}
?>
											</select>
									</div>
									</div>
                                    <div style="float:left;height:66px;">
									<div>
									<span class="bold">SELLER</span>
									</div>
									<div>
									<select name="lstSeller" class="formstyle" id="lstSeller">
												<option value="">All Seller</option>
												<option style="color:red;" value="0" <?=(strval($_GET["lstSeller"]) == "0")?" selected":""?>>SYSTEM</option>
<?php
	$sql = "SELECT * FROM `".TABLE_USERS."` WHERE user_groupid = '".PER_SELLER."' ORDER BY user_name";
	$allSeller = $db->fetch_array($sql);
	if (count($allSeller) > 0) {
		foreach ($allSeller as $seller)
			echo "<option style=\"color:".$user_groups[$seller['user_groupid']]["group_color"].";\" value=\"".$seller['user_id']."\"".(($_GET["lstSeller"] == $seller['user_id'])?" selected":"").">".$seller['user_name']."</option>";

	}
?>
											</select>
									</div>
									</div>
                                    <div style="float:left;height:66px;">
									<div>
									<a href='#' onclick="$('#txtBin').val('');$('#searchForm').submit();"><img src="./images/all-icon.png" class="iconcc" /></a>
											<a href='#' onclick="$('#txtBin').val(3);$('#searchForm').submit();"><img src="./images/american-express-icon.png" class="iconcc" /></a>
											<a href='#' onclick="$('#txtBin').val(4);$('#searchForm').submit();"><img src="./images/visa-icon.png" class="iconcc" /></a>
											<a href='#' onclick="$('#txtBin').val(5);$('#searchForm').submit();"><img src="./images/mastercard-icon.png" class="iconcc" /></a>
											<a href='#' onclick="$('#txtBin').val(6);$('#searchForm').submit();"><img src="./images/discover-icon.png" class="iconcc" /></a>
									</div>
									</div>
                                    <div style="float:left;height:66px;">
									<div>
									<span class="bold"><input type="checkbox" name="boxSSN" id="boxSSN" <?=($_GET["boxSSN"] != "")?"checked ":""?>>Have SSN</span>
									</div>
									</div>

                                    <div style="float:left;height:66px;">
									<div>
									<span><input type="checkbox" name="boxDob" id="boxDob" <?=($_GET["boxDob"] != "")?"checked ":""?>>Have DoB</span>
									</div>
									</div>



                                    </div>
                                    <div style="float:none; clear:both;"></div>
											<input name="btnSearch" type="submit" class="search_but" id="btnSearch" value="Search">
                                    </form>
							</div>
					</div>
				</div>
                <div style="float:none; clear:both;"></div>
				<div id="cards">
					<div class="section_title"><font color="white">AVAILABLE CARDS</font></div>
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
						<table class="content_table borderstyle">
							<tbody>
								<form name="addtocart" method="POST" action="./cart.php">
									<tr>
										<td class="centered">
											<span class="bold">CARD TYPE</span>
										</td>
										<td class="centered">
											<span class="bold">CARD NUMBER</span>
										</td>
										<td class="centered">
											<span class="bold">CATEGORY</span>
										</td>
										<td class="centered">
											<span class="bold">FIRST NAME</span>
										</td>
										<td class="centered">
											<span class="bold">CITY</span>
										</td>
										<td class="centered">
											<span class="bold">STATE</span>
										</td>
										<td class="centered">
											<span class="bold">ZIP</span>
										</td>
										<td class="centered">
											<span class="bold">COUNTRY</span>
										</td>
										<td class="centered">
											<span class="bold">SELLER</span>
										</td>
										<td class="centered">
											<span class="bold">SSN</span>
										</td>
										<td class="centered">
											<span class="bold">DOB</span>
										</td>
										<td class="centered">
											<span class="bold">PRICE</span>
										</td>
										<td class="centered">
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
											<span><?=$value['card_city']?></span>
										</td>
										<td class="centered">
											<span><?=$value['card_state']?></span>
										</td>
										<td class="centered">
											<span><?=$value['card_zip']?></span>
										</td>
										<td class="centered">
											<span><?=$value['card_country']?></span>
										</td>
										<td class="centered bold">
											<?php if ($value['card_sellerid'] == 0) { echo "<a href=\"?lstSeller=".$value['card_sellerid']."\"><span class=\"red\">SYSTEM</span></a>"; } else { echo "<a href=\"?lstSeller=".$value['card_sellerid']."\"><span style=\"color:".$user_groups[$listusers[$value['card_sellerid']]["user_groupid"]]["group_color"].";\">".$listusers[$value['card_sellerid']]["user_name"]."</span></a>"; }?>
										</td>
										<td class="centered">
											<span><?=($value['card_ssn'] == "")?"<img src='./images/untick.png' height='15px' width='15px' />":"<img src='./images/tick.png' height='15px' width='15px' />"?></span>
										</td>
										<td class="centered">
											<span><?=($value['card_dob'] == "")?"<img src='./images/untick.png' height='15px' width='15px' />":"<img src='./images/tick.png' height='15px' width='15px' />"?></span>
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