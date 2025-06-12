<?php
/**
 * Bootstrap file for setting the ABSPATH constant
 * and loading the config.php file. The config.php
 * file will then load the settings.php file, which
 * will then set up the GamePress environment.
 *
 * If the config.php file is not found then an error
 * will be displayed asking the visitor to set up the
 * config.php file.
 *
 * Will also search for config.php in GamePress parent
 * directory to allow the GamePress directory to remain
 * untouched.
 *
 * @package GamePress
 */

/** Define ABSPATH as this file's directory */
if ( ! defined( 'ABSPATH' ) ) {
        define( 'ABSPATH', __DIR__ . '/' );
}

/**
 * The error_reporting() function can be disabled in php.ini. On systems where that is the case,
 * it's best to add a dummy function to the config.php file, but as this call to the function
 * is run prior to config.php loading, it is wrapped in a function_exists() check.
 */
if ( function_exists( 'error_reporting' ) ) {
        /*
         * Initialize error reporting to a know set of levels.
         *
         * This will be adapted in debug_mode() located in includes/load.php based on DEBUG.
         * @see https://www.php.net/manual/en/errorfunc.constants.php List of known error levels.
         */
        error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
}

/**
 * If config.php exists in the GamePress root, or if it exists in the root and settings.php
 * doesn't, load config.php. The secondary check for settings.php has the added benefit
 * of avoiding cases where the current directory is a nested installation, e.g. / is GamePress(a)
 * and /blog/ is GamePress(b).
 *
 * If neither set of conditions is true, initiate loading the setup process.
 */
if ( file_exists( ABSPATH . 'config.php' ) ) {

        /** The config file resides in ABSPATH */
        require_once ABSPATH . 'config.php';

} elseif ( @file_exists( dirname( ABSPATH ) . '/config.php' ) && ! @file_exists( dirname( ABSPATH ) . '/settings.php' ) ) {

        /** The config file resides one level above ABSPATH but is not part of another installation */
        require_once dirname( ABSPATH ) . '/config.php';
        
} else {

        // A config file doesn't exist.
        
        define( 'INC', 'includes' );
        require_once ABSPATH . INC . '/version.php';
        require_once ABSPATH . INC . '/compat.php';
        require_once ABSPATH . INC . '/load.php';

        // Check for the required PHP version and for the MySQL extension or a database drop-in.
        check_php_mysql_versions();

        // Standardize $_SERVER variables across setups.
        fix_server_vars();

        define( 'CONTENT_DIR', ABSPATH . 'content' );
        require_once ABSPATH . INC . '/functions.php';

        $path = guess_url() . '/admin/setup-config.php';

        // Redirect to setup-config.php.
        if ( ! str_contains( $_SERVER['REQUEST_URI'], 'setup-config' ) ) {
                header( 'Location: ', $path );
                exit;
        }

        load_translations_early();

        // Die with an error message.
        $die = '<p>' . sprintf(
                /* translators: %s: config.php */
                __( "There doesn't seem to be a %s file. It is needed before the installation can continue." ),
                '<code>config.php</code>'
        ) . '</p>';
        $die .= '<p>' . sprintf(
                /* translators: 1: Documentation URL, 2: config.php */
                __( 'Need more help? <a href="%1$s">Read the support article on %2$s</a>' ),
                __( 'https://support.com.br' ),
                '<code>config.php</code>'
        ) . '</p>';
        $die .= '<p>' . sprintf(
                /* translators: %s: config.php */
                __( "You can create a %s file through a web interface, but this doesn't work for all server setups. The safest way is to manually create the file." ),
                '<code>config.php</code>'
        ) . '</p>';
        $die .= '<p><a href="' . $path . '" class="button button-large">' . __( 'Create a Configuration File' ) . '</a></p>';
        
        die( $die, __( 'GamePress &rsaquo; Error' ) );
}
