<?php
/*
=====================================================
 GamePress - by Antonio Silva Sousa
-----------------------------------------------------
 Email - santonio450@gmail.com
-----------------------------------------------------
 Copyright (c) 2025 Antonio Silva Sousa
=====================================================
 This code is protected by copyright
=====================================================
 File: install.php
-----------------------------------------------------
 Use: Script installation
=====================================================
*/

error_reporting( E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE );
ini_set( 'error_reporting', E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE );
ini_set( 'display_errors', '1' );
ini_set( 'display_startup_errors', '1' );
ini_set( 'html_errors', '0' );

session_start();

header( "Content-type: text/html; charset=utf-8" );

define( 'GAMEPRESS', true );
define( 'ROOT_DIR', dirname( __FILE__ ) );
define( 'ENGINE_DIR', ROOT_DIR . '/engine' );

require_once( ENGINE_DIR . '/inc/include/functions.inc.php' );

$is_loged_in = false;
$selected_language = 'Portugues';
$PHP_MIN_VERSION = '8.0';

$_REQUEST['action'] = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';

$url = explode( basename( $_SERVER['PHP_SELF'] ), $_SERVER['PHP_SELF'] );
$url = reset( $url );
$_IP = get_ip();

if ( isSSL() ) $url = "https://" . $_SERVER['HTTP_HOST'] . $url;
else $url = "http://" . $_SERVER['HTTP_HOST'] . $url;

if ( isset( $_POST['selected_language'] ) ) {

	 $_POST['selected_language'] = totranslit( $_POST['selected_language'], false, false );

	 if ( $_POST['selected_language'] and @is_dir( ROOT_DIR . '/language/' . $_POST['selected_language'] ) ) {

		  $selected_language = $_POST['selected_language'];
		  set_cookie( "selected_language", $selected_language, 365 );
	 }

} elseif ( isset( $_COOKIE['selected_language'] ) ) {

	$_COOKIE['selected_language'] = totranslit( $_COOKIE['selected_language'], false, false );

	if ( $_COOKIE['selected_language'] != "" and @is_dir( ROOT_DIR . '/language/' . $_COOKIE['selected_language'] ) ) {
		$selected_language = $_COOKIE['selected_language'];
	}

}

include_once ( ROOT_DIR . '/language/' . $selected_language . '/adminpanel.lng' );
include_once ( ROOT_DIR . '/language/' . $selected_language . '/install.lng' );

if ( $lang['direction'] == 'rtl' ) $rtl_prefix = '_rtl'; else $rtl_prefix = '';

$skin_header = <<<HTML
<!doctype html>
<html lang="{$lang['language_code']}" dir="{$lang['direction']}">
<head>
	<meta charset="utf-8">
	<title>{$lang['install_1']}</title>
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, width=device-width">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">
	<link href="engine/skins/fonts/fontawesome/styles.min.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="engine/skins/stylesheets/application{$rtl_prefix}.css" media="screen" rel="stylesheet" type="text/css" />
	<script src="engine/skins/javascripts/application.js"></script>
</head>
<body class="no-theme">
<script>
	var gp_act_lang     = [];
	var cal_language    = '{$lang['language_code']}';
	var filedefaulttext = '';
	var filebtntext     = '';
</script>
<style>
.installbox {
	width: 95%;
	max-width: 950px;
	margin-left: auto;
	margin-right: auto;
}
@media (min-width: 769px) {
	.installpanel {
		display: table-cell;
		vertical-align: middle;
	}
	@media (min-height: 600px) {
		.installbox {
			margin-top: -100px;
		}
	}
}
</style>
<div class="navbar navbar-inverse bg-primary-700 mb-20">
	<div class="navbar-header">
		<a class="navbar-brand" href="install.php">{$lang['install_1']}</a>
	</div>
</div>
<div class="page-container">
	<div class="installpanel">
		<div class="installbox">
<!--MAIN area-->
HTML;


$skin_footer = <<<HTML;
	 <!--MAIN area-->
	</div>
	</div>
</div>
</body>
</html>
HTML;

function msgbox( $text, $back = false ) {
	global $lang, $skin_header, $skin_footer;

	if ( $back ) {
		$back = "onclick=\"history.go(-1); return false;\"";
		$lang['install_2'] = $lang['install_3'];
	} else {
		$back = "";
	}

	echo $skin_header;

	echo <<<HTML;
<form method="post">
<div class="panel panel-default">
	<div class="panel-heading">
	{$lang['install_4']}
	</div>
	<div class="panel-body">
		{$text}
	</div>
	<div class="panel-footer">
	<button type="submit" {$back} class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-arrow-circle-o-right position-left"></i>{$lang['install_2']}</button>
	</div>
</div>
</form>
HTML;

	echo $skin_footer;

	exit();
}

