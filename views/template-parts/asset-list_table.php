<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

function muxvideo_display_assets_list() {
	$limit = absint( 10 ); // int | Number of items to include in the response
	if(!isset($_REQUEST['nonce-form']) || !wp_verify_nonce($_REQUEST['nonce-form'], '_wpnonce')) {
		$current_page = isset( $_REQUEST['paged'] ) ? absint( sanitize_text_field( $_REQUEST['paged'] ) ) : 1; // int | Offset by this many pages, of the size of `limit`
	}
	$assetsApi = muxvideo_api_client_init();
	$data = $assetsApi->listAssets( $limit, $current_page );
	$empty = count( $data->getData() ) <= 0;
	$playback_restrictions_data = muxvideo_get_data_playback_restrictions();
	?>
	<?php if ( $empty ) : ?>
		<div class="assets-list__no-items">
			<img class="ui-icon assets-list__no-items__img"
				src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../../assets/images/video-player-icon-2.svg' ); ?>"
				alt="video player icon">
			<?php echo esc_html_e( 'no_items_to_show', '2coders-integration-mux-video' ); ?>
		</div>
	<?php endif; ?>
	<?php foreach ( $data->getData() as $asset ) :
		try {
			$asset_data = muxvideo_get_asset_data( $asset, $playback_restrictions_data );
			$json_asset_data = $asset_data;
			if ( isset( $asset_data['tracks'] ) ) {
				$json_asset_data['tracks'] = '';
				$json_asset_data['tracks_type'] = $asset_data['tracks'][0]->getType();
			}
			if ( ! is_null( $playback_restrictions_data ) ) {
				$json_asset_data['playback_restriction_id'] = $playback_restrictions_data->id;
			}
			$json_asset_data = wp_json_encode( $json_asset_data );
			?>
			<div class="assets-list__item <?php echo esc_attr( $asset_data['status'] == 'errored' ? 'asset-disabled' : '' ); ?>"
				id="<?php echo esc_attr( $asset_data['id'] ); ?>">
				<div class="row">
					<div class="col thumbnail-container">
						<div class="preview-container" data-id="<?php echo esc_attr( $asset_data['playback_id'] ); ?>"
							data-jwt="<?php echo esc_attr( $asset_data['jwt'] ); ?>" <?php echo $asset_data['status'] == 'ready' ? 'onclick="createMuxPlayer(this)"' : 'style="pointer-events:none;"'; ?>>
							<?php echo muxvideo_get_asset_image( $asset_data['tracks'], $asset_data['playback_id'], $asset_data['jwt_t'], $asset_data['policy'] ); ?>
							<div class="overlay">
								<div class="text"><i class="fas fa-play-circle"></i></div>
							</div>
						</div>
					</div>
					<div class="col asset-id">
						<p title="<?php echo esc_attr( $asset_data['id'] ); ?>">
							<?php echo esc_html( $asset_data['id'] ); ?>
						</p>
					</div>
					<div class="col status">
						<p style="color: <?php echo esc_attr( muxvideo_get_asset_status_color( $asset_data['status'] ) ); ?>">
							<?php echo esc_html( ucfirst( $asset_data['status_translated'] ) ); ?>
						</p>
						&nbsp;
						<?php
						$lock_icon = $asset_data['policy'] == 'signed' ? '<img class="ui-icon" src="' . esc_url( plugin_dir_url( __FILE__ ) . '../../assets/images/icon-lock.svg' ) . '" alt="lock icon">' : '';
						echo wp_kses( $lock_icon, array( 'img' => array( 'class' => array(), 'src' => array(), 'alt' => array() ) ) );
						?>

					</div>
					<div class="col">
						<p>
							<?php echo esc_html( muxvideo_format_asset_duration( intval( $asset_data['duration'] ) ) ); ?>
						</p>
					</div>
					<div class="col">
						<p>
							<?php echo esc_html( gmdate( "d-m-Y H:i", intval( $asset_data['created_at'] ) ) ); ?>
						</p>
					</div>
					<div class="action-container col">
						<div class="copy-shortcode-container">
							<p class="btn-tertiary" onclick="copyShortode(this)"
								data-id="<?php echo esc_html( $asset_data['playback_id'] ) ?>"
								data-privacy="<?php echo esc_html( $asset_data['policy'] ) ?>"><img class="ui-icon"
									src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../../assets/images/clipboard-list.svg' ); ?>"
									alt="clipboard icon">
								<?php esc_html_e( 'asset_list_button_copy_shortcode', '2coders-integration-mux-video' ) ?>
							</p>
							<div id="copied-success" class="copied">
								<i class="fas fa-check-circle"></i>
								<span>
									<?php esc_html_e( 'asset_list_message_shortcode_copied', '2coders-integration-mux-video' ) ?>
								</span>
							</div>
						</div>
						<div class="open-actions" onclick="openActions(this)">
							<img class="ui-icon"
								src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../../assets/images/more-vertical.svg' ); ?>"
								style="width:24px; height:24px;" alt="three dots">
						</div>
						<div class="action-buttons">
							<button name="customize-shortcode" onclick="customizeShortcode(this)" class="action-asset"
								data-asset='<?php echo esc_attr( $json_asset_data ); ?>'
								value="<?php echo esc_attr( $asset_data['id'] ); ?>">
								<img class="ui-icon"
									src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../../assets/images/edit-icon.svg' ); ?>"
									alt="edit icon">
								<?php esc_html_e( 'asset_list_button_customize_shortcode', '2coders-integration-mux-video' ) ?>
							</button>
							<button name="delete-asset" onclick="deleteSingleAsset(this)" class="action-asset delete-asset"
								value="<?php echo esc_attr( $asset_data['id'] ); ?>"><img class="ui-icon"
									src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../../assets/images/trash-icon.svg' ); ?>"
									alt="trash icon">
								<?php esc_html_e( 'asset_list_button_delete_asset', '2coders-integration-mux-video' ) ?>
							</button>
						</div>
						<div class="btn-details-container" onclick="openDetails(this)">
							<a class="toggle-details">
								<img class="ui-icon"
									src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../../assets/images/right-arrow.svg' ); ?>"
									alt="right arrow icon">
							</a>
						</div>
					</div>
				</div>
				<div class="assets-list__item__details">
					<div class="col">
						<div class="assets-list__item__details__title">
							<p>
								<?php esc_html_e( 'asset_list_asset_content_streamurl', '2coders-integration-mux-video' ) ?>
							</p>
						</div>
						<div onclick="copyStreamUrl(this)"
							class="assets-list__item__details__content assets-list__item__details__streamurl">
							<p data-playback-id="<?php echo esc_attr( $asset_data['playback_id'] ); ?>">
								<?php $stream_url = 'https://stream.mux.com/' . $asset_data['playback_id'] . '.m3u8';
								$stream_url .= $asset_data['policy'] == 'signed' ? '?token=' . $asset_data['jwt'] : '';
								echo esc_url( $stream_url );
								?>
							</p>

							<img class="ui-icon"
								src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../../assets/images/clipboard-list.svg' ); ?>"
								alt="clipboard icon">
							<div id="copied-success" class="copied">
								<i class="fas fa-check-circle"></i>
								<span>
									<?php esc_html_e( 'asset_list_message_url_copied', '2coders-integration-mux-video' ) ?>
								</span>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="assets-list__item__details__title">
							<p class="">
								<?php esc_html_e( 'asset_list_asset_content_details', '2coders-integration-mux-video' ) ?>
							</p>
						</div>
						<div class="assets-list__item__details__content">
							<?php if ( $asset_data['id'] ) : ?>
								<div class="assets-list__item__details__grid">
									<div class="col col-key">
										<?php esc_html_e( 'asset_list_asset_content_details_id', '2coders-integration-mux-video' ) ?>
									</div>
									<div class="col col-value">
										<?php echo esc_html( $asset_data['id'] ); ?>
									</div>
								</div>
							<?php endif; ?>
							<?php if ( $asset_data['duration'] ) : ?>
								<div class="assets-list__item__details__grid">
									<div class="col col-key">
										<?php esc_html_e( 'asset_list_asset_content_details_duration', '2coders-integration-mux-video' ) ?>
									</div>
									<div class="col col-value">
										<?php echo esc_html( muxvideo_format_asset_duration_details( intval( $asset_data['duration'] ) ) ); ?>
									</div>
								</div>
							<?php endif; ?>
							<?php if ( $asset_data['max_resolution'] ) : ?>
								<div class="assets-list__item__details__grid">
									<div class="col col-key">
										<?php esc_html_e( 'asset_list_asset_content_details_max_resolution', '2coders-integration-mux-video' ) ?>
									</div>
									<div class="col col-value">
										<?php echo esc_html( $asset_data['max_resolution'] ); ?>
									</div>
								</div>
							<?php endif; ?>
							<?php if ( $asset_data['max_frame_rate'] ) : ?>
								<div class="assets-list__item__details__grid">
									<div class="col col-key">
										<?php esc_html_e( 'asset_list_asset_content_details_max_frame_rate', '2coders-integration-mux-video' ) ?>
									</div>
									<div class="col col-value">
										<?php echo esc_html( $asset_data['max_frame_rate'] ); ?>
									</div>
								</div>
							<?php endif; ?>
							<?php if ( $asset_data['aspect_ratio'] ) : ?>
								<div class="assets-list__item__details__grid">
									<div class="col col-key">
										<?php esc_html_e( 'asset_list_asset_content_details_aspect_ratio', '2coders-integration-mux-video' ) ?>
									</div>
									<div class="col col-value">
										<?php echo esc_html( $asset_data['aspect_ratio'] ); ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		<?php } catch (\Throwable $th) {
			error_log( 'Error displaying asset' . $th->getMessage() );
		} ?>
	<?php endforeach; ?>
<?php }

