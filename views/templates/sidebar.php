<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

if ( MUXVIDEO_FS_CAN_USE_PREMIUM_CODE ) {
	return;
}
;

?>
<div class="muxvideo-sidebar">
	<p class="featured-text">
		<?php esc_html_e( 'common_premium_name', '2coders-integration-mux-video' ) ?>
	</p>
	<img class="icon" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../../assets/images/chain-icon.svg' ); ?>" alt="chain icon">
	<h3>
		<?php esc_html_e( 'common_premium_banner_title', '2coders-integration-mux-video' ) ?>
	</h3>
	<p>
		<?php esc_html_e( 'common_premium_banner_description', '2coders-integration-mux-video' ) ?>
	</p>
</div>