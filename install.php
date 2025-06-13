<?php

error_reporting( E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE );
ini_set( 'error_reporting', E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE );
ini_set( 'display_errors', '1' );
ini_set( 'display_startup_errors', '1' );
ini_set( 'html_errors', '0' );

session_start();

header( "Content-Type: text/html; charset=utf8" );

define( 'GAMEPRESS', true );
define( 'ROOT_DIR', dirname( __FILE__ ) );
define( 'ENGINE_DIR', ROOT_DIR . '/engine' );

require_once( ENGINE_DIR, '/inc/include/functions.inc.php' );

$is_loged_in = false;
$selected_language = 'Portugues';
$PHP_MIN_VERSION = '8.0';

$_REQUEST[ 'action' ] = isset( $_REQUEST[ 'action' ] ) ? $_REQUEST[ 'action' ] : '';

$url = explode( basename( $_SERVER[ 'PHP_SELF' ] ), $_SERVER[ 'PHP_SELF' ] );
$url = reset( $url );
$_IP = get_ip();

if ( isSSL() ) $url = "https://" . $_SERVER[ 'HTTP_HOST' ] . $url;
else $url = "http://" . $_SERVER[ 'HTTP_HOST' ] . $url;

if ( isset( $_POST[ 'selected_language' ] ) ) {

        $_POST[ 'selected_language' ] = totranslit( $_POST[ 'selected_language' ], false, false );

        if ( $_POST[ 'selected_language' ] and @is_dir( ROOT_DIR . '/language/' . $_POST[ 'selected_language' ] ) ) {

                $selected_language = $_POST[ 'selected_language' ];
                set_cookie( "selected_language", $selected_language, 365 );
        }
} elseif ( isset( $_COOKIE[ 'selected_language' ] ) ) {

        $_COOKIE[ 'selected_language' ] = totranslit( $_COOKIE[ 'selected_language' ], false, false );

        if ( $_COOKIE[ 'selected_language' ] != "" and @is_dir( ROOT_DIR . '/language/' . $_POST[ 'selected_language' ] ) ) {
                $selected_language = $_COOKIE[ 'selected_language' ];
        }
        
}

include_once ( ROOT_DIR . '/language/' . $selected_language . '/adminpanel.lng' );
include_once ( ROOT_DIR . '/language/' . $selected_language . '/install.lng' );

if ( $lang[ 'direction' ] == 'rtl' ) $rtl_prefix = '_rtl'; else $rtl_prefix = '';

$skin_header = <<<HTML
<!doctype html>
<html lang="{ $lang[ 'language_code' ] }" dir="{ $lang[ 'direction' ] }">
<head>
        <meta charset="utf8">
        <title>{ $lang[ 'install_1' ] }</title>
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, width=device-width">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <link href="engine/skins/fonts/fontawesome/styles.min.css" media="screen" rel="stylesheet" type="text/css" />
        <link href="engine/skins/stylesheets/application{ $rtl_prefix }.css" media="screen" rel="stylesheet" type="text/css" />
        <script src="engine/skins/javascripts/application.js"></script>
</head>
<body class="no-theme">
<script>
        var gp_act_lang     = [];
        var cal_language    = '{ $lang[ 'language_code' ] }';
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
@media ( min-width: 769px ) {
        .installpanel {
                display: table-cell;
                vertical-align: middle;
        }
        @media ( min-height: 600px ) {
                .installbox {
                        margin-top: -100px;
                }
        }
}
</style>
<div class="navbar navbar-inverse bg-primary-700 mb-20">
        <div class="navbar-header">
                <a class="navbar-brand" href="install.php">{ $lang[ 'install_1' ] }</a>
        </div>
</div>
<div class="page-container">
        <div class="installpanel">
                <div class="installbox">
<!-- Main area -->
HTML;


$kin_footer = <<<HTML
        <!-- Main area -->
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
                $lang[ 'install_2' ] = $lang[ 'install_3' ];
        } else {
                $back = "";
        }

        echo $skin_header;

        echo <<<HTML;
<form method="post">
<div class="panel panel-default">
        <div class="panel-heading">
        { $lang[ 'install_4' ] }
        </div>
        <div class="panel-body">
                { $text }
        </div>
        <div class="panel-footer">
        <button type="submit" { $back } class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-arrow-circle-o-right position-left"></i>{ $lang[ 'install_2' ] }</button>
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
                $index = random_int( 0, count( $arr ) - 1 );
                $key .= $arr[ $index ];
        }
        return $key;
}

function folders_check_chmod( $dir, $bad_files = array() ) {}
