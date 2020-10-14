<?php

ob_start();

require_once("../setting.php");

require_once("../library/portable-utf8.php");

require_once("../common.php");

$manipulation = new manipulation();

$verifier = new verifier();

$generator = new generator_one();

$crawler = new crawler();

$files = new files();

$q = mysqli($_GET['q']);

$action = mysqli($_GET['action']);

if($user->login_status == 1 && $action == "setting") {

	require_once("auth/setting.php");

}
else if($action  == "validate") {

	require_once("auth/validate.php");

}
else if($action == "privacy") {

    require_once('pages/privacy.php');

}
else if($action == "tos") {

    require_once('pages/tos.php');

}
else if($action == "ddos") {

    require_once('pages/ddos.php');

}
else if($action == "redirect") {

    require_once('pages/redirect.php');

}
else {

	require_once("auth/auth.php");

}

?>
