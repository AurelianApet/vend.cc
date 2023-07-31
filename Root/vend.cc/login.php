<?php
require("./header.php");
if ($checkLogin) {
?>
			<script type="text/javascript">
			$(document).ready(function() {
				<?=($count_message > 0)?"alert('You have $count_message new message(s)\\nPlease go to User CP -> Message to read');":""?>
				setTimeout("window.location = './'", 1000);
			});
			</script>
			<div id="login_success">
				Thank you for logging in, <b><?=$_SESSION['user_name']?></b>.<br/>
				<a href="./">Click here if your browser does not automatically redirect you.</a>
			</div>
<?php
}
else {
	require("./minilogin.php");
}
require("./footer.php");
?>