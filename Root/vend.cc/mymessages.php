<?php
require("./header.php");
if ($checkLogin && $user_info["user_groupid"] < intval(PER_UNACTIVATE)) {
	if ($_GET["act"] == "view" && isset($_GET["message_id"])) {
	
//echo 'INN View';
		$sql = "SELECT ".TABLE_MESSAGES.".*, ".TABLE_USERS.".user_name AS message_fromuser, ".TABLE_USERS.".user_groupid AS message_fromgroup FROM `".TABLE_MESSAGES."` LEFT JOIN `".TABLE_USERS."` ON ".TABLE_MESSAGES.".message_fromid = ".TABLE_USERS.".user_id WHERE message_toid = '".$db->escape($user_info["user_id"])."' AND message_id = '".$_GET["message_id"]."' AND message_todelete = '0' ORDER BY message_time DESC, message_id DESC";
		
		//echo($sql);
		$value = $db->query_first($sql, $_GET["message_id"]);
?>
				<div id="check_history">
					<div class="section_title">PRIVATE MESSAGES</div>
					<div class="section_title"><a href="./mymessages.php">Inbox</a> | <a href="?act=sent">Sent</a></div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		if ($value) {
			if ($value["message_status"] == 1) {
				$message_update["message_status"] = 0;
				$db->update(TABLE_MESSAGES, $message_update, "message_id=?", $_GET["message_id"]);
			}
?>
								<tr>
									<td class="formstyle centered bold">
										Subject:
									</td>
									<td class="borderstyle left">
										<span class="bold"><?=$value['message_subject']?></span>
									</td>
								</tr>
								<tr>
									<td class="formstyle centered bold">
										From:
									</td>
									<td class="borderstyle left">
										<span class="bold" style="color:<?=$user_groups[$value["message_fromgroup"]]["group_color"]?>;"><?=$value['message_fromuser']?></span>
<?php
				if (intval($user_info["user_groupid"]) == intval(PER_ADMIN)) {
?>
										<span><a href="./admincp/users.php?txtUserid=<?=$value['message_fromid']?>&txtUsername=&txtUsermail=&txtUseryahoo=&lstUserbalance=&lstUsertype=&btnSearch=Search">(Manager)</a></span>
<?php
				}
?>
										(<?=date("H:i:s d/M/Y", $value['message_time'])?>)
									</td>
								</tr>
								<tr>
									<td class="formstyle centered bold">
										Message:
									</td>
									<td class="borderstyle left message_message">
										<textarea name="message_message" class="message_message"><?=htmlentities($value['message_message'])?></textarea>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="borderstyle centered bold">
										<a href="?act=compose&message_id=<?=$value['message_id']?>">Reply</a> | <a href="?act=delete&message_id=<?=$value['message_id']?>" onClick="return confirm('Are you sure you want to DELETE this Message?')">Delete</a>
									</td>
								</tr>
<?php
		} else {
?>
								<tr>
									<td class="bold centered">
										<span class="error">Message ID Invalid.</span>
									</td>
								</tr>
<?php
		}
?>
							</tbody>
						</table>
					</div>
				</div>
<?php
	} else if ($_GET["act"] == "viewsent" && isset($_GET["message_id"])) {
	
//echo 'INN View Sent';
		$sql = "SELECT ".TABLE_MESSAGES.".*, ".TABLE_USERS.".user_name AS message_touser, ".TABLE_USERS.".user_groupid AS message_togroup FROM `".TABLE_MESSAGES."` LEFT JOIN `".TABLE_USERS."` ON ".TABLE_MESSAGES.".message_toid = ".TABLE_USERS.".user_id WHERE message_fromid = '".$db->escape($_SESSION["user_id"])."' AND message_id = '".$db->escape($_GET["message_id"])."' AND message_fromdelete='0' ORDER BY message_time DESC, message_id DESC";
		$value = $db->query_first($sql);
?>
				<div id="check_history">
					<div class="section_title">PRIVATE MESSAGES</div>
					<div class="section_title"><a href="./mymessages.php">Inbox</a> | <a href="?act=sent">Sent</a></div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
<?php
		if ($value) {
?>
								<tr>
									<td class="formstyle centered bold">
										Subject:
									</td>
									<td class="borderstyle left">
										<span class="bold"><?=$value['message_subject']?></span>
									</td>
								</tr>
								<tr>
									<td class="formstyle centered bold">
										To:
									</td>
									<td class="borderstyle left">
										<span class="bold" style="color:<?=$user_groups[$value["message_togroup"]]["group_color"]?>;"><?=$value['message_touser']?></span>
<?php
				if (intval($_SESSION["user_groupid"]) == intval(PER_ADMIN)) {
?>
										<span><a href="./admincp/users.php?txtUserid=<?=$value['message_fromid']?>&txtUsername=&txtUsermail=&txtUseryahoo=&lstUserbalance=&lstUsertype=&btnSearch=Search">(Manager)</a></span>
<?php
				}
?>
										(<?=date("H:i:s d/M/Y", $value['message_time'])?>)
									</td>
								</tr>
								<tr>
									<td class="formstyle centered bold">
										Message:
									</td>
									<td class="borderstyle left message_message">
										<textarea name="message_message" class="message_message"><?=htmlentities($value['message_message'])?></textarea>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="borderstyle centered bold">
										<a href="?act=compose&message_id=<?=$value['message_id']?>">Reply</a> | <a href="?act=deletesent&message_id=<?=$value['message_id']?>" onClick="return confirm('Are you sure you want to DELETE this Message?')">Delete</a>
									</td>
								</tr>
<?php
		} else {
?>
								<tr>
									<td class="bold centered">
										<span class="error">Message ID Invalid.</span>
									</td>
								</tr>
<?php
		}
?>
							</tbody>
						</table>
					</div>
				</div>
<?php
	} else if ($_GET["act"] == "compose" && $_GET["message_id"] != "") {
?>
				<div id="check_history">
					<div class="section_title">PRIVATE MESSAGES</div>
					<div class="section_title"><a href="./mymessages.php">Inbox</a> | <a href="?act=sent">Sent</a></div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<form action="?act=compose&message_id=<?=$_GET["message_id"]?>" method="POST">
<?php
		if ($_GET["message_id"] != "") {
			$sql = "SELECT ".TABLE_MESSAGES.".*, ".TABLE_USERS.".user_name AS message_fromuser, ".TABLE_USERS.".user_groupid AS message_fromgroup FROM `".TABLE_MESSAGES."` LEFT JOIN `".TABLE_USERS."` ON ".TABLE_MESSAGES.".message_fromid = ".TABLE_USERS.".user_id WHERE message_toid = '".$db->escape($_SESSION["user_id"])."' AND message_id = '".$db->escape($_GET["message_id"])."' AND message_todelete = '0' ORDER BY message_time DESC, message_id DESC";
			$value = $db->query_first($sql);
			if ($value) {
				$message_toid = $value["message_fromid"];
				$message_touser = $value["message_fromuser"];
				$message_subject = "Re: " . $value["message_subject"];
			} else {
				$errorMsg = "Message ID Invalid.";
			}
		}
		if (isset($_POST["submit"])) {
			$message_subject = htmlentities($_POST["message_subject"]);
			$message_message = $_POST["message_message"];
			if ($message_touser == $_SESSION["user_name"]) {
				$errorMsg = "You can't send message to your self";
			} else if ($message_subject == "") {
				$errorMsg = "Please enter message subject";
			} else if ($message_message == "") {
				$errorMsg = "Please enter message body";
			} else {
				$message_import["message_fromid"] = $_SESSION["user_id"];
				$message_import["message_toid"] = $message_toid;
				$message_import["message_subject"] = htmlentities($_POST["message_subject"]);
				$message_import["message_message"] = $_POST["message_message"];
				$message_import["message_time"] = time();
				if($db->insert(TABLE_MESSAGES, $message_import)) {
					$errorMsg = "";
				}
				else {
					$errorMsg = "Send new message error.";
				}
			}
			if ($errorMsg == "") {
?>
									<script type="text/javascript">setTimeout("window.location = '?act=sent'", 1000);</script>
									<tr>
										<td colspan="2" class="centered">
											<span class="success">Send private message successful.</span>
										</td>
									</tr>
<?php
			}
		}
		if ($errorMsg != "") {
?>
									<tr>
										<td colspan="2" class="centered">
											<span class="error"><?=$errorMsg?></span>
										</td>
									</tr>
<?php
		}
		if ($_GET["message_id"] != "") {
?>
									<tr>
										<td class="formstyle centered bold">
											Subject:
										</td>
										<td class="borderstyle left">
											<input type="TEXT" name="message_subject" value="<?=$message_subject?>" size="80"/>
										</td>
									</tr>
									<tr>
										<td class="formstyle centered bold">
											Message:
										</td>
										<td class="borderstyle left message_message">
<?php
			if ($message_toid != "") {
?>
											<textarea name="message_message" class="message_message">
<?php
				echo "\n\n\t========================\n\tFrom ".$value['message_fromuser']." at ".date("H:i:s d/M/Y", $value['message_time'])."\n\t-------------------------------------------\n";
				$old_message_lines = htmlentities($value['message_message']);
				$old_message_lines = explode("\n", $old_message_lines);
				if (is_array($old_message_lines) && count($old_message_lines) > 0) {
					foreach ($old_message_lines as $old_message_line) {
						echo "\t\t".$old_message_line."\n";
					}
				}
?>
</textarea>
<?php
			} else {
?>
											<textarea name="message_message" class="message_message"><?=$message_message?></textarea>
<?php
			}
?>
										</td>
									</tr>
									<tr>
										<td colspan="2" class="borderstyle centered bold">
											<input type="submit" name="submit" value="Send P.M" /> | <input type="reset" name="reset" value="Reset" />
										</td>
									</tr>
<?php
		}
?>
								</form>
							</tbody>
						</table>
					</div>
				</div>
<?php
	} else if ($_GET["act"] == "delete" && $_GET["message_id"] != "") {
		$message_id = $db->escape($_GET["message_id"]);
		$message_update["message_todelete"] = 1;
		$db->update(TABLE_MESSAGES, $message_update, "message_id='".$message_id."' AND message_toid='".$db->escape($_SESSION["user_id"])."' AND message_todelete = '0'");
		if ($db->affected_rows > 0) {
?>
				<script type="text/javascript">setTimeout("window.location = './mymessages.php'", 1000);</script>
				<div id="check_history">
					<div class="section_title">MESSAGE DELETE</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<tr>
									<td class="success">
										Delete Message ID <?=$message_id?> successful.
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
<?php
		}
		else {
?>
				<div id="check_history">
					<div class="section_title">MESSAGE DELETE</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<tr>
									<td class="error">
										Message ID Invalid.
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
<?php
		}
	} else if ($_GET["act"] == "deletesent" && $_GET["message_id"] != "") {
		$message_id = $db->escape($_GET["message_id"]);
		$message_update["message_fromdelete"] = 1;
		$db->update(TABLE_MESSAGES, $message_update, "message_id='".$message_id."' AND message_fromid='".$db->escape($_SESSION["user_id"])."' AND message_fromdelete='0'");
		if ($db->affected_rows > 0) {
?>
				<script type="text/javascript">setTimeout("window.location = '?act=sent'", 1000);</script>
				<div id="check_history">
					<div class="section_title">MESSAGE DELETE</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<tr>
									<td class="success">
										Delete Message ID <?=$message_id?> successful.
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
<?php
		}
		else {
?>
				<div id="check_history">
					<div class="section_title">MESSAGE DELETE</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<tr>
									<td class="error">
										Message ID Invalid.
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
<?php
		}
	} else if ($_GET["act"] == "sent") {
		$sql = "SELECT count(*) FROM `".TABLE_MESSAGES."` WHERE message_fromid = '".$db->escape($_SESSION["user_id"])."' AND message_fromdelete='0' ORDER BY message_time DESC, message_id DESC";
		$totalRecords = $db->query_first($sql);
		$totalRecords = $totalRecords["count(*)"];
		$perPage = 10;
		$totalPage = ceil($totalRecords/$perPage);
		if (isset($_GET["page"])) {
			$page = $db->escape($_GET["page"]);
			if ($page < 1) {
				$page = 1;
			}
			else if ($page > $totalPage) {
				$page = 1;
			}
		} else {
			$page = 1;
		}
		$sql = "SELECT ".TABLE_MESSAGES.".*, ".TABLE_USERS.".user_name AS message_touser, ".TABLE_USERS.".user_groupid AS message_tgroup FROM `".TABLE_MESSAGES."` LEFT JOIN `".TABLE_USERS."` ON ".TABLE_MESSAGES.".message_toid = ".TABLE_USERS.".user_id WHERE message_fromid = '".$db->escape($_SESSION["user_id"])."' AND message_fromdelete='0' ORDER BY message_time DESC, message_id DESC LIMIT ".(($page-1)*$perPage).",".$perPage;
		$allMessages = $db->fetch_array($sql);
?>
				<div id="check_history">
					<div class="section_title">PRIVATE MESSAGES</div>
					<div class="section_title"><a href="./mymessages.php">Inbox</a> | Sent</div>
					<div class="section_page_bar">
<?php
		if ($totalRecords > 0) {
			echo "Page:";
			if ($page>1) {
				echo "<a href=\"?act=sent&page=".($page-1)."\">&lt;</a>";
				echo "<a href=\"?act=sent&page=1\">1</a>";
			}
			if ($page>3) {
				echo "...";
			}
			if (($page-1) > 1) {
				echo "<a href=\"?act=sent&page=".($page-1)."\">".($page-1)."</a>";
			}
			echo "<input type=\"TEXT\" class=\"page_go\" value=\"".$page."\" onchange=\"window.location.href='?act=sent&page='+this.value\"/>";
			if (($page+1) < $totalPage) {
				echo "<a href=\"?act=sent&page=".($page+1)."\">".($page+1)."</a>";
			}
			if ($page < $totalPage-2) {
				echo "...";
			}
			if ($page<$totalPage) {
				echo "<a href=\"?act=sent&page=".$totalPage."\">".$totalPage."</a>";
				echo "<a href=\"?act=sent&page=".($page+1)."\">&gt;</a>";
			}
		}
?>
					</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<tr>
									<td class="formstyle centered">
										<strong>SUBJECT</strong>
									</td>
									<td class="formstyle centered">
										<strong>TO</strong>
									</td>
									<td class="formstyle centered">
										<strong>DATE</strong>
									</td>
									<td class="formstyle centered">
										<strong>ACTION</strong>
									</td>
								</tr>
<?php
		if (count($allMessages) > 0) {
			foreach ($allMessages as $key=>$value) {
?>

								<tr class="formstyle">
									<td class="centered">
										<span><a href="?act=viewsent&message_id=<?=$value['message_id']?>"><?=$value['message_subject']?></a></span>
									</td>
									<td class="centered">
										<span style="color:<?=$user_groups[$value["message_tgroup"]]["group_color"]?>;"><?=$value['message_touser']?></span><?php
				if (intval($_SESSION["user_groupid"]) == intval(PER_ADMIN)) {
?>
										<span><a href="./admincp/users.php?txtUserid=<?=$value['message_fromid']?>&txtUsername=&txtUsermail=&txtUseryahoo=&lstUserbalance=&lstUsertype=&btnSearch=Search">(Manager)</a></span>
<?php
				}
?>
									</td>
									<td class="centered">
										<span><?=date("H:i:s d/M/Y", $value['message_time'])?></span>
									</td>
									<td class="centered">
										<span><a href="?act=viewsent&message_id=<?=$value['message_id']?>">View</a> | <a href="?act=deletesent&message_id=<?=$value['message_id']?>" onClick="return confirm('Are you sure you want to DELETE this Message?')">Delete</a></span>
									</td>
								</tr>
<?php
			}
		}
		else {
?>
								<tr>
									<td colspan="4" class="error">
										No record found.
									</td>
								</tr>
<?php
		}
	} else {
	
	//echo 'INN Init ID='.$db->escape($_SESSION["user_id"]);
		$sql = "SELECT count(*) FROM `".TABLE_MESSAGES."` WHERE message_toid = '".$db->escape($_SESSION["user_id"])."' AND message_todelete = '0' ORDER BY message_time DESC, message_id DESC";
		
		//echo $sql;
		$totalRecords = $db->query_first($sql);
		$totalRecords = $totalRecords["count(*)"];
		$perPage = 10;
		$totalPage = ceil($totalRecords/$perPage);
		if (isset($_GET["page"])) {
			$page = $db->escape($_GET["page"]);
			if ($page < 1) {
				$page = 1;
			}
			else if ($page > $totalPage) {
				$page = 1;
			}
		} else {
			$page = 1;
		}
		$sql = "SELECT ".TABLE_MESSAGES.".*, ".TABLE_USERS.".user_name AS message_fromuser, ".TABLE_USERS.".user_groupid AS message_fromgroup FROM `".TABLE_MESSAGES."` LEFT JOIN `".TABLE_USERS."` ON ".TABLE_MESSAGES.".message_fromid = ".TABLE_USERS.".user_id WHERE message_toid = '".$db->escape($_SESSION["user_id"])."' AND message_todelete = '0' ORDER BY message_time DESC, message_id DESC LIMIT ".(($page-1)*$perPage).",".$perPage;
		$allMessages = $db->fetch_array($sql);
?>
				<div id="check_history">
					<div class="section_title">PRIVATE MESSAGES</div>
					<div class="section_title">Inbox | <a href="?act=sent">Sent</a></div>
					<div class="section_page_bar">
<?php
		if ($totalRecords > 0) {
			echo "Page:";
			if ($page>1) {
				echo "<a href=\"?page=".($page-1)."\">&lt;</a>";
				echo "<a href=\"?page=1\">1</a>";
			}
			if ($page>3) {
				echo "...";
			}
			if (($page-1) > 1) {
				echo "<a href=\"?page=".($page-1)."\">".($page-1)."</a>";
			}
			echo "<input type=\"TEXT\" class=\"page_go\" value=\"".$page."\" onchange=\"window.location.href='?page='+this.value\"/>";
			if (($page+1) < $totalPage) {
				echo "<a href=\"?page=".($page+1)."\">".($page+1)."</a>";
			}
			if ($page < $totalPage-2) {
				echo "...";
			}
			if ($page<$totalPage) {
				echo "<a href=\"?page=".$totalPage."\">".$totalPage."</a>";
				echo "<a href=\"?page=".($page+1)."\">&gt;</a>";
			}
		}
?>
					</div>
					<div class="section_content">
						<table class="content_table">
							<tbody>
								<tr>
									<td class="formstyle centered">
										<strong>SUBJECT</strong>
									</td>
									<td class="formstyle centered">
										<strong>FROM</strong>
									</td>
									<td class="formstyle centered">
										<strong>DATE</strong>
									</td>
									<td class="formstyle centered">
										<strong>ACTION</strong>
									</td>
								</tr>
<?php
		if (count($allMessages) > 0) {
			foreach ($allMessages as $key=>$value) {
?>

								<tr class="formstyle<?php if ($value["message_status"] == 1) echo " bold";?>">
									<td class="centered">
										<span><a href="?act=view&message_id=<?=$value['message_id']?>"><?=$value['message_subject']?></a></span>
									</td>
									<td class="centered">
										<span style="color:<?=$user_groups[$value["message_fromgroup"]]["group_color"]?>;"><?=$value['message_fromuser']?></span>
<?php
				if (intval($_SESSION["user_groupid"]) == intval(PER_ADMIN)) {
?>
										<span><a href="./admincp/users.php?txtUserid=<?=$value['message_fromid']?>&txtUsername=&txtUsermail=&txtUseryahoo=&lstUserbalance=&lstUsertype=&btnSearch=Search">(Manager)</a></span>
<?php
				}
?>
									</td>
									<td class="centered">
										<span><?=date("H:i:s d/M/Y", $value['message_time'])?></span>
									</td>
									<td class="centered">
										<span><a href="?act=view&message_id=<?=$value['message_id']?>">View</a> | <a href="?act=delete&message_id=<?=$value['message_id']?>" onClick="return confirm('Are you sure you want to DELETE this Message?')">Delete</a></span>
									</td>
								</tr>
<?php
			}
		}
		else {
?>
								<tr>
									<td colspan="4" class="error">
										No record found.
									</td>
								</tr>
<?php
		}
	}
?>
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