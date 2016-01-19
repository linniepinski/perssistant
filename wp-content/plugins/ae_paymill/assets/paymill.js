(function($, Views) {
    Views.PaymillForm = Views.Modal_Box.extend({
        el: $('div#paymill_modal'),
        events: {
            'submit form#paymill_form': 'submitPaymill'
        },
        initialize: function(options) {
            Views.Modal_Box.prototype.initialize.apply(this, arguments);
            // bind event to modal
            _.bindAll(this, 'paymillResponseHandler', 'setupData');
            // Paymill.setPublishableKey(ae_Paymill.public_key);
            this.blockUi = new Views.BlockUi();
            // catch event select extend gateway
            AE.pubsub.on('ae:submitPost:extendGateway', this.setupData);
        },
        // callback when user select Paymill, set data and open modal
        setupData: function(data) {
            if (data.paymentType == 'paymill') {
                this.openModal();
                this.data = data,
                plans = JSON.parse($('#package_plans').html());
                var packages = [];
                _.each(plans, function(element) {
                    if (element.sku == data.packageID) {
                        packages = element;
                    }
                })
                var align = parseInt(ae_paymill.currency.align);
                if (align) {
                    var price = ae_paymill.currency.icon + packages.et_price;
                } else {
                    var price = packages.et_price + ae_paymill.currency.icon;
                }

                this.data.price = packages.et_price;

                //if($('.coupon-price').html() !== '' && $('#coupon_code').val() != '' ) price  =   $('.coupon-price').html();
                this.$el.find('span.plan_name').html(packages.post_title + ' (' + price + ')');
                this.$el.find('span.plan_desc').html(packages.post_content);
            }
        },
        // catch user event click on pay
        submitPaymill: function(event) {
            event.preventDefault();
            var $form = $(event.currentTarget),
                data = {
                    number: $('.card-number').val(), // required
                    exp_month: $('.card-expiry-month').val(), // required
                    exp_year: $('.card-expiry-year').val(), // required
                    cvc: $('.card-cvc').val(), // required
                    amount_int: parseInt(this.data.price*100), // required, e.g. "4900" for 49.00 EUR
                    currency: ae_paymill.currency.code // required
                };
            this.blockUi.block($form);
            paymill.createToken(data, this.paymillResponseHandler);
        },
        /**
         * validate card and return mesage 
         * @param error
         * @since 1.0
         * @author Dakachi
         */
        validateCard: function(error) {
            var type = error.apierror,
                msg = ae_paymill.unknow_error;
            switch (type) {
                case 'field_invalid_card_number':
                    $('#paymill_number').addClass('error');
                    $('#paymill_number').focus();
                    msg = ae_paymill.card_number_msg;
                    break;
                case 'field_invalid_card_exp':
                    $('#exp_year').addClass('error');
                    $('#exp_year').focus();
                    msg = ae_paymill.exp_msg;
                    break;
                case 'field_invalid_card_cvc':
                    $('#paymill_cvc').addClass('error');
                    $('#paymill_cvc').focus();
                    msg = ae_paymill.cvc_msg;
                    break;
                case 'invalid_public_key':
                    msg = ae_paymill.public_key_msg
                    break;
                default:
                    msg = msg.unknow_error;
                    break;
            }
            return msg;
        },
        /**
         * handle paymill response 
         * @param error
         * @param response
         * @since 1.0
         * @author Dakachi
         */
        paymillResponseHandler: function(error, response) {
            var view = this;
            if (error) {
                AE.pubsub.trigger('ae:notification', {
                    msg: view.validateCard(error),
                    notice_type: 'error'
                });
                $("#button_paymill").removeAttr("disabled");
                this.blockUi.unblock();
                return false;
            } else {
                $("#button_paymill").attr("disabled", "disabled");
                view.submitPayment(response);
            }
        },
        /**
         * send payment request to server 
         * @param res
         * @since 1.0
         * @author Dakachi
         */
        submitPayment: function(res) {
            var view = this;
            view.data.token = res.token;
            $.ajax({
                type: 'post',
                url: ae_globals.ajaxURL,
                data: view.data,
                beforeSend: function() {
                    //$("#submit_paymill").removeAttr("disabled");
                },
                success: function(res) {
                    view.blockUi.unblock();
                    if (res.success) {
                        view.closeModal();
                        window.location = res.data.url;
                    } else {
                        $("#button_paymill").removeAttr("disabled");
                        AE.pubsub.trigger('ae:notification', {
                            msg: res.msg,
                            notice_type: 'error'
                        });
                    }
                }
            });
        }
    });
    // init Paymill form
    $(document).ready(function() {
        new Views.PaymillForm();
    });
})(jQuery, AE.Views);