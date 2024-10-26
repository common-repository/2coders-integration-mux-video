<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

?>

<div class="modal-wrapper modal-transition shortcode-customizer__container">
	<h3>
		<?php esc_html_e( 'sc_customizer_title', '2coders-integration-mux-video' ); ?>
	</h3>
	<div class="">
		<?php
		if(!isset($_REQUEST['nonce-form']) || !wp_verify_nonce($_REQUEST['nonce-form'], '_wpnonce')) {
			try {
				$asset_data = array_combine(
					array_map(
						function ($key) {
							return sanitize_key( $key );
						},
						array_keys( $_POST['asset_data'] )
					),
					array_map( 'sanitize_text_field', $_POST['asset_data'] )
				);
			} catch (\Throwable $th) {
				error_log( "Error: sanitizing shortcode customizer data" . $th->getMessage() );
			}
		}

		$public_img = 'https://image.mux.com/' . $asset_data['playback_id'] . '/thumbnail.png?width=258&height=160&fit_mode=smartcrop&time=';
		$signed_img = 'https://image.mux.com/' . $asset_data['playback_id'] . '/thumbnail.png?token=' . $asset_data['jwt_t'] . '&time=';
		$current_img = ( $asset_data['policy'] == 'signed' ) ? $signed_img : $public_img;
		?>
		<ul class="tabs-nav">
			<li id="nav-tab-1" class="tab-active"><a href="#tab-1">
					<?php esc_html_e( 'sc_customizer_tab_1', '2coders-integration-mux-video' ) ?>
				</a></li>
			<li id="nav-tab-2"><a href="#tab-2">
					<?php esc_html_e( 'sc_customizer_tab_2', '2coders-integration-mux-video' ); ?>
				</a></li>
			<li id="nav-tab-3"><a href="#tab-3">
					<?php esc_html_e( 'sc_customizer_tab_3', '2coders-integration-mux-video' ); ?>
				</a></li>
		</ul>
		<div class="tabs-container">
			<form id="shortcode-customizer-form">
				<div class="inner-container">
					<div id="tab-1" class="tab">
						<div class="shortcode-customizer__container__row">
							<label>
								<?php esc_html_e( 'sc_customizer_video_title', '2coders-integration-mux-video' ); ?>
							</label>
							<input type="text" name="video-title">
						</div>
						<div class="shortcode-customizer__container__row">
							<label>
								<?php esc_html_e( 'sc_customizer_thumbnail_time', '2coders-integration-mux-video' ); ?>
							</label>
							<img id="thumbnailImage" src="<?php echo esc_url( $current_img ); ?>0" alt="Mux image"
								class="image image-preview">
							<div>
								<input type="range" name="thumbnail-time" class="choose-image-preview" value="0" min="0"
									max="<?php echo esc_attr( round( $asset_data['duration'] ) ) ?>" autocomplete="off"
									data-jwt="" />
								<output id="thumbnailTimeValue" class="time-output">00:00:00</output>
							</div>
						</div>
					</div>
					<div id="tab-2" class="tab">
						<div class="toggle-container">
							<?php echo muxvideo_display_player_options() ?>
						</div>
					</div>
					<div id="tab-3" class="tab">
						<?php echo muxvideo_display_player_other_data( $asset_data, $current_img ) ?>
					</div>

					<input type="hidden" id="" name="playback-id"
						value="<?php echo esc_attr( $asset_data['playback_id'] ) ?>" />
					<input type="hidden" id="" name="privacy" value="<?php echo esc_attr( $asset_data['policy'] ) ?>" />
				</div>
				<div class="sc-buttons-container">
					<a id="sc-customizer-cancel-button" onclick="hideShortcodeCustomizer()"
						class="btn btn-cancel btn-tertiary">
						<?php esc_html_e( 'sc_customizer_cancel_button', '2coders-integration-mux-video' ); ?>
					</a>
					<button type="submit" id="sc-customizer-create-button" class="btn btn-primary">
						<?php esc_html_e( 'sc_customizer_create_button', '2coders-integration-mux-video' ); ?>
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>

	var debounceTimer;

	$('.choose-image-preview').on('change', function () {
		let element = $(this);
		let imageUrl = '';
		let imageJwt = '';
		let img = element.parents('.shortcode-customizer__container__row').children('img')
		img.addClass('blur')
		clearTimeout(debounceTimer);
		debounceTimer = setTimeout(function () {
			<?php if ( $asset_data['policy'] == 'signed' ) { ?>
				let assetData = '<?php echo wp_json_encode( $asset_data ) ?>';
				let defaultUrl = '<?php echo html_entity_decode( esc_url( "https://image.mux.com/" . $asset_data['playback_id'] . "/thumbnail.png?token=" ) ) ?>';

				getJwtDinamically(assetData, element.val(), element).then(function (response) {
					imageJwt = response
					imageUrl = defaultUrl + imageJwt;
					img.attr('src', imageUrl);
					setTimeout(() => {
						img.removeClass('blur')
					}, 1500);
				})
			<?php } else { ?>
				imageUrl = '<?php echo html_entity_decode( esc_url( $current_img ) ); ?>' + element.val();
				setTimeout(() => {
					img.removeClass('blur')
				}, 1500);
				img.attr('src', imageUrl)


			<?php } ?>
		}.bind(element), 100);
		checkImageUrlForSignedAssets('.shortcode-customizer .image-preview')
	});

	$('.choose-image-preview').on('input', function () {
		let element = $(this);
		updateThumbnailTime(element, element.val());
	})

	function updateThumbnailTime(element, seconds) {
		var hours = Math.floor(seconds / 3600);
		var minutes = Math.floor((seconds % 3600) / 60);
		var remainingSeconds = seconds % 60;

		var formattedTime = pad(hours) + ':' + pad(minutes) + ':' + pad(remainingSeconds);

		element.siblings('output').text(formattedTime);
	}

	function pad(number) {
		return (number < 10 ? '0' : '') + number;
	}
</script>