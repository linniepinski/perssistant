(function($, Views, Models, Collections) {
    $(document).ready(function() {
        Views.Modal_AcceptBid = Views.Modal_Box.extend({
            el: '#acceptance_project',
            template: _.template($('#bid-info-template').html()),
            events: {
                // user register
                'submit form#escrow_bid': 'submit'
            },
            /**
             * init view setup Block Ui and Model User
             */
            initialize: function() {
                // init block ui
                this.blockUi = new Views.BlockUi();
            },
            // setup a bid id to modal accept bid
            setBidId: function(id) {
                this.bid_id = id;
                this.getPaymentInfo();
            },
            // load payment info and display
            getPaymentInfo: function() {
                var view = this;
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'get',
                    data: {
                        bid_id: view.bid_id,
                        action: 'ae-accept-bid-info',
                    },
                    beforeSend: function() {
                        view.blockUi.block(view.$el);
                    },
                    success: function(res) {
                        view.blockUi.unblock();
                        if (res.success) {
                            view.$el.find('.escrow-info').html(view.template(res.data));
                        }else{
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            },
            // submit accept bid an pay
            submit: function(event) {
                event.preventDefault();
                var $target = $(event.currentTarget),
                    view = this;
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: {
                        bid_id: view.bid_id,
                        action: 'ae-escrow-bid',
                    },
                    beforeSend: function() {
                        view.blockUi.block($target);
                    },
                    success: function(res) {
                        view.blockUi.unblock();
                        if (res.redirect_url) {
                            window.location.href = res.redirect_url;
                        }else{
                            AE.pubsub.trigger('ae:notification', {
                                msg : res.msg,
                                type : 'error'
                            })
                        }
                    }
                });
            }
        });
    });
})(jQuery, AE.Views, AE.Models, AE.Collections);