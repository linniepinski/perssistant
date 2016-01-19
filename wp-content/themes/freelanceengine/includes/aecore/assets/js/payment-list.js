/**
 * backend order, control user list in user manage list
 * search order by post name
 * filter order by gateway
 * use Views.BlockUi add view block loading
 */
(function(Models, Views, Collections, $, Backbone) {
    Views.OrderList = Backbone.View.extend({
        events: {
            'click .load-more': 'loadMore',
            'change input.order-search': 'search',
            'change select.et-input': 'search',
            'submit .search-box form': 'search', 
            'click a.publish' : 'publishOrder',
            'click a.decline' : 'declineOrder'
        },

        initialize: function(options) {

            this.paged = 1;
            this.pages = options.pages;
            this.blockUi = new Views.BlockUi();

        },
        /**
         * build ajax params for ajax
         */
        buildParams: function(reset) {

            var view = this,
                keywork = this.$('input.order-search').val(),
                loadmore = view.$('.load-more'),
                gateway = this.$('select.et-input').val(),
                // get ajax params from AE globals
                ajaxParams = AE.ajaxParams;

            if (!reset) {
                $target = this.$('.load-more');
            } else {
                $target = this.$('ul');
                view.paged = 1;
            }

            ajaxParams.success = function(result) {
                var data = result.data;
                view.blockUi.unblock();
                if (result.pages < result.page) {
                    loadmore.hide();
                } else {
                    loadmore.show();
                }
                view.paged = result.page;
                if (reset) view.$('ul').html('');

                view.$('ul').append(data);
                if (data == '') {
                    view.$('ul').append('<li class="user-not-found">' + result.msg + '</li>');
                    loadmore.hide();
                }

            }

            ajaxParams.beforeSend = function() {
                view.blockUi.block($target);
            }
            /**
             * filter param
             */
            ajaxParams.data = {
                search: keywork,
                paged: view.paged
            };
            if (gateway != '') ajaxParams.data.payment = gateway;

            ajaxParams.data.action = 'ae-fetch-orders';

            return ajaxParams
        },

        /**
         * load more user event
         */
        loadMore: function(event) {
            var view = this,
                $target = $(event.currentTarget);

            var ajaxParams = this.buildParams(false)

            $.ajax(ajaxParams);

        },
        /**
         * search user
         */
        search: function(e) {
            e.preventDefault();
            this.paged = 0;
            var ajaxParams = this.buildParams(true);
            $.ajax(ajaxParams);
        }, 
        /**
         * admin publish and order
         */
        publishOrder : function(event){
            event.preventDefault();
            var $target =  $(event.currentTarget),
                data = {
                    'ID' : $target.attr('data-id'),
                    'status' : 'publish', 
                    'action' : 'ae-sync-order'
                };
            this.syncOrder(data, $target);
        }, 
        /**
         * admin decline an order
         */
        declineOrder : function(event){
            event.preventDefault();
            var $target =  $(event.currentTarget),
                data = {
                    'ID' : $target.attr('data-id'),
                    'status' : 'draft', 
                    'action' : 'ae-sync-order'
                };
            this.syncOrder(data, $target);
        },
        /**
         * js sync order
         */
        syncOrder : function(data, $target){
            var ajaxParams = AE.ajaxParams, 
                view = this;

            ajaxParams.data = data;
            ajaxParams.success = function(response) {
                view.blockUi.unblock();
                if(response.success){
                    
                    if(data.status == 'publish') {
                        $target.parents('li').find('.content span.icon').attr('data-icon', '2').end()
                        .find('a.error').removeClass('error color-red').addClass('color-green');
                    }else{
                        $target.parents('li').find('.content span.icon').attr('data-icon', '*').end()
                            .find('a.error').removeClass('error color-red').addClass('color-grey');;    
                    }
                    $target.parent().find('.action').remove()/*.end()
                        .prepend('success')*/;
                }
            }
            ajaxParams.beforeSend = function() {
                view.blockUi.block($target);
            }
            
            $.ajax(ajaxParams);
        }

    });

})(window.AE.Models, window.AE.Views, window.AE.Collections, jQuery, Backbone);