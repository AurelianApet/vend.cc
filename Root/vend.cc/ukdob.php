<?php
set_time_limit(0);
require("./header.php");
require_once './api_ukdob/DOB_API.php';
require_once './api_ukdob/DOB_API_Exceptions.php';
function ukdob_log($errorMsg)
{
	$filename = "ukdob_log.txt";
	if ($handle = fopen($filename, 'a+')) {
		if (fwrite($handle, $errorMsg) === FALSE) {
			echo "Cannot write to file ($filename)";
		}
	}
	else {
		echo "Cannot open file ($filename)";
	}
	fclose($handle);
}
if ($checkLogin && $user_info["user_groupid"] < intval(PER_UNACTIVATE)) {
	if ($_POST["btnGetInfo"] != "") {
		$name = $_POST["name"];
		$city = $_POST["city"];
		$zip = $_POST["zip"];
		if (doubleval($user_info["user_balance"]) >= $db_config["ukdob_fee"]) {
			if ($name == "") {
				$name_error = "Name are requires.";
			}
			if ($name_error == "") {
				try {
					$ob = DOB_API::get_instance($db_config['ukdob_url'], $db_config['ukdob_key']);
					$get_available_funds = doubleval($ob->get_available_funds());
					$get_dob_price = doubleval($ob->get_dob_price());
					if ($get_dob_price > $get_available_funds) {
						$ukdobSearchError = "<span class=\"red bold centered\">SSN/DOB credit is over, please contact administrator.</span>";
					} else if ($get_dob_price > doubleval($db_config["ukdob_fee"])) {
						$ukdobSearchError = "<span class=\"red bold centered\">Bad Price, please contact administrator to fix this.</span>";
					} else {
						$params = array(
							'name' => $name,
							'city' => $city,
							'zip' => $zip
						);
						$search_result = $ob->search($params);
						if (count($search_result["data"]) > 0) {
							$search_result = $search_result["data"];
						} else {
							$ukdobSearchError = "<span class=\"red bold centered\">Not found any record that meets your search criteria.</span>";
						}
					}
				} catch(API_Client_Exception $e) {
					/*
						This exception means that you passed some incorrect settings or params to DOB_API class
						it's client side exception so - your responsibility
					*/
					$ukdobSearchError = "<span class=\"red bold centered\">(DOB API Client config error, please contact administrator.)</span>";
					ukdob_log('Client Exception: ' . $e->getMessage());
				} catch(API_Server_Exception $e) {
					/*
						This exception means that we have some problems with our DOB API on server.
						Please contact DOB API support for details.
					*/
					$ukdobSearchError = "<span class=\"red bold centered\">(DOB API Server config error, please contact administrator.)</span>";
					ukdob_log('Server Exception: ' . $e->getMessage());
				}
			}
			else {
				$ukdobSearchError = "<span class=\"red bold centered\">Please fill all required information.</span>";
			}
		}
		else {
			$ukdobSearchError = "<span class=\"red bold centered\">Need $".number_format($db_config["ukdob_fee"], 2, '.', '')." to search</span>";
		}
	}
?>
				<div id="myaccount">
					<div class="section_title">UK DATE OF BIRTH SEARCHER - ONLY LOSE CREDIT IF YOU CLICK BUY RESULT</div>
					<div class="section_title"><?=$ukdobSearchError?></div>
					<div class="section_content">
						<table class="content_table bordered">
							<tbody>
								<form action="" method="POST">
									<tr>
										<td class="paygate_title">
											Name <span class="red">(*)</span>
										</td>
										<td class="ssndob_content">
											<input name="name" type="text" value="<?=$_POST["name"]?>">
										</td>
										<td class="ssndob_content red bold">
											<?=$first_name_error?>
										</td>
									</tr>
									<tr>
										<td class="paygate_title">
											City
										</td>
										<td class="ssndob_content">
											<input name="city" type="text" value="<?=$_POST["city"]?>">
										</td>
										<td class="red bold">
										</td>
									</tr>
									<tr>
										<td class="paygate_title">
											Zipcode
										</td>
										<td class="ssndob_content">
											<input name="zip" type="text" value="<?=$_POST["zip"]?>">
										</td>
										<td class="red bold">
										</td>
									</tr>
									<tr>
										<td colspan="3" class="centered">
											<input type="submit" name="btnGetInfo" value="Search UK DOB" />
											<input type="button" name="btnCancel" value="Cancel" onclick="window.location='./'" />
										</td>
									</tr>
								</form>
							</tbody>
						</table>
					</div>
				</div>
<?php
	if (count($search_result) > 0) {
?>
				<div id="myaccount">
					<div class="section_title">SEARCH RESULT
					<div class="section_content">
						<table class="ssndob_result bordered" style="width: 800px; margin: auto;">
							<tbody>
								<tr>
									<td class="formstyle centered bold">Full Name</td>
									<td class="formstyle centered bold">City</td>
									<td class="formstyle centered bold">Zipcode</td>
									<td class="formstyle centered bold">Action & Result</td>
								</tr>
<?php
		foreach ($search_result as $line) {
?>
								<tr class="formstyle">
									<td><?php echo $line["name"];?></td>
									<td><?php echo $line["city"];?></td>
									<td><?php echo $line["zip"];?></td>
									<td><span id="item<?php echo $line["id"];?>"><a href="#" onclick="javascript:ukdob('<?php echo $line["id"];?>');">Buy This!</a></span></td>
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
}
else if ($checkLogin && $_SESSION["user_groupid"] == intval(PER_UNACTIVATE)){
	require("./miniactivate.php");
}
else {
	require("./minilogin.php");
}
require("./footer.php");
?>