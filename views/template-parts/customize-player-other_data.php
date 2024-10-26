<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

?>

<div class="shortcode-customizer__container__row">
	<label>
		<?php esc_html_e( 'sc_customizer_size_title', '2coders-integration-mux-video' ); ?>
	</label>
	<div class="shortcode-customizer__container__player-size">
		<div>
			<input type="checkbox" name="full-width" value="true" selected>
			<?php esc_html_e( 'sc_customizer_full_width', '2coders-integration-mux-video' ); ?>
		</div>
		<div id="sc-max-width-container">
			<span>
				<?php esc_html_e( 'sc_customizer_max_width', '2coders-integration-mux-video' ); ?>
			</span>
			<input type="number" name="max-width" min="0" max="2000">
			<span> px</span>
		</div>
	</div>
</div>
<hr>
<div class="shortcode-customizer__container__row">
	<label>
		<?php esc_html_e( 'sc_customizer_aspect_ratio', '2coders-integration-mux-video' ); ?>
	</label>
	<div class="shortcode-customizer__container__aspect-ratio">
		<div class="shortcode-customizer__container__aspect-ratio__wrapper">
			<div class="shortcode-customizer__container__aspect-ratio__example" style="width: 97px; height: 58px;">16:9
			</div>
			<div><input type="radio" name="aspect-ratio" value="16 / 9" checked></div>
		</div>
		<div class="shortcode-customizer__container__aspect-ratio__wrapper">
			<div class="shortcode-customizer__container__aspect-ratio__example" style="width: 58px; height: 58px;">1:1
			</div>
			<div><input type="radio" name="aspect-ratio" value="1 / 1"></div>
		</div>
		<div class="shortcode-customizer__container__aspect-ratio__wrapper">
			<div class="shortcode-customizer__container__aspect-ratio__example" style="width: 58px; height: 97px;">9:16
			</div>
			<div><input type="radio" name="aspect-ratio" value="9 / 16"></div>
		</div>
	</div>
</div>
<hr>
<div class="shortcode-customizer__container__row">
	<label>
		<?php esc_html_e( 'sc_customizer_start_time', '2coders-integration-mux-video' ); ?>
	</label>
	<img id="startImage" src="<?php echo esc_url( $current_img ); ?>0" alt="Mux image" class="image image-preview">
	<div>
		<input type="range" name="start-time" class="choose-image-preview" value="0" min="0"
			max="<?php echo esc_attr( round( $asset_data['duration'] ) ) ?>" autocomplete="off" />
		<output id="startTimeValue">00:00:00</output>
	</div>
</div>
<hr>
<div class="shortcode-customizer__container__row">
	<label>
		<?php esc_html_e( 'sc_customizer_max_resolution', '2coders-integration-mux-video' ); ?>
	</label>
	<?php
	$stream_url = 'https://stream.mux.com/' . $asset_data['playback_id'] . '.m3u8?token=' . $asset_data['jwt'];
	$resolutions = muxvideo_get_resolutions_by_stream_url( $stream_url );
	?>
	<select name="max-resolution" id="max-resolution"
		class="shortcode-customizer__container__max-resolution <?php echo esc_attr( ( sizeof( $resolutions ) <= 1 ) ? 'disabled' : '' ) ?>">
		<?php if ( ! empty( $resolutions ) ) :
			foreach ( $resolutions as $resolution ) : ?>
				<option value="<?php echo esc_attr( $resolution ) ?>">
					<?php echo $resolution == $resolutions[0] ? 'Auto (' . esc_html( $resolution ) . ')' : esc_html( $resolution ) ?>
				</option>
			<?php endforeach; ?>
		<?php else : ?>
			<option value="<?php echo esc_attr( $asset_data['max_resolution'] ) ?>">
				<?php echo 'Auto (' . esc_html( muxvideo_convert_resolution( $asset_data['max_resolution'] ) ) . ')' ?>
			</option>
		<?php endif; ?>
	</select>
</div>

<script>
	$('input[name="full-width"]').on('change', function () {
		if ($(this).is(':checked')) {
			$('#sc-max-width-container').addClass('is-disabled');
		} else {
			$('#sc-max-width-container').removeClass('is-disabled');
		}
	});
</script>