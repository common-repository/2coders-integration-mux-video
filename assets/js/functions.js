jQuery.noConflict();
const $ = jQuery;

let currentUploadObj;
let currentUploadItemId;
let warnUser = false;
let targetUrl

$(document).ready(function () {

    let securitySettingsModified = false;
    window.onbeforeunload = unloadPage;

    $('.droparea').on({
		dragenter: function () {
			updateHolder('is-dragging');
		},

		dragleave: function () {
			restoreHolder('is-dragging');
		},

		drop: function () {
			restoreHolder('is-dragging');
		}
	});

    $( 'body' ).on( 'click', '.delete-alert', function() {
        $('.muxvideo-notice').fadeOut().remove();
    })

    $('.switches-container input[name="switchPrivacy"]').on('change', function() {
        createNewUploadInstance();
    });

    // Change tab class and display content
    $('body').on('click', '.tabs-nav a', function(event){
        event.preventDefault();
        if(typeof securitySettingsModified !== 'undefined' && !securitySettingsModified){
            $(this).parent().siblings().removeClass('tab-active');
            $(this).parent().addClass('tab-active');
            $('.tabs-container .tab').hide();
            $($(this).attr('href')).show();
        }else{
            securitySettingsModified = false;
        }

    });

    $('body').on('submit', '#shortcode-customizer-form',  function (e) {
        e.preventDefault();
        var formArray = $('#' + $(this).attr('id')).serializeArray();
        $('#' + $(this).attr('id') + ' input[type="checkbox"]').each(function() {
          var checkbox = $(this);
          if (!checkbox.is(':checked')) {
            formArray.push({
              name: checkbox.attr('name'),
              value: 'false'
            });
          }
        });
        let formatedArray = $.map(formArray, function(element) {
            if (element['value'] === "on") {
                element['value'] = "true"
            }
            return element;
        });
        copyToClipboard(generateCustomizedShortcode(formatedArray))
        throwNotice(window.getTranslations.translations.common_notice_shortcode_copied_success)
    });

    /**
     * Protect input type password from modifications in the dom
     */
    const inputPasswordElements = document.querySelectorAll('input[type=password]');
    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'type') {
                mutation.target.value = '';
            }
        });
    });
    Array.from(inputPasswordElements).forEach(function (input) {
        observer.observe(input, {attributes: true});
    });

})

function checkImageUrlForSignedAssets(target) {
    var images = $(target);
    images.each(function () {
        var image = this;
        var imageUrl = this.src;

        $.ajax({
            url: imageUrl,
            type: 'HEAD',
            success: function () {
                // Nothing to do if the image is correct
            },
            error: function () {
                $(image).attr('src', window.defaultImg[0])
                if($(image).hasClass('image-preview')){
                    $(image)
                        .closest('.shortcode-customizer__container__row')
                        .addClass('is-disabled')
                }
            }
        });
    });
}

function updateHolder(className){
    $('.droparea').addClass(className);
    $('.not-dragging-text').hide();
    $('.dragging-text').show();
}

function restoreHolder(className){
    $('.droparea').removeClass(className);
    $('.dragging-text').hide();
    $('.not-dragging-text').show();
}

function hideAlert(element){
    $(element).closest('.alert').hide('slow');
}

function throwWarnModalOnSave(headerText, descriptionText, cancelText, confirmText, event){
    $('.modal').remove();
    let modal = getModal(headerText, descriptionText, cancelText, confirmText);

    $(modal).insertAfter($('.muxvideo-header'));
    $('.modal').toggleClass('is-visible');
	
    $('#modal-btn-cancel').on( 'click', function() {
        $('.modal').toggleClass('is-visible');
    });

    $('#modal-btn-confirm').on( 'click', function() {
        $('.modal').toggleClass('is-visible');
        if(setModalRedirection(event)){
            window.location.href = setModalRedirection(event)
        }
    });
}

function throwWarnModalOnDeleting(headerText, descriptionText, cancelText, confirmText, onConfirm){
    $('.modal').remove();
    let modal = getModal(headerText, descriptionText, cancelText, confirmText);
    
    $(modal).insertAfter($('.muxvideo-header'));
    $('.modal').toggleClass('is-visible');

    $('#modal-btn-cancel').on( 'click', function() {
        $('.modal').toggleClass('is-visible');
    });

    $('#modal-btn-confirm').on( 'click', function() {
        warnUser = false
        $('.modal').toggleClass('is-visible');
        if (typeof onConfirm === 'function') {
            onConfirm();
        }
    });
}


function throwWarnModal(headerText, descriptionText, cancelText, confirmText, upload, uploadItemId){
    let modal = getModal(headerText, descriptionText, cancelText, confirmText);
        
    $(modal).insertAfter($('.muxvideo-header'));

    $('#modal-btn-cancel').on( 'click', function() {
        $('.modal').toggleClass('is-visible');
    });

    $('#modal-btn-confirm').on( 'click', function() {
        warnUser = false
        document.cookie = "muxAssetProgress=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        $('.modal').toggleClass('is-visible');
        if(targetUrl){
            if(upload){
                cancelCurrentUpload(upload, uploadItemId);
            }
            window.location.href = targetUrl
        }
    });
}