function muxvideo_get_asset_data( $asset, $playback_restrictions_data ) {
	$asset_data = array();
	switch ( $asset->getStatus() ) {
		case 'ready':
			$asset_status = __( 'asset_list_asset_status_ready', '2coders-integration-mux-video' );
			break;
		case 'errored':
			$asset_status = __( 'asset_list_asset_status_errored', '2coders-integration-mux-video' );
			break;
		case 'preparing':
			$asset_status = __( 'asset_list_asset_status_preparing', '2coders-integration-mux-video' );
			break;
		case 'disabled':
			$asset_status = __( 'asset_list_asset_status_disabled', '2coders-integration-mux-video' );
			break;

		default:
			break;
	}
	$asset_data = [ 
		'id' => $asset->getId(),
		'status' => $asset->getStatus(),
		'status_translated' => $asset_status,
		'playback_id' => $asset->getPlaybackIds()[0]->getId(),
		'policy' => $asset->getPlaybackIds()[0]->getPolicy(),
		'created_at' => $asset->getCreatedAt(),
		'duration' => $asset->getDuration(),
		'max_frame_rate' => $asset->getMaxStoredFrameRate(),
		'max_resolution' => $asset->getMaxStoredResolution(),
		'aspect_ratio' => $asset->getAspectRatio(),
		'tracks' => $asset->getTracks(),
		'signing_id' => get_option( 'muxvideo_options' )['muxvideo_signing_data']['id'],
		'signing_private_key' => get_option( 'muxvideo_options' )['muxvideo_signing_data']['private_key'],
	];

	try {
		$asset_data['jwt'] =
			( ! is_null( $playback_restrictions_data ) && $asset_data['policy'] == 'signed' )
			? muxvideo_get_json_web_token( $asset_data['playback_id'], $asset_data['signing_id'], $asset_data['signing_private_key'], 'v', $playback_restrictions_data->id )
			: '';

	} catch (\Exception $e) {
		$asset_data['jwt'] = '';
		error_log( 'muxvideo_get_json_web_token(): ' . $e->getMessage() );
	}

	try {
		$asset_data['jwt_t'] =
			( ! is_null( $playback_restrictions_data ) && $asset_data['policy'] == 'signed' )
			? muxvideo_get_json_web_token( $asset_data['playback_id'], $asset_data['signing_id'], $asset_data['signing_private_key'], 't', $playback_restrictions_data->id )
			: '';
	} catch (\Exception $e) {
		$asset_data['jwt_t'] = '';
		error_log( 'muxvideo_get_json_web_token()' . $e->getMessage() );
	}

	return $asset_data;
}

function muxvideo_escape_json_string( $value ) {
	$escapers = array( "\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c" );
	$replacements = array( "\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b" );
	return str_replace( $escapers, $replacements, $value );
}