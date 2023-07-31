
<?php
require("./includes/config.inc.php");

$lines = explode("\n", file_get_contents('country.txt'));

//var_dump($lines);

foreach ( $lines as $line) {
		$temp = explode("|", $line);
		$country['full_name'] = trim($temp[0]);
		$country['mini_name'] = trim($temp[1]);
		
		$db->insert('country', $country);
}
exit;

?>

