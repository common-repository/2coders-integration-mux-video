$( 'body' ).on( 'click', '.delete-alert', function() {
    $('.muxvideo-notice').fadeOut();
})

$(window).on('offline', function(event) {
    $('#picker').attr('disabled', 'disabled');
    let msg = window.getTranslations.translations.common_notice_offline
    throwErrorNotice(msg);
});

let isResetUploadErrored = false
$(window).on('online', function(event) {
    if(typeof isResetUploadErrored !== 'undefined' && isResetUploadErrored){
        cancelCurrentUpload(currentUploadObj, currentUploadItemId);
        isResetUploadErrored = false;
    }
    if(typeof isUploading == 'undefined' || !isUploading){
        $('#picker').removeAttr('disabled');
    }

    let msg = window.getTranslations.translations.common_notice_online_back;
    throwNotice(msg);

    setInterval(() => {
        $('.muxvideo-notice__success').fadeOut();
    }, 5000);
});

$(document).on('mouseup', function (e) {
    var container = $(".modal-wrapper");

    // if the target of the click isn't the container or a descendant of the container
    if (!container.is(e.target) && container.has(e.target).length === 0) {
        $('.modal').not('.shortcode-customizer').removeClass('is-visible');
    }
});