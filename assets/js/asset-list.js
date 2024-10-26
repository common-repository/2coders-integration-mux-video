window.assetList = {
    currentPage: 1
};

function openDetails(item){
    if($(item).children('.toggle-details').hasClass("active")){
        $(item).siblings('.action-buttons').removeClass('active')
        $(item).siblings('.open-actions').removeClass('active')
    }
    $(item).children('.toggle-details').toggleClass("active");
    let detailsElement = $(item).closest('.assets-list__item').children('.assets-list__item__details');
    $(item).closest('.assets-list__item').toggleClass("active");
    detailsElement.toggleClass("active");
  }

function copyShortode(elem){
    let defaultShortcodeData = ' hotkeys="true" controls="true" seek-buttons="true" seek-buttons-duration="15" max-width="" aspect-ratio="16 / 9" start-time="0" autoplay="false" mute="false" looping-content="false" closed-captions="false" full-width="true" ';
    let value = '[muxvideo_asset playback-id="' + $(elem).data('id') + '" privacy="' + $(elem).data('privacy') + '"' + defaultShortcodeData +']';
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(value).select()
    document.execCommand("copy");
    $temp.remove();
    let copySuccess = $(elem).closest('.copy-shortcode-container').children('.copied');
    copySuccess.fadeTo(50, 1);
    setTimeout(function(){ copySuccess.fadeTo(50, 0);}, 2000);
}

function copyStreamUrl(elem){
    let value = $(elem ).children('p').text();
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(value).select()
    document.execCommand("copy");
    $temp.remove();
    let copySuccess = $(elem).children('.copied');
    copySuccess.fadeTo(50, 1);
    setTimeout(function(){ copySuccess.fadeTo(50, 0);}, 2000);
}

function createMuxPlayer(elem){
    let jwt = $(elem).data('jwt')
    let playbackID = $(elem).data('id')
    let posterPlayer = $(elem).children('img').attr('src')
    let streamUrl = $('p[data-playback-id="' + playbackID + '"').text();
    const not_found = 'not_found'
    const not_authorized = 'not_authorized'
    let previewContainer = $('.preview-container[data-id="' + playbackID + '"]')
    let assetListContainer= previewContainer.parents('.assets-list__item:first')
    $.getJSON(streamUrl)
    .fail(function(jqXHR) {
        if(jqXHR.statusText == 'error'){
            let msg
            if(jqXHR.responseJSON && jqXHR.responseJSON.error.type.length){
                let errorType = jqXHR.responseJSON.error.type; 
                switch (errorType) {
                    case not_found:
                        msg = window.getTranslations.translations.common_notice_error_deleted_asset;
                        throwErrorNotice(msg);
                        assetListContainer.addClass('asset-deleted');
                        assetListContainer.find('.status').text('Deleted');
                        break;
                    case not_authorized:
                        msg = window.getTranslations.translations.common_notice_error_unauth_asset;
                        throwErrorNotice(msg);
                        break;
                    default:
                        break;
                }
            }
            msg = window.getTranslations.translations.common_notice_error_unauth_asset;
            throwErrorNotice(msg);
            // getJSON error because you are offline.
        }else{
            let muxPlayer = `
                <mux-player
                    poster="` + posterPlayer + `"
                    stream-type="on-demand" 
                    autoplay="muted"
                    playback-id="` + playbackID + `"
                    metadata-video-title="Video title VOD"
                    metadata-viewer-user-id="` + window.freemiusData.current_fs_user + `"
                    playback-token="` + jwt + `"
                    thumbnail-token="` + jwt + `"
                    storyboard-token="` + jwt + `"
                    style="--controls: none;
                    --captions-button: none;
                    --airplay-button: none;
                    --pip-button: none;
                    --cast-button: none;
                    --playback-rate-button: none;
                    --volume-range: none;
                    --time-range: none;
                    --time-display: none;
                    --duration-display: none;
                    --rendition-selectmenu: none;
                    "
                    > 
                </mux-player>`;
            $(elem).css({"filter": "brightness(0)"})
            $(elem).html(muxPlayer);
            $(elem).prop("onclick", null).off("click");
            // Filter and width, Requirement for controls to work, after a mux update.
            setTimeout(function(){
                $(elem).children('mux-player').css({ "width": "129px", '--controls': 'inherit'})
                $(elem).css({"filter": "brightness(1)"})
            }, 400)
        }
    })
}

function openActions(item){
    $('.action-buttons, .open-actions')
        .not($(item))
        .not($(item).siblings('.action-buttons'))
        .removeClass('active');
    $('.row').removeClass('go-to-front')
    $(item).closest('.row').toggleClass('go-to-front')
    $(item).toggleClass("active");
    $(item).siblings('.action-buttons').toggleClass('active');
}