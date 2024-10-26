let dataHasBeenModified = false;
let securitySettingsModified = false;
$(document).ready(function () {
    let idValue = $('#muxvideo_token_id').val();
    let secretValue = $('#muxvideo_token_secret').val();
    let regex_id = /^[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}$/;
    let regex_secret = /^.{75}$/
    let regex

    $("#edit-keys-btn").on('click', function () {
        $('.config-ready').removeClass('config-ready')
        $('#edit-keys-btn')
            .addClass('disabled')
            .css('opacity', '0.4')
        $('#muxvideo_token_secret').show()
    });

    $("#muxvideo_token_id, #muxvideo_token_secret").on('click keydown contextmenu paste', function (e) {

        $('#success-msg, #unauth-msg, #error-msg').hide();

        switch (e.target.id) {
            case 'muxvideo_token_id':
                regex = regex_id;
                break;

            case 'muxvideo_token_secret':
                regex = regex_secret
                break;

            default:
                break;
        }
        setTimeout(function () {
            validateInput($('#' + e.target.id), regex)
            if (idValue != $('#muxvideo_token_id').val() || secretValue != $('#muxvideo_token_secret').val()) {
                dataHasBeenModified = true
            }
        }, 100);
    });

    function unloadPage() {
        if (dataHasBeenModified) {
            return "";
        }
    }

    window.onbeforeunload = unloadPage;

    $('#submit').on( 'click', function () {
        dataHasBeenModified = false;
    });

    $("body").on("click", "a", function (event) {
        if (!dataHasBeenModified) {
            return "";
        }
        dataHasBeenModified = false;
        throwWarnModalOnSave(
            window.getTranslations.translations.settings_warn_modal_save_title,
            window.getTranslations.translations.settings_warn_modal_save_description,
            window.getTranslations.translations.settings_warn_modal_save_cancel,
            window.getTranslations.translations.settings_warn_modal_save_exit,
            event,
        );
        if(securitySettingsModified){
            $('.tabs-nav li').removeClass('tab-active');
            $('#nav-tab-2').addClass('tab-active');
            $('.tabs-container .tab').hide();
            $('#tab-2').show();
        }
        event.preventDefault()
        event.stopPropagation()
    });

    /** jQuery input tags to add allowed domains */
    const tagInput = $('#tag-input');
    const tagContainer = $('#tag-container .tag-container__tags');
    let allowedDomains = [];

    if($('.tag-text').length){
        $('.tag-text').each(function(){
            allowedDomains.push($.trim($(this).text()));
        });
    }

    function addTag() {
        let tagText = tagInput.val().trim();
        dataHasBeenModified = true;
        securitySettingsModified = true;

        if (tagText !== '') {
            let tag = $('<div>', { class: 'tag' });
            let tagTextSpan = $('<span>', { class: 'tag-text', text: tagText });
            let tagRemoveSpan = $('<span>', { class: 'tag-remove', text: 'x' });
            
            if(allowedDomains.length >= 10){
                throwErrorNotice(window.getTranslations.translations.settings_security_notice_limit_domains)
            }else{
                tag.append(tagTextSpan, tagRemoveSpan);
                tagContainer.append(tag);
                allowedDomains.push(tagText);
            }

            tagInput.val('');
        }
    }

    function removeTag() {
        let tagText = $(this).siblings('.tag-text').text();
        let index = allowedDomains.indexOf(tagText);
        dataHasBeenModified = true;
        securitySettingsModified = true;

        if (index !== -1) {
            allowedDomains.splice(index, 1);
            $(this).parent('.tag').remove();
        }
    }

    $('#switch-referrer').on('change', function() {
        dataHasBeenModified = true;
        securitySettingsModified = true;
    })

    $('#tag-form').submit(function (event) {
        event.preventDefault();
        let allowNoReferrer = $('#switch-referrer').prop('checked');
        let formData = {
            allowed_domains: [...allowedDomains],
            allow_no_referrer: allowNoReferrer
        };
        let jsonFormData = JSON.stringify(formData);
        tagInput.val('');
        createUpdatePlaybackRestrictions(jsonFormData);
    });

    tagInput.on('keydown', function (event) {
        if (event.key === 'Enter') {
            
            event.preventDefault();
            if(isValidDomain(tagInput.val(), allowedDomains)){
                $('.muxvideo-notice').remove();
                addTag();
            }else{
                throwErrorNotice(window.getTranslations.translations.settings_security_notice_invalid_domain)
            }
        }
    });

    tagContainer.on('click', '.tag-remove', removeTag);

});

/**
 * Validations
 */
function isValidDomain(domain, allowedDomains) {
    let regex = new RegExp(/^(?!-)(\*\.|[A-Za-z0-9-]+\.){0,1}[A-Za-z0-9-]+\.[A-Za-z]{2,6}$/);
    if (domain == null) {
        return false;
    }
    return (regex.test(domain) && $.inArray(domain, allowedDomains) == -1) ? true : false;
}

function validateInput(inputField, regex) {
    var inputValue = inputField.val();
    var isValid = regex.test(inputValue);
    if (isValid) {
        inputField
            .addClass("valid")
            .removeClass("invalid")
            .siblings('.error-icon, .error-msg').hide();
    } else {
        inputField
            .removeClass("valid")
            .addClass("invalid")
            .siblings('.error-icon, .error-msg').show();
    }

    if ($('#muxvideo_token_id').hasClass("invalid") || $('#muxvideo_token_secret').hasClass("invalid")) {
        $("#submit").attr("disabled", true);
    } else {
        $("#submit").attr("disabled", false);
    }
}