<?php
require("./header.php");

if ($checkLogin && $user_info["user_groupid"] < intval(PER_UNACTIVATE)) {
    if (isset($_GET["btnSearch"])) {
        $currentGet = "";
        $currentGet .= ($_GET["boxDob"]!="")?"boxDob=".$_GET["boxDob"]."&":"";
        $currentGet .= "txtBin=".$_GET["txtBin"]."&lstCountry=".$_GET["lstCountry"]."&lstState=".$_GET["lstState"]."&lstCity=".$_GET["lstCity"]."&txtZip=".$_GET["txtZip"];
        $currentGet .= ($_GET["boxSSN"]!="")?"&boxSSN=".$_GET["boxSSN"]:"";
        $currentGet .= "&btnSearch=Search&";
    }

    $sql = "SELECT * FROM `".TABLE_CARDS."` WHERE card_status = '".STATUS_DEFAULT."' AND card_userid = ".$user_info["user_id"]."";
    $sql_vars = array();

    if(!empty($_GET["txtBin"])){
        $sql_vars[]=$_GET["txtBin"]."%";
        $sql .= " AND AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') LIKE ?";
    }
    if(!empty($_GET["lstCountry"])){
        $sql_vars[]=$_GET["lstCountry"];
        $sql .= " AND card_country = ?";
    }
    if(!empty($_GET["lstState"])){
        $sql_vars[]=$_GET["lstState"];
        $sql .= " AND card_state = ?";
    }
    if(!empty($_GET["lstCity"])){
        $sql_vars[]=$_GET["lstCity"];
        $sql .= " AND card_city = ?";
    }
    if(!empty($_GET["txtZip"])){
        $sql_vars[]=$_GET["txtZip"]."%";
        $sql .= " AND card_zip LIKE ?";
    }
    if(!empty($_GET["expire"])) {
        $arrExpire = explode('-',$_GET["expire"]);
        if(isset($arrExpire[0])) {
            $sql_vars[] = $_GET["expire"];
            foreach($arrExpire as $key=>$vl) {
                if($vl > 12) {
                    $sql .= " AND ".TABLE_CARDS.".card_year LIKE '%".$vl."%'";
                } else {
                    $sql .= " AND ".TABLE_CARDS.".card_month like'%".$vl."%'";
                }
            }
        }
    }
    if($_GET["boxSSN"] == "on") $sql .= " AND card_ssn <> ''";
    if($_GET["boxDob"] == "on") $sql .= " AND card_dob <> ''";

    $perPage = 20;
    $totalPage = ceil($totalRecords/$perPage);

    $page = ceil($_GET["page"]);
    if(!is_numeric($page)||$page<1||$page>$totalPage) $page=1;

    $sql = str_replace(' * ', " * , AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number ", $sql);
    $sql .= " ORDER BY card_buyTime ASC LIMIT ".(($page-1)*$perPage).",".$perPage;
    //echo $sql;die;
    $listcards = $db->fetch_array($sql, $sql_vars);
    ?>
    <div id="search_cards">
        <div class="section_title">SEARCH CARDS</div>

        <table style="width: 100%;">
            <thead class="thead-gray" style="font-size:12px">
            <tr>
                <th>CARD NUMBER</th>
                <th>COUNTRY</th>
                <th>STATE</th>
                <th>CITY</th>
                <th>Expire</th>
                <th>Action</th>
            </tr>
            </thead>
            <form name="search" method="GET" action="mycards.php">
            <tbody>
                <td><input style="width:150px" name="txtBin" type="text" id="txtBin" value="<?=$_GET["txtBin"]?>" size="20" maxlength="20"></td>
                <td>
                    <select name="lstCountry" class="formstyle" id="lstCountry">
                        <option value="">All Country</option>
                        <?php
                        $sql = "SELECT DISTINCT card_country FROM `".TABLE_CARDS."` WHERE card_status = '".STATUS_DEFAULT."' AND card_userid = '".$user_info["user_id"]."'";
                        $allCountry = $db->fetch_array($sql);
                        if (count($allCountry) > 0) {
                            foreach ($allCountry as $country) {
                                echo "<option value=\"".$country['card_country']."\"".(($_GET["lstCountry"] == $country['card_country'])?" selected":"").">".$country['card_country']."</option>";
                            }
                        }
                        ?>
                    </select>
                </td>
                <td><input style="width:150px" name="lstState" type="text" id="lstState" value="<?=$_GET["lstState"]?>"></td>
                <td><input style="width:150px" name="lstCity" type="text" id="lstCity" value="<?=$_GET["lstCity"]?>"></td>
                <td><input type="text" maxlength="7" style="width:150px" value="<?php echo $_GET["expire"]?>" name="expire"
                           placeholder="Exp. 12-<?php echo date('y')+1;?> OR <?php echo date('y')+1;?>" ></td>
                <td>
                    <center>
                        <ul>
                            <li><input type="submit" name="btnSearch" class="minimal" value="Search"
                                       style="background-color:#3090C7;color:#fff;text-shadow: 0 1px 0 #000;"></li>
                        </ul>
                    </center>
                </td>
            </tbody>
                </form>
        </table>
    </div>
    <div style="float:none; clear:both;"></div>
    <div id="cards">
    <div class="section_ttle">
    <div class="section_title">Enjoy your cards</div>
    <div class="section_title">Click on 'Check' to check card, check fee is $<?=number_format($db_config["check_fee"], 2, '.', '')?>.</div>
    <div class="section_title">You have 10 minutes to get refund
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
            <table cellspacing="1" cellpadding="0" border="0" class="tablesorter" id="tablesorter" style="border-spacing: 1px;">
                <thead>
                <form name="mycards" method="POST" action="cardprocess.php">
                    <tr>
                        <th class="header">
                            <span class="bold">CARD NUMBER</span>
                        </th>
                        <th class="header">
                            <span class="bold">EXPIRE</span>
                        </th>
                        <th class="header">
                            <span class="bold">CVV</span>
                        </th>
                        <th class="header">
                            <span class="bold">NAME</span>
                        </th>
                        <th class="header">
                            <span class="bold">ADDRESS</span>
                        </th>
                        <th class="header">
                            <span class="bold">CITY</span>
                        </th>
                        <th class="header">
                            <span class="bold">STATE</span>
                        </th>
                        <th class="header">
                            <span class="bold">ZIP</span>
                        </th>
                        <th class="header">
                            <span class="bold">COUNTRY</span>
                        </th>
                        <th class="header">
                            <span class="bold">PHONE</span>
                        </th>
                        <th class="header">
                            <span class="bold">SSN</span>
                        </th>
                        <th class="header">
                            <span class="bold">DOB</span>
                        </th>
                        <th class="header">
                            <span class="bold">CHECK</span>
                        </th>
                        <th class="header">
                            <center><input class="formstyle" type="checkbox" name="selectAllCards" id="selectAllCards" onclick="checkAll(this.id, 'cards[]')" value=""></center>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    if (count($listcards) > 0) {
                        $j = 0;
                        foreach ($listcards as $key=>$value) {
                            switch ($value['card_check']) {
                                case strval(CHECK_VALID):
                                    $value['card_checkText'] = "<span class=\"green bold\">VALID</span>";
                                    break;
                                case strval(CHECK_INVALID):
                                    $value['card_checkText'] = "<span class=\"red bold\">TIMEOUT</span>";
                                    break;
                                case strval(CHECK_REFUND):
                                    $value['card_checkText'] = "<span class=\"pink bold\">REFUNDED</span>";
                                    break;
                                case strval(CHECK_UNKNOWN):
                                    $value['card_checkText'] = "<span class=\"blue bold\">UNKNOWN</span>";
                                    break;
                                case strval(CHECK_WAIT_REFUND):
                                    $value['card_checkText'] = "<span class=\"pink bold\">WAIT REFUND</span>";
                                    break;
                                default :
                                    $value['card_checkText'] = "<span class=\"bold\"><a style=\"cursor:pointer\" onclick=\"checkCard('".$value['card_id']."')\">Check ($".number_format($db_config["check_fee"], 2, '.', '').")</a></span>";
                                    break;
                            }
                            $j++;
                            ?>
                            <tr class="<?=($j%2==0?"odd":"even");?>">
                                <td class="centered bold">
                                    <span><?=$value['card_number']?></span>
                                </td>
                                <td class="centered">
                                    <?php echo (strlen($value["card_month"]) == 1?"0":"").$value["card_month"]."/".substr($value["card_year"],-2);?>
                                </td>
                                <td class="centered">
                                    <span><?=$value['card_cvv']?></span>
                                </td>
                                <td class="centered">
                                    <span><?=$value['card_name']?></span>
                                </td>
                                <td class="centered">
                                    <span><?=$value['card_address']?></span>
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
                                <td class="centered">
                                    <span><?=$value['card_phone']?></span>
                                </td>
                                <td class="centered">
                                    <span><?=($value['card_ssn'] == "")?"<img src='./images/untick.png' height='15px' width='15px' />":"<img src='./images/tick.png' height='15px' width='15px' />"?></span>
                                    <span><?=$value['card_ssn']?></span>
                                </td>
                                <td class="centered">
                                    <span><?=($value['card_dob'] == "")?"<img src='./images/untick.png' height='15px' width='15px' />":"<img src='./images/tick.png' height='15px' width='15px' />"?></span>
                                    <span><?=$value['card_dob']?></span>
                                </td>
                                <td class="centered bold">
                                    <span id="check_<?=$value['card_id']?>"><?=$value['card_checkText']?></span>
                                </td>
                                <td class="centered">
                                    <center><input type="checkbox" name="cards[]" value="<?=$value['card_id']?>"></center>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="15" class="centered">
                            <p>
                                <label>
                                    <input name="delete_invalid" type="submit" id="delete_invalid" onClick="return confirm('Are you sure you want to delete the INVALID Cards?')" value="Delete Invalid/Refunded Cards">
                                </label>
                                <span> | </span>
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
else if ($checkLogin && $_SESSION["user_groupid"] == intval(PER_UNACTIVATE)){
    require("./miniactivate.php");
}
else {
    require("./minilogin.php");
}
require("./footer.php");
?>