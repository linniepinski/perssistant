/**
 * Created by ANDREY on 28.10.15.
 */

jQuery(document).ready(function ($) {
    jQuery('#custom_payment').submit(function () {
        event.preventDefault();
        amount = jQuery('#custom_amount').val();
        descr = jQuery('#custom_description').val();
        if (descr.trim() == '') {
            alert('Enter comment');
            //return false
        } else {
            jQuery.ajax({
                url: MyAjax.ajaxurl,//http://perssistant.ai.ukrosoft.com.ua/wp-admin/admin-ajax.php
                type: 'POST',
                data: {
                    action: 'get_payment_form',
                    custom_amount: amount,
                    custom_description: descr

                },
                beforeSend: function (xhr) {
                    //console.log(xhr);
                },
                success: function (data) {
                    //console.log(data);
                    jQuery('#created_payment').html(data);
                    // jQuery('.pay-button').addClass('btn btn-primary btn-block btn-custom-price')
                },
                error: function (errorThrown) {
                    //console.log(errorThrown);
                }
            });
        }
        return false;
    });
    jQuery('#stripe_modal').on('show.bs.modal', function (event) {
        var button = jQuery(event.relatedTarget);
        var amount = button.data('amount');
        var title = button.data('title');
        var modal = jQuery(this);
        if (typeof amount == 'undefined') {
            modal.find('#wp_stripe_amount').removeAttr('readonly');
            modal.find('#wp_stripe_amount').val('');
            modal.find('.modal-title').text(title);
            modal.find('#wp_stripe_comment').val(title);
        }
        else {
            modal.find('.modal-title').text(title);
            modal.find('#wp_stripe_amount').val(amount);
            modal.find('#wp_stripe_amount').attr('readonly', true);
            modal.find('#wp_stripe_comment').val(title);
        }
    });
    jQuery('#stripe_modal').on('hide.bs.modal', function (event) {
        var modal = jQuery(this);
            modal.find('#custom_amount').val('');
            modal.find('#created_payment').html('');
    });

    });