function getModal(headerText, descriptionText, cancelText, confirmText){
    return  `<div class="modal">
                <div class="modal-overlay modal-toggle"></div>
                <div class="modal-wrapper modal-transition">
                <div class="modal-header">
                    <p class="modal-heading">${headerText}</p>
                    <p>${descriptionText}</p>
                </div>
                
                <div class="modal-body">
                    <div class="modal-content">
                        <button id="modal-btn-cancel" class="btn btn-cancel btn-modal btn-modal__cancel modal-toggle">${cancelText}</button>
                        <button id="modal-btn-confirm" class="btn btn-save btn-modal btn-modal__save modal-toggle">${confirmText}</button>
                    </div>
                </div>
                </div>
            </div>`;
}


function setModalRedirection(event){
    targetUrl = event.target.href ? event.target.href : event.target.closest('a').href;
    return targetUrl
}

function unloadPage(){
    if(typeof warnUser !== 'undefined' && warnUser){
        return "";
    }
}

/**
 * Display notices to user by frontend
 */

function throwNotice(message){
    $('.muxvideo-notice').remove();
    let alert = 
        `<div class="muxvideo-notice muxvideo-notice__container">
            <div class="muxvideo-notice__msg muxvideo-notice__success"><span><i class="fas fa-check-circle"></i>&nbsp ${message} &nbsp</span><i class="delete-alert fas fa-times-circle"></i></div>	
        </div>`
	$(alert).insertAfter('.muxvideo-header').hide().slideDown( 'fast' );
}

function throwErrorNotice(message){
    $('.muxvideo-notice').remove();
    let errorAlert = 
        `<div class="muxvideo-notice muxvideo-notice__container">
            <div class="muxvideo-notice__msg muxvideo-notice__error"><span><i class="fas fa-exclamation-circle"></i>&nbsp ${message} &nbsp</span><i class="delete-alert fas fa-times-circle"></i></div>	
        </div>`
	$(errorAlert).insertAfter('.muxvideo-header').hide().slideDown( 'fast' );
}

function displayLoader(target, className){
    let loader = 
        `<div class="loading-container ${className} fade-in-transition">
            <div></div>
            <div class="ball-2"></div>
            <div class="ball-3"></div>
        </div>`
    $(target).html(loader);
}

async function customizeShortcode(asset) {
    let assetData = $(asset).data('asset')
    $('#shortcode-customizer').remove();
    let modalContainer = '<div id="shortcode-customizer" class="modal shortcode-customizer"><div class="modal-overlay modal-toggle"></div><div class="loader-customizer-container"></div>'
    $(modalContainer).insertAfter($('.muxvideo-header'));
    $('#shortcode-customizer').addClass('is-visible');
    displayLoader('.loader-customizer-container', '');
    setTimeout(() => {
        displayModalCustomizeShortcode(assetData)
    }, 100);
}

function displayModalCustomizeShortcode(assetData) {
    $.ajax({
        type: "POST",
        url: window.ajaxurl,
        data:{action:'muxvideo_display_modal_customize_shortcode', asset_data: assetData},
        success: function(response) {
            $('.loader-customizer-container').remove();
            $(response).insertAfter($('#shortcode-customizer .modal-overlay'));
            setInterval(() => {
                $('.shortcode-customizer__container').css("opacity", "1")
            }, 100);
            checkImageUrlForSignedAssets('.image-preview')
            $('html').css('overflow','hidden')
        },
        error: function(xhr, status, error) {
            console.error(error);
            $('html').css('overflow','auto')
        }
    });
}

function getJwtDinamically(assetData, time, element){
    return new Promise(function(resolve, reject) {
        $.ajax({
            type: "POST",
            url: window.ajaxurl,
            data:{action:'muxvideo_get_jwt_dinamically', formated_data: assetData, time: time },
            success: function(response) {
                $(element).data('jwt', response)
                resolve(response)
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    })
}

function generateCustomizedShortcode(jsonData){
    let shortcode = '[muxvideo_asset';

    $.each(jsonData, function(index, item) {
        let name = item.name;
        let value = item.value;
    
        value = value.replace(/"/g, '\\"');
    
        shortcode += ' ' + name + '="' + value + '"';
    });
  
    shortcode += ']';
    return shortcode;
}

function hideShortcodeCustomizer() {
    $('html').css('overflow','auto')
    removeFadeOut($('#shortcode-customizer'), 250);
}

function copyToClipboard(string) {
    var $textarea = $('<textarea>');
    $textarea.val(string);
    $('body').append($textarea);
    $textarea.select();
    document.execCommand('copy');
    $textarea.remove();
  }


function removeFadeOut(el, speed) {
    $(el).css({
        "transition": "opacity " + (speed / 1000) + "s ease",
        "opacity": 0
    });

    setTimeout(function() {
        $(el).remove();
    }, speed);
}