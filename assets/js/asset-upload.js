let isUploading = false;

$(document).ready(function () {
	const picker = document.getElementById('picker');
	let uploadNumber = 0;
	
	picker.onchange = async () => {
		let pickerFile = picker.files[0]
		const originalFileName = pickerFile.name
		const newFile = new File([pickerFile], convertToSlug(pickerFile.name), {type: pickerFile.type, lastModified: pickerFile.lastModified})
		pickerFile = newFile
		// mimetypes
		let supportedFormats = [
			'video/mp4',
			'video/x-m4v',
			'video/x-msvideo',
			'video/avi',
			'video/quicktime',
			'video/mpeg',
			'video/x-ms-wmv',
			'video/x-flv',
			'video/webm',
			'video/x-matroska',
			'video/ogg',
			'video/3gpp',
			'video/3gpp2',
			'audio/mpeg',
			'audio/mp3',
			'audio/wav',
			'audio/ogg',
			'audio/mp4',
			'audio/flac',
			'audio/amr',
			'audio/x-m4a',
			'audio/x-m4r',
		]
		
		// Check if the format of the current file is in the white list - mkv and mp2 returns empty
		if(!supportedFormats.includes(pickerFile.type) && originalFileName.substring(originalFileName.length - 4) != '.mkv' && originalFileName.substring(originalFileName.length - 4) != '.mp2' ){
			throwErrorNotice(window.getTranslations.translations.error_format_not_supported);
			$('#picker').val('')
			return;
		}
		// Check if exists any upload in progress by cookie
		if(checkCookie('muxAssetProgress')){
			throwErrorNotice(window.getTranslations.translations.error_upload_in_progress);
			$('#picker').val('')
			return;
		}
		
		let now = new Date();
		now.setTime(now.getTime() + (60 * 5000));
		document.cookie = "muxAssetProgress=true; expires=" + now.toUTCString() + "; path=/";

		isUploading = true;
		$('#picker').attr('disabled','disabled');
		let msgContainer = document.getElementById('progress-container');
		msgContainer.innerHTML = '';
		let uploadUrl = $('#upload-url').val();
		if(uploadUrl == ""){
			await new Promise((resolve, reject) => {
				resetUploadUrl(resolve, reject)
			})
			uploadUrl = $('#upload-url').val();
		}

		const upload = UpChunk.createUpload({
			endpoint: uploadUrl,
			file: pickerFile,
			chunkSize: 30720, // Uploads the file in ~30 MB chunks
		});
		
		let assetName = pickerFile.name.replace(/\./g, '');
		let uploadItemId =  assetName + '-' + uploadNumber;
		uploadNumber++;
		let isUploadInProgress = false;
		if($('.modal').length == 0){
			throwWarnModal(
				window.getTranslations.translations.asset_upload_warn_modal_uploading_title,
				window.getTranslations.translations.asset_upload_warn_modal_uploading_description,
				window.getTranslations.translations.asset_upload_warn_modal_uploading_cancel, 
				window.getTranslations.translations.asset_upload_warn_modal_uploading_confirm, 
				upload, 
				uploadItemId
			)
		}
		createUploadRow(msgContainer, uploadItemId, originalFileName, pickerFile)
		$('#' + uploadItemId).data('status', 'uploading');
		upload.on('progress', progress => {
			if(!isUploadInProgress){
				linkListener(true)
				isUploadInProgress = true;
				$( ".delete-current-asset" ).on( "click", function() {
					cancelCurrentUpload(upload, uploadItemId)
					
				})
			}
			let progressDiv = `<div class="background-bar">
					<div class="progress-detail" style="width: ${Math.trunc(progress.detail)}%;"></div>	
				</div>
				<span>${Math.trunc(progress.detail)}%</span>` ;
			
			$('#' + uploadItemId + " .upload-item__progress").html(progressDiv);			
		});

		upload.on('error', err => {
			cancelCurrentUpload(upload, uploadItemId);
			throwErrorNotice();
		});
		
		upload.on('success', () => {
			$('#' + uploadItemId).data('status', 'uploaded');
			let statusElement       = document.getElementById( 'item-status' );
			displayLoader(statusElement, '')
			$( "#uploaded-container" ).append( $( ".upload-item" ) );
            clearUploadInstance( upload );
		});
	};

	$('body').on('click', '.delete-cancelled-upload', function() {
		$(this).closest('.upload-item').hide('slow');
	});
});



function linkListener(toggle){
	if(toggle){
		$( "body" ).on( "click", "a", function(e) {
			setModalRedirection(e)
			e.preventDefault()
			e.stopPropagation()
			$('.modal').toggleClass('is-visible');
		}); 
	}else{
		$( "body" ).off( "click", "a");    
	}  
}

function createUploadRow(msgContainer, uploadItemId, originalFileName, pickerFile, progress){
	msgContainer.innerHTML = 
		`<div id="${uploadItemId}" style="display:none" class="upload-item" data-status="">
			<div class="upload-item__icon icon-column">
				<i class="fas fa-film"></i>
			</div>
			<div class="upload-item__detail">
				<p class="upload-item__detail__title">${originalFileName}</p>
				<p class="upload-item__detail__size">${formatBytes(pickerFile.size)}</p>
				<div class="upload-item__progress">
					<div class="background-bar">
						<div class="progress-detail" style="width: 0%;"></div>	
					</div>
					<span>0%</span>
				</div>
			</div>
			<div id="item-status" class="upload-item__status">
				<span class="fade-in-transition" style="height: fit-content;"><a class="btn-cancel delete-current-asset">` + window.getTranslations.translations.asset_upload_button_cancel_upload + `</a></span>
			</div>
		</div>`;
		$('i.asset-uploaded, i.delete-cancelled-upload').removeClass( 'fade-in-transition' );
		$('#' + uploadItemId)
			.slideDown('slow')
			.animate(
			{ opacity: 1,
			 },
			{ queue: false, duration: 'slow' }
			);
}

function cancelCurrentUpload(upload, uploadItemId){
	clearUploadInstance(upload, uploadItemId)
	$('#' + uploadItemId).data('status', 'cancelled');
	$('#' + uploadItemId).addClass('aborted-asset');
	let msgCancelling = '<span>' +  window.getTranslations.translations.asset_upload_asset_status_cancelling + '</span>'
	let statusElement = document.getElementById('item-status');
	$('#' + uploadItemId + " .upload-item__progress span").html(msgCancelling);
	displayLoader(statusElement, '')
	$("#uploaded-container").append($(".upload-item"));
}

function clearUploadInstance(upload, uploadItemId){
	linkListener(false)
	resetUploadUrl(upload, uploadItemId);
	upload.abort();
	delete upload;
	document.cookie = "muxAssetProgress=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
}

function convertToSlug(Text) {
	return Text.toLowerCase()
		.replace(/[^\w ]+/g, '')
		.replace(/ +/g, '-');
}

function formatBytes(bytes, decimals = 2) {
	if (!+bytes) return '0 Bytes'

	const k = 1024
	const dm = decimals < 0 ? 0 : decimals
	const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']

	const i = Math.floor(Math.log(bytes) / Math.log(k))

	return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`
}

function checkCookie(name) {
    const cookiePattern = new RegExp('(^|; )' + name + '=');
    return cookiePattern.test(document.cookie);
}