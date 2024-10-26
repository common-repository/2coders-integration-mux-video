<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly  
}

?>

<div class="muxvideo-page muxvideo-assets-page">
	<?php
	require_once MUXVIDEO_PLUGIN_DIR . '/includes/functions.php';

	try {
		$limit = 10; // int | Number of items to include in the response
		if(!isset($_REQUEST['nonce-form']) || !wp_verify_nonce($_REQUEST['nonce-form'], '_wpnonce')) {
			$current_page = isset( $_REQUEST['paged'] ) ? sanitize_text_field( $_REQUEST['paged'] ) : 1; // int | Offset by this many pages, of the size of `limit`
		}
		$assetsApi = muxvideo_api_client_init();
		$data = $assetsApi->listAssets( $limit, $current_page );
		$empty = count( $data->getData() ) <= 0;
		?>
		<?php muxvideo_get_header(); ?>

		<?php muxvideo_get_pro_version_banner(); ?>

		<div class="muxvideo-container">
			<div class="content-container">
				<div class="wrap header-container inner-container">
					<ul class="tabs-nav">
						<li class="tab-active"><a>
								<?php esc_html_e( 'asset_list_title_page', '2coders-integration-mux-video' ) ?>
							</a></li>
					</ul>
					<div class="header-container__actions">
						<span id="refresh-list-button" class="header-container__actions__refresh"
							onclick="refreshAssetsList()">
							<img class="ui-icon"
								src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../assets/images/refresh-icon.svg' ); ?>"
								alt="refresh icon">
						</span>
						<a href="admin.php?page=muxvideo-asset-upload" class="btn btn-primary">
							<?php echo esc_html__( 'asset_list_button_upload_video', '2coders-integration-mux-video' ); ?>
						</a>
					</div>
				</div>
				<div class="inner-container">
					<div id="" class='assets-list shadow-scroll-x assets-container container'>
						<div id="assets-list-header" class="assets-list__header row" style="display:none">
							<div class="col">
								<?php esc_html_e( 'asset_list_table_title_preview', '2coders-integration-mux-video' ) ?>
							</div>
							<div class="col assets-list__header__id">
								<?php esc_html_e( 'asset_list_table_title_id', '2coders-integration-mux-video' ) ?>
							</div>
							<div class="col assets-list__header__status">
								<?php esc_html_e( 'asset_list_table_title_status', '2coders-integration-mux-video' ) ?>
							</div>
							<div class="col assets-list__header__duration">
								<?php esc_html_e( 'asset_list_table_title_duration', '2coders-integration-mux-video' ) ?>
							</div>
							<div class="col assets-list__header__created">
								<?php esc_html_e( 'asset_list_table_title_date', '2coders-integration-mux-video' ) ?>
							</div>
							<div class="col"></div>
						</div>
						<div id="assets-list-container">
							<?php // loadAssetsList() will display asset list via ajax  ?>
						</div>
					</div>
					<?php if ( sizeof( $data->getData() ) >= $limit ) : ?>
						<button id="load-more" name="load-more" class="btn btn-primary" onclick="loadMoreAssets(this)">
							<span class="load-more-text">
								<?php esc_html_e( 'asset_list_button_load_more', '2coders-integration-mux-video' ) ?>
							</span>
						</button>
					<?php endif; ?>
				</div>
			</div>
			<?php muxvideo_get_sidebar(); ?>
		</div>
		<?php echo muxvideo_add_disclaimer_in_footer(); ?>
	<?php } catch (Exception $e) {
		error_log( 'Exception when calling AssetsApi->listAssets: ', $e->getMessage() );
	} ?>
</div>

