<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

function muxvideo_format_asset_duration( $duration ) {
	$duration = absint( $duration );

	$hour = floor( $duration / 3600 );
	$minutes = floor( ( $duration - ( $hour * 3600 ) ) / 60 );

	if ( $hour > 0 ) {
		return gmdate( 'h:i:s', $duration );
	} elseif ( $minutes > 0 ) {
		return gmdate( 'i:s', $duration );
	} else {
		return '0:' . gmdate( 's', $duration );
	}
}

function muxvideo_format_asset_duration_details( $duration ) {
	$hour = floor( $duration / 3600 );
	$minutes = floor( ( $duration - ( $hour * 3600 ) ) / 60 );
	$seconds = $duration - ( $hour * 3600 ) - ( $minutes * 60 );
	$splitter = ' ';
	$hour_splitter = __( 'asset_list_hours', '2coders-integration-mux-video' ) . $splitter;
	$minutes_splitter = __( 'asset_list_minutes', '2coders-integration-mux-video' ) . $splitter;
	$seconds_splitter = __( 'asset_list_seconds', '2coders-integration-mux-video' ) . $splitter;

	if ( $hour > 0 ) {
		return $hour . $hour_splitter . $minutes . $minutes_splitter . $seconds . $seconds_splitter;
	} else if ( $minutes > 0 ) {
		return $minutes . $minutes_splitter . $seconds . $seconds_splitter;
	} else {
		return $seconds . $seconds_splitter;
	}
}

function muxvideo_get_asset_status_color( $asset_status ) {
	switch ( $asset_status ) {
		case 'ready':
			$color = '#00AA3C';
			break;
		case 'preparing':
			$color = '#FFB200';
			break;
		case 'disabled':
			$color = '#828C97';
			break;
		case 'errored':
			$color = '#EA3737';
			break;
		default:
			$color = '#000';
			break;
	}
	return $color;
}

function muxvideo_get_asset_image( $tracks, $asset_playback_id, $jwt, $asset_policy ) {
	$default_poster_url = plugin_dir_url( __FILE__ ) . '../assets/images/default-poster.png';
	$default_poster_audio_url = plugin_dir_url( __FILE__ ) . '../assets/images/default-poster-audio.svg';
	$image_muxvideo_url_prefix = 'https://image.mux.com/';
	$image_src_prefix = '<img src="';
	$image_extension = '/thumbnail.png?';

	if ( is_null( $tracks ) ) :
		$img = $image_src_prefix . $default_poster_url . '">';
	else :
		if ( $tracks == 'audio' || sizeof( $tracks ) == 1 && $tracks[0]->getType() == 'audio' ) :
			$img = $image_src_prefix . $default_poster_audio_url . '">';
		else :
			$url_params = 'width=258&height=160&fit_mode=smartcrop&time=2';
			$signed_url_params = 'token=' . $jwt;
			if ( $asset_policy == 'signed' ) :
				$url_img = $image_muxvideo_url_prefix . $asset_playback_id . $image_extension . $signed_url_params;
				if ( get_headers( $url_img ) && @getimagesize( $url_img ) !== false ) :
					$img = $image_src_prefix . $url_img . '" alt="Mux image" class="image">';
				else :
					$img = $image_src_prefix . $default_poster_url . '">';
				endif;
			else :
				if ( get_headers( $image_muxvideo_url_prefix . $asset_playback_id . $image_extension . $url_params ) ) :
					$img = $image_src_prefix . $image_muxvideo_url_prefix . $asset_playback_id . $image_extension . $url_params . '" alt="Mux image" class="image">';
				else :
					$img = $image_src_prefix . $default_poster_url . '">';
				endif;
			endif;
		endif;
	endif;
	return wp_kses( $img, array( 'img' => array( 'src' => array(), 'alt' => array(), 'class' => array() ) ) );
}

function muxvideo_convert_resolution( $resolution ) {
	$resolutions = [ 
		'240p' => '240p',
		'360p' => '360p',
		'480p' => 'SD',
		'640p' => '640p',
		'720p' => 'HD',
		'960p' => '960p',
		'1080p' => 'FHD',
		'1280p' => '1280p',
		'1440p' => '2K',
		'2160p' => 'UHD'
	];

	if ( isset( $resolutions[ $resolution ] ) ) {
		return $resolutions[ $resolution ];
	} else {
		return 'Auto';
	}
}

function muxvideo_get_resolutions_by_stream_url( $stream_url ) {
	$remote_get_response = wp_remote_get( $stream_url );

	$lines = explode( "\n", $remote_get_response["body"] );

	$resolutions = array();
	foreach ( $lines as $line ) {
		if ( strpos( $line, "#EXT-X-STREAM-INF" ) !== false ) {
			preg_match( '/RESOLUTION=(\d+x\d+)/', $line, $matches );
			if ( isset( $matches[1] ) ) {
				$resolution = $matches[1];
				$formattedResolution = preg_replace( '/(\d+)x(\d+)/', '$2', $resolution );
				$resolutions[] = $formattedResolution;
			}
		}
	}

	if ( empty( $resolutions ) || ! is_array( $resolutions ) || is_null( $resolutions ) ) {
		return array( 'Not available' );
	}

	rsort( $resolutions );
	switch ( $resolutions[0] ) {
		case 1080:
			$resolutions = array( "1080p", "720p" );
			break;
		case 720:
			$resolutions = array( "720p" );
			break;
		default:
			$resolutions = array( strval( $resolutions[0] ) . "p" );
			break;
	}

	return $resolutions;

}