<?php
require("./header.php");
if (!$checkLogin) require("./minilogin.php");
else {
	?>
	
	<div id="balance">
	<script type="text/javascript">
		  function get_amount() {
		   var amount = document.getElementById('in_amount').value;
		   if (amount>=<?=$db_config["paygate_minimum"]?>)
			{
			 document.getElementById('wmz_amount').value=amount;
			 document.getElementById('pm_amount').value=amount;
			 document.getElementById('ukash_amount').value=amount;
			 document.getElementById('bitcoin_amount').value=amount;
			}
		}
	</script>
	
	<form action="../paygates/btcn.php" method="post" name="perfectgold" id="perfectgold2">
	<div class="section_title">PLEASE ENTER AMOUNT OF USD TO LOAD IN BOX BELOW</div>
	<div align="center">AMOUNT: $<input id="in_amount" name="need_dollar" type="text" class="textbox" value="<?=$db_config["paygate_minimum"]?>" onkeyup="javascript:get_amount();" onblur="javascript:get_amount();" onfocus="javascript:get_amount();" onchange="javascript:get_amount();" /></div>
	<div class="section_title">TO DEPOSIT MONEY | CLICK ON IMAGE OF YOUR PAYGATE</div>
	<hr>
	<div class="section_title"><input type="image"  src="btcn.png" style="color: #FF0000" alt="submit" width="200" height="56" /></div>
	</form>
	
	<hr><br />
	
	<?php
}
require("./footer.php");
?>