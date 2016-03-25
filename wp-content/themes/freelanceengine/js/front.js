(function($, Models, Collections, Views) {

    $(document).ready(function() {

        /**

         * define blog item view

         */

        BlogItem = Views.PostItem.extend({

            tagName: 'div',

            className: 'blog-wrapper post-item',

            template: _.template($('#ae-post-loop').html()),

            onItemBeforeRender: function() {

                // before render view

            },

            onItemRendered: function() {

                // after render view

            }

        });

        /**

         * list view control blog list

         */

        ListBlogs = Views.ListPost.extend({

            tagName: 'div',

            itemView: BlogItem,

            itemClass: 'post-item'

        });



        /**

         * model Notify

         */

        Models.Notify = Backbone.Model.extend({

            action: 'ae-notify-sync',

            initialize: function() {}

        });

        /**

         * Notify collections

         */

        Collections.Notify = Backbone.Collection.extend({

            model: Models.Notify,

            action: 'ae-fetch-notify',

            initialize: function() {

                this.paged = 1;

            }

        });

        /**

         * define notify item view

         * @since 1.2

         * @author Dakachi

         */

        NotifyItem = Views.PostItem.extend({

            tagName: 'li',

            className: 'notify-item',

            template: _.template($('#ae-notify-loop').html()),

            onItemBeforeRender: function() {

                // before render view

                // console.log('render');

            },

            onItemRendered: function() {

                // after render view

            }

        });

        /**

         * list view control notification list

         * @since 1.2

         * @author Dakachi

         */

        ListNotify = Views.ListPost.extend({

            tagName: 'li',

            itemView: NotifyItem,

            itemClass: 'notify-item'

        });



        // notification list control

        if ($('#notification_container').length > 0) {



            if ($('#notification_container').find('.postdata').length > 0) {

                var postsdata = JSON.parse($('#notification_container').find('.postdata').html()),

                    posts = new Collections.Notify(postsdata);

            } else {

                var posts = new Collections.Notify();

            }

            /**

             * init list blog view

             */

            new ListNotify({

                itemView: NotifyItem,

                collection: posts,

                el: $('#notification_container').find('.notification-list')

            });

            /**

             * init block control list blog

             */

            new Views.BlockControl({

                collection: posts,

                el: $('#notification_container')

            });

        }



        /**

         * model project

         */

        Models.Project = Backbone.Model.extend({

            action: 'ae-project-sync',

            initialize: function() {}

        });

        /**

         * project collections

         */

        Collections.Projects = Backbone.Collection.extend({

            model: Models.Project,

            action: 'ae-fetch-projects',

            initialize: function() {

                this.paged = 1;

            }

        });

        /**

         * define project item view

         */

        ProjectItem = Views.PostItem.extend({

            tagName: 'li',

            className: 'project-item',

            template: _.template($('#ae-project-loop').html()),

            onItemBeforeRender: function() {

                // before render view

            },

            onItemRendered: function() {

                // after render view

            }

        });



        User_BidItem = Views.PostItem.extend({

            tagName: 'li',

            className: 'user-bid-item',

            template: _.template($('#ae-user-bid-loop').html()),

            onItemBeforeRender: function() {

                // before render view

            },

            onItemRendered: function() {

                // after render view

            }

        });

        /**

         * list view control project list

         */

        ListProjects = Views.ListPost.extend({

            tagName: 'ul',

            itemView: ProjectItem,

            itemClass: 'project-item'

        });



        User_ListBids = Views.ListPost.extend({

            tagName: 'ul',

            itemView: User_BidItem,

            itemClass: 'user-bid-item'

        });

        /**

         * Model profile

         */

        Models.Profile = Backbone.Model.extend({

            action: 'ae-profile-sync',

            initialize: function() {}

        });

        /**

         * Profile collection

         */

        Collections.Profiles = Backbone.Collection.extend({

            model: Models.Profile,

            action: 'ae-fetch-profiles',

            initialize: function() {

                this.paged = 1;

            }

        });

        /**

         * Define profile item view

         */

        ProfileItem = Views.PostItem.extend({

            tagName: 'div',

            className: 'col-md-6 col-sm-12 col-xs-12 profile-item',

            template: _.template($('#ae-profile-loop').html()),

            onItemBeforeRender: function() {

                //before render item

            },

            onItemRendered: function() {

                //after render view

                var view = this;

                view.$('.rate-it').raty({

                    readOnly: true,

                    half: true,

                    score: function() {

                        return view.model.get('rating_score');

                    },

                    hints: raty.hint

                });

            }

        });

        /**

         * List view control profiles list

         */

        ListProfiles = Views.ListPost.extend({

            tagName: 'div',

            itemView: ProfileItem,

            itemClass: 'profile-item'

        });

        /**

         * Model portfolio

         */

        Models.Portfolio = Backbone.Model.extend({

            action: 'ae-portfolio-sync',

            initialize: function() {}

        });

        /**

         * Portfolio collection

         */

        Collections.Portfolios = Backbone.Collection.extend({

            model: Models.Portfolio,

            action: 'ae-fetch-portfolios',

            initialize: function() {

                this.paged = 1;

            }

        });

        /**

         * Define portfolio item view

         */

        // var clicked_portfolio_model;
        PortfolioItem = Views.PostItem.extend({

            events: {

                'click a.delete': 'removePortfolio',
                'click a.edit': 'editPortfolio'

            },

            initialize: function() {

                this.blockUi = new Views.BlockUi();

            },

            tagName: 'li',

            className: 'portfolio-item col-md-4',

            template: _.template($('#ae-portfolio-loop').html()),

            removePortfolio: function(event) {

                event.preventDefault();

                var view = this;

                this.model.destroy({

                    beforeSend: function() {

                        view.blockUi.block(view.$el);

                    },

                    success: function(res) {

                        view.blockUi.unblock();

                    }

                });

            },
            editPortfolio: function(event) {
                event.preventDefault();
                var portfolio = new Models.Portfolio();
                if (typeof this.modalPortfolio === 'undefined') {
                    this.modalPortfolio = new Views.Modal_Edit_Portfolio({
                        el: '#modal_edit_portfolio',
                        collection: this
                            //collection: this.portfolio_colection
                            // model: portfolio
                    });
                }
                this.modalPortfolio.setModel(portfolio);
                this.modalPortfolio.openModal();

            },

            onItemBeforeRender: function() {

            },

            onItemRendered: function() {

                this.$el.find('.image-gallery').magnificPopup({
                    type: 'image',
                    image: {
                        headerFit: true,
                        captionFit: true,
                        preserveHeaderAndCaptionWidth: false,
                        markup: '<div class="mfp-figure">' +
                            '<figure>' +
                            '<header class="mfp-header">' +
                            '<div class="mfp-top-bar">' +
                            '<div class="mfp-title"></div>' +
                            '<div class="mfp-close"></div>' +
                            '</div>' +
                            '</header>' +
                            '<section class="mfp-content-container">' +
                            '<div class="mfp-img"></div>' +
                            '</section>' +
                            '<footer class="mfp-footer">' +
                            '<figcaption class="mfp-figcaption">' +
                            '<div class="mfp-bottom-bar-container">' +
                            '<div class="mfp-bottom-bar">' +
                            '<div class="mfp-counter"></div>' +
                            '<div class="mfp-description"></div>' +
                            '</div>' +
                            '</div>' +
                            '</figcaption>' +
                            '</footer>' +
                            '</figure>' +
                            '</div>',
                        titleSrc: function(item) {
                            return item.el.attr('title');
                        },
                        description: function(item) {
                            console.log(item.el.data('description'));
                            return item.el.data('description');
                        }

                    },
                    callbacks: {
                        //                elementParse: function(item) {
                        //                    // Function will fire for each target element
                        //                    // "item.el" is a target DOM element (if present)
                        //                    // "item.src" is a source that you may modify
                        //                   // item.find('.mfp-description').html(item.el.data('description'));
                        //                    //console.log(jQuery('.mfp-description')); // Do whatever you want with "item" object
                        //                },
                        markupParse: function(template, values, item) {
                            values.description = item.el.data('description'); // or item.img.data('description');
                            console.log(values);
                        }
                    }
                });
            }

        });




        /**

         * List view control Portfolios list

         */

        ListPortfolios = Views.ListPost.extend({

            tagName: 'li',

            itemView: PortfolioItem,

            itemClass: 'portfolio-item'

        });



        /**

         *  MODEL WORK HISTORY

         */

        Models.Bid = Backbone.Model.extend({

            action: 'ae-bid-sync',

            initialize: function() {}

        });

        /**

         * Bid collection

         */

        Collections.Bids = Backbone.Collection.extend({

            model: Models.Bid,

            action: 'ae-fetch-bid',

            initialize: function() {

                this.paged = 1;

            }

        });

        /**

         * Define profile item view

         */

        BidItem = Views.PostItem.extend({

            tagName: 'li',

            className: 'bid-item',

            template: _.template($('#ae-bid-history-loop').html()),

            onItemBeforeRender: function() {

                //before render item

            },

            onItemRendered: function() {

                //after render view

                var view = this;

                view.$('.rate-it').raty({

                    readOnly: true,

                    half: true,

                    score: function() {

                        return view.model.get('rating_score');

                    },

                    hints: raty.hint

                });

            }

        });



        WorkHistoryItem = Views.PostItem.extend({

            tagName: 'li',

            className: 'bid-item',

            template: _.template($('#ae-work-history-loop').html()),

            onItemBeforeRender: function() {

                //before render item

            },

            onItemRendered: function() {

                //after render view

                var view = this;

                view.$('.rate-it').raty({

                    readOnly: true,

                    half: true,

                    score: function() {

                        return view.model.get('rating_score');

                    },

                    hints: raty.hint

                });

            }

        });

        /**

         * List view control bid list

         */

        ListBids = Views.ListPost.extend({

            tagName: 'li',

            itemView: BidItem,

            itemClass: 'bid-item'

        });



        /**

         * List view control bid list

         */

        ListWorkHistory = Views.ListPost.extend({

            tagName: 'li',

            itemView: WorkHistoryItem,

            itemClass: 'bid-item'

        });



        if ($('#ae-bid-loop').length > 0) {

            /* bid item in single project*/

            SingleBidItem = Views.PostItem.extend({

                tagName: 'div',

                className: 'info-bidding',

                template: _.template($('#ae-bid-loop').html()),

                onItemBeforeRender: function() {

                    //before render item

                },

                onItemRendered: function() {

                    //after render view

                    var view = this;

                    view.$('.rate-it').raty({

                        readOnly: true,

                        half: true,

                        score: function() {

                            return view.model.get('rating_score');

                        },

                        hints: raty.hint

                    });

                }

            });

            /* bid list in single project*/

            SingleListBids = Views.ListPost.extend({

                tagName: 'div',

                itemView: SingleBidItem,

                itemClass: 'info-bidding',

                initialize: function() {

                    this.tagName = 'ul';

                },



            });

        }



        /*

        *

        * F R O N T  V I E W S

        *

        */

        Views.Front = Backbone.View.extend({

            el: 'body',

            model: [],

            events: {

                'click a.popup-login': 'openModalLogin',

                //'click a.register-btn' : 'openModalRegister',				

                'click .trigger-notification': 'updateNotify',

                'click .trigger-notification-2': 'updateNotify'

            },

            initialize: function(options) {

                _.bindAll(this, 'editPost', 'updateAuthButtons', 'rejectPost');

                if ($('body').find('.all_skills').length > 0)

                    this.all_skills = JSON.parse($('body').find('.all_skills').html());



                // if ($('#user_id').length > 0) {

                // 	this.user = new Models.User(JSON.parse($('#user_id').html()));

                // 	//$('#user_id').remove();

                // } else {

                // 	this.user = new Models.User();

                // }

                this.user = this.model;

                /**

	             * unhighlight chosen

	            */

                $('select.chosen, select.chosen-single').on('change', function(event, params) {

                    if (typeof params.selected !== 'undefined') {

                        var $container = $(this).closest('div');

                        if ($container.hasClass('error')) {

                            $container.removeClass('error');

                        }

                        $container.find('div.message').remove().end();

                    }

                });

                // this.$('.multi-tax-item').chosen({

                //     width: '95%',

                //     max_selected_options: parseInt(ae_globals.max_cat),

                //     inherit_select_classes: true

                // });

                if (typeof $.validator !== 'undefined') {

                    $.validator.setDefaults({

                        // prevent the form to submit automatically by this plugin

                        // so we need to apply handler manually

                        onsubmit: true,

                        onfocusout: function(element, event) {

                            if (!this.checkable(element) && element.tagName.toLowerCase() === 'textarea') {

                                this.element(element);

                            } else if (!this.checkable(element) && (element.name in this.submitted || !this.optional(element))) {

                                this.element(element);

                            }

                        },

                        validClass: "valid", // the classname for a valid element container

                        errorClass: "message", // the classname for the error message for any invalid element

                        errorElement: 'div', // the tagname for the error message append to an invalid element container

                        // append the error message to the element container

                        errorPlacement: function(error, element) {

                            $(element).closest('div').append(error);

                        },

                        // error is detected, addClass 'error' to the container, remove validClass, add custom icon to the element

                        highlight: function(element, errorClass, validClass) {

                            var $container = $(element).closest('div');

                            if (!$container.hasClass('error')) {

                                $container.addClass('error').removeClass(validClass);

                            }


                            if (this.mark != 1) {
                                if (jQuery(element).hasClass('chosen')) {
                                    jQuery(element).next(".chosen-container").find(".default").focus();
                                } else if (jQuery(element).hasClass('wp-editor-area')) {
                                    window.scrollTo(0, jQuery(element).parent().offset().top - 100);
                                } else {
                                    jQuery(element).focus();
                                }
                                this.mark = 1;
                            }
                        },

                        // remove error when the element is valid, remove class error & add validClass to the container

                        // remove the error message & the custom error icon in the element

                        unhighlight: function(element, errorClass, validClass) {

                            var $container = $(element).closest('div');

                            if ($container.hasClass('error')) {

                                $container.removeClass('error').addClass(validClass);

                            }

                            $container.find('div.message').remove().end();

                            this.mark = 0;
                        }

                    });

                }



                this.noti_templates = new _.template('<div class="notification autohide {{= type }}-bg">' + '<div class="main-center">' + '{{= msg }}' + '</div>' + '</div>');

                //edit project pending

                AE.pubsub.on('ae:model:onEdit', this.editPost, this);



                //catch action reject project

                AE.pubsub.on('ae:model:onReject', this.rejectPost, this);



                // event handler for when receiving response from server after requesting login/register

                AE.pubsub.on('ae:user:auth', this.handleAuth, this);

                // event handle notification

                AE.pubsub.on('ae:notification', this.showNotice, this);

                /*

                 * check not is mobile, after user login, update authentication button

                 */

                //if(!parseInt(ae_globals.ae_is_mobile)){

                // render button in header

                this.model.on('change:ID', this.updateAuthButtons);

                //}



                $('textarea').autosize();



            },

            /**

             * callback after user ID change and update header authentication button 

             * @since 1.0

             * @author Dakachi

             */

            updateAuthButtons: function(model) {

                if ($('#header_login_template').length > 0) {

                    var header_template = _.template($('#header_login_template').html());

                    if ($('.dropdown-info-acc-wrapper').length > 0) return;

                    this.$('.non-login').remove();

                    this.$('.login-form-header-wrapper').html(header_template(model.attributes));

                    console.log('logged in');

                }

                console.log('logged in 2');

            },



            openModalLogin: function(event) {

                event.preventDefault();

                var view = this;

                this.modalLogin = new Views.Modal_Login({

                    el: "#modal_login",

                    model: view.model

                });

                this.modalLogin.openModal();

            },

            openModalRegister: function(event) {

                event.preventDefault();

                var view = this;

                this.modalRegister = new Views.Modal_Register({

                    el: "#modal_register",

                    model: view.model

                });

                this.modalRegister.openModal();

            },



            updateNotify: function(event) {

                this.user.set('read_notify', 1);

                this.user.save();

                this.$('.avatar .circle-new').remove();

                this.$('.notify-number').remove();

            },

            /*

             * Show notification

             */

            showNotice: function(params) {

                var view = this;

                // remove existing notification

                $('div.notification').remove();

                var notification = $(view.noti_templates({

                    msg: params.msg,

                    type: params.notice_type

                }));

                if ($('#wpadminbar').length !== 0) {

                    notification.addClass('having-adminbar');

                }

                notification.hide().prependTo('body').fadeIn('fast').delay(1000).fadeOut(5000, function() {

                    $(this).remove();

                });

            },

            handleAuth: function(model, resp, jqXHR) {

                // check if authentication is successful or not

                if (resp.success) {



                    AE.pubsub.trigger('ae:notification', {

                        msg: resp.msg,

                        notice_type: 'success'

                    });



                    var data = resp.data;



                    //action login

                    if (data.do == "login" && !ae_globals.is_submit_project) {

                        window.location.reload();

                        //window.location.href = data.redirect_url;

                        //action register

                    } else if (data.do == "register" && !ae_globals.is_submit_project) {

                        if (model.get('role') == "freelancer" || model.get('role') == "employer") {

                            window.location.href = data.redirect_url;

                        } else {

                            window.location.reload();

                        }

                    }



                    if (!ae_globals.user_confirm) this.model.set(resp.data);



                } else {

                    AE.pubsub.trigger('ae:notification', {

                        msg: resp.msg,

                        notice_type: 'error'

                    });

                }

            },



            /**

	         * setup reject post modal and trigger event open modal reject

	         */

            rejectPost: function(model) {

                if (typeof this.rejectModal === 'undefined') {

                    this.rejectModal = new Views.RejectPostModal({

                        el: $('#reject_post')

                    });

                }

                this.rejectModal.onReject(model);

            },



            /**

	         * setup model for modal edit post and trigger event open the modal EditPost

	         */

            editPost: function(model) {



                if (typeof this.editModal === 'undefined') {

                    this.editModal = new Views.EditPost({

                        el: $('#modal_edit_' + model.get('post_type'))

                    });

                }

                this.editModal.onEdit(model, this.all_skills);

            },



        });

        /*

        *

        * M O D A L  R E G I S T E R  V I E W S

        *

        */

        Views.Modal_Register = Views.Modal_Box.extend({

            events: {

                // user register

                'submit form.signup_form': 'doRegister',

            },



            /**

             * init view setup Block Ui and Model User

             */

            initialize: function() {



                this.user = AE.App.user;



                this.blockUi = new Views.BlockUi();

                this.initValidator();

                //check button 

                /*var clickCheckbox = document.querySelector('.sign-up-switch'),

		            roleInput     = $("input#role");

		            hide_text = $('.hide-text').val();

		            work_text = $('.work-text').val();

		            view 		  = this;

	            if( $('.sign-up-switch').length > 0 ){

	                clickCheckbox.onchange = function() {

	                    

	                    if(clickCheckbox.checked){

	                        roleInput.val("employer");

	                        view.$('.user-type span.text').text(hide_text).removeClass('work').addClass('hire');

	                    } else {

	                        roleInput.val("freelancer");

	                        view.$('.user-type span.text').text(work_text).removeClass('hire').addClass('work');

	                    } 

	                };

	                var moveIt = this.$(".user-role").remove();

        			this.$(".switchery").append(moveIt);

	            }*/

            },

            /**

             * init form validator rules

             * can override this function by using prototype

             */

            initValidator: function() {

                if ($('#agreement').length > 0) {

                    this.register_validator = $("form.signup_form").validate({

                        rules: {

                            user_login: "required",

                            user_pass: "required",

                            agreement: "required",

                            user_email: {

                                required: true,

                                email: true

                            },

                            repeat_pass: {

                                required: true,

                                equalTo: "#repeat_pass"

                            }

                        }

                    });

                    return true;

                }

                /**

                 * register rule

                 */

                this.register_validator = $("form.signup_form").validate({

                    rules: {

                        user_login: "required",

                        user_pass: "required",

                        user_email: {

                            required: true,

                            email: true

                        },

                        repeat_pass: {

                            required: true,

                            equalTo: "#repeat_pass"



                        }

                    }

                });

            },

            /**

             * user sign-up catch event when user submit form signup

             */

            doRegister: function(event) {

                event.preventDefault();

                event.stopPropagation();

                // *

                //  * call validator init



                this.initValidator();

                var form = $(event.currentTarget),

                    button = form.find('button.btn-submit'),

                    view = this;

                /**

                 * scan all fields in form and set the value to model user

                 */

                form.find('input, textarea, select').each(function() {

                    view.user.set($(this).attr('name'), $(this).val());

                })

                // check form validate and process sign-up

                if (this.register_validator.form() && !form.hasClass("processing")) {

                    this.user.set('do', 'register');

                    this.user.request('create', {

                        beforeSend: function() {

                            view.blockUi.block(button);

                            form.addClass('processing');

                        },

                        success: function(user, status, jqXHR) {

                            view.blockUi.unblock();

                            form.removeClass('processing');

                            // trigger event process authentication

                            AE.pubsub.trigger('ae:user:auth', user, status, jqXHR);



                            if (status.success) {

                                AE.pubsub.trigger('ae:notification', {

                                    msg: status.msg,

                                    notice_type: 'success'

                                });

                                view.closeModal();

                                form.trigger('reset');

                            } else {

                                AE.pubsub.trigger('ae:notification', {

                                    msg: status.msg,

                                    notice_type: 'error'

                                });

                            }

                        }

                    });



                }

            }

        });

        /*

        *

        * M O D A L  L O G I N  V I E W S

        *

        */

        Views.Modal_Login = Views.Modal_Box.extend({

            events: {

                // user login

                'submit form.signin_form': 'doLogin',

                // show forgotpass form

                'click a.show-forgot-form': 'showForgot'

            },



            /**

             * init view setup Block Ui and Model User

             */

            initialize: function() {

                this.user = AE.App.user;

                this.blockUi = new Views.BlockUi();

                //validate forms

                this.initValidator();

            },

            /**

             * init form validator rules

             * can override this function by using prototype

             */

            initValidator: function() {

                // login rule

                this.login_validator = this.$("form.signin_form").validate({

                    rules: {

                        user_login: "required",

                        user_pass: "required"

                    }

                });

            },

            /**

             * show modal forgot pass form

             */

            showForgot: function(event) {

                event.preventDefault();

                event.stopPropagation();

                this.forgot = new Views.Modal_Forgot({
                    el: "#modal_forgot"
                });

                //close sign in form

                this.closeModal();

                //open forgot form

                this.forgot.openModal();

            },

            /**

             * user login,catch event when user submit login form

             */

            doLogin: function(event) {

                event.preventDefault();

                event.stopPropagation();

                /**

                 * call validator init

                 */

                this.initValidator();



                var form = $(event.currentTarget),

                    button = form.find('button.btn-submit'),

                    view = this;



                /**

                 * scan all fields in form and set the value to model user

                 */

                form.find('input, textarea, select').each(function() {

                    view.user.set($(this).attr('name'), $(this).val());

                })



                // check form validate and process sign-in

                if (this.login_validator.form() && !form.hasClass("processing")) {

                    this.user.set('do', 'login');

                    this.user.request('read', {

                        beforeSend: function() {

                            view.blockUi.block(button);

                            form.addClass('processing');

                        },

                        success: function(user, status, jqXHR) {

                            view.blockUi.unblock();

                            form.removeClass('processing');

                            // trigger event process authentication

                            AE.pubsub.trigger('ae:user:auth', user, status, jqXHR);

                            if (status.success) {

                                AE.pubsub.trigger('ae:notification', {

                                    msg: status.msg,

                                    notice_type: 'success'

                                });

                                view.closeModal();

                                form.trigger('reset');

                            } else {

                                AE.pubsub.trigger('ae:notification', {

                                    msg: status.msg,

                                    notice_type: 'error'

                                });

                            }

                        }

                    });



                }

            },

        });

        /*

        *

        * M O D A L  L O G I N  V I E W S

        *

        */

        Views.Modal_Change_Pass = Views.Modal_Box.extend({

            events: {

                // user login

                'submit form.chane_pass_form': 'doChangePass',

            },



            /**

             * init view setup Block Ui and Model User

             */

            initialize: function() {

                this.user = AE.App.user;

                this.LoadingButtonNew = new Views.LoadingButtonNew();

                this.blockUi = new Views.BlockUi();

                this.initValidator();

            },

            /**

             * init form validator rules

             * can override this function by using prototype

             */

            initValidator: function() {

                // login rule

                this.changepass_validator = $("form.chane_pass_form").validate({

                    rules: {

                        old_password: "required",

                        new_password: "required",

                        renew_password: {

                            required: true,

                            equalTo: "#new_password"

                        }

                    },

                });

                jQuery('#new_password').keyup(function() {
                    var pswd = jQuery(this).val();
                    if (pswd.length < 8) {
                        jQuery('#length').removeClass('valid').removeClass('required').addClass('invalid');
                    } else {
                        jQuery('#length').removeClass('invalid').addClass('valid').addClass('required');
                    }
                    //validate letter
                    if (pswd.match(/[A-z]/)) {
                        jQuery('#letter').removeClass('invalid').addClass('valid');
                    } else {
                        jQuery('#letter').removeClass('valid').addClass('invalid');
                    }

                    //validate capital letter
                    if (pswd.match(/[A-Z]/)) {
                        jQuery('#capital').removeClass('invalid').addClass('valid');
                    } else {
                        jQuery('#capital').removeClass('valid').addClass('invalid');
                    }

                    //validate number
                    if (pswd.match(/\d/)) {
                        jQuery('#number').removeClass('invalid').addClass('valid');
                    } else {
                        jQuery('#number').removeClass('valid').addClass('invalid');
                    }
                    var count_valid = jQuery('#pswd_info li.valid').length;
                    if (count_valid && jQuery('#pswd_info li.required').length >= 1) {
                        if (count_valid >= 2) {
                            jQuery('.strong-level').text('minimum')
                        }
                        if (count_valid >= 3) {
                            jQuery('.strong-level').text('medium')
                        }
                        if (count_valid >= 4) {
                            jQuery('.strong-level').text('strong')
                        }
                    } else {
                        jQuery('.strong-level').text('danger')
                    }
                }).focus(function() {
                    jQuery('#pswd_info').fadeIn('slow');
                }).blur(function() {
                    jQuery('#pswd_info').fadeOut('fast');
                })

            },


            /**

             * user login,catch event when user submit login form

             */

            doChangePass: function(event) {

                event.preventDefault();

                event.stopPropagation();

                if (jQuery('#pswd_info li.valid').length < 2 || jQuery('#pswd_info li.required').length < 1) {
                    jQuery('#pswd_info').fadeIn('slow');
                    return false;
                }

                /**

                 * call validator init

                 */

                this.initValidator();



                var form = $(event.currentTarget),

                    button = form.find('.btn-submit'),

                    view = this;



                /**

                 * scan all fields in form and set the value to model user

                 */

                form.find('input, textarea, select').each(function() {

                    view.user.set($(this).attr('name'), $(this).val());

                })
                if (jQuery('#old_password').val() === jQuery('#new_password').val()) {

                    AE.pubsub.trigger('ae:notification', {

                        msg: ae_globals.validator_messages.old_pass_equal_new_error,

                        notice_type: 'error'

                    });
                    return false;
                }


                // check form validate and process sign-in

                if (this.changepass_validator.form() && !form.hasClass("processing")) {

                    this.user.save('do', 'changepass', {

                        beforeSend: function() {

                            view.LoadingButtonNew.loading(button);

                            form.addClass('processing');

                        },

                        success: function(user, status, jqXHR) {

                            view.LoadingButtonNew.finish(button);

                            form.removeClass('processing');

                            // trigger event process after change pass

                            AE.pubsub.trigger('ae:user:changepass', user, status, jqXHR);

                            if (status.success) {

                                AE.pubsub.trigger('ae:notification', {

                                    msg: status.msg,

                                    notice_type: 'success'

                                });

                                view.closeModal();

                                form.trigger('reset');

                                jQuery('#length').removeClass('valid').addClass('invalid');
                                jQuery('#number').removeClass('valid').addClass('invalid');
                                jQuery('#capital').removeClass('valid').addClass('invalid');
                                jQuery('#letter').removeClass('valid').addClass('invalid');
                                jQuery('.strong-level').text('danger')

                                //window.location.href = ae_globals.homeURL;

                            } else {

                                AE.pubsub.trigger('ae:notification', {

                                    msg: status.msg,

                                    notice_type: 'error'

                                });

                            }

                        }

                    });



                }

            },

        });

        /*

         *

         * M O D A L  F O R G O T  V I E W S

         *

         */

        Views.Modal_Forgot = Views.Modal_Box.extend({

            events: {

                // user forgot password

                'submit form.forgot_form': 'doSendPassword',

            },



            /**

             * init view setup Block Ui and Model User

             */

            initialize: function() {

                this.user = AE.App.user;

                this.blockUi = new Views.BlockUi();

                this.initValidator();

            },

            /**

             * init form validator rules

             * can override this function by using prototype

             */

            initValidator: function() {

                /**

                 * forgot pass email rule

                 */

                this.forgot_validator = $("form.forgot_form").validate({

                    rules: {

                        user_email: {

                            required: true,

                            email: true

                        },

                    }

                });

            },

            /**

             * user forgot password

             */

            doSendPassword: function(event) {

                event.preventDefault();

                event.stopPropagation();

                /**

                 * call validator init

                 */

                this.initValidator();

                var form = $(event.currentTarget),

                    email = form.find('input#user_email').val(),

                    button = form.find('button.btn-submit'),

                    view = this;



                if (this.forgot_validator.form() && !form.hasClass("processing")) {



                    this.user.set('user_login', email);

                    this.user.set('do', 'forgot');

                    this.user.request('read', {

                        beforeSend: function() {

                            view.blockUi.block(button);

                            form.addClass('processing');

                        },

                        success: function(user, status, jqXHR) {

                            form.removeClass('processing');

                            view.blockUi.unblock();

                            if (status.success) {

                                view.closeModal();

                                AE.pubsub.trigger('ae:notification', {

                                    msg: status.msg,

                                    notice_type: 'success'

                                });

                                form.trigger('reset');

                            } else {

                                AE.pubsub.trigger('ae:notification', {

                                    msg: status.msg,

                                    notice_type: 'error'

                                });

                            }



                        }

                    });



                }

            }

        });

        /*

        *

        * S E A R C H  H E A D E R  V I E W S

        *

        */

        /*Views.SearchForm = Backbone.View.extend({

        	events: {

        		//change search type job or profile?

        		'change select.search-filter': 'changeSearchType'

        	},

        	initialize: function() {

        		this.container           = this.$("#search_form");

        		this.search_type         = "project";

        		this.collection_projects = new Collections.Projects();

        		this.collection_profiles = new Collections.Profiles();

        		// projects list

        		if (typeof ListProjects !== "undefined") {

        			//list projects

        			new ListProjects({

        				itemView: ProjectItem,

        				collection: this.collection_projects,

        				el: $('#projects_list')

        			});

        			//list profiles

        			new ListProfiles({

        				itemView: ProfileItem,

        				collection: this.collection_profiles,

        				el: $('#profiles_list')

        			});

        		}

        		if (typeof Views.BlockControl !== "undefined") {

        			//project control

        			SearchProjectControl  = Views.BlockControl.extend({

        				onBeforeFetch : function() { 

        					this.$el.find('.no-result').remove();

        				},

        				onAfterFetch : function(result, res) {

        					if(!res.success) {

        						$('#projects_list').append($('#project-no-result').html());	

        					}							

        				}

        			});

        			new SearchProjectControl({

        				collection: this.collection_projects,

        				el: this.$(".projects-search-container"),

        				query: {

        					paginate: 'page'

        				}

        			});



        			SearchProfileControl  = Views.BlockControl.extend({

        				onBeforeFetch : function() { 

        					this.$el.find('.no-result').remove();

        				},

        				onAfterFetch : function(result, res) {

        					if(!res.success) {

        						$('#profiles_list').append($('#profile-no-result').html());	

        					}							

        				}

        			});

        			//profile control

        			new SearchProfileControl({

        				collection: this.collection_profiles,

        				el: this.$(".profiles-search-container"),

        				query: {

        					paginate: 'page'

        				}

        			});

        		}

        	},

        	changeSearchType: function(event) {

        		var target = $(event.currentTarget);

        		this.search_type = target.val();

        		if (this.search_type == "profile") {

        			$(".profiles-search-container").show();

        			$(".projects-search-container").hide();

        		} else {

        			$(".projects-search-container").show();

        			$(".profiles-search-container").hide();

        		}

        	}

        });*/



        /**

	     * modal edit project

	     */

        Views.EditPost = Views.Modal_Box.extend({

            events: {

                'submit form#frm_edit_project': 'submitPost',

                // 'click form .btn-submit': 'submitPost'          



            },

            initialize: function() {

                _.bindAll(this, 'onEdit', 'cbSuccess', 'cbBeforeSend');

                AE.Views.Modal_Box.prototype.initialize.call();

                this.blockUi = new Views.BlockUi();
                this.LoadingButtonNew = new Views.LoadingButtonNew();


                this.initValidator();

                this.$('#project_category').chosen({

                    width: '100%',

                    max_selected_options: parseInt(ae_globals.max_cat),

                    inherit_select_classes: true

                });



                if ($('.sw_skill').length > 0) {

                    this.$('.sw_skill').chosen({

                        max_selected_options: parseInt(ae_globals.max_cat),

                        inherit_select_classes: false,

                        width: '100%'

                    });

                }



            },

            /**

            * validate form before submit

            */



            initValidator: function() {

                /**

                 * post form validate

                 */



                $("form#frm_edit_project").validate({

                    ignore: "",

                    rules: {

                        post_title: "required",

                        et_budget: "required",

                        project_category: "required",

                        post_content: "required"

                    },

                    errorPlacement: function(label, element) {

                        // position error label after generated textarea

                        // position error label after generated textarea

                        if (element.is("textarea")) {

                            label.insertAfter(element.next());

                        } else {

                            $(element).closest('div').append(label);

                        }

                             AE.pubsub.trigger('ae:notification', {

                                                 msg: ae_globals.msg,

                                                 notice_type: 'error',

                                             });

                    }

                });

            },

            // user submit form edit post

            submitPost: function(event) {

                event.preventDefault();

                var view = this,

                    form = $(event.currentTarget),

                    button = form.find('button.btn'),

                    temp = new Array();



                /**

                 * update model from input, textarea, select

                 */

                view.$el.find('input,textarea,select').each(function() {
                    if ($(this).attr('id') == 'post_title') {
                        view.model.set('post_title', $(this).val());

                    } else if ($(this).attr('id') == 'et_budget') {
                        view.model.set('et_budget', $(this).val());

                    } else if ($(this).attr('id') == 'edit_projects') {
                        view.model.set('post_content', $(this).val());
                    } else if ($(this).attr('id') == 'skill') {
                        view.model.set('skill', $(this).val());
                    } else if ($(this).attr('id') == 'project_category') {
                        view.model.set('project_category', $(this).val());
                    } else {
                        view.model.set($(this).attr('name'), $(this).text());

                    }
                });


                view.$el.find('input[type=checkbox]').each(function() {

                    var name = $(this).attr('name');

                    view.model.set(name, []);

                });



                /**

                 * update input check box to model

                 */

                view.$el.find('input[type=checkbox]:checked').each(function() {

                    var name = $(this).attr('name');

                    if (typeof temp[name] !== 'object') {

                        temp[name] = new Array();

                    }

                    temp[name].push($(this).val());

                    view.model.set(name, temp[name]);

                });

                /**

                 * update input radio to model

                 */

                view.$el.find('input[type=radio]:checked').each(function() {

                    view.model.set($(this).attr('name'), $(this).val());

                });



                view.model.set('skill', view.skill_control.model.get('skill'));



                /**

                 * save model

                 */

                if (this.$("form#frm_edit_project").validate()) {

                    view.model.save('', '', {

                        beforeSend: function() {

                            //view.blockUi.block(button);
                            view.LoadingButtonNew.loading(button);

                        },

                        success: function(result, res, jqXHR) {

                            //view.blockUi.unblock();
                            view.LoadingButtonNew.finish(button);
                            if (res.success) {

                                view.closeModal();

                                if (ae_globals.is_single) {

                                    window.location.reload();

                                }

                                view.success(res);

                            } else {

                                view.error(res);

                            }

                        }

                    });

                }



            },

            /**

             * on edit a model and setup modal data views

             */

            onEdit: function(model, skills) {

                var view = this;

                this.model = model;

                this.all_skills = skills;

                // open the modal



                this.openModal();

                // setup fields

                this.setupFields();



                if (typeof this.skill_control === 'undefined') {

                    this.skill_control = new Views.Skill_Control({
                        el: view.$('.skill-control')
                    });

                }

                // console.log(this.model);

                this.skill_control.setModel(this.model);


            },

            /**

             *

             */

            setupFields: function() {

                var view = this,

                    form_field = view.$('.form-group');



                AE.pubsub.trigger('AE:beforeSetupFields', this.model);

                /**

                 * update form value for input, textarea select

                 */

                form_field.find('input[type="text"],input[type="number"],input[type="hidden"], textarea,select').each(function() {
                    //debugger;
                    var $input = $(this);

                    if ($input.attr('name') == 'post_title') {

                        var elem = document.createElement('textarea');
                        elem.innerHTML = view.model.get($input.attr('name'));
                        var decoded = elem.value;
                        $input.val(decoded);

                    } else {
                        $input.val(view.model.get($input.attr('name')));

                    }
                    // trigger chosen update if is select

                    if ($input.get(0).nodeName === "SELECT") $input.trigger('chosen:updated');

                });

                form_field.find('input[type="radio"]').each(function() {

                    var $input = $(this),

                        name = $input.attr('name');

                    if ($input.val() == view.model.get(name)) {

                        $input.attr('checked', true);

                    }

                });



                form_field.find('input[type="checkbox"]').each(function() {

                    var $input = $(this),

                        name = $input.attr('name');

                    if ($.inArray(parseInt($input.val()), view.model.get(name)) > -1) {

                        $input.attr('checked', true);

                    }

                });

                // update value for post content editor

                if (typeof tinyMCE !== 'undefined') {

                    tinymce.EditorManager.execCommand('mceAddEditor', true, "edit_projects");

                    tinymce.EditorManager.get('edit_projects').setContent(view.model.get('post_content'));

                }

                form_field.find("ul#skills_list").html('');

                // init carousel

                if (typeof view.carousels === 'undefined') {

                    view.carousels = new Views.Carousel({

                        el: $('#gallery_container'),

                        model: view.model,

                        extensions: 'pdf,doc,docx,png,jpg,gif,zip'

                    });

                } else {

                    view.carousels.setModel(view.model);

                    view.carousels.setupView();

                }

                AE.pubsub.trigger('AE:afterSetupFields', this.model);

            },

            resetUploader: function() {

                if (typeof this.uploader === 'undefined') return;

                this.uploader.controller.splice();

                this.uploader.controller.refresh();

                this.uploader.controller.destroy();

            },

            cbSuccess: function(res) {

                var view = this;

                view.blockUi.unblock();

                // view.model.set('cover_image', res.data.attach_id);

                // view.model.set('cover_image_url', res.data.full[0]);

                // view.$('#cover_background').css('background', 'url(' + res.data.full[0] + ') no-repeat center center / cover cadetblue');

                view.model.set('uploadingCarousel', false);

            },

            cbBeforeSend: function(ele) {

                var view = this;

                button = $(ele).find('.image');

                view.blockUi.block(button);

                view.model.set('uploadingCarousel', true);

            }

        });



        /*

	     *

	     * S U M I T  P R O J E C T  V I E W S

	     *

	    */

        Views.SubmitProject = Views.SubmitPost.extend({

            onAfterInit: function() {

                var view = this;

                if ($('#edit_postdata').length > 0) {

                    var postdata = JSON.parse($('#edit_postdata').html());

                    this.model = new Models.Project(postdata);

                    this.model.set('renew', 1);

                    this.setupFields();

                } else {

                    this.model = new Models.Project();

                }



                view.carousels = new Views.Carousel({

                    el: $('#gallery_container'),

                    model: view.model,

                    extensions: 'pdf,doc,docx,png,jpg,gif,zip,ppt,pptx'

                });



                if ($('.sw_skill').length > 0) {

                    this.$('.sw_skill').chosen({

                        max_selected_options: parseInt(ae_globals.max_cat),

                        inherit_select_classes: false,

                        width: '95%',

                    });

                }

                if (view.$('.skill-control').length > 0) {

                    //new skills view

                    new Views.Skill_Control({

                        model: this.model,

                        el: view.$('.skill-control'),

                        name: 'skill'

                    });

                }

                this.$('.multi-tax-item').chosen({

                    width: '95%',

                    max_selected_options: parseInt(ae_globals.max_cat),

                    inherit_select_classes: true

                });

            },

            onLimitFree: function() {

                AE.pubsub.trigger('ae:notification', {

                    msg: ae_globals.limit_free_msg,

                    notice_type: 'error',

                });

            },

            onAfterShowNextStep: function(step) {

                $('.step-heading').find('i.fa-caret-down').removeClass('fa-caret-right fa-caret-down').addClass('fa-caret-right');

                $('.step-' + step).find('.step-heading i.fa-caret-right').removeClass('fa-caret-right').addClass('fa-caret-down');

            },

            onAfterSelectStep: function(step) {

                $('.step-heading').find('i').removeClass('fa-caret-right fa-caret-down').addClass('fa-caret-right');

                step.find('i').removeClass('fa-caret-right').addClass('fa-caret-down');

            },

            // on after Submit auth fail

            onAfterAuthFail: function(model, res) {

                AE.pubsub.trigger('ae:notification', {

                    msg: res.msg,

                    notice_type: 'error',

                });

            },

            onAfterPostFail: function(model, res) {

                AE.pubsub.trigger('ae:notification', {

                    msg: res.msg,

                    notice_type: 'error',

                });

            },

            onAfterSelectPlan: function($step, $li) {

                var label = $li.attr('data-label');

                $step.find('.text-heading-step').html(label);

            }

        });




        Views.SubmitBibPlan = Views.SubmitPost.extend({

            onAfterInit: function() {

                var view = this;

                if ($('#edit_postdata').length > 0) {

                    var postdata = JSON.parse($('#edit_postdata').html());

                    this.model = new Models.Bid(postdata);

                    this.model.set('renew', 1);

                    this.setupFields();

                } else {

                    this.model = new Models.Bid();

                    //this.model.set('post_parent', 2858); //???

                }

                //new skills view

                new Views.Skill_Control({

                    model: this.model,

                    el: view.$('.skill-control')

                });



                this.$('.multi-tax-item').chosen({

                    width: '95%',

                    max_selected_options: parseInt(ae_globals.max_cat),

                    inherit_select_classes: true

                });

            },

            onLimitFree: function() {

                AE.pubsub.trigger('ae:notification', {

                    msg: ae_globals.limit_free_msg,

                    notice_type: 'error',

                });

            },

            onAfterShowNextStep: function(step) {

                $('.step-heading').find('i.fa-caret-down').removeClass('fa-caret-right fa-caret-down').addClass('fa-caret-right');

                $('.step-' + step).find('.step-heading i.fa-caret-right').removeClass('fa-caret-right').addClass('fa-caret-down');

            },

            onAfterSelectStep: function(step) {

                $('.step-heading').find('i').removeClass('fa-caret-right fa-caret-down').addClass('fa-caret-right');

                step.find('i').removeClass('fa-caret-right').addClass('fa-caret-down');

            },

            // on after Submit auth fail

            onAfterAuthFail: function(model, res) {

                AE.pubsub.trigger('ae:notification', {

                    msg: res.msg,

                    notice_type: 'error',

                });

            },

            onAfterPostFail: function(model, res) {

                AE.pubsub.trigger('ae:notification', {

                    msg: res.msg,

                    notice_type: 'error',

                });

            }

        });




        $('.slider-ranger').slider();



        DPGlobal.dates = ae_globals.dates;

        $('.datepicker').datepicker({

            format: 'mm/dd/yyyy'

        });

        // $('#datepicker').on('changeDate', function(ev){

        //     $(this).datepicker('hide');

        // });

        $('.tooltip-style').tooltip();

        $('.image-gallery').magnificPopup({
            type: 'image',
            image: {
                headerFit: true,
                captionFit: true,
                preserveHeaderAndCaptionWidth: false,
                markup: '<div class="mfp-figure">' +
                    '<figure>' +
                    '<header class="mfp-header">' +
                    '<div class="mfp-top-bar">' +
                    '<div class="mfp-title"></div>' +
                    '<div class="mfp-close"></div>' +

                    '</div>' +
                    '</header>' +
                    '<section class="mfp-content-container">' +
                    '<div class="mfp-img"></div>' +
                    '</section>' +
                    '<footer class="mfp-footer">' +
                    '<figcaption class="mfp-figcaption">' +
                    '<div class="mfp-bottom-bar-container">' +
                    '<div class="mfp-bottom-bar">' +
                    '<div class="mfp-counter"></div>' +
                    '<div class="mfp-description"></div>' +
                    '</div>' +
                    '</div>' +
                    '</figcaption>' +
                    '</footer>' +
                    '</figure>' +
                    '</div>',
                titleSrc: function(item) {
                    return item.el.attr('title');
                },
                description: function(item) {
                    return item.el.data('description');
                }

            },
            callbacks: {
                //                elementParse: function(item) {
                //                    // Function will fire for each target element
                //                    // "item.el" is a target DOM element (if present)
                //                    // "item.src" is a source that you may modify
                //                   // item.find('.mfp-description').html(item.el.data('description'));
                //                    //console.log(jQuery('.mfp-description')); // Do whatever you want with "item" object
                //                },
                markupParse: function(template, values, item) {
                    values.description = item.el.data('description'); // or item.img.data('description');
                }
            }
        });



        $('.trigger-menu').click(function() {

            $('.search-fullscreen').hide();

            $('.notification-fullscreen').hide();

            $('.menu-fullscreen').show();

            $('body').addClass('fre-menu-overflow');

            // $('#video-background-wrapper').hide();

        });



        $('.overlay-close').click(function() {

            $('body').removeClass('fre-menu-overflow');

            // $('#video-background-wrapper').show();

        });

        $('.trigger-search').click(function() {

            $('.menu-fullscreen').hide();

            $('.notification-fullscreen	').hide();

            $('.search-fullscreen').show();

            $('body').addClass('fre-menu-overflow');

            //$('#video-background-wrapper').hide();

        });



        $('.trigger-notification, .trigger-notification-2').on('click', function() {

            //$('.menu-fullscreen').hide(); 

            //$('.search-fullscreen').hide();

            //$('.notification-fullscreen').show();

            //$('body').addClass('fre-menu-overflow');

        });



        if ($('.menu-fullscreen  li').length > 6) {

            $('.overlay nav').css({
                height: '80%'
            });

            $('.menu-main > li').css({
                height: '80px'
            });

        }

        if ($('.menu-fullscreen  li').length > 10) {

            $('.overlay nav').css({
                height: '95%'
            });

        }

        $('.menu-fullscreen ul.sub-menu').each(function() {

            var li = $(this).find('li').length;

            li++;

            $(this).parents('li').css({
                height: li * 60 + 10 + 'px'
            });

        });



        // Select style

        var class_chosen = $(".chosen-select");

        $(".chosen-select").each(function() {

            var data_chosen_width = $(this).attr('data-chosen-width'),

                data_chosen_disable_search = $(this).attr('data-chosen-disable-search'),

                max_selected_options = $(this).attr('data-max-select');

            $(this).chosen({
                width: data_chosen_width,
                disable_search: data_chosen_disable_search,
                max_selected_options: max_selected_options
            });

        });



        // $('.chosen-select-date').chosen({width : '70%', disable_search: true});	



        // Resize search input header

        function resizeInput() {

            var contents = $(this).val(),

                charlength = contents.length;

            if (charlength > 0) {

                $(this).attr('size', charlength);

                $(this).css('width', 'auto');

            } else {

                $(this).css('width', '540px');

            }



        }

        $('input.field-search-top')

        // event handler

        .keyup(resizeInput)

        // resize on page load

        .each(resizeInput);



        //iOS7 Switcher

        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

        elems.forEach(function(html) {

            var switchery = new Switchery(html, {});

        });



        //iOS7 Switcher for sign-up

        $('.sign-up-switch').each(function(index, el) {

            new Switchery(el, {
                color: 'rgba(231,76,60,.9)',
                secondaryColor: 'rgba(42,62,80,.9)'
            });

        });



        /**

         * Menu style fixed

        */
        var lastScrollTop = 0
        $(window).scroll(function(e) {
            $el = $('#header-wrapper');
//            console.log($(window).scrollTop());
            var st = $(this).scrollTop();
            var scrollBottom = $(window).scrollTop() + $(window).height();

            if ($(window).scrollTop() > $el.height() && $(document).height() > $(window).height() * 1.20 && scrollBottom < $(document).height() - 100) {
                if (st > lastScrollTop) {
                    $el.removeClass("on-top").removeClass("on-bottom").removeClass("sticky").addClass("hidden-sticky");
                } else {
                    $el.removeClass("on-top").removeClass("on-bottom").removeClass("hidden-sticky").addClass("sticky");
                }
            } else if ($(window).scrollTop() < 10) {
                $el.removeClass("sticky").removeClass("hidden-sticky").removeClass("on-bottom").addClass("on-top");
            } else {
                $el.removeClass("sticky").removeClass("hidden-sticky").addClass("on-bottom");
            }
            lastScrollTop = st;


        });




        /**

         * COUNTER 

         */

        if ($('.odometer').length > 0) {

            $('.odometer').waypoint(function() {

                var data_number = $(this).attr('data-number');

                $(this).html(data_number);

            }, {
                offset: '75%'
            });

        }

        /**

         * TABS

         */

        $('#authenticate_tab a').click(function(e) {

            e.preventDefault();

            $(this).tab('show');

        });

        $('#standardmenu .active > a').click(function() {
            return false;
        });



        /**

         * RATE IT

         */

        $('.rate-it').raty({

            readOnly: true,

            half: true,

            score: function() {

                return $(this).attr('data-score');

            },

            hints: raty.hint

        });

    });

})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);

