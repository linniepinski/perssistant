/**
 * Created by ANDREY on 02.09.15.
 */

var update_request;
var last_chat_id;
var contact_with_user;

jQuery(document).ready(function () {
    var options = {
        url: MyAjax.ajaxurl,
        type: 'post',
        data: {
            action: 'myajax_submit'
        },
        dataType: 'xml',
        target: '#chat_his',
        beforeSubmit: showRequest,
        success: showResponse,
        clearForm: true
    };
    jQuery('#chat').submit(function () {
        jQuery(this).ajaxSubmit(options);
        jQuery("#send").addClass("disabled");
        jQuery("div.chat_history").mCustomScrollbar("scrollTo", "bottom");
        return false;
    });
});


function showRequest(formData, jqForm, options) {
      //  console.log(jQuery('#contact_with').val());
    if (jQuery('#contact_with').val() == ''){
        status_chat('Error.', ' Select a contact.', 'alert-danger');
        return false;
    }else{
        status_chat('Wait...', ' Message sending.', 'alert-info');
        return true;
    }
}
function showResponse(responseText, statusText, xhr, $form) {
    jQuery('.panell').remove();
    jQuery("#send").removeClass("disabled");
    if (jQuery(responseText).find("errors").text() !== '') {
        status_chat('File upload failed!', jQuery(responseText).find("errors").text() + ' <strong>Message sent.</strong>', 'alert-warning');
    }
    else {
        setTimeout("status_chat('Success!',' Message sent.','alert-success')", 2500);
    }
    var htmlItem = jQuery(responseText).find("response_data").text();
    jQuery("div.chat_history .mCSB_container").append(htmlItem);
    jQuery('#chat').clearForm();
    jQuery("div.chat_history").mCustomScrollbar("scrollTo", "bottom");
}


function chatroom_check_updates() {
//    chat_id_count = jQuery("div[chat_id]").length;
    last_chat_id = jQuery("div[chat_id]:last").attr('chat_id');
    contact_with_user = jQuery('#contact_with').val();

    jQuery.ajax({
        url: MyAjax.ajaxurl,
        type: 'POST',
        data: {
            //chat_id_count: chat_id_count,
            last_chat_id: last_chat_id,
            contact_with_user: contact_with_user,
            action: 'myajax_check_updates'
        },
        beforeSend: function (xhr) {
            if (update_request == 1) {
                xhr.abort();
            }
            update_request = 1;
        },
        success: function (data) {
            var htmlItem = jQuery(data).find("response_data").text();
            jQuery("div.chat_history .mCSB_container").append(htmlItem);
            jQuery("#right-column-chat > div.chat_history").mCustomScrollbar("update");

        },
        complete: function () {
            update_request = 0;
            setTimeout("chatroom_check_updates()", 5000);
        },
        error: function (errorThrown) {
            status_chat('Error!', 'Something went wrong.', 'alert-danger');
            console.log(errorThrown);
            setTimeout("chatroom_check_updates()", 20000);
        }
    });
}

