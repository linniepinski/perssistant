(function($, Views, Models, Collections) {
    $(document).ready(function() {
        AE.Views.SingleProject = Backbone.View.extend({
            // action: 'ae-project-sync',
            el: 'body.single',
            events: {
                'click a.btn-apply-project': 'modalBidProject',
                //'click button.btn-accept-bid': 'confirmShow',                
                'click button.btn-apply-project-item': 'confirmShow',
                'click a.btn-complete-project': 'showReviewModal',
                'click .confirm .btn-agree': 'acceptBid',
                'click .confirm .btn-skip': 'skipAccept',
                // open close project modal
                'click a.btn-close-project': 'openCloseProjectModal',
                // freelancer quit project
                'click a.btn-quit-project': 'openQuitProjectModal',
                /*
                 * delete a bidding
                 */
                'click .btn-del-project-modal': 'deleteBiddingModal',
                'click .btn-del-project': 'deleteBidding',
                /*
                 *for mobile js
                 */
                'click .btn-bid-mobile': 'toggleBidForm',
                'submit form.bid-form-mobile': 'submitBidProject',
                'click .btn-complete-mobile': 'toggleReviewForm',
                'submit form.review-form-mobile': 'submitReview',
                'mouseover .single-project-wrapper .info-bidding': 'showAccept',
                'mouseout .single-project-wrapper .info-bidding': 'hideAccept',
                // 'click a.btn-refund-project' : 'refundProjectPayment',
                'click a.manual-transfer' : 'transferMoney',

                'submit form.transfer-escrow': 'executeProjectPayment',
                // user click on action button such as edit, archive, reject
                'click a.action': 'acting'
            },
            /**
             * event callback when user click on action button
             * edit
             * archive
             * reject
             * toggleFeatured
             * approve
             */
            acting: function(e) {
                // e.preventDefault();
                var target = $(e.currentTarget),
                    action = target.attr('data-action'),
                    model = this.model;
                view = this;
                // fetch model data
                switch (action) {
                    case 'edit':
                        //trigger an event will be catch by AE.App to open modal edit
                        AE.pubsub.trigger('ae:model:onEdit', model);
                        break;
                    case 'reject':
                        //trigger an event will be catch by AE.App to open modal reject
                        AE.pubsub.trigger('ae:model:onReject', model);
                        break;
                    case 'archive':
                        // archive a model
                        //model.set('do', 'archivePlace');
                        if (confirm(ae_globals.confirm_message)) {
                            model.save('post_status', 'archive', {
                                beforeSend: function() {
                                    view.blockUi.block(target);
                                },
                                success: function(result, status) {
                                    view.blockUi.unblock();
                                    if (status.success) {
                                        AE.pubsub.trigger('ae:notification', {
                                            msg: status.msg,
                                            notice_type: 'success',
                                        });
                                        window.location.reload();
                                    } else {
                                        AE.pubsub.trigger('ae:notification', {
                                            msg: status.msg,
                                            notice_type: 'error',
                                        });
                                    }
                                }
                            });
                        }
                        break;
                    case 'toggleFeature':
                        // toggle featured
                        //model.set('do', 'toggleFeature');
                        if (parseInt(model.get('et_featured')) == 1) {
                            model.set('et_featured', 0);
                        } else {
                            model.set('et_featured', 1);
                        }
                        model.save('', '', {
                            beforeSend: function() {
                                view.blockUi.block(target);
                            },
                            success: function(result, status) {
                                view.blockUi.unblock();
                                if (status.success) {
                                    AE.pubsub.trigger('ae:notification', {
                                        msg: status.msg,
                                        notice_type: 'success',
                                    });
                                    window.location.reload();
                                } else {
                                    AE.pubsub.trigger('ae:notification', {
                                        msg: status.msg,
                                        notice_type: 'error',
                                    });
                                }
                            }
                        });
                        break;
                    case 'approve':
                        // publish a model
                        model.save('publish', '1', {
                            beforeSend: function() {
                                view.blockUi.block(target);
                            },
                            success: function(result, status) {
                                view.blockUi.unblock();
                                if (status.success) {
                                    window.location.href = model.get('permalink');
                                }
                            }
                        });
                        break;
                    case 'resolve-dispute' :
                        // publish a model
                        model.save('disputed', '1', {
                            beforeSend: function() {
                                view.blockUi.block(target);
                            },
                            success: function(result, status) {
                                view.blockUi.unblock();
                                if (status.success) {
                                    window.location.href = model.get('permalink');
                                }
                            }
                        });
                        break;
                    default:
                        break;
                }
            },
            /** 
             * init view single project
             */
            initialize: function() {
                _.bindAll(this, 'modalBidProject');
                var view = this;
                if($('body').find('.biddata').length > 0 ) {
                    // parset biddata to create collection
                    var biddata = JSON.parse($('body').find('.biddata').html());
                    // create a bid collections
                    this.collection_bids = new Collections.Bids(biddata);
                }else{
                    this.collection_bids = new Collections.Bids();
                }
                    
                // get project id    
                view.project_id = this.$el.find('input#project_id').val();
                this.model = new AE.Models.Project(JSON.parse($('body').find('#project_data').html()));
                
                // init modal bid for freelancer can user to submit a bid
                view.modal_bid = new AE.Views.Modal_Bid();

                // init modal bid for freelancer can user to update a bid
                view.modal_bid_update = new AE.Views.Modal_Bid_Update();

                // init block ui
                view.blockUi = new Views.BlockUi();
                new SingleListBids({
                    //itemView: BidItem,
                    collection: this.collection_bids,
                    el: $('.list-bidding')
                });
                if (typeof Views.BlockControl !== "undefined") {
                    //list user bid control
                    new Views.BlockControl({
                        collection: this.collection_bids,
                        el: this.$(".info-bidding-wrapper"),
                        query: {
                            paginate: 'page'
                        },
                    });
                }
                $(".btn-login-trigger").click(function() {
                    //$("a.login-btn").trigger('click');	
				
$("a.popup-login").trigger('click');
                });
                $('.rating-it').raty({
                    half: true,
                    hints: raty.hint
                });
            },
            // show modal bid project
            modalBidProject: function() {
                var view = this;
                view.modal_bid.openModal();
            },
            // open modal review project
            showReviewModal: function(event) {
                var view = this;
                if (typeof view.modal_review == 'undefined') {
                    view.modal_review = new AE.Views.Modal_Review();
                }
                view.modal_review.setProject(view.project_id);
                view.modal_review.openModal();
            },
            /**
             *Emplooyer accept this bid
             */
            confirmShow: function(event) {
                event.preventDefault();
                var $target = $(event.currentTarget),
                    view = this;
                view.bid_id = $target.attr('id');
                if (typeof Views.Modal_AcceptBid === 'undefined') {
                    $row = $target.closest(".col-md-3");
                    $target.hide();
                    $('.info-bidding').removeClass('hide-accept');
                    $target.parents('.info-bidding').addClass('hide-accept');
//                    $(".col-md-3").find(".confirm").html('');
                    $.ajax({
                        url: ae_globals.ajaxURL,
                        type: 'post',
                        data: {
                            bid_id: view.bid_id,
                            action: 'ae-accept-bid',
                        },
                        beforeSend: function() {
                            view.blockUi.block($target);
                        },
                        success: function(res) {
                            view.blockUi.unblock();
                            if (res.success) {
                                $("a.btn-project-status").html(single_text.working);
                                $target.find("button.btn-bid-status").after('<span class="ribbon"><i class="fa fa-trophy"></i></span>');
//                                $target.find('.confirm').remove();
//                                $target.parents('.info-bidding').find('.btn-invate-on-bid').removeClass('hidden');

//                                $target.parents('.info-bidding').find('.btn-skip').addClass('hidden');

                                $(".info-bidding").find("button.btn-bid-status").remove();
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'success'
                                });
                            } else {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'error'
                                });
                            }
                        }
                    });
                    setTimeout(function() {
//                        <button class="btn btn-agree"> ' + single_text.agree + ' </button>
//                        $row.find('span.confirm').html('<button class="btn btn-skip"> ' + single_text.skip + ' </button>');
//                        $("span.confirm").fadeIn(500);
                    }, 300);

                    return false;
                } else {
                    console.log('modal accept bid');
                    if (typeof view.acceptbid_modal == 'undefined') {
                        view.acceptbid_modal = new Views.Modal_AcceptBid();
                    }
                    view.acceptbid_modal.setBidId(view.bid_id);
                    view.acceptbid_modal.openModal();
                }
            },
            // hide confirm if user cancel an accept
            skipAccept: function(event) {
                var $target = $(event.currentTarget);
                view = this;
                //console.log(this);
                console.log($target.attr('data-id'));
//                console.log($target.find('data-id'));

//                $target.parents('.info-bidding').removeClass('hide-accept');
//                $target.closest(".confirm").toggle();
//                $target.parents('.info-bidding').find('button.btn-apply-project-item').fadeIn(500);
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: {
                        bid_id: $target.attr('data-id'),
                        action: 'ae-skip-bid',
                    },
                    beforeSend: function() {
                        view.blockUi.block($target);
//                        console.log($target.parents().find('.bid-item').remove())
//                        return false;
                    },
                    success: function(res) {
                        view.blockUi.unblock();
                        console.log(res);
                        if (res.success) {
                            $target.parents().find('.bid-item').remove();
//                            $("a.btn-project-status").html(single_text.working);
//                            $target.find("button.btn-bid-status").after('<span class="ribbon"><i class="fa fa-trophy"></i></span>');
//                            $target.find('.confirm').remove();
//                            $target.parents('.info-bidding').find('.btn-invate-on-bid').removeClass('hidden');

//                            $(".info-bidding").find("button.btn-bid-status").remove();
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'success'
                            });
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
               // $target.parents('.number-price-project').find("button.btn-apply-project-item").fadeIn(500);
            },
            /**
             * accept a bid, sync to server
             */
            acceptBid: function(event) {
                var view = this,
                    $target = $(event.currentTarget).closest(".info-bidding");
                $target.removeClass('hide-accept');
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: {
                        bid_id: view.bid_id,
                        action: 'ae-accept-bid',
                    },
                    beforeSend: function() {
                        view.blockUi.block($target);
                    },
                    success: function(res) {
                        view.blockUi.unblock();
                        if (res.success) {
                            $("a.btn-project-status").html(single_text.working);
                            $target.find("button.btn-bid-status").after('<span class="ribbon"><i class="fa fa-trophy"></i></span>');
                            $target.find('.confirm').remove();
                            $target.find('.btn-invate-on-bid').removeClass('hidden')
                            $(".info-bidding").find("button.btn-bid-status").remove();
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'success'
                            });
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            },
            // open modal close project
            openCloseProjectModal: function(event) {
                var view = this;
                if (typeof view.modal_close === 'undefined') {
                    view.modal_close = new AE.Views.Modal_Close();
                }
                view.modal_close.setProject(view.project_id);
                view.modal_close.openModal();
            },
            // open modal quit project
            openQuitProjectModal: function(event) {
                var view = this;
                if (typeof view.modal_quit === 'undefined') {
                    view.modal_quit = new AE.Views.Modal_Quit();
                }
                view.modal_quit.setProject(view.project_id);
                view.modal_quit.openModal();
            },
            /*
             * For freelancer delete a bidding.
             */
            deleteBiddingModal: function(event) {
                jQuery('#modal-deleting-bid').modal('show');
            },

            deleteBidding: function(event) {
                event.preventDefault();
                var view = this,
                    $target = $(event.currentTarget),
                    bid_id = $target.attr('ID');
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: {
                        ID: bid_id,
                        action: 'ae-sync-bid',
                        method: 'remove'
                    },
                    beforeSend: function(event) {
                        jQuery('#modal-deleting-bid').modal('hide');

                    },
                    success: function(res) {

                        if (res.success) {
                            $target.closest('.info-bidding').remove();
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'success'
                            });
                            location.reload();
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            },
            /*
             * for mobile version. toggle bid form
             */
            toggleBidForm: function(event) {
                event.preventDefault();
                var display = $('#bid_form').css('display');
                if (display == 'block') $('#bid_form').slideUp();
                else $('#bid_form').slideDown();
                return false;
            },
            /* 
             *submid bid on mobile version
             */
            submitBidProject: function(event) {
                event.preventDefault();
                var view = this,
                    $target = $(event.currentTarget),
                    button = $target.find('button.btn-submit');
                data = $target.serializeObject() || [];
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: data,
                    beforeSend: function() {
                        view.blockUi.block(button);
                    },
                    success: function(res) {
                        view.blockUi.unblock();
                        AE.pubsub.trigger('ae:notification', {
                            msg: res.msg,
                            notice_type: res.success
                        });
                        console.log(res);
                        if (res.success) {
                            location.reload();
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
                return false;
            },
            /*
             * toggle review form on mobile version.
             */
            toggleReviewForm: function(event) {
                event.preventDefault();
                var display = $('#review_form').css('display');
                if (display == 'block') $('#review_form').slideUp();
                else $('#review_form').slideDown();
                return false;
            },
            /*
             * review on mobile version
             */
            submitReview: function(event) {
                event.preventDefault();
                var view = this,
                    $target = $(event.currentTarget),
                    button = $target.find('button.btn-submit');
                data = $target.serializeObject() || [];
                view.blockUi = new Views.BlockUi();
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: data,
                    beforeSend: function() {
                        view.blockUi.block(button);
                    },
                    success: function(res) {
                        view.blockUi.unblock();
                        if (res.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'success'
                            });
                            $(".btn-project-status").removeClass('btn-complete-project');
                            $(".btn-project-status").html(single_text.completed);
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
                return false;
            },
            // show confirm accept bid
            showAccept: function(event) {
                //btn-accept-bid
                var $target = $(event.currentTarget);
                $('.info-bidding').find('.btn-accept-bid').hide();
                if (!$target.hasClass('hide-accept')) {
                    $target.find('.btn-accept-bid').show();
                }
            },
            // hid confirm accept bid
            hideAccept: function(event) {
                //btn-accept-bid
                var $target = $(event.currentTarget);
                $target.find('.btn-accept-bid').hide();
            },
            /**
             * refund payment to employer
             */
            // refundProjectPayment : function(event){
            //     event.preventDefault();
            //     var view = this, 
            //         $target = $(event.currentTarget);
            //     if(confirm('You are going to send money back to employer.')) {
            //         $.ajax({
            //             url: ae_globals.ajaxURL,
            //             type: 'post',
            //             data : {project_id : view.project_id, action:'refund_payment'},
            //             beforeSend: function(){
            //                 view.blockUi.block($target);
            //             },
            //             success:function(res){
            //                 view.blockUi.unblock();
            //                 if (res.success) {
            //                     AE.pubsub.trigger('ae:notification', {
            //                         msg: res.msg,
            //                         notice_type: 'success'
            //                     });
            //                 } else {
            //                     AE.pubsub.trigger('ae:notification', {
            //                         msg: res.msg,
            //                         notice_type: 'error'
            //                     });
            //                 }
            //             }
            //         });
            //     }
            // }, 
            transferMoney : function(event){
                event.preventDefault();
                var view = this,
                    $target = $(event.currentTarget);
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: {
                        project_id: view.project_id,
                        action: 'transfer_money'
                    },
                    beforeSend: function() {
                        view.blockUi.block($target);
                    },
                    success: function(res) {
                        view.blockUi.unblock();
                        if (res.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'success'
                            });
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            },
            // send payment to freelancer
            executeProjectPayment: function(event) {
                event.preventDefault();
                var view = this,
                    $target = $(event.currentTarget);
                if ($target.find('.transfer-select').val() == 'freelancer') {
                    var text = 'You are going to finish disputing and send money to freelancer.',
                        action = 'execute_payment';
                } else {
                    var text = 'You are going to send money back to employer.',
                        action = 'refund_payment';
                }
                if (confirm(text)) {
                    $.ajax({
                        url: ae_globals.ajaxURL,
                        type: 'post',
                        data: {
                            project_id: view.project_id,
                            action: action
                        },
                        beforeSend: function() {
                            view.blockUi.block($target);
                        },
                        success: function(res) {
                            view.blockUi.unblock();
                            if (res.success) {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'success'
                                });
                            } else {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'error'
                                });
                            }
                        }
                    });
                }
            }
        });
        AE.Views.Modal_Bid = AE.Views.Modal_Box.extend({
            el: '#modal_bid',
            events: {
                'submit form.bid-form': 'submitBidProject',
            },
            initialize: function() {
                AE.Views.Modal_Box.prototype.initialize.apply(this, arguments);
                this.LoadingButtonNew = new Views.LoadingButtonNew();
                $("form#bid_form").validate({
                    ignore: "",
                    rules: {
                        bid_content: "required",
                    }
                    // errorPlacement: function(label, element) {
                    //     // position error label after generated textarea
                    //     if (element.is("textarea")) {
                    //         label.insertAfter(element.next());
                    //     } else {
                    //         $(element).closest('div').append(label);
                    //     }
                    // }
                });
            },
            submitBidProject: function(event) {
                event.preventDefault();
                var view = this,
                    $target = $(event.currentTarget),
                    button = $target.find('button.btn-submit');
                data = $target.serializeObject() || [];
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: data,
                    beforeSend: function() {
                        view.LoadingButtonNew.loading(button);
//return false;
                    },
                    success: function(res) {
                        view.LoadingButtonNew.finish(button);
                        AE.pubsub.trigger('ae:notification', {
                            msg: res.msg,
                            notice_type: res.success
                        });
                        if (res.success) {
                            location.reload();
                            view.closeModal();
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
                return false;
            },
        });

        //Modal for Update Bid
        AE.Views.Modal_Bid_Update = AE.Views.Modal_Box.extend({
            el: '#modal_bid_update',
            events: {
                'submit form.bid-form-update': 'submitBidProject_Update',
            },
            initialize: function() {
                AE.Views.Modal_Box.prototype.initialize.apply(this, arguments);
                this.blockUi = new Views.BlockUi();
                $("form#bid_form_update").validate({
                    ignore: "",
                    rules: {
                        post_content: "required",
                    }
                    // errorPlacement: function(label, element) {
                    //     // position error label after generated textarea
                    //     if (element.is("textarea")) {
                    //         label.insertAfter(element.next());
                    //     } else {
                    //         $(element).closest('div').append(label);
                    //     }
                    // }
                });
            },
            submitBidProject_Update: function(event) {
                event.preventDefault();
                var view = this,
                    $target = $(event.currentTarget),
                    button = $target.find('button.btn-submit-update');
                data = $target.serializeObject() || [];
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: data,
                    beforeSend: function() {
                        view.blockUi.block(button);
                    },
                    success: function(res) {
                        view.blockUi.unblock();
                        AE.pubsub.trigger('ae:notification', {
                            msg: res.msg,
                            notice_type: res.success
                        });
                        if (res.success) {
                            location.reload();
                            view.closeModal();
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
                return false;
            },
        });
        //END - Modal for Update Bid


        AE.Views.Modal_Review = AE.Views.Modal_Box.extend({
            el: '#modal_review',
            events: {
                'submit form.review-form': 'submitReview',
            },
            initialize: function() {
                AE.Views.Modal_Box.prototype.initialize.apply(this, arguments);
                this.blockUi = new Views.BlockUi();
                $("form.review-form").validate({
                    ignore: "",
                    rules: {
                        comment_content: "required",
                    }
                });
            },
            setProject: function(project_id) {
                this.project_id = project_id;
            },
            submitReview: function(event) {
                event.preventDefault();
                var view = this,
                    $target = $(event.currentTarget),
                    // button = $target.find('button.btn-submit');
                    data = $target.serializeObject() || [];
                data.project_id = view.project_id;
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: data,
                    beforeSend: function() {
                        view.blockUi.block($target);
                    },
                    success: function(res) {
                        view.blockUi.unblock();
                        if (res.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'success'
                            });
                            // view.closeModal();
                            // $(".btn-project-status").removeClass('btn-complete-project');
                            // $(".btn-project-status").html(single_text.completed);
                            window.location.reload();
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
                return false;
            },
        });
        AE.Views.Modal_Finish = AE.Views.Modal_Box.extend({
            el: '#finish_project',
            events: {},
            initialize: function(options) {
                AE.Views.Modal_Box.prototype.initialize.apply(this, arguments);
                this.blockUi = new Views.BlockUi();
                // $("form.review-form").validate({
                //     ignore: "",
                //     rules: {
                //         post_content: "required",
                //     }
                // });
            },
            submitFinish: function() {}
        });
        AE.Views.Modal_Close = AE.Views.Modal_Box.extend({
            el: '#quit_project',
            events: {
                'submit form.quit_project_form': 'submitClose'
            },
            initialize: function(options) {
                AE.Views.Modal_Box.prototype.initialize.apply(this, arguments);
                this.blockUi = new Views.BlockUi();
                this.$("form.quit_project_form").validate({
                    ignore: "",
                    rules: {
                        comment_content: "required",
                    }
                });
            },
            setProject: function(project_id) {
                this.project_id = project_id;
            },
            submitClose: function(event) {
                event.preventDefault();
                event.preventDefault();
                var view = this,
                    $target = $(event.currentTarget),
                    // button = $target.find('button.btn-submit');
                    data = $target.serializeObject() || [];
                data.comment_post_ID = view.project_id;
                data.action = 'fre-close-project';
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: data,
                    beforeSend: function() {
                        view.blockUi.block($target);
                    },
                    success: function(res) {
                        view.blockUi.unblock();
                        if (res.success) {
                            window.location.reload();
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            }
        });
        AE.Views.Modal_Quit = AE.Views.Modal_Box.extend({
            el: '#quit_project',
            events: {
                'submit form.quit_project_form': 'submitQuit'
            },
            initialize: function(options) {
                AE.Views.Modal_Box.prototype.initialize.apply(this, arguments);
                this.blockUi = new Views.BlockUi();
                this.$("form.quit_project_form").validate({
                    ignore: "",
                    rules: {
                        comment_content: "required",
                    }
                });
            },
            setProject: function(project_id) {
                this.project_id = project_id;
            },
            submitQuit: function(event) {
                event.preventDefault();
                event.preventDefault();
                var view = this,
                    $target = $(event.currentTarget),
                    // button = $target.find('button.btn-submit');
                    data = $target.serializeObject() || [];
                data.comment_post_ID = view.project_id;
                data.action = 'fre-quit-project';
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: data,
                    beforeSend: function() {
                        view.blockUi.block($target);
                    },
                    success: function(res) {
                        view.blockUi.unblock();
                        if (res.success) {
                            window.location.reload();
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            }
        });
        new AE.Views.SingleProject();
    });
})(jQuery, AE.Views, AE.Models, AE.Collections);