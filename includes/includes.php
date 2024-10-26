<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

if ( ! defined( 'MUXVIDEO_PLUGIN_NAME' ) ) {
	define( 'MUXVIDEO_PLUGIN_NAME', '2coders-integration-mux-video' );
}

if ( ! defined( 'MUXVIDEO_PLUGIN_SLUG' ) ) {
	define( 'MUXVIDEO_PLUGIN_SLUG', 'two-coders-integration-mux-video' );
}

if ( ! defined( 'MUXVIDEO_PLUGIN_OPTION' ) ) {
	define( 'MUXVIDEO_PLUGIN_OPTION', 'muxvideo_options' );
}

if ( ! defined( 'MUXVIDEO_PLUGIN_VERSION' ) ) {
	$plugin_file = MUXVIDEO_PLUGIN_DIR . '/muxvideo.php';
	$plugin_data = get_plugin_data( $plugin_file );
	define( 'MUXVIDEO_PLUGIN_VERSION', $plugin_data['Version'] );
}

// Check if is mux plugin
if ( ! defined( 'IS_MUXVIDEO_PLUGIN' ) ) {
	if(!isset($_GET['nonce-form']) || !wp_verify_nonce($_GET['nonce-form'], '_wpnonce')) {
		$muxvideo_page_request = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : '';
		$is_muxvideo_plugin = str_contains( $muxvideo_page_request, 'muxvideo' );
		define( 'IS_MUXVIDEO_PLUGIN', $is_muxvideo_plugin );
	}
}

require_once MUXVIDEO_PLUGIN_DIR . '/includes/admin.php';
require_once MUXVIDEO_PLUGIN_DIR . '/includes/init.php';
require_once MUXVIDEO_PLUGIN_DIR . '/vendor/autoload.php';
require_once MUXVIDEO_PLUGIN_DIR . '/includes/functions.php';
require_once MUXVIDEO_PLUGIN_DIR . '/includes/muxvideo_fs.php';

$auth_response = muxvideo_authorization_check();
global $auth_response;

function muxvideo_load_textdomain() {
	$languages_path = MUXVIDEO_PLUGIN_DIR . '/languages/';
	load_plugin_textdomain( MUXVIDEO_PLUGIN_NAME, false, $languages_path );

	// Check if current language translation exists, if not, set english
	$mofile = $languages_path . MUXVIDEO_PLUGIN_NAME . '-' . get_locale() . '.mo';

	if ( ! load_textdomain( MUXVIDEO_PLUGIN_NAME, $mofile ) ) {
		$mofile = $languages_path . MUXVIDEO_PLUGIN_NAME . '-en_US.mo';
		load_textdomain( MUXVIDEO_PLUGIN_NAME, $mofile );
	}
}
add_action( 'init', 'muxvideo_load_textdomain' );

if ( is_admin() ) {
	function muxvideo_register_options_page() {
		$plugin_name = '2Coders integration for Mux Video';
		if ( muxvideo_fs()->is__premium_only() ) :
			$plugin_name = MUXVIDEO_FS_CAN_USE_PREMIUM_CODE ? '2Coders integration for Mux Video Pro' : '2Coders integration for Mux Video';
		endif;
		add_menu_page( 'Mux', $plugin_name, 'manage_options', 'muxvideo', '', plugin_dir_url( __FILE__ ) . '../assets/images/icono-plugin.svg' );
		add_submenu_page( 'muxvideo', 'Settings', 'Settings', 'manage_options', 'muxvideo-settings', 'muxvideo_settings_page' );
		global $auth_response;
		if ( $auth_response != 401 ) :
			add_submenu_page( 'muxvideo', 'Asset List', 'Asset List', 'manage_options', 'muxvideo-asset-list', 'muxvideo_asset_list_page' );
			add_submenu_page( 'muxvideo', 'New Asset', 'Asset Upload', 'manage_options', 'muxvideo-asset-upload', 'muxvideo_asset_upload_page' );
		endif;

		remove_submenu_page( 'muxvideo', 'muxvideo' );
	}
	add_action( 'admin_menu', 'muxvideo_register_options_page' );
}