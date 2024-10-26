<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

muxvideo_enqueue_script_upchunk();
require_once MUXVIDEO_PLUGIN_DIR . '/includes/functions.php';
$uploadsApi = muxvideo_api_client_init_upload();
$admin_url_ajax = admin_url( "admin-ajax.php" );

$createAssetRequest = new MuxPhp\Models\CreateAssetRequest( [ "playback_policy" => [ MuxPhp\Models\PlaybackPolicy::_PUBLIC ] ] );
$createUploadRequest = new MuxPhp\Models\CreateUploadRequest( [ "timeout" => 3600, "new_asset_settings" => $createAssetRequest, "cors_origin" => "*" ] );
$upload = $uploadsApi->createDirectUpload( $createUploadRequest );
$playback_restrictions_data = muxvideo_get_data_playback_restrictions();
$data_response = json_decode( $upload );
$url = $data_response->data->url;
$muxvideo_options = get_option( "muxvideo_options" );

$supported_formats = "video/mp4,
	video/x-m4v,
	video/x-msvideo,
	video/avi,
	video/quicktime,
	video/mpeg,
	video/x-ms-wmv,
	video/x-flv,
	video/webm,
	video/x-matroska,
	video/ogg,
	video/3gpp,
	video/3gpp2,
	.mkv,
	audio/mpeg,
	audio/mp3,
	audio/wav,
	audio/ogg,
	audio/mp4,
	audio/flac,
	audio/amr,
	audio/x-m4a,
	audio/x-m4r,
	.mp2"
	?>
<div class="muxvideo-page">
	<?php muxvideo_get_header(); ?>

	<?php muxvideo_get_pro_version_banner(); ?>

	<div class="muxvideo-container">
		<div class="content-container inner-container">
			<div class="muxvideo-asset-upload container">
				<div class="header-container">
					<h1>
						<?php echo esc_html__( 'asset_upload_title_page', '2coders-integration-mux-video' ); ?>
					</h1>
					<a href="admin.php?page=muxvideo-asset-list" class="btn btn-primary">
						<?php echo esc_html__( 'asset_upload_button_view_assets', '2coders-integration-mux-video' ); ?>
					</a>
				</div>
				<div class="content-page">
					<p class="title-switch-privacy">
						<?php esc_html_e( 'asset_upload_privacy_title', '2coders-integration-mux-video' ); ?>
					</p>
					<div class="switches-container">
						<input type="radio" id="switchProtected" name="switchPrivacy" value="signed"
							class="<?php echo is_null( $playback_restrictions_data ) ? 'disabled' : ''; ?>" />
						<input type="radio" id="switchPublic" name="switchPrivacy" value="public" checked="checked" />
						<label for="switchProtected"
							class="<?php echo ( is_null( $playback_restrictions_data ) || $muxvideo_options["muxvideo_signing_data"] == "" ) ? 'disabled' : ''; ?>">
							<?php esc_html_e( 'asset_upload_privacy_switcher_signed', '2coders-integration-mux-video' ); ?>
						</label>
						<label for="switchPublic">
							<?php esc_html_e( 'asset_upload_privacy_switcher_public', '2coders-integration-mux-video' ); ?>
						</label>
						<div class="switch-wrapper">
							<div class="switch">
								<div class="switch__protected">
									<?php esc_html_e( 'asset_upload_privacy_switcher_signed', '2coders-integration-mux-video' ); ?>
								</div>
								<div class="switch__public">
									<?php esc_html_e( 'asset_upload_privacy_switcher_public', '2coders-integration-mux-video' ); ?>
								</div>
							</div>
						</div>
					</div>

					<form action="" method="POST" id="upload-form" enctype="multipart/form-data">
						<div class="droparea">
							<div class="icon-column">
								<img class="icon"
									src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../assets/images/upload-icon.svg' ); ?>"
									alt="upload icon">
							</div>
							<div id="input-msg">
								<p class="not-dragging-text">
									<strong>
										<?php echo esc_html__( 'asset_upload_message_drop_video', '2coders-integration-mux-video' ); ?>
									</strong>
									<?php echo esc_html__( 'asset_upload_message_drop_video_2', '2coders-integration-mux-video' ); ?>
									<br>
									<?php echo esc_html__( 'asset_upload_message_or', '2coders-integration-mux-video' ); ?> <br>
									<a class="btn btn-primary">
										<?php echo esc_html__( 'asset_upload_message_pick_file', '2coders-integration-mux-video' ); ?>
									</a>
								</p>
								<p class="dragging-text">
									<?php echo esc_html__( 'asset_upload_message_drop_video_hovered', '2coders-integration-mux-video' ); ?>
								</p>
							</div>
							<input class="input" type="file" id="picker"
								accept="<?php echo esc_attr( $supported_formats ); ?>" />
							<input type="hidden" value="<?php echo esc_url( $url ); ?>" name="upload-url" id="upload-url">
						</div>
					</form>
				</div>

				<div class="instructions">
					<p class="instructions__title">
						<?php echo esc_html__( 'common_instructions_title', '2coders-integration-mux-video' ); ?>
					</p>
					<p class="instructions__content">
						<?php echo wp_kses( __( 'asset_upload_instructions_message_first', '2coders-integration-mux-video' ), array( 'a' => array( 'href' => array(), 'title' => array() ) ) ); ?>
					</p>
					<p class="instructions__content">
						<?php echo esc_html__( 'asset_upload_instructions_message_second', '2coders-integration-mux-video' ); ?>
					</p>
				</div>
			</div>

		</div>

		<?php muxvideo_get_sidebar(); ?>
		<div id="history-container" class="history-container">
			<div id="progress-container" class="progress-container">
			</div>
			<div id="uploaded-container" class="uploaded-container">
			</div>
		</div>
	</div>
	<?php echo muxvideo_add_disclaimer_in_footer(); ?>
