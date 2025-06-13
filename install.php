<?php
/*
=====================================================
 GamePress
-----------------------------------------------------
 Antonio Silva Sousa
-----------------------------------------------------
 Copyright (c) 2025
=====================================================
 This code is protected by copyright
=====================================================
 File: install.php
-----------------------------------------------------
 Use: Script installation
=====================================================
*/

error_reporting(E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE);
ini_set("error_reporting", E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE);
ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
ini_set("html_errors", "0");

session_start();

header("Content-Type: text/html; charset=utf-8");

define("GAMEPRESS", true);
define("ROOT_DIR", dirname(__FILE__) );
define("ENGINE_DIR", ROOT_DIR . "/engine");

require_once ENGINE_DIR . "/inc/include/functions.inc.php";

$is_loged_in = false;
$selected_language = "Portugues";
$PHP_MIN_VERSION = "8.0";

$_REQUEST["action"] = isset($_REQUEST["action"]) ? $_REQUEST["action"] : "";

$url = explode(basename($_SERVER["PHP_SELF"]), $_SERVER["PHP_SELF"]);
$url = reset($url);
$_IP = get_ip();

if (isSSL()) {
    $url = "https://" . $_SERVER["HTTP_HOST"] . $url;
} else {
    $url = "http://" . $_SERVER["HTTP_HOST"] . $url;
}

if (isset($_POST["selected_language"])) {
    $_POST["selected_language"] = totranslit(
        $_POST["selected_language"],
        false,
        false
    );

    if (
        $_POST["selected_language"] and
        @is_dir(ROOT_DIR . "/language/" . $_POST["selected_language"])
    ) {
        $selected_language = $_POST["selected_language"];
        set_cookie("selected_language", $selected_language, 365);
    }
} elseif (isset($_COOKIE["selected_language"])) {
    $_COOKIE["selected_language"] = totranslit(
        $_COOKIE["selected_language"],
        false,
        false
    );

    if (
        $_COOKIE["selected_language"] != "" and
    )
}