function chatroom_check_online() {

    jQuery.ajax({
        url: MyAjax.ajaxurl,
        type: 'POST',
        data: {
            action: 'myajax_check_online'
        },
        beforeSend: function (xhr) {

        },
        success: function (data) {
            jQuery("section.sieve-custom .mCSB_container").html(data);
            CountSearchMatches();
            setTimeout("chatroom_check_online()", 60000);
        },
        error: function (errorThrown) {
            status_chat('Error!', 'Something went wrong.', 'alert-danger');
            console.log(errorThrown);
            setTimeout("chatroom_check_online()", 10000);
        }
    });


}
function chatroom_refresh() {
    var chatblockUi = new AE.Views.BlockUi();
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        type: 'POST',
        data: {
            action: 'myajax_refresh',
            contact_with_user: contact_with_user
        },
        beforeSend: function (xhr) {
            //jQuery('.right-column-chat').dataLoader();
            chatblockUi.block(jQuery('.right-column-chat'));
        },
        success: function (data) {
            //console.log(contact_with_user);
            jQuery("div.chat_history .mCSB_container").empty();
            jQuery("div.chat_history .mCSB_container").html(data);
            jQuery("#right-column-chat > div.chat_history").mCustomScrollbar("update");
            //jQuery('.right-column-chat').dataLoader('loaded');
            chatblockUi.unblock(jQuery('.right-column-chat'));

            jQuery("div.chat_history").mCustomScrollbar("scrollTo", "bottom");
        },
        complete: function () {
            setTimeout("chatroom_check_updates()", 5000);
        },
        error: function (errorThrown) {
            status_chat('Error!', 'Something went wrong.', 'alert-danger');
            console.log(errorThrown);
            setTimeout("chatroom_check_updates()", 5000);
        }
    });
}
function chatroom_contact() {
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        type: 'POST',
        data: {
            action: 'myajax_contact',
            contact_with_user: contact_with_user
        },
        beforeSend: function (xhr) {
        },
        success: function (data) {
            jQuery("#contact").html(data);
        },
        error: function (errorThrown) {
            status_chat('Error!', 'Something went wrong.', 'alert-danger');
            console.log(errorThrown);
        }
    });
}
function chatroom_notifications_everywhere() {
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        type: 'POST',
        data: {
            action: 'myajax_notifications_everywhere'
        },
        beforeSend: function (xhr) {

        },
        success: function (data) {
            if (jQuery(data).find("status").text() == 'success') {
                sendNotification(jQuery(data).find("sender").text(), {
                    body: jQuery(data).find("message").text(),
                    icon: 'icon.jpg',
                    dir: 'auto'
                });
            }
            setTimeout("chatroom_notifications_everywhere()", 5000);
        },
        complete: function () {
        },
        error: function (errorThrown) {
            status_chat('Error!', 'Something went wrong.', 'alert-danger');
            console.log(errorThrown);
            setTimeout("chatroom_notifications_everywhere()", 30000);
        }
    });
}
function chatroom_count() {
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        type: 'POST',
        data: {
            action: 'myajax_count'
        },
        beforeSend: function (xhr) {
        },
        success: function (data) {
//            console.log(data);
            jQuery(".count-chat").html(data.trim());
            setTimeout("chatroom_count()", 60000);

        },
        complete: function () {
        },
        error: function (errorThrown) {
            status_chat('Error!', 'Something went wrong.', 'alert-danger');
            console.log(errorThrown);
            setTimeout("chatroom_count()", 120000);
        }
    });
}
function chatroom_loadprev() {
    var prev_chat_id = jQuery("div[chat_id]:first").attr('chat_id');
    contact_with_user = jQuery('#contact_with').val();
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        type: 'POST',
        data: {
            action: 'myajax_loadprev',
            contact_with_user: contact_with_user,
            prev_chat_id: prev_chat_id
        },
        beforeSend: function (xhr) {
            jQuery('.panell').remove();
            jQuery("#loadprev").addClass("disabled");
            status_chat('Loading...', 'Wait...', 'alert-info');
            //jQuery('#loadprev').button('loading');
            jQuery("#right-column-chat > div.chat_history").mCustomScrollbar("update");
        },
        success: function (data) {
//            console.log(data);
            if(data.status == true){
                if(data.type == 'empty'){
                    jQuery("#loadprev").remove();
                    status_chat('', data.msg, 'alert-warning');
                }
            }else{
                jQuery("#loadprev").after(data);
                status_chat('Success!', ' Message history loaded.', 'alert-success');
                jQuery("#loadprev").removeClass("disabled");
            }
        },
        error: function (errorThrown) {
            status_chat('Error!', 'Something went wrong.', 'alert-danger');
            console.log(errorThrown);
        }
    });
}
function status_chat(strong, message, cclass) {
    jQuery("#status-alert").show();
    jQuery("#status-alert").addClass(cclass);
    jQuery(".alert-status").html('<strong>' + strong + '</strong>' + message);
    jQuery("#status-alert").fadeTo(2000, 500).slideUp(500, function () {
        jQuery("#status-alert").hide().removeClass(cclass);
    });
}

