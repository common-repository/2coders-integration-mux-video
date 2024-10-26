<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

?>

<div class="toggle-container__column">
	<div class="toggle-container__row">
		<input type="checkbox" id="toggle-autoplay" name="autoplay" value="true" checked />
		<label for="toggle-autoplay">
			<?php echo esc_html__( 'Yes', '2coders-integration-mux-video' ) ?>
		</label>
		<span>
			<?php echo esc_html__( 'sc_customizer_player_options_autoplay', '2coders-integration-mux-video' ) ?>
		</span>
	</div>
	<div class="toggle-container__row">
		<input type="checkbox" id="toggle-mute" name="mute" />
		<label for="toggle-mute">
			<?php echo esc_html__( 'Yes', '2coders-integration-mux-video' ) ?>
		</label>
		<span>
			<?php echo esc_html__( 'sc_customizer_player_options_mute', '2coders-integration-mux-video' ) ?>
		</span>
	</div>
	<div class="toggle-container__row">
		<input type="checkbox" id="toggle-hotkeys" name="hotkeys" value="true" checked />
		<label for="toggle-hotkeys">
			<?php echo esc_html__( 'Yes', '2coders-integration-mux-video' ) ?>
		</label>
		<span>
			<?php echo esc_html__( 'sc_customizer_player_options_hotkeys', '2coders-integration-mux-video' ) ?>
		</span>
	</div>
	<div class="toggle-container__row">
		<input type="checkbox" id="toggle-looping-content" name="looping-content" />
		<label for="toggle-looping-content">
			<?php echo esc_html__( 'Yes', '2coders-integration-mux-video' ) ?>
		</label>
		<span>
			<?php echo esc_html__( 'sc_customizer_player_options_looping_content', '2coders-integration-mux-video' ) ?>
		</span>
	</div>
</div>

<div class="toggle-container__column">
	<div class="toggle-container__row">
		<input type="checkbox" id="toggle-controls" name="controls" value="true" checked />
		<label for="toggle-controls">
			<?php echo esc_html__( 'Yes', '2coders-integration-mux-video' ) ?>
		</label>
		<span>
			<?php echo esc_html__( 'sc_customizer_player_options_show_controls', '2coders-integration-mux-video' ) ?>
		</span>
	</div>
	<div id="related-controls-container" class="is-active">
		<div class="toggle-container__row">
			<input type="checkbox" id="toggle-closed-captions" name="closed-captions" />
			<label for="toggle-closed-captions">
				<?php echo esc_html__( 'Yes', '2coders-integration-mux-video' ) ?>
			</label>
			<span>
				<?php echo esc_html__( 'sc_customizer_player_options_closed_captions', '2coders-integration-mux-video' ) ?>
			</span>
		</div>
		<div class="toggle-container__row">
			<input type="checkbox" id="toggle-seek-buttons" name="seek-buttons" value="true" checked />
			<label for="toggle-seek-buttons">
				<?php echo esc_html__( 'Yes', '2coders-integration-mux-video' ) ?>
			</label>
			<span>
				<?php echo esc_html__( 'sc_customizer_player_options_seek_buttons', '2coders-integration-mux-video' ) ?>
			</span>
		</div>
		<div class="toggle-container__row seek-buttons-container">
			<label class="no-toggle">
				<?php echo esc_html__( 'sc_customizer_player_options_seek_buttons_duration', '2coders-integration-mux-video' ); ?>
			</label>
			<div>
				<input type="range" name="seek-buttons-duration" value="5" min="5" max="60" step="5" autocomplete="off"
					onmousemove="seekButtonsDuration.value=value" />
				<output id="seekButtonsDuration">5</output><span>
					<?php echo esc_html__( 'secs', '2coders-integration-mux-video' ) ?>
				</span>
			</div>
		</div>
	</div>
</div>

<script>
	jQuery(document).ready(function ($) {
		$('#toggle-controls').on('change', function () {
			$('#related-controls-container').toggleClass('is-disabled');
		});
	});
</script>