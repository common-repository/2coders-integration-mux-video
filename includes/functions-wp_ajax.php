<?php

/**
 * Custom AJAX endpoints handler
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

/**
 * Create or update playback restrictions
 */
add_action( 'wp_ajax_muxvideo_create_update_playback_restrictions', 'muxvideo_create_update_playback_restrictions' );
add_action( 'wp_ajax_nopriv_muxvideo_create_update_playback_restrictions', 'muxvideo_create_update_playback_restrictions' );
function muxvideo_create_update_playback_restrictions() {
	try {
		$api_playback_restrictions = muxvideo_api_client_init_playback_restrictions();
		$playback_restrictions_data = muxvideo_get_data_playback_restrictions();

		if(!isset($_REQUEST['nonce-form']) || !wp_verify_nonce($_REQUEST['nonce-form'], '_wpnonce')) {
			$request_data = isset( $_POST['form_data'] ) ? stripslashes( sanitize_text_field( $_POST['form_data'] ) ) : '';
			$update_referrer_domain_restriction_request = json_decode( $request_data, true ); // \MuxPhp\Models\UpdateReferrerDomainRestrictionRequest
			if ( $playback_restrictions_data ) : // If exists update playback restriction
				try {
					$result = $api_playback_restrictions->updateReferrerDomainRestriction( $playback_restrictions_data->id, $update_referrer_domain_restriction_request );
				} catch (Exception $e) {
					error_log( 'Exception when calling PlaybackRestrictionsApi->updateReferrerDomainRestriction: ', $e->getMessage() );
				}
			else : // Create a new playback restriction
				$create_playback_restriction_request = [ "referrer" => $update_referrer_domain_restriction_request ];
				try {
					$result = $api_playback_restrictions->createPlaybackRestriction( $create_playback_restriction_request );
					print_r( $result );
				} catch (Exception $e) {
					error_log( 'Exception when calling PlaybackRestrictionsApi->createPlaybackRestriction: ', $e->getMessage() );
				}
			endif;
		}

	} catch (Exception $e) {
		error_log( 'Exception when calling PlaybackRestrictionsApi->listPlaybackRestrictions: ', $e->getMessage() );
	}
	wp_die();
}

/**
 * Display the content to Customize shortcode
 */
add_action( 'wp_ajax_muxvideo_display_modal_customize_shortcode', 'muxvideo_display_modal_customize_shortcode' );
add_action( 'wp_ajax_nopriv_muxvideo_display_modal_customize_shortcode', 'muxvideo_display_modal_customize_shortcode' );
function muxvideo_display_modal_customize_shortcode() {
	try {
		include_once MUXVIDEO_PLUGIN_DIR . '/views/template-parts/customize-shortcode.php';
		wp_die();
	} catch (\Throwable $th) {
		error_log( "Error: muxvideo_display_modal_customize_shortcode " . $th->getMessage() );
	}
}

/**
 * Display the content to Customize shortcode
 */
add_action( 'wp_ajax_muxvideo_get_jwt_dinamically', 'muxvideo_get_jwt_dinamically' );
add_action( 'wp_ajax_nopriv_muxvideo_get_jwt_dinamically', 'muxvideo_get_jwt_dinamically' );
function muxvideo_get_jwt_dinamically() {
	if(!isset($_REQUEST['nonce-form']) || !wp_verify_nonce($_REQUEST['nonce-form'], '_wpnonce')) {
		$json_string = isset( $_POST['formated_data'] ) ? wp_unslash( sanitize_text_field( $_POST['formated_data'] ) ) : '';
		$asset_data = json_decode( sanitize_text_field( $json_string ), true );
		$time = isset( $_POST['time'] ) ? absint( sanitize_text_field( $_POST['time'] ) ) : 0;
	
		if ( ! empty( $asset_data ) && is_array( $asset_data ) ) {
			try {
				$jwt_t = muxvideo_get_json_web_token(
					sanitize_text_field( $asset_data['playback_id'] ),
					sanitize_text_field( $asset_data['signing_id'] ),
					sanitize_text_field( $asset_data['signing_private_key'] ),
					't',
					sanitize_text_field( $asset_data['playback_restriction_id'] ),
					$time
				);
	
				wp_send_json( $jwt_t );
				wp_die();
			} catch (\Throwable $th) {
				error_log( "Error: muxvideo_get_jwt_dinamically " . $th->getMessage() );
			}
		} else {
			wp_send_json_error( 'No valid data' );
			wp_die();
		}
	}

}

