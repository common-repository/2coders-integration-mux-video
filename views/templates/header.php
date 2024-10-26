<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

?>

<header class="muxvideo-header">
	<div class="muxvideo-header__logo">
		<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../../assets/images/plugin-logo.svg' ); ?>"
			alt="2Coders integration for Mux Video Logo">
		<h1>
			<?php
			$plugin_name = '2Coders integration for Mux Video';
			if ( muxvideo_fs()->is__premium_only() ) :
				$plugin_name = MUXVIDEO_FS_CAN_USE_PREMIUM_CODE ? '2Coders integration for Mux Video Pro' : '2Coders integration for Mux Video';
			endif;
			echo esc_html( $plugin_name );
			?>
		</h1>
	</div>
	<?php global $muxvideo_fs; ?>
	<div class="muxvideo-header__information">
		<span class="muxvideo-header__information__version">
			<?php echo esc_html__( 'header_content_version_title', '2coders-integration-mux-video' ) . ': ' . esc_html( MUXVIDEO_PLUGIN_VERSION ) ?>
		</span>
		<?php if ( $muxvideo_fs->get_plan_name() != 'PLAN_NAME' ) : ?>
			<span class="muxvideo-header__information__license">
				<?php echo esc_html__( 'header_content_license_title', '2coders-integration-mux-video' ) . ': ' . esc_html( ucfirst( $muxvideo_fs->get_plan_name() ) ) ?>
			</span>
		<?php endif; ?>
	</div>
</header>