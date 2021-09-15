<?php

session_start();

$_SESSION = array();
$_POST = array();
$_GET = array();

session_destroy();

header('Location: index.php');
exit;