function generate_auth_key() {

	$arr = array(
		'a', 'b', 'c', 'd', 'e', 'f',
		'g', 'h', 'i', 'j', 'k', 'l',
		'm', 'n', 'o', 'p', 'r', 's',
		't', 'u', 'v', 'x', 'y', 'z',
		'A', 'B', 'C', 'D', 'E', 'F',
		'G', 'H', 'I', 'J', 'K', 'L',
		'M', 'N', 'O', 'P', 'R', 'S',
		'T', 'U', 'V', 'X', 'Y', 'Z',
		'1', '2', '3', '4', '5', '6',
		'7', '8', '9', '0', '.', ',',
		'(', ')', '[', ']', '!', '?',
		'&', '^', '%', '@', '*', ' ',
		'<', '>', '/', '|', '+', '-',
		'{', '}', '`', '~', '#', ';',
		'/', '|', '=', ':', '`'
	);

	$key = "";
	for ( $i = 0; $i < 64; $i++ ) {
		$index = random_int(0, count($arr) - 1);
		$key .= $arr[$index];
	}
	return $key;

}

function folders_check_chmod($dir, $bad_files = array()) {

	if (!is_writable($dir) OR !is_dir($dir)) {
		$folder = str_replace(ROOT_DIR, "", $dir);
		$bad_files[] = $folder . "/";
	}

	if ($dh = @opendir($dir)) {

		while (false !== ($file = readdir($dh))) {

			if ($file == '.' or $file == '..' or $file == '.svn' or $file == '.DS_store') {
				continue;
			}

			if (is_dir($dir . "/" . $file)) {

				$bad_files = folders_check_chmod($dir . "/" . $file, $bad_files);
			}
		}
	}

	return $bad_files;
}

if ($_REQUEST['action'] and !isset($_SESSION['gp_install'])) {
	msgbox("{$lang['install_5']} <br><br><a href=\"{$url}install.php\">{$url}install.php</a>");
}

if ( file_exists(ENGINE_DIR.'/data/config.php') ) {

	msgbox( $lang['install_6'] );

}

if ($_REQUEST['action'] == "eula") {

	echo $skin_header;

	echo <<<HTML
<form id="check-eula" method="get" action="">
<input type="hidden" name="action" value="function_check">
<script language="javascript">
function check_eula(){

	if( document.getElementById( 'eula' ).checked == true )
	{
		return true;
		
	} else {

		GPalert( '{$lang['install_16']}', '{$lang['all_info']}' );
		return false;
	}
};
document.getElementById( 'check-eula' ).onsubmit = check_eula;
</script>
<div class="panel panel-default">
	<div class="panel-heading">
	{$lang['install_11']}
	</div>
	<div class="panel-body">
		{$lang['install_12']}
		<br><br>
		<div style="height: 300px; border: 1px solid #76774c; background-color: #FDFDD3; padding: 5px; overflow: auto;">{$lang['install_13']}</div>
		<div class="checkbox"><label><input type="checkbox" name="eula" id="eula" class="icheck">{$lang['install_14']}</label></div>
	</div>
	<div class="panel-footer">
	<button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-arrow-circle-o-right position-left"></i>{$lang['install_15']}</button>
	</div>
</div>
</form>
HTML;

	echo $skin_footer;

} elseif ($_REQUEST['action'] == "function_check") {

	$message = <<<HTML;
<form method="get" action="">
<input type="hidden" name="action" value="function_check">
<div class="panel panel-default">
	<div class="panel-heading">
	{$lang['install_17']}
	</div>
	<div class="table-responsive">
<table class="table table-striped table-xs">
<thead>
<th width="330">{$lang['install_18']}</th>
<th colspan="2">{$lang['install_19']}</th>
</thead>
HTML;

	$errors = false;

	if (version_compare(phpversion(), $PHP_MIN_VERSION, '<')) {
		$status = '<span class="text-danger"><b>' . phpversion() . '</b></span>';
		$errors = true;
 	} else {
		$status = '<span class="text-success"><b>' . $lang['install_21'] . '</b></span>';
	}

	$lang['install_22'] = str_replace( '{version}', $PHP_MIN_VERSION, $lang['install_22']);

	$message .= "<tr>
 		 <td>{$lang['install_22']}</td>
    		 <td colspan="2">{$status}</td>
       		 </tr>";

	if (function_exists('mysqli_connect')) {
		$status = '<span class="text-success"><b>' . $lang['install_21'] . '</b></span>';
	} else {
		$status = '<span class="text-danger"><b>' . $lang['install_20'] . '</b></span>';
		$errors = true;
	}

	$message .= "<tr>
 		 <td>{$lang['install_23']}</td>
    		 <td colspan="2">{$status}</td>
       		 </tr>";

	if (class_exists('ZipArchive')) {}
