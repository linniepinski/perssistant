/**
 * Created by ANDREY on 02.09.15.
 */
function secondsToString(seconds_input) {
    if (seconds_input > 86399) {
        var sec_num = parseInt(seconds_input, 10); // don't forget the second param
        var hours = Math.floor(sec_num / 3600);
        var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
        var seconds = sec_num - (hours * 3600) - (minutes * 60);

        if (hours < 10) {
            hours = "0" + hours;
        }
        if (minutes < 10) {
            minutes = "0" + minutes;
        }
        if (seconds < 10) {
            seconds = "0" + seconds;
        }
        var time = hours + ':' + minutes;//+':'+seconds;

    } else {
        var sec_num = parseInt(seconds_input, 10); // don't forget the second param
        var hours = Math.floor(sec_num / 3600);
        var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
        var seconds = sec_num - (hours * 3600) - (minutes * 60);

        if (hours < 10) {
            hours = "0" + hours;
        }
        if (minutes < 10) {
            minutes = "0" + minutes;
        }
        if (seconds < 10) {
            seconds = "0" + seconds;
        }
        var time = hours + ':' + minutes;//+':'+seconds;

    }
    return time;
}

jQuery(document).ready(function () {

    if (window.location.pathname == '/diary/') {
        var project_id = jQuery("input[name='project_id']").val();
        var current_user = jQuery("input[name='current_user']").val();
        var memoline = jQuery('.with-memo');
        var size = jQuery('.data-block').height() + memoline.height() + parseInt(jQuery('.image-line').css("margin-bottom").replace("px", ""));
        // var margin = (size - jQuery('.center-ver').height())/2;
        //console.log(margin);
        jQuery('.time-block').height(size)//css("margin-bottom", margin).css("margin-top", margin);
        jQuery('.empty-img-confirm').height(jQuery('.img-confirm').height() - 6);
        jQuery('.deleted-item').height(jQuery('.img-confirm').height() + jQuery('.info-block-empty').height() + 3);


        jQuery("#diary_section :checkbox").change(function () {
            var count_time = 0;
            if (jQuery(this).hasClass('row-time-item')) {
                //console.log(jQuery(this).attr('data-target'));
                data_target = jQuery(this).attr('data-target');
                //console.log(data_target);
                if (jQuery(this).attr('checked') == 'checked') {
                    jQuery("input[data-parent='" + data_target + "']").attr("checked", "checked");
                } else {
                    jQuery("input[data-parent='" + data_target + "']").removeAttr("checked");
                }
            } else {
                if (jQuery(this).hasClass('time-item')) {

                }
            }
            checked_items = jQuery("#diary_section .time-item:checked");
            if (checked_items.length == 0) {
                //count_time = 0;
                //console.log(count_time);
                jQuery('.select-count-diary').text('00:00');
            } else {
                checked_items.each(function (index) {
                    //console.log(index);
                    count_time = count_time + parseInt(jQuery(this).attr('data-trace-time'));
                    //console.log(count_time);


                    jQuery('.select-count-diary').text(secondsToString(count_time));
//            var optionthis = jQuery("option[value='" + jQuery(this).attr('value') + "']");
//            var current_value = jQuery(this).attr('value');
//            if (jQuery(this).attr('checked') == 'checked') {
//                jQuery("option[value='" + current_value + "']").attr("selected", "selected");
//            } else {
//                jQuery("option[value='" + current_value + "']").removeAttr("selected");
//            }
//            if (index === count - 1) {
//                optionthis.change();
//            }

                });
            }


        });

        jQuery('#diary_section').tooltip({
            selector: '[data-toggle=tooltip]'
        });
        jQuery('#select_all').on('click', function (event) {
            if (jQuery(this).attr('data-toogle') == '1') {
                jQuery("#diary_section .time-item:checkbox").each(function (index) {
                    jQuery(this).attr("checked", "checked");
                });
                jQuery(this).attr('data-toogle', '0');
                jQuery("#diary_section .time-item:checkbox").change();
            } else {
                jQuery("#diary_section .time-item:checkbox").each(function (index) {
                    jQuery(this).removeAttr("checked");
                });
                jQuery(this).attr('data-toogle', '1');
                jQuery("#diary_section .time-item:checkbox").change();

            }
            //jQuery(this).attr('data-toogle')

        });
        jQuery('#delete_items').on('click', function (event) {
            var string_array = '';
            var memo_starts_array = '';
            checked_items = jQuery("#diary_section .time-item:checked");
            if (checked_items.length == 0) {
            } else {
                checked_items.each(function (index) {
                    string_array = string_array + jQuery(this).val() + ',';
                    this_memo_line = jQuery(this).parent().parent().parent().parent().parent().find('div.memo-line');
                    console.log(this_memo_line);
                    var classList = this_memo_line.attr('class').split(/\s+/);
                    jQuery.each(classList, function (index, item) {
                        current_class_part = item.split('-');

                        // console.log(jQuery(this));
                        if (current_class_part[0] == 'first') {
                            //console.log(current_class_part[2]);
                            memo_starts_array = memo_starts_array + current_class_part[2] + ',';
//console.log(memo_starts_array);
                        }
                    });

                });
            }
            //console.log(string_array);

            jQuery(this).attr('disabled', 'disabled');

            jQuery.ajax({
                url: MyAjax.ajaxurl,//http://perssistant.ai.ukrosoft.com.ua/wp-admin/admin-ajax.php
                type: 'POST',
                data: {
                    action: 'delete_items',
                    array: string_array,
                    memo_starts: memo_starts_array,
                    project_id: project_id,
                    current_user: current_user

                },
                beforeSend: function (xhr) {
                    console.log(xhr);
                },
                success: function (data) {
                    console.log(data);

                    if (data.status == 'success') {
                        AE.pubsub.trigger('ae:notification', {
                            msg: 'Deleted',
                            notice_type: 'success'
                        });

                        jQuery.each(data.memo, function (key, val) {
                            jQuery('div.first-id-' + key).replaceWith(val.memo_line);
                        });
                        checked_items.each(function (index) {
                            current = jQuery(this).parent().parent().parent();
                            current.removeClass('data-block').addClass('deleted-item').html('<div class="empty-img-confirm"></div><div class="row info-block-empty"></div>').height(jQuery('.img-confirm').height());
                        });

                    } else {
                        AE.pubsub.trigger('ae:notification', {
                            msg: 'Error',
                            notice_type: 'error'
                        });
                    }
                    jQuery(this).removeAttr('disabled');
                },

                error: function (errorThrown) {
                    console.log(errorThrown);
                    jQuery(this).removeAttr('disabled');
                }
            });


            jQuery(this).removeAttr('disabled');
            //location.reload();

        });
    }
});