/*=============== Javascript Serialize Object =================== */

jQuery.fn.serializeObject = function() {

    var self = this,

        json = {},

        push_counters = {},

        patterns = {

            "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,

            "key": /[a-zA-Z0-9_]+|(?=\[\])/g,

            "push": /^$/,

            "fixed": /^\d+$/,

            "named": /^[a-zA-Z0-9_]+$/

        };

    this.build = function(base, key, value) {

        base[key] = value;

        return base;

    };

    this.push_counter = function(key) {

        if (push_counters[key] === undefined) {

            push_counters[key] = 0;

        }

        return push_counters[key]++;

    };

    jQuery.each(jQuery(this).serializeArray(), function() {

        // skip invalid keys

        if (!patterns.validate.test(this.name)) {

            return;

        }

        var k,

            keys = this.name.match(patterns.key),

            merge = this.value,

            reverse_key = this.name;

        while ((k = keys.pop()) !== undefined) {

            // adjust reverse_key

            reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

            // push

            if (k.match(patterns.push)) {

                merge = self.build([], self.push_counter(reverse_key), merge);

            }

            // fixed
            else if (k.match(patterns.fixed)) {

                merge = self.build([], k, merge);

            }

            // named
            else if (k.match(patterns.named)) {

                merge = self.build({}, k, merge);

            }

        }

        json = jQuery.extend(true, json, merge);

    });

    return json;

};