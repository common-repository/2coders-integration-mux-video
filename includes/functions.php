<?php

/**
 * Core functions and includes other functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

require_once MUXVIDEO_PLUGIN_DIR . '/vendor/autoload.php';

require_once MUXVIDEO_PLUGIN_DIR . '/includes/functions-components.php';

use \Firebase\JWT\JWT;

function muxvideo_assert_failure( $file, $line, $assertion, $description ) {
	print( "ASSERT FAIL: " . $assertion . " " . $description . " at " . $file . ":" . $line . "\n" );
	exit( 1 );
}
assert_options( ASSERT_BAIL, true );
assert_options( ASSERT_CALLBACK, 'muxvideo_assert_failure' );

function muxvideo_get_global_config() {
	try {
		$muxvideo_options = get_option( 'muxvideo_options' );
		$muxvideo_token_id = isset( $muxvideo_options['muxvideo_token_id'] ) ? $muxvideo_options['muxvideo_token_id'] : '';
		$muxvideo_token_secret = isset( $muxvideo_options['muxvideo_token_secret'] ) ? $muxvideo_options['muxvideo_token_secret'] : '';

		return MuxPhp\Configuration::getDefaultConfiguration()
			->setUsername( $muxvideo_token_id )
			->setPassword( $muxvideo_token_secret );
	} catch (\Throwable $th) {
		error_log( 'Exception when calling muxvideo_get_global_config: ', $th->getMessage() );
		return '';
	}

}

function muxvideo_get_request_headers() {
	return [ 'headers' => [ 'x-source-platform' => '2Coders Wordpress Free | 1.0' ] ];
}

function muxvideo_getMuxConfig( $token_id, $token_secret ) {
	return MuxPhp\Configuration::getDefaultConfiguration()
		->setUsername( $token_id )
		->setPassword( $token_secret );
}

function muxvideo_get_signing_key_api() {
	$config = muxvideo_get_global_config();
	return new MuxPhp\Api\URLSigningKeysApi(
		new GuzzleHttp\Client(),
		$config
	);
}

// API Client Initialization
function muxvideo_api_client_init() {
	$headers = muxvideo_get_request_headers();
	$config = muxvideo_get_global_config();
	return new MuxPhp\Api\AssetsApi(
		new GuzzleHttp\Client( $headers ),
		$config
	);
}

// API Client Initialization to Uploads
function muxvideo_api_client_init_upload() {
	$config = muxvideo_get_global_config();
	return new MuxPhp\Api\DirectUploadsApi(
		new GuzzleHttp\Client(),
		$config
	);
}

// API Client Initialization to playback restrictions
function muxvideo_api_client_init_playback_restrictions() {
	$config = muxvideo_get_global_config();
	try {
		return new MuxPhp\Api\PlaybackRestrictionsApi(
			new GuzzleHttp\Client(),
			$config
		);
	} catch (\Throwable $th) {
		error_log( 'Exception when calling muxvideo_api_client_init_playback_restrictions: ', $th->getMessage() );
		return '';
	}
}

/**
 * Create and catch update options for private_key in SigningKeyResponse
 */

function muxvideo_add_field_to_options( $new_options ) {
	$option_name = 'muxvideo_options';

	$token_id = isset( $new_options['muxvideo_token_id'] ) ? sanitize_text_field( $new_options['muxvideo_token_id'] ) : '';
	$token_secret = isset( $new_options['muxvideo_token_secret'] ) ? sanitize_text_field( $new_options['muxvideo_token_secret'] ) : '';

	if ( $token_id && $token_secret ) {
		$signing_data = muxvideo_create_signing_key();

		$new_options['muxvideo_token_id'] = $token_id;
		$new_options['muxvideo_token_secret'] = $token_secret;
		$new_options['muxvideo_signing_data'] = $signing_data;

		update_option( $option_name, $new_options );
	}
}

function muxvideo_handle_option_update( $option_name, $old_value, $new_value ) {
	if ( $option_name === 'muxvideo_options' ) {
		remove_action( 'updated_option', 'muxvideo_handle_option_update', 10 );
		muxvideo_add_field_to_options( $new_value );
		add_action( 'updated_option', 'muxvideo_handle_option_update', 10, 3 );
	}
}
add_action( 'updated_option', 'muxvideo_handle_option_update', 10, 3 );

function muxvideo_handle_option_create( $option, $value ) {
	muxvideo_handle_option_update( $option, [], $value );
}
add_action( 'added_option', 'muxvideo_handle_option_create', 10, 3 );

/**
 * Create signing key
 */
function muxvideo_create_signing_key() {
	$apiInstance = muxvideo_get_signing_key_api();
	$signing_data = [];
	try {
		$signing_key_data = $apiInstance->createUrlSigningKeyWithHttpInfo(); //create a signingkey and recieve the data;
		$signing_data['id'] = $signing_key_data[0]->getData()->getId();
		$signing_data['private_key'] = $signing_key_data[0]->getData()->getPrivateKey();
		return $signing_data;
	} catch (Exception $e) {
		error_log( 'Exception when calling SigningKeysApi->createSigningKey: ', $e->getMessage() );
		return '';
	}
}

/**
 * Generate a JWT for a signed asset
 */