function resizeChat() {
    var body = jQuery('body');
    //var body_wrapper = jQuery('.body-wrapper');
    var parallax = jQuery('#chat');
    var parallax_banner = parallax.closest('#chat');

    if (jQuery(window).width() <= 767) {
        parallax_banner.height(jQuery(window).height('100%'));
    } else {
        parallax_banner.height(Math.round(jQuery(window).height() * 0.75));
    }
}
function OnKeyCodeEvents() {
    jQuery('input.inter-search').keydown(function(e) {
        if(e.keyCode == 13) {
            return false;
        }
    });
    jQuery('.message_box_chat').on('keydown', function (event) {
        if (event.keyCode == 13 && event.shiftKey) {
            event.preventDefault();
            var content = this.value;
            var caret = getCaret(this);
            this.value = content.substring(0, caret) + "\n" + content.substring(caret, content.length);
            event.stopPropagation();

        } else if (event.keyCode == 13) {
            event.preventDefault();
//            console.log('192');
            var options = {
                url: MyAjax.ajaxurl,
                type: 'post',
                data: {
                    action: 'myajax_submit'
                },
                dataType: 'xml',
                target: '#chat_his',
                beforeSubmit: showRequest,
                success: showResponse,
                clearForm: true
            };

            jQuery(this.form).ajaxSubmit(options);
            jQuery("#send").addClass("disabled");
            jQuery("div.chat_history").mCustomScrollbar("scrollTo", "bottom");
            return false;
        }
    });
}

jQuery(document).ready(function () {
    var options_for_invate = {
        url: MyAjax.ajaxurl,
        type: 'post',
        data: {
            action: 'invate_freelancer'
        },
        dataType: 'json',
        //target: '#chat_his',
        beforeSubmit: showRequestInvate,
        success: showResponseInvate,
        clearForm: true
    };
    jQuery('#invate_freelancer_form').submit(function () {
        jQuery(this).ajaxSubmit(options_for_invate);
        return false;
    });
});


function showRequestInvate(formData, jqForm, options) {
    jQuery('#popup_invate_freelancer_to_chat').modal('hide');
    return true;
}
function showResponseInvate(responseText, statusText, xhr, $form) {
    if(responseText.success){
        AE.pubsub.trigger('ae:notification', {
            msg : responseText.msg,
            notice_type: 'success'
        });
    } else {
    AE.pubsub.trigger('ae:notification', {
        msg : responseText.msg,
        notice_type: 'error'
    });
}
}
function invate_freelancer(user_id_receiver, user_id_sender, project_id_invate, display_name_sender) {

    var href = window.location.href;
    //var htmllinkToProject = '<a href="' + href + '" target="_blank">'+ jQuery('.content-title-project-item').text() +'</a>';
    var textInvate ='Hello!My name is ' + display_name_sender + ' and I would like to discuss "'+ jQuery('.content-title-project-item').text() +'" project with you!';
    jQuery('#popup_invate_freelancer_to_chat .invate-to-chat-message').text(textInvate);
    jQuery('#sender_id').val(user_id_sender);
    jQuery('#guid').val(href);
    jQuery('#title').val(jQuery('.content-title-project-item').text());
    jQuery('#project_id_invate').val(project_id_invate);
    jQuery('#reciever_id').val(user_id_receiver);

    jQuery('#popup_invate_freelancer_to_chat').modal('show');
    centerModals(jQuery("#popup_invate_freelancer_to_chat"));
}
function Init_CountSearchMatches(){
    jQuery('.inter-search').focus(function () {
        if (jQuery('.inter-search').val().length > 0) {
            jQuery('.count-matches').fadeIn('slow');
        }
    }).blur(function () {
        jQuery('.count-matches').fadeOut('fast');
    });

}

