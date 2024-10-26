<?php

/**
 * Plugin Name: 2Coders integration for Mux Video
 * Plugin URI: https://muxvideo.2coders.com/
 * Description: Streamline your WordPress video content with the ultimate Mux integration plugin.
 * Version: 1.0.3
 * Requires at least: 5.9
 * Tested up to: 6.4
 * Requires PHP: 7.2
 * Author: 2Coders Studio
 * Author URI: https://www.2coders.com/
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: 2coders-integration-mux-video
 * Domain Path: /languages/
 *
 * @package Mux  
 *  ____   ____          _                 ____  _             _ _       
 * |___ \ / ___|___   __| | ___ _ __ ___  / ___|| |_ _   _  __| (_) ___  
 *   __) | |   / _ \ / _` |/ _ \ '__/ __| \___ \| __| | | |/ _` | |/ _ \ 
 *  / __/| |__| (_) | (_| |  __/ |  \__ \  ___) | |_| |_| | (_| | | (_) |
 * |_____|\____\___/ \__,_|\___|_|  |___/ |____/ \__|\__,_|\__,_|_|\___/ 
 *                                                                     
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

if ( function_exists( 'muxvideo_fs' ) ) {
	muxvideo_fs()->set_basename( true, __FILE__ );
} else {
	// DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
	if ( ! function_exists( 'muxvideo_fs' ) ) {
		// Create a helper function for easy SDK access.
		function muxvideo_fs() {
			global $muxvideo_fs;

			if ( ! isset( $muxvideo_fs ) ) {
				// Include Freemius SDK.
				require_once dirname( __FILE__ ) . '/freemius/start.php';

				$muxvideo_fs = fs_dynamic_init( array(
					'id' => '12655',
					'slug' => 'mux-media',
					'type' => 'plugin',
					'public_key' => 'pk_8d5a7f9435bdb0602b57080f5b123',
					'is_premium' => true,
					'premium_suffix' => 'PRO',
					// If your plugin is a serviceware, set this option to false.
					'has_premium_version' => true,
					'has_addons' => false,
					'has_paid_plans' => true,
					'menu' => array(
						'slug' => 'muxvideo',
						'first-path' => 'admin.php?page=muxvideo-settings',
						'contact' => true,
						'support' => false,
						'pricing' => true,
					),
					// Set the SDK to work in a sandbox mode (for development & testing).
					// IMPORTANT: MAKE SURE TO REMOVE SECRET KEY BEFORE DEPLOYMENT.
					'secret_key' => 'sk_c6Kqe(8LQ33e1qb!_z<5w>AzL)^wQ',
				) );
			}
			// Set license permissions into a variable
			if ( ! defined( 'MUXVIDEO_FS_CAN_USE_PREMIUM_CODE' ) ) {
				$muxvideo_fs_can_use_premium_code = $muxvideo_fs->can_use_premium_code();
				define( 'MUXVIDEO_FS_CAN_USE_PREMIUM_CODE', $muxvideo_fs_can_use_premium_code );
			}

			return $muxvideo_fs;
		}

		// Init Freemius.
		muxvideo_fs();

		// Signal that SDK was initiated.
		do_action( 'muxvideo_fs_loaded' );
	}

	try {
		if ( ! defined( 'MUXVIDEO_PLUGIN_DIR' ) ) {
			define( 'MUXVIDEO_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
		}

		require_once MUXVIDEO_PLUGIN_DIR . '/includes/includes.php';
	} catch (\Throwable $th) {
		error_log( "Error: defined MUXVIDEO_PLUGIN_DIR" . $th->getMessage() );
	}

}