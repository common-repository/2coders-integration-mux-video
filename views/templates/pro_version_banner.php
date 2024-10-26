<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

if ( MUXVIDEO_FS_CAN_USE_PREMIUM_CODE ) {
	return;
}
;

?>
<div class="pro-version-banner">
	<p class="featured-text">
		<?php esc_html_e( 'common_premium_name', '2coders-integration-mux-video' ) ?>
	</p>
	<h3>
		<?php esc_html_e( 'common_premium_banner_title', '2coders-integration-mux-video' ) ?>
	</h3>
	<p class="pro-version-banner__description">
		<?php esc_html_e( 'common_premium_banner_description', '2coders-integration-mux-video' ) ?>
	</p>
</div>