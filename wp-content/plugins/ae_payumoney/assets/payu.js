(function($, Views) {
    Views.PayuForm = Views.Modal_Box.extend({
        el: $('div#payu_modal'),
        events: {
            'submit form#payu_form': 'submitPayu'
        },
        initialize: function(options) {
            Views.Modal_Box.prototype.initialize.apply(this, arguments);
            // bind event to modal
            _.bindAll(this, 'setupData');
            this.blockUi = new Views.BlockUi();
            // catch event select extend gateway
            AE.pubsub.on('ae:submitPost:extendGateway', this.setupData);
        },
        // callback when user select Paymill, set data and open modal
        setupData: function(data) {
            if (data.paymentType == 'payu') {
                this.openModal();
                this.data = data,
                plans = JSON.parse($('#package_plans').html());
                var packages = [];
                _.each(plans, function(element) {
                    if (element.sku == data.packageID) {
                        packages = element;
                    }
                })
              var align = parseInt(ae_payu.currency.align);
                if (align) {
                    var price = ae_payu.currency.icon + packages.et_price;
                } else {
                    var price = packages.et_price + ae_payu.currency.icon;
                }

                this.data.price = packages.et_price;

                //if($('.coupon-price').html() !== '' && $('#coupon_code').val() != '' ) price  =   $('.coupon-price').html();
                this.$el.find('span.plan_name').html(packages.post_title + ' (' + price + ')');
                this.$el.find('span.plan_desc').html(packages.post_content);
            }
        },
        // catch user event click on pay
        submitPayu: function(event) {
            event.preventDefault();
            var $form = $(event.currentTarget),
                data = this.data;
                data.payu_firstname = $form.find('#payu_firstname').val();
                data.payu_lastname  = $form.find('#payu_lastname').val();
                data.payu_email     = $form.find('#payu_email').val();
                data.payu_phone     = $form.find('#payu_phone').val();
                if(data.payu_firstname=="" && data.payu_emai==""){
                    alert("error");
                    return false;
                }
             var view = this;   
                //neu muon post sang setup payment
            $.ajax({
                type : 'post',
                url : ae_globals.ajaxURL,
                data : data,
                 beforeSend: function() {
                    view.blockUi.block('#button_payu');
                },
                success:function(res){
                   // view.blockUi.unblock();
                    if(res.success){
                        //return false;
                    //alert(res.data.salt);
                        $('#payu_hash').val(res.data.hash);
                        $('#payu_txnid').val(res.data.txnid);
                        $('#payu_key').val(res.data.key);
                        $("#payu_amount").val(res.data.amount);
                        $("#payu_firstname_h").val(res.data.firstname);
                        $("#payu_email_h").val(res.data.email);
                        $("#payu_phone_h").val(res.data.phone);
                        $("#payu_productinfo").val(res.data.productinfo);
                        $("#payu_hidden_form").attr("action", res.data.url);
                        $('#payu_surl').val(res.data.surl);
                        $('#payu_furl').val(res.data.furl);
                        $('#payu_curl').val(res.data.furl);
                        $('#button_payu_h').trigger("click");
                    }else{
                        AE.pubsub.trigger('ae:notification', {
                            msg: ae_payu.errorpost,
                            notice_type: 'error'
                        });
                        return false
                    }
                }
                
            });
        },
    });
    // init Payu form
    $(document).ready(function() {
        new Views.PayuForm();
    });
})(jQuery, AE.Views);

function ae_payu_validateEmail(email) {   
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([\w\W\-0-9]+\.)+[\w\W]{2,}))$/;
    return re.test(email);
}
jQuery('#button_payu').click(function(){
    if(jQuery("#payu_firstname").val()==""){
         AE.pubsub.trigger('ae:notification', {
            msg: ae_payu.empty_field,
            notice_type: 'error'
        });
        jQuery("#payu_firstname").focus();
        return false    
    }
    $email = jQuery("#payu_email").val();
    if($email == ""){
        AE.pubsub.trigger('ae:notification', {
            msg: ae_payu.empty_field,
            notice_type: 'error'
        });
        if(ae_payu_validateEmail($email)){
            AE.pubsub.trigger('ae:notification', {
            msg: ae_payu.email_error,
            notice_type: 'error'
        });
            
        }        
        jQuery("#payu_email").focus();
        return false    
    }
})
