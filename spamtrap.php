<?php
/*
Plugin Name: SpamTrap
Plugin URI: http://spamtrap.ro/
Description: This plugin will make your blog more secure by adding rules which spambots will not pass.
Author: Andrei Husanu
Author URI: http://spamtrap.ro/
Version: 0.3.3
License: GPLv2 or later
*/

// Make sure we don't expose any info of called directly
if( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

/// ////////////////////////////////////////////////////////////////////////////

define( 'SPAMTRAP_VERSION'    , '0.3.3' );
define( 'SPAMTRAP_PLUGIN_URL' , plugin_dir_url( __FILE__ ) );
define( 'SPAMTRAP_PLUGIN_DIR' , plugin_dir_path( __FILE__ ) );
define( 'SPAMTRAP_CFG'        , 'spamtrap_cfg' );
define( 'SPAMTRAP_TEST_MODE'  , 1 );

///

if( SPAMTRAP_TEST_MODE ) {
	function lg( $msg ) {
		$stderr = fopen( 'php://stderr', 'w' ); 
		fwrite( $stderr, $msg . "\n" ); 
		fclose( $stderr ); 
	}
}
else {
	function lg( $msg ) { }	
}

///

include_once( "harvesters.php" );
add_action( 'wp_footer', array( 'SPAMTRAP_HARVESTERS', 'html' ) );

include_once( "spamtrap.class.php" );
SPAMTRAP::init( );

if( is_admin( ) ) {
	require_once dirname( __FILE__ ) . '/admin.php';
	SPAMTRAP_ADMIN::init( );
}
