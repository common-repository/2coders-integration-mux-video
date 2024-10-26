<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

if ( function_exists( 'muxvideo_fs' ) ) :

	/* Clear database traces after uninstall */
	function muxvideo_fs_uninstall_cleanup() {
		if ( function_exists( 'delete_option' ) ) {
			delete_option( 'muxvideo_options' );
		}
	}
	muxvideo_fs()->add_action( 'after_uninstall', 'muxvideo_fs_uninstall_cleanup' );

	function muxvideo_fs_custom_connect_message_on_update(
		$message,
		$user_first_name,
		$plugin_title,
		$user_login,
		$site_link,
		$freemius_link
	) {
		return sprintf(
			__( 'freemius_message_welcome_user', '2coders-integration-mux-video' ) . ',<br><br>' .
			__( 'freemius_message_opt_in', '2coders-integration-mux-video' ),
			$user_first_name,
			'<b>' . $plugin_title . '</b>',
			'<b>' . $user_login . '</b>',
			$site_link,
			$freemius_link
		);
	}
	muxvideo_fs()->add_filter( 'connect_message_on_update', 'muxvideo_fs_custom_connect_message_on_update', 10, 6 );

	function muxvideo_fs_custom_icon() {
		return dirname( __FILE__ ) . '/../assets/images/mux-logo-positive.svg';
	}
	muxvideo_fs()->add_filter( 'plugin_icon', 'muxvideo_fs_custom_icon' );

endif;

add_action( 'in_admin_header', 'muxvideo_insert_content_in_freemius_pages', 99 );
function muxvideo_insert_content_in_freemius_pages() {
	if(!isset($_REQUEST['nonce-form']) || !wp_verify_nonce($_REQUEST['nonce-form'], '_wpnonce')) {
		$page_request = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : '';
		switch ( $page_request ) {
			case 'muxvideo-account':
			case 'muxvideo-pricing':
				muxvideo_get_header();
				break;
			case 'muxvideo-contact':
				muxvideo_get_header();
				muxvideo_get_contact_container();
				break;
			default:
				break;
		}
	}
}

add_action( 'admin_head', 'muxvideo_add_styles_to_upgrade_button_menu' );
function muxvideo_add_styles_to_upgrade_button_menu() {
	$custom_styles = '
    .toplevel_page_muxvideo .fs-submenu-item.pricing.upgrade-mode {
        color: white;
        padding: 5px 10px;
        background: #0038D2;
        display: block;
        text-align: center;
        border-radius: 3px;
        transition: all .3s;
    }
    .fs-submenu-item.pricing.upgrade-mode:hover{
        background: #01114A;
    }';

	echo '<style>' . esc_html( $custom_styles ) . '</style>';
}

// The confirmation link is broken when you make the purchase, we redirect to the main page of the plugin
add_action( 'admin_init', 'muxvideo_fix_email_confirm_redirect' );
function muxvideo_fix_email_confirm_redirect() {
	if(!isset($_REQUEST['nonce-form']) || !wp_verify_nonce($_REQUEST['nonce-form'], '_wpnonce')) {
		if ( isset( $_GET['fs_action'] ) && $_GET['fs_action'] == 'mux-media_activate_new' && $_GET['page'] == 'muxvideo' ) {
			wp_safe_redirect( admin_url( 'admin.php?page=muxvideo-settings' ) );
			exit;
		}
	}
}