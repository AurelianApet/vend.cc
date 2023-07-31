<?php
$max_search_bin=100;
require("./header.php");
if($checkLogin&&$_SESSION["user_groupid"]<intval(PER_UNACTIVATE)){
    if($_GET["category_id"]!=""){
        $_GET["category_id"]=intval($_GET["category_id"]);
        if($_GET["category_id"]>0){
            $searchCategory="card_categoryid = '".$_GET["category_id"]."'";
        }else if($_GET["category_id"]==0){
            $searchCategory="card_categoryid NOT IN (SELECT category_id FROM `".TABLE_CATEGORYS."`)";
        }else{
            $searchCategory="1";
            $_GET["category_id"]="";
        }
    }else{
        $searchCategory="1";
    }
    if(strtolower($_GET["stagnant"])=="true"){$_GET["stagnant"]="true";
    }else if(strtolower($_GET["stagnant"])=="false"){$_GET["stagnant"]="false";
    }else{$_GET["stagnant"]="";
    }$currentGet="category_id=".$_GET["category_id"]."&";
    $currentGet.="stagnant=".$_GET["stagnant"]."&";
    $currentGet.="txtBin=".$_GET["txtBin"]."&";
    //brand
    $currentGet .= "lstBrand=".$_GET["lstBrand"]."&";
    // price
    $currentGet .= "min_price=".$_GET["min_price"]."&";
    $currentGet .= "max_price=".$_GET["max_price"]."&";
    // Valid Rate
    $currentGet .= "min_valid=".$_GET["min_valid"]."&";
    $currentGet .= "max_valid=".$_GET["max_valid"]."&";
    // expire
    $currentGet .= "expire=".$_GET["expire"]."&";
    // bank name
    $currentGet .= "lstBank=".$_GET["lstBank"]."&";
    $_GET["lstSeller"]=trim($_GET["lstSeller"]);
    if(isset($_GET["btnSearch"])){$currentGet.="lstSeller=".$_GET["lstSeller"]."&lstCountry=".$_GET["lstCountry"]."&lstState=".$_GET["lstState"]."&lstCity=".$_GET["lstCity"]."&txtZip=".$_GET["txtZip"];
        $currentGet.=($_GET["boxDob"]!="")?"&boxDob=".$_GET["boxDob"]:"";
        $currentGet.=($_GET["boxSSN"]!="")?"&boxSSN=".$_GET["boxSSN"]:"";
        $currentGet.="&btnSearch=Search&";
    }$searchCVV=1;
    if($_GET["stagnant"]=="true"){$searchExpire="(card_year = ".date("Y")." AND card_month = ".date("n").")";
    }else if($_GET["stagnant"]=="false"){$searchExpire="(card_year > ".date("Y")." OR (card_year = ".date("Y")." AND card_month > ".date("n")."))";
    }else{$searchExpire="1";
    }$searchSeller=!is_numeric($_GET["lstSeller"])||$_GET["lstSeller"]==""?$searchSeller="card_sellerid >= 0":$searchSeller="card_sellerid = ".$_GET["lstSeller"];
    // search Bins
    $search_where_data=array(trim($_GET["txtBin"]));
    if(empty($_GET["txtBin"])){
        $search_where_data=array();
        $searchBinWhere=array();
    }else{$search_where_data=array(trim($_GET["txtBin"]));
        $searchBinWhere=array();
    }
    $searchBins = array();
    if(isset($_GET["txtBin"]) AND $_GET["txtBin"]) {
        $searchBins=explode(",",$_GET["txtBin"]);
    }
    if(is_array($searchBins)&&count($searchBins)>0){
        $count_bin=1;
        $listCardBin = '';
        for($i=0; $i < count($searchBins); $i++) {
            if($i > 0) {
                $listCardBin .= ',';
            }
            $searchBins[$i] = (int)trim($searchBins[$i]);
            if($searchBins[$i] > 0) {
                $listCardBin .= $searchBins[$i];
            }
        }
        $searchBinWhere[] = " ".TABLE_CARDS.".card_bin IN (".$listCardBin.")";
        /*var_dump($searchBins);
        foreach($searchBins as&$searchBin){
            if($count_bin>$max_search_bin){
                break;
            }else{
                if(strlen($searchBin)>0){
                    if (strlen($searchBin)==1) {
                        $search_where_data[]=$db->escape(substr(trim($searchBin),0,1));
                        $searchBinWhere[]=" SUBSTRING(".TABLE_CARDS.".card_bin, 1, 1) = '".$db->escape(substr(trim($searchBin),0,1))."' ";
                    } else {
                        $search_where_data[]=$db->escape(substr(trim($searchBin),0,6))."%";
                        $searchBinWhere[]=" ".TABLE_CARDS.".card_bin LIKE '%".$db->escape(substr(trim($searchBin),0,6))."%' ";
                    }
                }
                $count_bin++;
            }
        }*/

    }
    $search_where = count($searchBinWhere) > 0 ? ' AND ( ' . implode(' OR ' , $searchBinWhere) . ' )' : '';
    //$search_where= implode(' OR ' , $searchBinWhere);
    if (!empty($_GET["lstCountry"])) {
        array_push($search_where_data, $_GET["lstCountry"]);
        $search_where .= " AND " . TABLE_CARDS . ".card_country = '" . $_GET["lstCountry"] . "'";
    }
    if (!empty($_GET["lstState"])) {
        array_push($search_where_data, $_GET["lstState"]);
        $search_where .= "  AND " . TABLE_CARDS . ".card_state = '" . $_GET["lstState"] . "'";
    }
    if (!empty($_GET["lstCity"])) {
        array_push($search_where_data, $_GET["lstCity"]);
        $search_where .= " AND " . TABLE_CARDS . ".card_city = '" . $_GET["lstCity"] . "'";
    }
    $searchZip = array();
    if (!empty($_GET["txtZip"])) {
        array_push($search_where_data, $_GET["txtZip"] . "%");
        $searchZip = explode(',',$_GET["txtZip"]);
        $listCardZip = '';
        for($i=0; $i < count($searchZip); $i++) {
            if($i > 0) {
                $listCardZip .= ',';
            }
            $searchZip[$i] = (int)trim($searchZip[$i]);
            if($searchZip[$i] > 0) {
                $listCardZip .= $searchZip[$i];
            }
        }
        $search_where .= " AND ".TABLE_CARDS.".card_zip IN (".$listCardZip.")";
    }
    if(!empty($_GET["lstBrand"])) {
        array_push($search_where_data, $_GET["lstBrand"] . "%");
        $search_where .= " AND " . TABLE_BINS . ".card_type LIKE '" . $_GET["lstBrand"] . "'";
    }

    if(!empty($_GET["lstType"])){
        array_push($search_where_data,$_GET["lstType"]);
        $search_where.=" AND ".TABLE_BINS.".card_client = '".$_GET["lstType"]."'";
    }
    if(!empty($_GET["lstLevel"])){
        array_push($search_where_data,$_GET["lstLevel"]);
        $search_where.=" AND ".TABLE_BINS.".card_level = '".$_GET["lstLevel"]."'";
    }
    if(!empty($_GET["lstBank"])){
        array_push($search_where_data,$_GET["lstBank"]);
        $search_where.=" AND ".TABLE_BINS.".card_bank LIKE '%".$_GET["lstBank"]."%'";
    }
    if(!empty($_GET["min_price"])) {
        array_push($search_where_data,$_GET["min_price"]);
        $search_where.=" AND ".TABLE_CARDS.".card_price >= ".$_GET["min_price"];
    }
    if(!empty($_GET["max_price"])) {
        array_push($search_where_data,$_GET["max_price"]);
        $search_where.=" AND ".TABLE_CARDS.".card_price <= ".$_GET["max_price"];
    }
    if(!empty($_GET["min_valid"])) {
        array_push($search_where_data,$_GET["min_valid"]);
        $search_where.=" AND ((".TABLE_BINS.".card_valid/".TABLE_BINS.".card_total)*100) >= ".$_GET["min_valid"];
    }
    if(!empty($_GET["max_valid"])) {
        array_push($search_where_data,$_GET["max_valid"]);
        $search_where.=" AND ((".TABLE_BINS.".card_valid/".TABLE_BINS.".card_total)*100) <= ".$_GET["max_valid"];
    }
    if(!empty($_GET["expire"])) {
        $arrExpire = explode('-',$_GET["expire"]);
        if(isset($arrExpire[0])) {
            array_push($search_where_data,$_GET["expire"]);
            foreach($arrExpire as $key=>$vl) {
                if($vl > 12) {
                    $search_where .= " AND ".TABLE_CARDS.".card_year LIKE '%".$vl."%'";
                } else {
                    $search_where .= " AND ".TABLE_CARDS.".card_month like'%".$vl."%'";
                }
            }
        }
    }
    $searchSSN=($_GET["boxSSN"]=="1")?" AND card_ssn <> ''":"";
    $searchDob=($_GET["boxDob"]=="1")?" AND card_dob <> ''":"";
    $group_where_data=array($searchCategory,$searchCVV,$searchExpire);
    $group_where=" $searchCategory AND ".$searchSeller." AND card_status = '".STATUS_DEFAULT."' AND card_userid = '0'";
    $search_where.=$searchSSN.$searchDob;
    $sql="SELECT count(*) FROM `".TABLE_CARDS."` INNER JOIN `".TABLE_BINS."` ON ".TABLE_BINS.".card_bin = ".TABLE_CARDS.".card_bin  WHERE 1=1 AND ".$group_where."  ".$search_where;
    //echo $sql;die;
    $sql_data=array_merge($group_where_data,$search_where_data);
    $totalRecords=$db->num_rows($sql,$sql_data);
    $perPage=20;
    $totalPage=ceil($totalRecords/$perPage);
    $page=is_numeric($_GET["page"])?ceil($_GET["page"]):1;
    if($page>$totalPage)$page=1;

    $sql="SELECT `".TABLE_CARDS."`.*, `".TABLE_CATEGORYS."`.*, `".TABLE_BINS."`.card_bank, `".TABLE_BINS."`.card_client, `".TABLE_BINS."`.card_type, `".TABLE_BINS."`.card_level, `".TABLE_BINS."`.card_total, `".TABLE_BINS."`.card_refund, `".TABLE_BINS."`.card_valid FROM (`".TABLE_CARDS."` LEFT JOIN `".TABLE_CATEGORYS."` ON ".TABLE_CARDS.".card_categoryid = ".TABLE_CATEGORYS.".category_id) LEFT JOIN `".TABLE_BINS."` ON ".TABLE_CARDS.".card_bin = ".TABLE_BINS.".card_bin WHERE ".$group_where."  ".$search_where." ORDER BY RAND() LIMIT ".(($page-1)*$perPage).",".$perPage;
    //echo $sql;
    $listcards=$db->fetch_array($sql,$sql_data);
    $sql="SELECT * FROM `".TABLE_USERS."` WHERE user_groupid = '".PER_SELLER."'" ;

    $listusers=$db->fetch_array($sql);
    $newlistusers=array();
    foreach($listusers as $user)$newlistusers[$user["user_id"]]=$user;
    $listusers=$newlistusers;
    //echo count($listusers);die;
    unset($newlistusers);
    ?>
    <div id="search_cards" style="display: table;margin-left: auto;margin-right: auto; width: 100%">
        <div class="section_title">SEARCH CARDS</div>
        <form method="get" action="" enctype="multipart/form-data">
        <table style="width: 100%;">
            <thead class="thead-gray" style="font-size:12px">
            <tr>
                <th>Category</th>
                <th>Country +$<?=number_format($db_config["countryPrice"], 2)?></th>
                <th>Brand +$<?=number_format($db_config["binPrice"], 2)?></th>
                <th>Level +$<?=number_format($db_config["statePrice"], 2)?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <form action="" method="GET" id="searcher"></form>
                <input type="hidden" name="action" value="search">
                <td><select name="category_id" id="category_id" style="width:200px;">
                        <option value="">All</option>
                        <?php
                        $sql = "SELECT category_id, category_name FROM `".TABLE_CATEGORYS."` WHERE 1 ORDER BY category_name";
                        $allCategory = $db->fetch_array($sql, $group_where_data);
                        if (count($allCategory) > 0) {
                            foreach ($allCategory as $category) {
                                echo "<option value=\"".$category['category_id']."\"".(($_GET["category_id"] == $category['category_id'])?" selected":"").">".$category['category_name']."</option>";
                            }
                        }
                        ?>
                    </select></td>
                <td><select name="lstCountry" id="lstCountry" style="width:200px;">
                        <option value="">All Country</option>
                        <?php
                        $sql = "SELECT card_country, count(*) FROM `".TABLE_CARDS."` WHERE ".$group_where." GROUP BY card_country ORDER BY card_country";
                        $allCountry = $db->fetch_array($sql, $group_where_data);
                        if (count($allCountry) > 0) {
                            foreach ($allCountry as $country) {
                                echo "<option value=\"".$country['card_country']."\"".(($_GET["lstCountry"] == $country['card_country'])?" selected":"").">".$country['card_country']." (".$country['count(*)'].")</option>";
                            }
                        }
                        ?>
                    </select>
                    </select></td>
                <td>
                    <select name="lstBrand" id="lstBrand" style="width:200px;">
                        <option value="">All</option>
                        <?php
                        $sql = "SELECT DISTINCT card_type FROM `".TABLE_BINS."` ORDER BY card_type";
                        $allType = $db->fetch_array($sql, $group_where_data);

                        if (count($allType ) > 0) {
                            foreach ($allType as $type)
                                echo "<option value=\"".$type['card_type']."\"".(($_GET["lstBrand"] == $type['card_type'])?" selected":"").">".$type['card_type']."</option>";

                        }
                        ?>
                    </select>
                    </td>
                <td><select name="lstLevel" id="lstLevel">
                        <?php
                        $sql = "SELECT DISTINCT card_level FROM `".TABLE_BINS."` ORDER BY card_level";
                        $allLevel = $db->fetch_array($sql, $group_where_data);
                        if (count($allLevel) > 0) {
                            foreach ($allLevel as $level) {
                                echo "<option value=\"".$level['card_level']."\"".(($_GET["lstLevel"] == $level['card_level'])?" selected":"").">".($level['card_level'] == ""?"All":$level['card_level'])."</option>";
                            }

                        }
                        ?>
                    </select></td>
            </tr>
            </tbody>
            <thead class="thead-gray" style="font-size:12px">
            <tr>
                <th>Bank name</th>
                <th>State</th>
                <th>Type</th>
                <th>Expire</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><input type="text" style="width:200px" value="<?php echo (isset($_GET["lstBank"])?$_GET["lstBank"]:"");?>" name="lstBank" placeholder="Bank name"></td>
                <td><input type="text" style="width:200px" value="<?php echo (isset($_GET['lstState'])?$_GET['lstState']:'');?>" name="lstState" placeholder="State"></td>
                <td>
                    <select name="lstType" style="width:200px;" id="lstType">
                        <?php
                        $sql = "SELECT DISTINCT card_client FROM `".TABLE_BINS."` ORDER BY card_client";
                        $allType = $db->fetch_array($sql, $group_where_data);
                        if (count($allType ) > 0) {
                            foreach ($allType as $type)
                                echo "<option value=\"".$type['card_client']."\"".(($_GET["lstType"] == $type['card_client'])?" selected":"").">".(!$type['card_client']?"All":$type['card_client'])."</option>";

                        }
                        ?>
                    </select>
                </td>
                <td><input type="text" maxlength="7" style="width:150px" value="<?php echo $_GET["expire"]?>" name="expire"
                           placeholder="Exp. 12-<?php echo date('y')+1;?> OR <?php echo date('y')+1;?>" ></td>
            </tr>
            </tbody>
            <thead class="thead-gray" style="font-size:12px">
            <tr>
                <th>Bins</th>
                <th>City</th>
                <th>Fullz options</th>
                <th>Valid rate</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><input type="text" name="txtBin" style="width:200px" id="txtBin" placeholder="371322, 444796" value="<?php echo (isset($_GET['txtBin'])?$_GET['txtBin']:"");?>"></td>
                <td><input type="text" style="width:200px" value="<?php echo (isset($_GET['lstCity'])?$_GET['lstCity']:'');?>" name="lstCity" placeholder="City"></td>
                <td><input type="checkbox" name="boxSSN" id="boxSSN" value="1" <?php echo ((isset($_GET["boxSSN"]) AND $_GET["boxSSN"] == 1)?' checked="checked"':""); ?> class="checkbox_margin"> <span
                        class="font12">Have SSN</span> <br>
                    <input type="checkbox" name="boxDob" id="boxDob" value="1"<?php echo ((isset($_GET["boxDob"]) AND $_GET["boxDob"] == 1)?' checked="checked"':""); ?> class="checkbox_margin"> <span class="font12">Have DoB</span> <br></td>
                <td>
                    <table class="tablesorter 2" id="tablesorter5"
                           style="width:auto;background-color:#fff;">
                        <tbody>
                        <tr>
                            <td><label for="price" style="font-size: 15px;text-transform: uppercase;">Min</label></td>
                            <td>
                                <div class="input-group" style="display: table;border: 0px solid #ccc;"><input
                                        type="text" value="<?php echo (isset($_GET["min_valid"])?$_GET["min_valid"]:"");?>" name="min_valid" id="min_valid" style="width:50px"><span
                                        class="input-group-addon" style="border: 0 none;padding:6px;">%</span></div>
                            </td>
                            <td><label for="price" style="font-size: 15px;text-transform: uppercase;">Max</label></td>
                            <td>
                                <div class="input-group" style="display: table;border: 0px solid #ccc;">
                                    <input type="text" value="<?php echo (isset($_GET["max_valid"])?$_GET["max_valid"]:"");?>" name="max_valid" id="max_valid" style="width:50px"><span
                                        class="input-group-addon" style="border: 0 none;padding:6px;">%</span>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>


            </tr>
            </tbody>
            <thead class="thead-gray" style="font-size:12px">
            <tr>
                <th>Price</th>
                <th>Zips</th>
                <th>SELLER</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <table class="tablesorter 2" id="tablesorter5"
                           style="width:auto;background-color:#fff;">
                        <tbody>
                        <tr>
                            <td><label for="price" style="font-size: 15px;text-transform: uppercase;">Min</label></td>
                            <td>
                                <div class="input-group" style="display: table;border: 0px solid #ccc;"><input
                                        type="text" value="<?php echo (isset($_GET["min_price"])?$_GET["min_price"]:"");?>" name="min_price" id="min_price" style="width:50px"><span
                                        class="input-group-addon" style="border: 0 none;padding:6px;">$</span></div>
                            </td>
                            <td><label for="price" style="font-size: 15px;text-transform: uppercase;">Max</label></td>
                            <td>
                                <div class="input-group" style="display: table;border: 0px solid #ccc;">
                                    <input type="text" value="<?php echo (isset($_GET["max_price"])?$_GET["max_price"]:"");?>" name="max_price" id="max_price" style="width:50px"><span
                                        class="input-group-addon" style="border: 0 none;padding:6px;">$</span>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td>
                    <input type="text" name="txtZip" style="width:200px" id="txtZip" placeholder="41560, BL15BN" value="<?php echo (isset($_GET['txtZip'])?$_GET['txtZip']:"");?>">
                </td>
                <td>
                    <select name="lstSeller" style="width:200px;" id="lstSeller">
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
                </td>
                <td>
                    <center>
                        <ul>
                            <li><input type="submit" name="btnSearch" class="minimal" value="Search"
                                       style="background-color:#3090C7;color:#fff;text-shadow: 0 1px 0 #000;"></li>
                        </ul>
                    </center>
                </td>
            </tr>
            </tbody>
        </table>
        </form>
    </div>
    <div style="float:none; clear:both;"></div>
    <div id="cards">
        <div class="section_title">AVAILABLE CARDS</div>
        <?php
        if ($totalPage > 1) { ?>
        <br>
        <div>
            <ul id="pagination">
            <?php
                    $numberPage = 1;
                    $listPages = '';
                    $isNext = false;
                    $isPrevious = false;
                    $isFirst = false;
                    for($i = $page; $i <= $totalPage; $i++) {
                        if($page > 1 AND $isFirst == false) {
                            $listPages = "<li class=\"First\"><a href=\"?".$currentGet."page=1\">«First</a></li>".$listPages;
                            $isFirst = true;
                        }
                        if ($page>1 AND $isPrevious == false) {
                            $listPages .= "<li class=\"previous\"><a href=\"?".$currentGet."page=".($page-1)."\">«Previous</a></li>";
                            $isPrevious = true;
                        }

                        if($i == $page) {
                            $listPages .= "<li class=\"active\">".$page."</li>";
                        } else if($numberPage < 26) {
                            $listPages .= "<li><a href=\"?".$currentGet."page=".$i."\">".$i."</a></li>";
                        }
                        if($numberPage > 26) {
                            if($page < $totalPage AND $isNext == false) {
                                $listPages .= "<li class=\"next\"><a href=\"?".$currentGet."page=".($page+1)."\">Next »</a></li>";
                                $isNext = true;
                            }
                            break;
                        }
                        $numberPage++;
                    }
                    echo $listPages;

                ?>
            </ul>
            <br>
        </div>
            <?php } ?>
        <br>
        <div class="section_content">
            <form name="addtocart" id="addtocart" method="POST" action="./cart.php">
            <table cellspacing="1" cellpadding="0" border="0" class="tablesorter" id="tablesorter" style="border-spacing: 1px;">
                <thead>
                <tr>
                    <th><center><input type="checkbox" name="selectAllCards" id="selectAllCards" onclick="checkAll(this.id, 'cards[]')" value=""></center></th>
                    <th class="header">Bin</th>
                    <th class="header">Type</th>
                    <th class="header">Country</th>
                    <th class="header">Bank name</th>
                    <th class="header">Level</th>
                    <th class="header">Brand</th>
                    <th class="header">Name</th>
                    <th class="header">Expire</th>
                    <th class="header">City</th>
                    <th class="header">Zip</th>
                    <th class="header">Address</th>
                    <th class="header">SSN</th>
                    <th class="header">DOB</th>
                    <th class="header">Category</th>
                    <th class="header">Valid rate</th>
                    <th class="header">Refund</th>
                    <th class="header">Price</th>
                    <th class="header">Cart</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    if (count($listcards) > 0) {
                        $i = 1;

                        $arrShoppingCard = ($_SESSION["shopping_card_items"]);

                        foreach ($listcards as $key=>$value) {
                            $card_firstname = explode(" ", $value['card_name']);
                            $card_firstname = $card_firstname[0];

                            $sql="SELECT mini_name FROM `country` where full_name='".$value['card_country']."'";
                            $flag=$db->query_first($sql);
                            $class = 'odd';
                            if($i % 2 == 0) {
                                $class = 'even';
                            }
                        ?>
                            <tr class="<?php echo $class;?>">
                            <td><center><input<?=(isset($arrShoppingCard[$value['card_id']])?' disabled="disabled"':''); ?> type="checkbox" id="card-<?=$value['card_id']?>" name="cards[]" value="<?=$value['card_id']?>"></center></td>
                            <td class="c"><?php echo $value['card_bin'];?></td>
                            <td><?=$value['card_client']?></td>
                            <td class="h">
                                <img src="images/flag/<?=$flag['mini_name'].'.png';?>" title="<?=$value['card_country']?>"> <?=$value['card_country']?>
                            </td>
                            <td title="<?=$value["card_bank"]; ?>" class="h"><?=$value["card_bank"]; ?></td>
                            <td style="font-weight:bold;" class="c"><?=$value["card_level"]; ?></td>
                            <td class="c">
                                <center>
                                    <?php
                                    switch (intval(substr($value['card_bin'], 0, 1))) {
                                        case 3:
                                            echo "<img src=\"./images/american-express-icon.png\" title=\"".$value["card_type"]."\"/>";
                                            break;
                                        case 4:
                                            echo "<img src=\"./images/visa-icon.png\" title=\"".$value["card_type"]."\"/>";
                                            break;
                                        case 5:
                                            echo "<img src=\"./images/mastercard-icon.png\" title=\"".$value["card_type"]."\"/>";
                                            break;
                                        case 6:
                                            echo "<img src=\"./images/discover-icon.png\" title=\"".$value["card_type"]."\"/>";
                                            break;
                                    }
                                    ?>
                                </center>
                            </td>
                            <td><?=$card_firstname?></td>
                            <td><?php echo $value["card_month"]."/".$value["card_year"];?></td>
                            <td><?=$value["card_city"];?></td>
                            <td><?=$value["card_zip"];?></td>
                            <td title="<?=$value["card_address"];?>" class="h"><?=$value["card_address"];?></td>

                            <td>
                                <center><?=($value['card_ssn'] == "")?"<img src='./images/untick.png' height='15px' width='15px' />":"<img src='./images/tick.png' height='15px' width='15px' />"?></center>
                            </td>
                            <td>
                                <center><?=($value['card_dob'] == "")?"<img src='./images/untick.png' height='15px' width='15px' />":"<img src='./images/tick.png' height='15px' width='15px' />"?></center>
                            </td>
                            <td class="h c"><?=($value['category_name']=="")?"(No Category)":$value['category_name']?></td>
                            <td style="color:#00b318;font-weight: bold;" class="c">
                                <?php
                                    $validRate = (($value['card_total'] == 0)?100:($value['card_valid'] == 0?10:(round(($value['card_valid']/$value['card_total'])*100))));
                                    if($validRate >=100) {
                                        $validRate = 95;
                                    }
                                    echo $validRate.'%';
                                ?>
                            </td>
                            <td class="c">
                                <?php if($value['card_refund'] > 0) { ?>
                                <img src="./images/tick.png"><br><span style="font-size:11px;"><?php echo $value["card_refund"];?> MIN</span>
                                <?php } else { ?>
                                <center><img width="15px" height="15px" src="./images/untick.png"></center>
                                <?php } ?>
                            </td>
                            <td class="c">
                                <?php
                                printf("$%.2f", $value['card_price']);
                                if (strlen($_GET["lstBrand"]) > 1 && $db_config["binPrice"] > 0) {
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
                            </td>
                            <td class="c">
                                    <button title="<?=(isset($arrShoppingCard[$value['card_id']])?'Remove out Shopping Cart':'Add to Shopping Cart'); ?>" type="button" type-action="<?=(isset($arrShoppingCard[$value['card_id']])?'del':'add'); ?>" card_id="<?=$value["card_id"];?>" class="<?=(isset($arrShoppingCard[$value['card_id']])?'btn_d':'btn-cart'); ?> btn-xs btn-default addToCartOneCard"></button>
                            </td>
                            </tr>
                        <?php
                            $i++;
                        }
                    }
                ?>
                </tbody>
            </table>
            <ul id="pagination">
                <li style="padding-right: 10px;"> <input type="submit" id="download_select" style="width: 300px;" value="Add Selected Cards to Shopping Cart" class="minimal" name="addToCart"></li>
                <li style="padding-right: 10px;"> <input type="button" value="Go To Shopping Cart" class="minimal" style="width: 200px;" id="go-to-cart"></li>
                <li> <input type="button" value="Clean Shopping Cart" class="minimal" style="width: 200px; " onclick="clean_shopping_cart();"></li>

            </ul>
            <div class="clearfix">&nbsp;</div>
                <input name="txtBin" type="hidden" id="txtBin" value="<?=$_GET["txtBin"]?>" />
                <input name="txtCountry" type="hidden" id="txtCountry" value="<?=$_GET["lstCountry"]?>" />
                <input name="lstState" type="hidden" id="lstState" value="<?=$_GET["lstState"]?>" />
                <input name="lstCity" type="hidden" id="lstCity" value="<?=$_GET["lstCity"]?>" />
                <input name="txtZip" type="hidden" id="txtZip" value="<?=$_GET["txtZip"]?>" />
                <script>
                    //$('#close-leftSide').trigger('click');
                    function closeLeftSide() {
                        $('#close-leftSide').click();
                    }
                    function clean_shopping_cart() {
                        $.post( "ajax.php", { fnc:'clean_cart'})
                            .done(function( data ) {
                                $("#number_shopping_cards").html(0);
                            });
                    }
                    $(document).ready(function() {
                        closeLeftSide();
                        $("#go-to-cart").click(function() {
                            window.location.href = "/cart.php";
                        });
                        $(".addToCartOneCard").click(function(){
                            var carId = $(this).attr("card_id");
                            var type_action = $(this).attr("type-action");
                            $(this).prop("disabled",true);
                            var total_card = $("#number_shopping_cards").html()*1;
                            var obj = this;
                            <?php
                                $listParam = '';
                                $listParam .= $_GET["txtBin"]?'txtBin:"'.$_GET["txtBin"].'",':"";
                                $listParam .= $_GET["lstCountry"]?'txtCountry:"'.$_GET["lstCountry"].'",':"";
                                $listParam .= $_GET["lstState"]?'lstState:"'.$_GET["lstState"].'",':"";
                                $listParam .= $_GET["lstCity"]?'lstCity:"'.$_GET["lstCity"].'",':"";
                                $listParam .= $_GET["txtZip"]?'txtZip:"'.$_GET["txtZip"].'",':"";
                                $listParam = trim($listParam,',');
                            ?>
                            $.post( "ajax.php", { type_action:type_action,card_id: carId,fnc:"addToCart"<?=($listParam?",".$listParam:"")?>  })
                            .done(function( data ) {
                                if(data == 1) {
                                    $("#card-" + carId).prop("disabled",true);
                                    if(type_action == 'add') {
                                        $(obj).removeClass("btn-cart");
                                        $(obj).addClass("btn_d");
                                        $(obj).attr("type-action","del");
                                        total_card += 1;
                                    } else {
                                        $(obj).removeClass("btn_d");
                                        $(obj).addClass("btn-cart");
                                        $(obj).attr("type-action","add");
                                        total_card -= 1;
                                    }
                                    $("#number_shopping_cards").html(total_card);
                                    $(obj).prop("disabled",false);
                                }

                            });
                        });
                    });
                </script>
            </form>

        </div>
        <?php if ($totalPage > 1) { ?>
        <div style="padding-top: 10px;">
            <ul id="pagination">
                <?php

                   echo $listPages;

                ?>
            </ul>
            <br>
        </div>
        <br>
        <?php } ?>
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