(function($, Views) {
    $(document).ready(function() {
        // Modal stripe payment form
        Views.PPExpress = Views.Modal_Box.extend({
            el: $('li#ppdigital-payment'),
            ad: [],
            events: {
                // 'click #ppdigital-button': 'submitPayment'
            },
            initialize: function() {
                _.bindAll(this, 'setupData' );
                this.dg = new PAYPAL.apps.DGFlow({
                    trigger: 'ppdigital-button',
                    expType: 'instant'
                    //PayPal will decide the experience type for the buyer based on his/her 'Remember me on your computer' option.
                });
                AE.pubsub.on('ae:submitPost:extendGateway', this.setupData);
                this.blockUi = new Views.BlockUi();
            },
            /**
             * setup order data
             */
            setupData : function(data){
            	var view = this;
            	if (data.paymentType == 'ppdigital') {
            		this.data = data;
            		$.ajax({
	                    type: 'post',
	                    url: ae_globals.ajaxURL,
	                    data: data,
	                    beforeSend: function() {
	                        view.blockUi.block($('#ppdigital-button'));
	                    },
	                    success: function(res) {
	                        var view = this;
	                        if (res.ACK === 'Success') {
	                            //$('#ppexpress_form').append ('<input type="hidden" name="token" value="'+res.TOKEN+'" />')
	                            $('#ppexpress_form').attr('action', res.url);
	                            view.dg = new PAYPAL.apps.DGFlow({
	                                trigger: 'ppdigital-button',
	                                expType: 'instant'
	                                //PayPal will decide the experience type for the buyer based on his/her 'Remember me on your computer' option.
	                            });
	                            $('#ppexpress_form').trigger('click');
	                            $('#ppexpress_form').submit();
	                        }
                            if( res.success ) {
                                window.location.href = res.data.url;
                            }
	                    }
	                });
            	}
            }
            
        });
        new Views.PPExpress();
    });
})(jQuery, AE.Views, AE.Models, AE.Collections, window.AE);