</div>

<script>
	function resetUploadUrl(upload, uploadItemId, scb = null, ecb = null) {
		let privacy = $('.switches-container input[name="switchPrivacy"]:checked').val();;
		$.ajax({
			type: "POST",
			url: '<?php echo esc_url( $admin_url_ajax ); ?>',
			data: { action: 'muxvideo_reset_upload_url', asset_privacy: privacy },
			error: function (xhr, status, error) {
				$('#upload-url').val("");
				console.error('Error restoring upload instance');
				if (ecb) {
					ecb("")
				}
				isResetUploadErrored = true;
				currentUploadObj = upload;
				currentUploadItemId = uploadItemId
			},
			success: function (uploadUrl) {
				$('#upload-url').val(uploadUrl);
				$('#upload-form').trigger("reset");
				$('#picker').removeAttr('disabled');
				let statusElement = document.getElementById('item-status');
				if ($('#' + uploadItemId).data('status') === 'cancelled') {
					statusElement.innerHTML = `
						<i class="delete-cancelled-upload fas fa-times-circle fade-in-transition"></i>`
					$('#' + uploadItemId + ' .upload-item__progress span').html('<span>' + window.getTranslations.translations.asset_upload_asset_status_cancelled + '</span>');
				} else {
					statusElement.innerHTML = `<i style="color:#0038D2" class="asset-uploaded fas fa-check-circle fade-in-transition"></i>`;
				}
				$("#uploaded-container").append($(".upload-item"));

				if (scb) {
					scb(uploadUrl)
				}
				isUploading = false
				$('#picker').removeAttr('disabled');
			}
		});
	}

	function deleteSingleAsset(asset) {
		let assetId = $(asset).val();
		let assetContainer = $('#' + assetId);
		$.ajax({
			type: "POST",
			url: '<?php echo esc_url( $admin_url_ajax ); ?>',
			data: { action: 'muxvideo_delete_asset', asset_id: assetId },
			error: function (xhr, status, error) {
				console.log(error);
			},
			success: function () {
				assetContainer.hide('slow', function () { assetContainer.remove(); });
			}
		});
		event.preventDefault();
	}

	function createNewUploadInstance() {
		let privacy = $('.switches-container input[name="switchPrivacy"]:checked').val();
		$.ajax({
			type: "POST",
			url: '<?php echo esc_url( $admin_url_ajax ); ?>',
			data: { action: 'muxvideo_reset_upload_url', asset_privacy: privacy },
			error: function (xhr, status, error) {
				console.error('Error restoring upload instance');
			},
			success: function (uploadUrl) {
				$('#upload-url').val(uploadUrl);
			}
		});
	}
</script>