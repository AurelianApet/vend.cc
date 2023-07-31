<?php
session_start();
setcookie("cookname", "", time()-60*60*24*365, "/");
setcookie("cookpass", "", time()-60*60*24*365, "/");
setcookie("remember", "", time()-60*60*24*365, "/");
unset($_SESSION);
$_SESSION = array();
session_destroy();
session_start();
header("location: ../");
?>