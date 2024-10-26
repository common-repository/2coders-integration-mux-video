<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

require_once MUXVIDEO_PLUGIN_DIR . '/includes/functions.php';

global $auth_response;
$playback_restrictions_data = muxvideo_get_data_playback_restrictions();
$request_data['allowed_domains'] = $request_data['allowed_domains'] ?? array();
$allowed_domains = array();

if ( is_array( $request_data['allowed_domains'] ) ) {
	foreach ( $request_data['allowed_domains'] as $domain ) {
		$allowed_domains[] = $domain;
	}
}

$allow_no_referrer = isset( $request_data['allow_no_referrer'] ) ? $request_data['allow_no_referrer'] : '';

$obj_data = array(
	'allowed_domains' => $allowed_domains,
	'allow_no_referrer' => $allow_no_referrer
);
$muxvideo_options = get_option( "muxvideo_options" );
$signing_data = $muxvideo_options["muxvideo_signing_data"] ?? '';

?>

<div class="muxvideo-page">
	<?php muxvideo_get_header(); ?>

	<?php muxvideo_get_pro_version_banner(); ?>

	<div class="muxvideo-container">
		<div class="content-container inner-container">
			<ul class="tabs-nav">
				<li id="nav-tab-1" class="tab-active"><a href="#tab-1">
						<?php esc_html_e( 'admin_title_key_section', '2coders-integration-mux-video' ) ?>
					</a></li>
				<li id="nav-tab-2" <?php echo ( $auth_response == 401 || $signing_data == "" ) ? 'class="disabled"' : ''; ?>>
					<a href="#tab-2">
						<?php esc_html_e( 'settings_security_section_title', '2coders-integration-mux-video' ); ?>
					</a></li>
			</ul>
			<div class="tabs-container">
				<div id="tab-1" class="tab">
					<div class="muxvideo-settings muxvideo-settings__auth content">
						<div class="inside">
							<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post"
								class="<?php echo esc_attr( $auth_response != 401 ? 'config-ready' : '' ); ?>">
								<?php settings_fields( 'muxvideo_options_group' ); ?>
								<?php do_settings_sections( 'muxvideo-options' ); ?>
								<?php
									$actual_url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; 
									$nonce_url = wp_nonce_url( $actual_url, '_wpnonce');
								?>
								<input type="hidden" name="nonce-form" value="<?php echo $nonce_url ?>" />
								<div class="auth-status-container">
									<?php if ( $muxvideo_options !== false ) : ?>
										<?php if ( $auth_response == 401 ) : ?>
											<p id="unauth-msg" style="color: red"><i class="fas fa-exclamation-circle"></i>&nbsp
												<?php echo esc_html__( 'settings_message_unauth', '2coders-integration-mux-video' ); ?>
											</p>
										<?php else : ?>
											<div id="success-msg" class="success-container">
												<p class="success-container__title"><i class="fas fa-check-circle"></i>&nbsp
													<?php echo esc_html__( 'settings_message_auth_title', '2coders-integration-mux-video' ); ?>
												</p>
												<p>
													<?php echo esc_html__( 'settings_message_auth_content', '2coders-integration-mux-video' ); ?>
													<a class="btn-tertiary"
														href="<?php echo esc_url( admin_url( 'admin.php?page=muxvideo-asset-upload' ) ); ?>">
														<?php echo esc_html__( 'settings_button_upload_video', '2coders-integration-mux-video' ); ?>
													</a>
												</p>
											</div>
										<?php endif; ?>
									<?php endif; ?>
								</div>
								<?php submit_button( esc_html__( 'settings_button_save', '2coders-integration-mux-video' ), 'btn btn-save', 'submit', true, 'disabled="disabled"' ); ?>
							</form>

						</div>
					</div>
					<?php if ( $auth_response !== 401 && $signing_data == "" ) : ?>
						<div class="instructions instructions__warning">
							<p>
								<?php esc_html_e( 'settings_message_signing_data_empty', '2coders-integration-mux-video' ) ?>
							</p>
						</div>
					<?php elseif ( isset( $muxvideo_options ) && empty( $muxvideo_options ) && $muxvideo_options == false ) : ?>
						<div class="instructions instructions__onboarding">
							<p>
								<?php echo wp_kses( __( 'settings_message_onboarding', '2coders-integration-mux-video' ), array( 'a' => array( 'href' => array(), 'title' => array() ) ) ); ?>
							</p>
						</div>
					<?php endif; ?>
					<div class="instructions">
						<p class="instructions__title">
							<?php echo esc_html__( 'common_instructions_title', '2coders-integration-mux-video' ); ?>
						</p>
						<p class="instructions__content">
							<?php echo wp_kses( __( 'asset_upload_instructions_message_first', '2coders-integration-mux-video' ), array( 'a' => array( 'href' => array(), 'title' => array() ) ) ); ?>
						</p>
						<p class="instructions__content">
							<?php echo esc_html__( 'settings_instructions_message_second', '2coders-integration-mux-video' ); ?>
						</p>
					</div>
				</div>
				<?php if ( $auth_response != 401 && $signing_data != "" ) : ?>
					<div id="tab-2" class="tab">
						<div class="muxvideo-settings-security">
							<p class="muxvideo-settings-security__title">
								<?php esc_html_e( 'settings_security_title', '2coders-integration-mux-video' ) ?>
							</p>
							<p class="muxvideo-settings-security__description">
								<?php esc_html_e( 'settings_security_description', '2coders-integration-mux-video' ) ?>
							</p>

							<form id="tag-form">
								<div id="tag-container" class="tag-container">
									<input type="text" id="tag-input">
									<div class="tag-container__tags">
										<?php if ( $playback_restrictions_data->allowed_domains ) :
											foreach ( $playback_restrictions_data->allowed_domains as $domain ) : ?>
												<div class="tag"><span class="tag-text">
														<?php echo esc_html( $domain ) ?>
													</span><span class="tag-remove">x</span></div>
											<?php endforeach; ?>
										<?php endif; ?>
									</div>
								</div>
								<div class="muxvideo-settings-security__referrer-container">
									<input type="checkbox" id="switch-referrer" name="allowNoReferrer" <?php if ( $playback_restrictions_data->allow_no_referrer ) {
										echo 'checked';
									} ?> /><label
										for="switch-referrer">
										<?php esc_html_e( 'settings_security_switch_referrer_input', '2coders-integration-mux-video' ) ?>
									</label><span>
										<?php esc_html_e( 'settings_security_switch_referrer_input', '2coders-integration-mux-video' ) ?>
									</span>
								</div>
								<button type="submit" id="submit-security-settings" class="btn btn-save">
									<?php esc_html_e( 'settings_button_save', '2coders-integration-mux-video' ); ?>
								</button>
							</form>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php muxvideo_get_sidebar(); ?>
	</div>
	<?php echo muxvideo_add_disclaimer_in_footer(); ?>
</div>

<script>

	function createUpdatePlaybackRestrictions(formData) {
		let saveButtonText = $('#submit-security-settings').html();
		displayLoader('#submit-security-settings', '');
		$.ajax({
			type: "POST",
			url: '<?php echo esc_url( admin_url( "admin-ajax.php" ) ); ?>',
			data: { action: 'muxvideo_create_update_playback_restrictions', form_data: formData },
			error: function (xhr, status, error) {
				console.log(error);
			},
			success: function (res) {
				dataHasBeenModified = false;
				throwNotice(window.getTranslations.translations.common_notice_saved_security_settings)
				$('#tag-form :submit').html(saveButtonText);
			}
		});
		event.preventDefault();
	}
</script>