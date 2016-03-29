jQuery(document).ready(function ($) {
    if(typeof is_login != 'undefined') {
        jQuery.ajax({
            url: MyAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'login_check'
            },
            beforeSend: function (xhr) {
            },
            success: function (data) {
                if (data.is_login != is_login) {
                    location.reload();
                }
            },
            complete: function () {
            },
            error: function (errorThrown) {
            }
        });
    }
});

