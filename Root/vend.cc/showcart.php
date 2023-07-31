<?php
session_start();
require("./includes/config.inc.php");
if (checkLogin(PER_USER)) {
	if ($_GET["order_id"] != "") {
		$order_id = $db->escape($_GET["order_id"]);
		$order_userid = $db->escape($_SESSION["user_id"]);
		$t_vals=array($order_id);
		$sql = "SELECT * FROM `".TABLE_ORDERS."` WHERE order_id=?";
		if ($user_info["user_groupid"] != PER_ADMIN){
			$t_vals[]=$order_userid;
			$sql .= " AND order_userid=?";
		}
		
		$record = $db->query_first($sql, $t_vals);
		if ($record) {
			$shoppingCart = unserialize($record["order_item"]);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>VIPSERVICE - CVVZONE.SU</title>
		<meta content="index, follow" name="robots" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="icon" href="./favicon.ico" type="image/x-icon" />
		<link rel="stylesheet" type="text/css" href="./styles/main.css" />
		<script type="text/javascript" src="./js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="./js/jquery.popupWindow.js"></script>
		<script type="text/javascript" src="./js/main.js" ></script>
	</head>
	<body style="width:880px;">
			<div id="cart">
				<div class="section_title">CART ID: <?=$order_id?></div>
				<div class="section_title"><?=$buyResult?></div>
				<div class="section_content">
					<form name="shoping_cart" method="POST" action="">
						<table class="content_table">
							<tbody>
								<tr>
									<td colspan="8" class="centered bold">
										Credit Cards
									</td>
								</tr>
								<tr>
									<td class="formstyle centered bold">
										<span>CARD NUMBER</span>
									</td>
									<td class="formstyle centered bold">
										<span>CATEGORY<span>
									</td>
									<td class="formstyle centered bold">
										<span>FIRST NAME</span>
									</td>
									<td class="formstyle centered bold">
										<span>COUNTRY</span>
									</td>
									<!--td class="formstyle centered bold">
										<span>STATE</span>
									</td>
									<td class="formstyle centered bold">
										<span>CITY</span>
									</td>
									<td class="formstyle centered bold">
										<span>ZIP</span>
									</td-->
									<td class="formstyle centered bold">
										<span>SSN</span>
									</td>
									<td class="formstyle centered bold">
										<span>DOB</span>
									</td>
									<td class="formstyle centered bold" style="width:70px;">
										<span>PRICE</span>
									</td>
								</tr>
<?php
			if (array_key_exists('shoppingCards', $shoppingCart) && count($shoppingCart['shoppingCards']) > 0) {
				foreach ($shoppingCart['shoppingCards'] as $key=>$value) {
					if (substr_count($value['card_name'], " ") > 0) {
						$value["card_firstname"] = explode(" ", $value['card_name']);
						$value["card_firstname"] = $value["card_firstname"][0];
					} else {
						$value["card_firstname"] = $value["card_name"];
					}
					$value['card_ssn'] = ($value['card_ssn'] == "")?"NO":"YES";
					$value['card_dob'] = ($value['card_dob'] == "")?"NO":"YES";
					$value['category_name'] = ($value['category_name'] == "")?"(No category)":$value['category_name'];
					$value['card_total_price_format'] .= "<i>(Price $".number_format($value['card_price'], 2);
					$value['card_total_price'] = $value['card_price'];
					$value['card_addition_price'] = 0;
					if ($_SESSION["shopping_card_items"][$value["card_id"]]["binPrice"] > 0) {
						$value['card_addition_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['binPrice'];
						$value['card_total_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['binPrice'];
						$value['card_total_price_format'] .= "<br/>Search Bin $".number_format($_SESSION["shopping_card_items"][$value["card_id"]]['binPrice'], 2);
					}
					if ($_SESSION["shopping_card_items"][$value["card_id"]]["countryPrice"] > 0) {
						$value['card_addition_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['countryPrice'];
						$value['card_total_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['countryPrice'];
						$value['card_total_price_format'] .= "<br/>Search Country $".number_format($_SESSION["shopping_card_items"][$value["card_id"]]['countryPrice'], 2);
					}
					if ($_SESSION["shopping_card_items"][$value["card_id"]]["statePrice"] > 0) {
						$value['card_addition_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['statePrice'];
						$value['card_total_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['statePrice'];
						$value['card_total_price_format'] .= "<br/>Search State $".number_format($_SESSION["shopping_card_items"][$value["card_id"]]['statePrice'], 2);
					}
					if ($_SESSION["shopping_card_items"][$value["card_id"]]["cityPrice"] > 0) {
						$value['card_addition_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['cityPrice'];
						$value['card_total_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['cityPrice'];
						$value['card_total_price_format'] .= "<br/>Search City $".number_format($_SESSION["shopping_card_items"][$value["card_id"]]['cityPrice'], 2);
					}
					if ($_SESSION["shopping_card_items"][$value["card_id"]]["zipPrice"] > 0) {
						$value['card_addition_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['zipPrice'];
						$value['card_total_price'] += $_SESSION["shopping_card_items"][$value["card_id"]]['zipPrice'];
						$value['card_total_price_format'] .= "<br/>Search Zip $".number_format($_SESSION["shopping_card_items"][$value["card_id"]]['zipPrice'], 2);
					}
					$value['card_total_price_format'] .= ")</i><br /><font class=\"bold pink\">$".number_format($value['card_total_price'], 2)."</font>";
?>
								<tr class="formstyle">
									<td class="centered bold">
										<span><?=$value['card_bin']?>******</span>
									</td>
									<td class="centered bold">
										<span><?=$value['category_name']?></span>
									</td>
									<td class="centered">
										<span><?=$value["card_firstname"]?></span>
									</td>
									<td class="centered">
										<span><?=$value['card_country']?></span>
									</td>
									<!--td class="centered">
										<span><?//=$value['card_state']?></span>
									</td>
									<td class="centered">
										<span><?//=$value['card_city']?></span>
									</td>
									<td class="centered">
										<span><?//=$value['card_zip']?></span>
									</td-->
									<td class="centered">
										<span><?=$value['card_ssn']?></span>
									</td>
									<td class="centered">
										<span><?=$value['card_dob']?></span>
									</td>
									<td class="centered">
										<span><?=$value['card_total_price_format']?></span>
									</td>
								</tr>
<?php
				}
			}
?>
							</tbody>
						</table>
						<table class="content_table">
							<tbody>
								<tr>
									<td colspan="8" class="centered bold">
										Other Accounts
									</td>
								</tr>
								<tr>
									<td class="formstyle centered bold">
										<span>ACCOUNT INFORMATION</span>
									</td>
									<td class="formstyle centered bold">
										<span>ACCOUNT TYPE</span>
									</td>
									<td class="formstyle centered bold">
										<span>CATEGORY<span>
									</td>
									<td class="formstyle centered bold" style="width:70px;">
										<span>PRICE</span>
									</td>
								</tr>
<?php
			if (array_key_exists('shoppingOtheraccounts', $shoppingCart) && count($shoppingCart['shoppingOtheraccounts']) > 0) {
				foreach ($shoppingCart['shoppingOtheraccounts'] as $key=>$value) {
					$value['other_category_name'] = ($value['other_category_name'] == "")?"(No category)":$value['other_category_name'];
					$value['otheraccount_total_price_format'] = "<font class=\"bold pink\">$".number_format($value['otheraccount_price'], 2)."</font>";
?>
								<tr class="formstyle">
									<td class="centered bold">
										<span><?=$value['otheraccount_info']?></span>
									</td>
									<td class="centered">
										<span><?=$value["otheraccount_type"]?></span>
									</td>
									<td class="centered">
										<span><?=$value['other_category_name']?></span>
									</td>
									<td class="centered">
										<span><?=$value['otheraccount_total_price_format']?></span>
									</td>
								</tr>
<?php
				}
			}
?>
							</tbody>
						</table>
						<table class="content_table">
							<tbody>
								<tr>
									<td colspan="10" class="centered bold">
										Dumps
									</td>
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
									<td class="formstyle centered" style="width:70px;">
										<span class="bold">PRICE</span>
									</td>
								</tr>
<?php
			if (array_key_exists('shoppingDumps', $shoppingCart) && count($shoppingCart['shoppingDumps']) > 0) {
				foreach ($shoppingCart['shoppingDumps'] as $key=>$value) {
					$value['dump_category_name'] = ($value['dump_category_name'] == "")?"(No category)":$value['dump_category_name'];
					$value['dump_total_price_format'] = "<font class=\"bold pink\">$".number_format($value['dump_price'], 2)."</font>";
?>
								<tr class="formstyle">
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
								</tr>
<?php
				}
			}
?>
							</tbody>
						</table>
						<table class="content_table">
							<tbody>
								<tr>
									<td class="red bold right">
										Total:
									</td>
									<td class="centered" style="width:70px;">
										<span class="red bold">$<?=number_format($totalCartPrice, 2)?></span>
									</td>
								</tr>
							</tbody>
						</table>
					</form>
				</div>
			</div>
	</body>
</html>
<?php
		}
	}
}
else {
	header("Location: login.php");
}
exit(0);
?>