function CountSearchMatches(){
    var label_output = jQuery('.count-matches');
    var count_search_result = jQuery('.sieve .item_contact:visible').length;
    text_output = 'Matching results : <span>' + count_search_result + '</span>';
    label_output.html(text_output);
    if (jQuery('.inter-search').val().length > 0) {
        label_output.fadeIn('slow');
    }else{
        label_output.fadeOut('fast');
    }
}

function InitGUI() {
    resizeChat();
    jQuery("#contacts > section").mCustomScrollbar({
        theme: "dark",
        updateOnContentResize: true,
        scrollButtons: {
            enable: true
        }
    });
    jQuery("#right-column-chat > div.chat_history").mCustomScrollbar({
        theme: "dark",
        updateOnContentResize: true,
        scrollButtons: {
            enable: true
        }
    });
    jQuery('input[type=file]').bootstrapFileInput();
    jQuery('#chat').tooltip({
        selector: '[data-toggle=tooltip]'
    });
    jQuery("section.sieve").sieve({ itemSelector: "div" });
    Init_CountSearchMatches();

}

jQuery(window).on('resize', function () {
    resizeChat();
});

function centerModals($element) {
    var $modals;
    if ($element.length) {
        $modals = $element;
    } else {
        $modals = jQuery('.modal-vcenter:visible');
    }
    $modals.each( function(i) {
        var $clone = jQuery(this).clone().css('display', 'block').appendTo('body');
        var top = Math.round(($clone.height() - $clone.find('.modal-content').height()) / 2);
        top = top > 0 ? top : 0;
        $clone.remove();
        jQuery(this).find('.modal-content').css("margin-top", top);
    });
}
jQuery('.modal-vcenter').on('show.bs.modal', function(e) {
    centerModals(jQuery(this));
});
jQuery(window).on('resize', centerModals);

jQuery(document).ready(function () {
    chatroom_count();
    chatroom_notifications_everywhere();

    //alert(window.location.pathname);
    if (window.location.pathname == '/chat-room/') {
        InitGUI();
        if (jQuery('#contact_with').val() != '') {
            contact_with_user = jQuery('#contact_with').val();
            chatroom_refresh();
            chatroom_contact();
        }
        chatroom_check_online();
        jQuery("#right-column-chat > div.chat_history").on('click', '#loadprev', function () {
            chatroom_loadprev();
        });
        jQuery("#contacts > section").on('click', '.item_contact', function () {
            contact_with_user = jQuery(this).attr('id_contact');
            jQuery('#contact_with').val(jQuery(this).attr('id_contact'));
            chatroom_refresh();
            chatroom_contact()
        });
        OnKeyCodeEvents();
    }
});

/////sendNotification /\/\/\/\/\/\/\/\
function sendNotification(title, options) {
    if (!("Notification" in window)) {
        //alert('Ва�? бра�?зер не поддерживает HTML Notifications, его необходимо обновить.');
    }
    else if (Notification.permission === "granted") {
        var notification = new Notification(title, options);

        function clickFunc() {
            //alert('Пользователь кликн�?л на �?ведомление');
        }

        notification.onclick = clickFunc;
    }
    else if (Notification.permission !== 'denied') {
        Notification.requestPermission(function (permission) {
            if (permission === "granted") {
                var notification = new Notification(title, options);
            } else {
                // alert('Вы запретили показывать �?ведомления'); // Юзер отклонил на�? запро�? на показ �?ведомлений
            }
        });
    } else {
    }
}
/////sendNotification end /\/\/\/\/\/\/\/