/**
 * Function called from ajax to get next page of assets 
 */
add_action( 'wp_ajax_muxvideo_load_more_assets', 'muxvideo_load_more_assets' );
add_action( 'wp_ajax_nopriv_muxvideo_load_more_assets', 'muxvideo_load_more_assets' );
function muxvideo_load_more_assets() {
	muxvideo_display_assets_list();
	wp_die();
}

/**
 * Create a delete request and remove the asset in Mux
 */
add_action( 'wp_ajax_muxvideo_delete_asset', 'muxvideo_delete_asset' );
add_action( 'wp_ajax_nopriv_muxvideo_delete_asset', 'muxvideo_delete_asset' );
function muxvideo_delete_asset() {
	require_once MUXVIDEO_PLUGIN_DIR . '/includes/functions.php';
	if(!isset($_REQUEST['nonce-form']) || !wp_verify_nonce($_REQUEST['nonce-form'], '_wpnonce')) {
		try {
			$assetsApi = muxvideo_api_client_init();
			$assetsApi->deleteAsset( sanitize_text_field( $_REQUEST['asset_id'] ) );
		} catch (Exception $e) {
			error_log( 'Exception when calling AssetsApi->deleteAsset: ', $e->getMessage() );
		}
		wp_die();
	}
}

/**
 * Create a new upload url to upload a new asset
 */
add_action( 'wp_ajax_muxvideo_reset_upload_url', 'muxvideo_reset_upload_url' );
add_action( 'wp_ajax_nopriv_muxvideo_reset_upload_url', 'muxvideo_reset_upload_url' );
function muxvideo_reset_upload_url() {
	require_once MUXVIDEO_PLUGIN_DIR . '/includes/functions.php';
	if(!isset($_REQUEST['nonce-form']) || !wp_verify_nonce($_REQUEST['nonce-form'], '_wpnonce')) {
		$asset_privacy =
			( $_REQUEST['asset_privacy'] && $_REQUEST['asset_privacy'] == 'signed' )
			? [ MuxPhp\Models\PlaybackPolicy::SIGNED ]
			: [ MuxPhp\Models\PlaybackPolicy::_PUBLIC ];
	
		try {
			$uploadsApi = muxvideo_api_client_init_upload();
			$createAssetRequest = new MuxPhp\Models\CreateAssetRequest( [ "playback_policy" => $asset_privacy ] );
			$createUploadRequest = new MuxPhp\Models\CreateUploadRequest( [ 
				"timeout" => 3600,
				"new_asset_settings" => $createAssetRequest,
				"cors_origin" => "*" ] );
			$upload = $uploadsApi->createDirectUpload( $createUploadRequest );
			$data_response = json_decode( $upload );
			$url = $data_response->data->url;
			wp_send_json( $url );
		} catch (Exception $e) {
			error_log( 'Exception when calling AssetsApi->deleteAsset: ', $e->getMessage() );
		}
		wp_die();
	}
}

/**
 * Refresh asset list dynamically
 */
add_action( 'wp_ajax_muxvideo_refresh_asset_list', 'muxvideo_refresh_asset_list' );
add_action( 'wp_ajax_nopriv_muxvideo_refresh_asset_list', 'muxvideo_refresh_asset_list' );
function muxvideo_refresh_asset_list() {
	try {
		muxvideo_display_assets_list();
		wp_die();
	} catch (\Exception $e) {
		error_log( 'Exception when calling muxvideo_refresh_asset_list: ', $e->getMessage() );
	}
}
