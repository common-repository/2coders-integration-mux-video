<?php
/**
 * Get plugin option.
 *
 * @since 1.0.0
 *
 * @param string $key Option key.
 * @return mixed Option value.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

function muxvideo_plugin_option( $key ) {
	if ( empty( $key ) ) {
		return false;
	}

	$plugin_options = wp_parse_args( (array) get_option( 'muxvideo_options' ), muxvideo_get_default_options() );

	$value = null;

	if ( isset( $plugin_options[ $key ] ) ) {
		$value = $plugin_options[ $key ];
	}

	return $value;
}

/**
 * Get default plugin options.
 *
 * @since 1.0.0
 *
 * @return array Default plugin options.
 */
function muxvideo_get_default_options() {
	$default = array();

	// Token.
	$default['muxvideo_token_id'] = '';
	$default['muxvideo_token_secret'] = '';

	return $default;
}


/**
 * Register token_id field.
 */
function muxvideo_field_token_id_callback() {
	$token_id = muxvideo_plugin_option( 'muxvideo_token_id' );
	?>
	<input type="text" name="muxvideo_options[muxvideo_token_id]" id="muxvideo_token_id" class="regular-text"
		value="<?php echo esc_attr( $token_id ); ?>" />
	<i class="error-icon fas fa-exclamation-circle" style="display:none"></i>
	<p class="error-msg" style="display:none">
		<?php esc_html_e( 'admin_message_format_error', '2coders-integration-mux-video' ) ?>
	</p>
	<?php
}

/**
 * Register token_secret field.
 */
function muxvideo_field_token_secret_callback() { ?>
	<input type="password" name="muxvideo_options[muxvideo_token_secret]" id="muxvideo_token_secret" class="regular-text"
		value="" />
	<i class="error-icon fas fa-exclamation-circle" style="display:none"></i>
	<p class="error-msg" style="display:none">
		<?php esc_html_e( 'admin_message_format_error', '2coders-integration-mux-video' ) ?>
	</p>
<?php }

function muxvideo_content_settings_section_callback() {
	global $auth_response;

	if ( $auth_response !== 401 ) :
		?>
		<div id="edit-keys-btn" class="btn-tertiary btn-edit-keys">
			<img class="ui-icon" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../assets/images/edit-icon.svg' ); ?>"
				alt="Edit Icon">
			<span>
				<?php esc_html_e( 'settings_button_edit_keys', '2coders-integration-mux-video' ); ?>
			</span>
		</div>
		<?php
	endif;
}

/**
 * Register plugin option fields
 */
function muxvideo_register_plugin_option_fields() {
	register_setting( 'muxvideo_options_group', 'muxvideo_options', 'muxvideo_validate_plugin_options' );

	add_settings_section( 'muxvideo_api_key_section', esc_html__( 'admin_title_key_section', '2coders-integration-mux-video' ), 'muxvideo_content_settings_section_callback', 'muxvideo-options' );

	add_settings_field( 'muxvideo_token_id', esc_html__( 'admin_title_site_key', '2coders-integration-mux-video' ), 'muxvideo_field_token_id_callback', 'muxvideo-options', 'muxvideo_api_key_section' );

	add_settings_field( 'muxvideo_token_secret', esc_html__( 'admin_title_secret_key', '2coders-integration-mux-video' ), 'muxvideo_field_token_secret_callback', 'muxvideo-options', 'muxvideo_api_key_section' );
}

add_action( 'admin_init', 'muxvideo_register_plugin_option_fields' );

/**
 * Validate plugin options.
 * @param array $input Options.
 * @return array Validated options.
 */

function muxvideo_validate_plugin_options( $input ) {
	$input['muxvideo_token_id'] = sanitize_text_field( $input['muxvideo_token_id'] );
	$input['muxvideo_token_secret'] = sanitize_text_field( $input['muxvideo_token_secret'] );

	return $input;
}

/**
 * Render views functions
 */
function muxvideo_settings_page() {
	require MUXVIDEO_PLUGIN_DIR . '/views/settings.php';
}

function muxvideo_asset_list_page() {
	require MUXVIDEO_PLUGIN_DIR . '/views/asset-list.php';
}

function muxvideo_asset_upload_page() {
	require MUXVIDEO_PLUGIN_DIR . '/views/asset-upload.php';
}