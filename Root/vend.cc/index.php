<?php

require("./header.php");

if ($checkLogin) {

$_SESSION["user_id"] = $user_info["user_id"];
//echo 'INN Init ID='.$db->escape($_SESSION["user_id"]);

	$sql = "SELECT count(*) FROM `".TABLE_ADS."`";
	$totalRecordsAds = $db->query_first($sql);
	$totalRecordsAds = $totalRecordsAds["count(*)"];
	$perPageAds = 10;
	$totalPageAds = ceil($totalRecordsAds/$perPageAds);
	
	$pageAds = $db->escape($_GET["pageAds"]);
	if (!is_numeric($pageAds)||$pageAds < 1 || $pageAds > $totalPageAds) $pageAds = 1;
	
	$sql = "SELECT * FROM `".TABLE_ADS."` ORDER BY ad_time DESC,ad_id DESC LIMIT ".(($pageAds-1)*$perPageAds).",".$perPageAds;
	$recordsAds = $db->fetch_array($sql);

	$sql = "SELECT count(*) FROM `".TABLE_NEWS."`";
	$totalRecords = $db->query_first($sql);
	$totalRecords = $totalRecords["count(*)"];
	$perPage = 10;
	$totalPage = ceil($totalRecords/$perPage);
	
	$page = $db->escape($_GET["page"]);
	if(!is_numeric($page)||$page < 1 || $page > $totalPage) $page = 1;
	
	$sql = "SELECT ".TABLE_NEWS.".*, ".TABLE_USERS.".user_id, ".TABLE_USERS.".user_name FROM `".TABLE_NEWS."` LEFT JOIN `".TABLE_USERS."` ON ".TABLE_NEWS.".news_author = ".TABLE_USERS.".user_id ORDER BY ".TABLE_NEWS.".news_time  DESC,".TABLE_NEWS.".news_id DESC LIMIT ".(($page-1)*$perPage).",".$perPage;
	$records = $db->fetch_array($sql);
?>
				<div id="ads">
					<div class="section_title">ADVERTISEMENTS</div>
					<div class="section_page_bar">
<?php
	if ($totalRecordsAds > 0) {
		echo "Page:";
		if ($pageAds>1) {
			echo "<a href=\"?page=".$page."&pageAds=".($pageAds-1)."\">&lt;</a>";
			echo "<a href=\"?page=".$page."&pageAds=1\">1</a>";
		}
		if ($pageAds>3) {
			echo "...";
		}
		if (($pageAds-1) > 1) {
			echo "<a href=\"?page=".$page."&pageAds=".($pageAds-1)."\">".($pageAds-1)."</a>";
		}
		echo "<input type=\"TEXT\" class=\"page_go\" value=\"".$pageAds."\" onchange=\"window.location.href='?page=".$page."&pageAds='+this.value\"/>";
		if (($pageAds+1) < $totalPageAds) {
			echo "<a href=\"?page=".$page."&pageAds=".($pageAds+1)."\">".($pageAds+1)."</a>";
		}
		if ($pageAds < $totalPageAds-2) {
			echo "...";
		}
		if ($pageAds<$totalPageAds) {
			echo "<a href=\"?page=".$page."&pageAds=".$totalPageAds."\">".$totalPageAds."</a>";
			echo "<a href=\"?page=".$page."&pageAds=".($pageAds+1)."\">&gt;</a>";
		}
	}
?>
					</div>
					<div class="formstyle section_content">
<?php
	if (count($recordsAds) > 0)
	{
		foreach ($recordsAds as $key=>$value) {
?>
						<table class="content_table">
							<tbody>
								<tr>
									<td class="ad_title lime">
										<span><?=$value['ad_title']?></span>
									</td>
								</tr>
								<tr>
									<td class="ad_content">
										<span><?=$value['ad_content']?></span>
									</td>
								</tr>
								<tr>
									<td class="ad_info">
										Published in <?=date("H:i:s d/M/Y", $value['ad_time'])?></span>
									</td>
								</tr>
							</tbody>
						</table>
<?php
		}
	}
	else {
?>
						<table class="content_table">
							<tbody>
								<tr>
									<td class="ad_title">
										<span>No record found.</span>
									</td>
								</tr>
							</tbody>
						</table>
<?php
	}
?>
					</div>
				</div>
				<div id="news">
					<div class="section_title">NEWS</div>
					<div class="section_page_bar">
<?php
	if ($totalRecords > 0) {
		echo "Page:";
		if ($page>1) {
			echo "<a href=\"?page=".($page-1)."&pageAds=".$pageAds."\">&lt;</a>";
			echo "<a href=\"?page=1&pageAds=".$pageAds."\">1</a>";
		}
		if ($page>3) {
			echo "...";
		}
		if (($page-1) > 1) {
			echo "<a href=\"?page=".($page-1)."&pageAds=".$pageAds."\">".($page-1)."</a>";
		}
		echo "<input type=\"TEXT\" class=\"page_go\" value=\"".$page."\" onchange=\"window.location.href='?page='+this.value+'&pageAds=".$pageAds."'\"/>";
		if (($page+1) < $totalPage) {
			echo "<a href=\"?page=".($page+1)."&pageAds=".$pageAds."\">".($page+1)."</a>";
		}
		if ($page < $totalPage-2) {
			echo "...";
		}
		if ($page<$totalPage) {
			echo "<a href=\"?page=".$totalPage."&pageAds=".$pageAds."\">".$totalPage."</a>";
			echo "<a href=\"?page=".($page+1)."&pageAds=".$pageAds."\">&gt;</a>";
		}
	}
?>
					</div>
					<div class="formstyle section_content">
<?php
	if (count($records) > 0)
	{
		foreach ($records as $key=>$value) {
?>
						<table class="content_table">
							<tbody>
								<tr>
									<td class="news_title lime">
										<span><?=$value['news_title']?></span>
									</td>
								</tr>
								<tr>
									<td class="news_content">
										<span><?=$value['news_content']?></span>
									</td>
								</tr>
								<tr>
									<td class="news_info">
										Posted <?=date("H:i:s d/M/Y", $value['news_time'])?> by <span class="bold"><?=$value['user_name']?></span>
									</td>
								</tr>
							</tbody>
						</table>
<?php
		}
	}
	else {
?>
						<table class="content_table">
							<tbody>
								<tr>
									<td class="news_title">
										<span class="error">No record found.</span>
									</td>
								</tr>
							</tbody>

						</table>
<?php
	}
?>
					</div>
				</div>

<?php
}
else {
	require("./minilogin.php");
}
require("./footer.php");
?>