//// search contacts \/\/\/\/\/\/\/\/
(function () {
    var $;

    $ = jQuery;

    $.fn.sieve = function (options) {
        var compact;
        compact = function (array) {
            var item, _i, _len, _results;
            _results = [];
            for (_i = 0, _len = array.length; _i < _len; _i++) {
                item = array[_i];
                if (item) {
                    _results.push(item);
                }
            }
            return _results;
        };
        return this.each(function () {
            var container, searchBar, settings;
            container = $(this);
            settings = $.extend({
                searchInput: null,
                searchTemplate: "<div id='search_chat' class='form-group'><label>Search contact:</label> <input type='text' class='inter-search form-control'><div class='count-matches'></div></div>",
                itemSelector: "tbody tr",
                textSelector: null,
                toggle: function (item, match) {
                    return item.toggle(match);
                },
                complete: function () {
//                    console.log('wtf');
                    CountSearchMatches();
                }
            }, options);
            if (!settings.searchInput) {
                searchBar = $(settings.searchTemplate);
                settings.searchInput = searchBar.find("input");
                container.before(searchBar);
            }
            return settings.searchInput.on("keyup.sieve change.sieve", function (event) {
                var items, query;
                query = compact($(this).val().toLowerCase().split(/\s+/));
                items = container.find(settings.itemSelector);
                items.each(function () {
                    var cells, item, match, q, text, _i, _len;
                    item = $(this);
                    if (settings.textSelector) {
                        cells = item.find(settings.textSelector);
                        text = cells.text().toLowerCase();
                    } else {
                        text = item.text().toLowerCase();
                    }
                    match = true;
                    for (_i = 0, _len = query.length; _i < _len; _i++) {
                        q = query[_i];
                        match && (match = text.indexOf(q) >= 0);
                    }
                    return settings.toggle(item, match);
                });
                return settings.complete();
            });
        });
    };

}).call(this);
//// search contacts end \/\/\/\/\/\/\/\/

//// loader hover /\/\//\/\/\/
jQuery.fn.dataLoader = function (options) {

    if (!this.length) return this;

    if (typeof(options) == 'undefined') options = {};

    if (typeof(options) == 'string') {

        switch (options) {
            case 'loaded':
                if (this.data('blinder')) {
                    this.data('blinder').remove();
                }
                break;
        }

        return this;
    }

    if (typeof(options.loader) == 'undefined') {
        options.loader = '/wp-content/themes/freelanceengine/includes/aecore/assets/img/loading.gif';
        //options.loader = '/wp-content/plugins/Perssistant-Chat/js/ajax-loader.gif';
    }

    if (typeof(options.opacity) == 'undefined') {
        options.opacity = '0.8';
    }

    if (typeof(options.color) == 'undefined') {
        options.color = '#ffffff';
    }

    if (typeof(options.zIndex) == 'undefined') {
        options.zIndex = '65000';
    }

    if (this.data('blinder')) {
        this.data('blinder').remove();
    }

    var blinder = jQuery('<div></div>')
        .css('opacity', options.opacity)
        .css('background-color', options.color)
        .css('background-size', '40px')
        .css('background-image', 'url(' + options.loader + ')')
        .css('background-repeat', 'no-repeat')
        .css('background-position', '50% 50%')
        .css('display', 'block')
        .css('position', 'absolute')
        .css('z-index', options.zIndex)
        .css('left', this.position().left + parseInt(this.css('margin-left')))
        .css('top', this.position().top + parseInt(this.css('margin-top')))
        .width(this.outerWidth())
        .height(this.outerHeight())
        .appendTo(this.parent());

    this.data('blinder', blinder);

    return this;
};
//// loader hover end /\/\/\/\/\/

///// textarea shift+enter=nextline\/\/\/\
function getCaret(el) {
    if (el.selectionStart) {
        return el.selectionStart;
    } else if (document.selection) {
        el.focus();

        var r = document.selection.createRange();
        if (r == null) {
            return 0;
        }

        var re = el.createTextRange(),
            rc = re.duplicate();
        re.moveToBookmark(r.getBookmark());
        rc.setEndPoint('EndToStart', re);

        return rc.text.length;
    }
    return 0;
}
//// textarea shift+enter=nextline end\/\/\/\