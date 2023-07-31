<?php
/**
 * Created by PhpStorm.
 * User: BangTD
 * Date: 1/3/16
 * Time: 11:48 AM
 */
set_time_limit(0);
session_start();
require("./includes/config.inc.php");

$login_array = checkLogin(PER_USER);
$checklogin = $login_array[0];
$user_info = $login_array[1];
if($checklogin == 1) {
    $fnc = $_REQUEST['fnc'];
    if($fnc == 'addToCart') {

        $message = -1;
        $cardID = (int)$_POST['card_id'];
        $sql = "SELECT card_id, card_categoryid, AES_DECRYPT(card_number, '".strval(DB_ENCRYPT_PASS)."') AS card_number, card_bin, card_cvv, card_name, card_country, card_state, card_city, card_zip, card_ssn, card_dob, card_price FROM `".TABLE_CARDS."` WHERE card_status = '".STATUS_DEFAULT."' AND card_userid = 0 AND card_id =".$cardID;
        $addCards = $db->fetch_array($sql);
        if(isset($addCards[0]["card_id"])) {
            $type_action = $_POST["type_action"];
            if($type_action == 'del') {
                $arr = $_SESSION["shopping_card_items"];
                unset($arr[$cardID]);
                $_SESSION["shopping_card_items"] = $arr;
                $message =  1;
            } else {
                $card["card_id"] = $cardID;
                if (strlen($_POST["txtBin"]) > 1) {
                    $card["binPrice"] = $db_config["binPrice"];
                }
                else {
                    $card["binPrice"] = 0;
                }
                if ($_POST["txtCountry"] != "") {
                    $card["countryPrice"] = $db_config["countryPrice"];
                }
                else {
                    $card["countryPrice"] = 0;
                }
                if ($_POST["lstState"] != "") {
                    $card["statePrice"] = $db_config["statePrice"];
                }
                else {
                    $card["statePrice"] = 0;
                }
                if ($_POST["lstCity"] != "") {
                    $card["cityPrice"] = $db_config["cityPrice"];
                }
                else {
                    $card["cityPrice"] = 0;
                }
                if ($_POST["txtZip"] != "") {
                    $card["zipPrice"] = $db_config["zipPrice"];
                }
                else {
                    $card["zipPrice"] = 0;
                }
                $_SESSION["shopping_card_items"][$cardID] = $card;
                $message = 1;
            }
            if (is_array($_SESSION["shopping_card_items"])) {
                $_SESSION["shopping_cards"] = array_keys($_SESSION["shopping_card_items"]);
            }
        }
        echo $message;
        exit();
    }
    if($fnc == 'clean_cart') {
        $_SESSION["shopping_cards"] = null;
        $_SESSION["shopping_card_items"] = null;
        echo 1;
    }

} else {
    echo 'You have to login!';
}