function chatroom_check() {

    jQuery.ajax({
        url: MyAjax.ajaxurl,//http://perssistant.ai.ukrosoft.com.ua/wp-admin/admin-ajax.php
        type: 'POST',
        data: {
            action: 'check_project_list',
            hash: 'f80dacde0b5d7051dd7165fe9740d21806dedef3deb64ae4510b3ac81982d22c',
            id: 22
        },
        beforeSend: function (xhr) {
            console.log(xhr);
        },
        success: function (data) {
            console.log(data);
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });

    jQuery.ajax({
        url: MyAjax.ajaxurl,//http://perssistant.ai.ukrosoft.com.ua/wp-admin/admin-ajax.php
        type: 'POST',
        data: {
            action: 'get_tracker_time_of_week',
            hash: 'f80dacde0b5d7051dd7165fe9740d21806dedef3deb64ae4510b3ac81982d22c',
            id: '22',
            project_id: '1496'

        },
        beforeSend: function (xhr) {
            console.log(xhr);
        },
        success: function (data) {
            console.log(data);
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });
    jQuery.ajax({//login ajax
        url: MyAjax.ajaxurl,//http://perssistant.ai.ukrosoft.com.ua/wp-admin/admin-ajax.php
        type: 'POST',
        data: {
            action: 'auth_trace',
            login: 'Helen',
            password: 'admin',
            method: 'login'
        },
        beforeSend: function (xhr) {
            console.log(xhr);
        },
        success: function (data) {
            console.log(data);
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });
    jQuery.ajax({//view
        url: MyAjax.ajaxurl,//http://perssistant.ai.ukrosoft.com.ua/wp-admin/admin-ajax.php
        type: 'POST',
        data: {
            action: 'get_view_data',
            id: '838',
            view: 'project'
        },
        beforeSend: function (xhr) {
            console.log(xhr);
        },
        success: function (data) {
            console.log(data);
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });
    jQuery.ajax({//view
        url: MyAjax.ajaxurl,//http://perssistant.ai.ukrosoft.com.ua/wp-admin/admin-ajax.php
        type: 'POST',
        data: {
            action: 'get_view_data',
            id: '22',
            role: 'freelancer',
            view: 'user'
        },
        beforeSend: function (xhr) {
            console.log(xhr);
        },
        success: function (data) {
            console.log(data);
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });
    jQuery.ajax({//view
        url: MyAjax.ajaxurl,//http://perssistant.ai.ukrosoft.com.ua/wp-admin/admin-ajax.php
        type: 'POST',
        data: {
            action: 'get_view_data',
            id: '67',
            role: 'employer',
            view: 'user'
        },
        beforeSend: function (xhr) {
            console.log(xhr);
        },
        success: function (data) {
            console.log(data);
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });
    jQuery.ajax({//send data
        url: MyAjax.ajaxurl,//http://perssistant.ai.ukrosoft.com.ua/wp-admin/admin-ajax.php
        type: 'POST',
        cache: false,
        contentType: 'multipart/form-data',
        processData: false,
        data: {
            action: 'get_trace_data',
            hash: '73ff4c9e5339fb486b7560524e8151b6d80f3bcbbdd17f0251b6cd479f2ecee8'

        },
        beforeSend: function (xhr) {
            console.log(xhr);
        },
        success: function (data) {
            console.log(data);
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });
    jQuery.ajax({//send data
        url: 'http://perssistant.ai.ukrosoft.com.ua/test.php',
        type: 'POST',
        cache: false,
        contentType: 'multipart/form-data',
        processData: false,
        data: {
            action: 'get_trace_data',
            hash: '73ff4c9e5339fb486b7560524e8151b6d80f3bcbbdd17f0251b6cd479f2ecee8'

        },
        beforeSend: function (xhr) {
            console.log(xhr);
        },
        success: function (data) {
            console.log(data);
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });
}