function muxvideo_get_json_web_token( $playback_id, $key_id, $key_secret, $aud, $playback_restriction_id, $time = 2 ) {

	$payload = array(
		"sub" => $playback_id,
		"aud" => $aud,          // v = video, t = thumbnail, g = gif.
		"exp" => time() + 1200, // Expiry time in epoch - in this case now + 10 mins
		"kid" => $key_id,
		"playback_restriction_id" => $playback_restriction_id,
		"time" => $time
	);
	if ( $aud === 't' ) :
		$params = array(
			"width" => 258,
			"height" => 160,
			"fit_mode" => "smartcrop",
			"time" => $time
		);
		$payload = array_merge( $params, $payload );
	endif;

	return JWT::encode( $payload, base64_decode( $key_secret ), 'RS256' );
}

/**
 * Get playback restrictions
 */
function muxvideo_get_data_playback_restrictions() {
	global $auth_response;
	if ( $auth_response != 401 ) {
		try {
			$api_playback_restrictions = muxvideo_api_client_init_playback_restrictions();
			$playback_restrictions = $api_playback_restrictions->listPlaybackRestrictions();
			$playback_restriction_data = $playback_restrictions->getData()[0];
			if ( is_null( $playback_restriction_data ) ) :
				return null;
			endif;
			$playback_restriction_referrer = $playback_restriction_data->getReferrer();
			$playback_restriction_obj = new stdClass();
			$playback_restriction_obj->id = $playback_restriction_data->getId();
			$playback_restriction_obj->allowed_domains = $playback_restriction_referrer->getAllowedDomains();
			$playback_restriction_obj->allow_no_referrer = $playback_restriction_referrer->getAllowNoReferrer();

			return $playback_restriction_obj;
		} catch (\Throwable $th) {
			error_log( 'Exception when calling muxvideo_get_data_playback_restrictions: ' . $th->getMessage() );
		}
	}
}

function muxvideo_get_header() {
	include_once MUXVIDEO_PLUGIN_DIR . '/views/templates/header.php';
}

function muxvideo_get_sidebar() {
	include_once MUXVIDEO_PLUGIN_DIR . '/views/templates/sidebar.php';
}

function muxvideo_get_pro_version_banner() {
	include_once MUXVIDEO_PLUGIN_DIR . '/views/templates/pro_version_banner.php';
}

function muxvideo_get_contact_container() {
	include_once MUXVIDEO_PLUGIN_DIR . '/views/templates/contact_container.php';
}

function muxvideo_display_player_options() {
	ob_start();
	include_once MUXVIDEO_PLUGIN_DIR . '/views/template-parts/customize-player-options.php';
	return ob_get_clean();
}

function muxvideo_display_player_other_data( $asset_data, $current_img ) {
	ob_start();
	include_once MUXVIDEO_PLUGIN_DIR . '/views/template-parts/customize-player-other_data.php';
	return ob_get_clean();
}

include_once MUXVIDEO_PLUGIN_DIR . '/includes/functions-wp_ajax.php';

include_once MUXVIDEO_PLUGIN_DIR . '/views/template-parts/asset-list_table.php';

function muxvideo_authorization_check() {
	try {
		$assetsApi = muxvideo_api_client_init();
		$assetsApi->getAsset( 0 );
		return true;
	} catch (Exception $e) {
		return $e->getCode();
	}
}

function muxvideo_enqueue_script_player() {
	/**
	 * Mux player: https://www.mux.com/player
	 * Url file: https://cdn.jsdelivr.net/npm/@mux/upchunk@3.3.2/dist/upchunk.min.js
	 */
	wp_register_script( 'muxvideo-script-player', plugin_dir_url( __FILE__ ) . '../assets/lib/mux-player-2.3.2.min.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'muxvideo-script-player' );
}

function muxvideo_enqueue_script_upchunk() {

	/**
	 * Mux Upchunk: https://github.com/muxinc/upchunk
	 * Url file: https://cdn.jsdelivr.net/npm/@mux/upchunk@3.3.2/dist/upchunk.min.js
	 */
	wp_register_script( 'muxvideo-script-upchunk', plugin_dir_url( __FILE__ ) . '../assets/lib/upchunk-3.3.2.min.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'muxvideo-script-upchunk' );
}

/**
 *  Hide native WP notices 
 */
function muxvideo_hide_update_nag() {
	if ( IS_MUXVIDEO_PLUGIN ) :
		remove_action( 'admin_notices', 'update_nag', 3 );
	endif;
}
add_action( 'admin_menu', 'muxvideo_hide_update_nag' );

/**
 * Add notice if the plugin is not configured
 */
function muxvideo_add_configuration_notice() {
	global $auth_response;
	if ( $auth_response === 401 ) {
		?>
		<div class="notice notice-warning is-dismissible">
			<label>
				<p>
					<?php echo wp_kses( __( 'common_notice_configuration_pending', '2coders-integration-mux-video' ), array( 'a' => array( 'href' => array(), 'title' => array() ) ) ); ?>
				</p>
			</label>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'muxvideo_add_configuration_notice', 2 );

/** Add disclaimer to all pages in the footer */
function muxvideo_add_disclaimer_in_footer() {
	return '<div class="muxvideo-disclaimer-container"><p>' . esc_html__( 'common_disclaimer_text', '2coders-integration-mux-video' ) . '</p></div>';
}