<script>

	// Function to load the additional content using an AJAX request
	function refreshAssetsList() {
		$('#load-more').hide()
		$('.header-container__actions__refresh')
			.addClass('rotate-element disabled')
		displayLoader('#assets-list-container', '');
		$('#assets-list-header').hide()
		if ($('#assets-list-container assets-list__item').length == 0) {
			$('#assets-list-header').hide()
		}
		window.assetList.currentPage = 1;
		loadAssetsList()
	}

	function loadAssetsList() {
		displayLoader('#assets-list-container', '');
		var xhr = new XMLHttpRequest();
		xhr.open('POST', window.ajaxurl + '?action=muxvideo_refresh_asset_list', true);
		xhr.setRequestHeader('Content-Type', 'application/json');
		xhr.onreadystatechange = function () {
			if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
				$("#assets-list-container").hide().append(xhr.responseText).fadeIn(1000);
				$('.loading-container').remove();
				$('.header-container__actions__refresh').removeClass('rotate-element disabled')
				checkImageUrlForSignedAssets('.preview-container img')
				$('.assets-list__item').css('opacity', '1');
				if ($('#assets-list-container .assets-list__item').length != 0) {
					$('#load-more').show()
					$('#assets-list-header').show()
				}
			}
		};
		xhr.send();
	}
	$('#load-more').hide()
	displayLoader('#assets-list-container', '');
	window.onload = loadAssetsList;

	function loadMoreAssets(asset) {
		let loadingIcon = $('.loading-container');
		let loadMoreText = $('#load-more').html();
		displayLoader('#load-more', 'loading-container__load-more');
		$('#load-more').css('pointer-events', 'none');
		window.assetList.currentPage++; // Do currentPage + 1, because we want to load the next page
		// currentPage = (currentPage == 2 ) ? 3 : currentPage;
		$.ajax({
			type: 'POST',
			url: '<?php echo esc_url( admin_url( "admin-ajax.php" ) ); ?>',
			dataType: 'html',
			data: {
				action: 'muxvideo_load_more_assets',
				paged: window.assetList.currentPage,
			},
			success: function (res) {
				if (res.length == 0) {
					$('#load-more').fadeOut();
				}
				const $res = $(res)
				window.ress = $res
				let result = []
				$res.map((i, a) => {
					if ($(a).hasClass('assets-list__item')) {
						result.push($(a))
					}
				})
				$('#assets-list-container').append(result);
				$('.assets-list__item').delay(200).queue(function (next) {
					$(this).css('opacity', '1');
					next();
				});
				loadingIcon.hide('slow');
				$('#load-more').html(loadMoreText);
				$('#load-more').css('pointer-events', 'all');
				if (result.length < 10) $("#load-more").fadeOut()
				checkImageUrlForSignedAssets('.preview-container img')
			}
		});
		event.preventDefault();
	}

	function deleteSingleAsset(asset) {
		let assetId = $(asset).val();
		let assetContainer = $('#' + assetId);
		let targetLoader = '#' + assetId + ' .action-container';
		throwWarnModalOnDeleting(
			window.getTranslations.translations.asset_list_warn_modal_delete_asset_title,
			window.getTranslations.translations.asset_list_warn_modal_delete_asset_description,
			window.getTranslations.translations.asset_list_warn_modal_delete_asset_cancel,
			window.getTranslations.translations.asset_list_warn_modal_delete_asset_confirm,
			function () {
				displayLoader(targetLoader, 'loading-container__asset-list-delete');
				$('#' + assetId + ' .action-container').prepend('<p>Deleting</p>');
				$('#' + assetId).css({
					'filter': 'grayscale(1)',
					'pointer-events': 'none'
				});
				$('#loading-delete').show('slow');

				$.ajax({
					type: "POST",
					url: '<?php echo esc_url( admin_url( "admin-ajax.php" ) ); ?>',
					data: { action: 'muxvideo_delete_asset', asset_id: assetId },
					error: function (xhr, status, error) {
						console.log(error);
					},
					success: function () {
						assetContainer.hide('slow', function () {
							assetContainer.remove();
							if ($('#assets-list-container > div:not(.assets-list__no-items)').length < 1) {
								$('#refresh-list-button').trigger('click')
							}
						});
					}
				});
			}
		);
		event.preventDefault();
	}
</script>