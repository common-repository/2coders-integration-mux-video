<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

if ( IS_MUXVIDEO_PLUGIN ) {
	function muxvideo_admin_scripts() {
		/**
		 * Font Awesome 5 Free version web
		 * https://fontawesome.com/
		 */
		wp_register_style( 'muxvideo-style-font-awesome', plugin_dir_url( __FILE__ ) . '../assets/lib/fontawesome-free-5.15.4-web/css/all.min.css' );
		wp_enqueue_style( 'muxvideo-style-font-awesome' );

		wp_register_style( 'muxvideo-style-admin', plugin_dir_url( __FILE__ ) . '../assets/css/admin.css' );
		wp_enqueue_style( 'muxvideo-style-admin' );

		muxvideo_enqueue_script_player();
	}
	add_action( 'admin_enqueue_scripts', 'muxvideo_admin_scripts' );

	function muxvideo_enqueue_scripts() {
		wp_enqueue_script( 'muxvideo-functions-script', plugin_dir_url( __FILE__ ) . '../assets/js/functions.js', array( 'jquery' ), true );
		wp_enqueue_script( 'muxvideo-event-listeners-script', plugin_dir_url( __FILE__ ) . '../assets/js/event-listeners.js', array( 'jquery' ), true );
		if(!isset($_REQUEST['nonce-form']) || !wp_verify_nonce($_REQUEST['nonce-form'], '_wpnonce')) {
			$current_page = sanitize_text_field( $_REQUEST['page'] );
			switch ( $current_page ) {
				case 'muxvideo-asset-upload':
					$added_class = MUXVIDEO_PLUGIN_SLUG . '-page-upload';
					wp_enqueue_script( 'muxvideo-asset-upload-script', plugin_dir_url( __FILE__ ) . '../assets/js/asset-upload.js', array( 'jquery' ), true );
					break;
	
				case 'muxvideo-asset-list':
					$added_class = MUXVIDEO_PLUGIN_SLUG . '-page-asset-list';
					wp_enqueue_script( 'muxvideo-asset-list-script', plugin_dir_url( __FILE__ ) . '../assets/js/asset-list.js', array( 'jquery' ), true );
					break;
	
				case 'muxvideo-settings':
					$added_class = MUXVIDEO_PLUGIN_SLUG . '-page-settings';
					wp_enqueue_script( 'muxvideo-settings-script', plugin_dir_url( __FILE__ ) . '../assets/js/settings.js', array( 'jquery' ), true );
					break;
	
				case 'muxvideo-account':
					$added_class = MUXVIDEO_PLUGIN_SLUG . '-page-account';
					break;
	
				case 'muxvideo-pricing':
					$added_class = MUXVIDEO_PLUGIN_SLUG . '-page-pricing';
					break;
	
				default:
					$added_class = '';
					break;
			}
	
			add_filter( 'admin_body_class', function ($classes) use ($added_class) {
				$classes .= " $added_class";
				return $classes;
			} );
		}
	}
	add_action( 'admin_enqueue_scripts', 'muxvideo_enqueue_scripts' );

	/**
	 * Enqueue translations
	 *
	 * @return void
	 */

	function muxvideo_add_window_variables() {
		wp_localize_script(
			'muxvideo-functions-script',
			'getTranslations',
			json_decode( muxvideo_get_translations(), true )
		);

		wp_localize_script(
			'muxvideo-functions-script',
			'defaultImg',
			array(
				plugin_dir_url( __FILE__ ) . '../assets/images/default-poster.png'
			)
		);
	}
	add_action( 'admin_enqueue_scripts', 'muxvideo_add_window_variables' );

	function muxvideo_get_translations() {
		$locale = get_locale();
		$po_file = MUXVIDEO_PLUGIN_DIR . '/languages/2coders-integration-mux-video-' . $locale . '.po';
		return muxvideo_convert_po_to_json( $po_file );
	}

	function muxvideo_convert_po_to_json( $file_path ) {
		$translations = array();
		$file_content = content_url( $file_path );
		$entries = explode( "\n\n", $file_content );

		$translations = array();

		foreach ( $entries as $entry ) {
			$lines = explode( "\n", $entry );

			$msgid = '';
			$msgstr = '';

			foreach ( $lines as $line ) {
				if ( strpos( $line, 'msgid' ) === 0 ) {
					$msgid = trim( substr( $line, 6, -1 ) );
				} elseif ( strpos( $line, 'msgstr' ) === 0 ) {
					$msgstr = trim( substr( $line, 7, -1 ) );

					$msgid = trim( stripcslashes( $msgid ), '"' );
					$msgstr = trim( stripcslashes( $msgstr ), '"' );

					if ( empty( $msgstr ) ) {
						$msgstr = '';

						for ( $i = 1; $i < count( $lines ); $i++ ) {
							$next_line = trim( $lines[ $i ] );

							if ( ! empty( $next_line ) && strpos( $next_line, 'msgid' ) !== 0 ) {
								$msgstr .= str_replace( array( 'msgstr', '&nbsp;' ), '', $next_line ) . '';
							}
						}

						$msgstr = trim( stripcslashes( str_replace( '"', '', $msgstr ) ) );
					}
				}
			}

			if ( ! empty( $msgid ) && ! empty( $msgstr ) ) {
				$msgid = trim( stripcslashes( $msgid ), '"' );
				$msgstr = trim( stripcslashes( $msgstr ), '"' );
				$translations[ $msgid ] = $msgstr;
			}
		}

		return wp_json_encode( array( 'translations' => $translations ) );
	}
}

/**
 * Include shortcodes and initialize them
 */
require_once MUXVIDEO_PLUGIN_DIR . '/views/shortcodes/asset-player.php';

function muxvideo_shortcodes_init() {
	add_shortcode( 'muxvideo_asset', 'muxvideo_asset_player_shortcode' );
}
add_action( 'init', 'muxvideo_shortcodes_init' );

function muxvideo_frontend_load_freemius_data() {
	global $current_user;
	$current_fs_user = Freemius::_get_user_by_email( $current_user->user_email );
	$current_fs_user = is_object( $current_fs_user ) ? $current_fs_user->id : 'not reachable';
	wp_localize_script(
		'muxvideo-functions-script',
		'freemiusData',
		array(
			'current_fs_user' => $current_fs_user,
		)
	);
}
add_action( 'admin_enqueue_scripts', 'muxvideo_frontend_load_freemius_data' );

if ( MUXVIDEO_FS_CAN_USE_PREMIUM_CODE ) {
	function muxvideo_add_class_if_is_premium( $classes ) {
		$classes .= ' muxvideo_fs-premium';
		return $classes;
	}
	add_filter( 'admin_body_class', 'muxvideo_add_class_if_is_premium' );
}

if ( IS_MUXVIDEO_PLUGIN ) {
	function muxvideo_add_class_media( $classes ) {
		$classes .= ' mux-media-body two-coders-integration-mux-video-body';
		return $classes;
	}
	add_filter( 'admin_body_class', 'muxvideo_add_class_media' );
}