<?php
session_start();

$file1 = './includes/config.inc.php';
$file2 = './includes/user.php';

if(file_exists($file1)&&file_exists($file2)){
	require $file1;
	require $file2;
	$user = new user($db);
	$user->valid_login();
	$user->logout();
} else {
	
	//Destroy sessions anyway
	foreach($_SESSION as $key => $val) $_SESSION[$key] = '';
	unset($_COOKIE['cookname']);	
	unset($_COOKIE['password']);
	unset($_COOKIE['sec_code']);
	unset($_COOKIE['user_groupid']);		
	session_unset();
	session_destroy();
}
header("Location: ./");
?>