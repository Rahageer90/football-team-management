<?php
session_start(); 

$_SESSION = array();

$_SESSION = [];

session_destroy();

header("Location: login.php");
exit;
