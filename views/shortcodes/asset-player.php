<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

function muxvideo_asset_player_shortcode( $atts = [], $tag = '' ) {
	muxvideo_enqueue_script_player();
	ob_start();
	// normalize attribute keys, lowercase
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );
	// override default attributes with user attributes
	try {
		$playback_restrictions_data = muxvideo_get_data_playback_restrictions();
	} catch (\Throwable $th) {
		error_log( 'Error when calling muxvideo_get_data_playback_restrictions(): ', $th->getMessage() );
	}

	$asset_atts_raw = muxvideo_create_data_object( $atts, $tag );

	$jwt =
		$asset_atts_raw['privacy'] == 'signed'
		? muxvideo_get_json_web_token( $asset_atts_raw['playback-id'], $asset_atts_raw['signing_id'], $asset_atts_raw['signing_private_key'], 'v', $playback_restrictions_data->id, intval( $asset_atts_raw['thumbnail-time'] ) )
		: '';

	$asset_atts = array_map( function ($value, $key) {
		return $key === $value ? '' : $value;
	}, $asset_atts_raw, array_keys( $asset_atts_raw ) );

	$asset_atts_raw = array_combine( array_keys( $asset_atts_raw ), $asset_atts );
	$asset_atts = array_map( function ($value) {
		if ( $value === 'true' ) {
			return true;
		}
		return $value === 'false' ? false : $value;
	}, $asset_atts_raw );
	?>
	<mux-player stream-type="on-demand" metadata-viewer-user-id="user-id-007" <?php echo muxvideo_get_player_metadata( $asset_atts, $jwt ); ?>></mux-player>
	<?php
	return ob_get_clean();
}

function muxvideo_create_data_object( $atts, $tag ) {
	return shortcode_atts(
		array(
			'playback-id' => 'playback-id',
			'video-title' => 'video-title',
			'thumbnail-time' => 'thumbnail-time',
			'autoplay' => 'autoplay',
			'mute' => 'mute',
			'hotkeys' => 'hotkeys',
			'controls' => 'controls',
			'seek-buttons' => 'seek-buttons',
			'seek-buttons-duration' => 'seek-buttons-duration',
			'full-width' => 'full-width',
			'max-width' => 'max-width',
			'aspect-ratio' => 'aspect-ratio',
			'start-time' => 'start-time',
			'max-resolution' => 'max-resolution',
			'looping-content' => 'looping-content',
			'closed-captions' => 'closed-captions',
			'privacy' => 'privacy',
			'signing_id' => get_option( 'muxvideo_options' )['muxvideo_signing_data']['id'],
			'signing_private_key' => get_option( 'muxvideo_options' )['muxvideo_signing_data']['private_key'],
		), $atts, $tag
	);
}

function muxvideo_get_player_metadata($asset_atts, $jwt){ 
	ob_start();
	?>
	playback-id="<?php echo esc_attr($asset_atts['playback-id']); ?>"
	metadata-video-title="<?php echo esc_attr($asset_atts['video-title']); ?>"
	<?php echo !empty($asset_atts['video-title']) ? 'title="'. esc_attr($asset_atts['video-title']) . '"' : '' ?>
	start-time="<?php echo esc_attr($asset_atts['start-time']); ?>"
	<?php echo $asset_atts['autoplay'] ? 'autoplay' : '' ?>
	forward-seek-offset="<?php echo esc_attr($asset_atts['seek-buttons-duration']); ?>"
	backward-seek-offset="<?php echo esc_attr($asset_atts['seek-buttons-duration']); ?>"
	<?php echo $asset_atts['mute'] ? 'muted' : '' ?>
	<?php echo !$asset_atts['hotkeys'] ? 'nohotkeys' : '' ?>
	<?php echo $asset_atts['looping-content'] ? 'loop' : '' ?>
	thumbnail-time="<?php echo esc_attr($asset_atts['thumbnail-time']) ?>"
	closed-captions="<?php echo esc_attr($asset_atts['closed-captions']) ?>"
	<?php if($asset_atts['max-resolution'] != '1080p' && $asset_atts['max-resolution'] != '720p'):
		$asset_atts['max-resolution'] = '';
	endif; ?>
	max-resolution="<?php echo esc_attr($asset_atts['max-resolution']) ?>"
	<?php if(!empty($jwt)):
			echo 'playback-token="' . esc_attr($jwt) .'"';
			echo 'thumbnail-token="' . esc_attr($jwt) .'"';
			echo 'storyboard-token="' . esc_attr($jwt) .'"';
	endif; ?>
	accent-color="#333333"
	style="
		<?php 
		echo $asset_atts['full-width'] == 'true' 
			? 'width: 100%; max-width:100%;' 
			: 'max-width: ' . esc_attr($asset_atts['max-width']) . 'px;';  
		echo !empty($asset_atts['aspect-ratio']) 
			? 'aspect-ratio: ' .  esc_attr($asset_atts['aspect-ratio']) . ';'
			: ''; 
		echo !$asset_atts['controls'] 
			? '--controls: none;'
			: ''; 
		echo !$asset_atts['seek-buttons'] 
			? '--center-controls: none; --top-captions-button: none;'
			: ''; 
		?>
	"
<?php 
	return ob_get_clean();
}