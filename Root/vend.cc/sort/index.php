<?php

require_once('index.inc.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Append Country to Card</title>
</head>
<body>
	<form action="" method="POST">
		<textarea name="cards" rows="10" cols="40"><?=htmlspecialchars($cards)?></textarea><br>
		<button type="submit">Submit</button>
		<button type="reset" style="margin-left: 2em">Clear Form</button>
	</form>

<?php if (isset($results) && $results): ?>
<?php foreach ($results as $country => $cresults): ?>
	<h3><?=$country?></h3>
	<textarea rows="10" cols="50"><?=htmlspecialchars(implode("\n", $cresults))?></textarea>
<?php endforeach; ?>
<?php else: ?>
	<h3>No results</h3>
<?php endif; ?>

</body>
</html>