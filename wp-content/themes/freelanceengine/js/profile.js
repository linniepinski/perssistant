(function ($, Models, Collections, Views) {
    /*
     *
     * E D I T  P R O F I L E  V I E W S
     *
     */
    Views.Profile = Backbone.View.extend({
        el: '.list-profile-wrapper',
        events: {
            // user account details
            'submit form#account_form': 'saveAccountDetails',
            // user profile details
            'submit form#profile_form': 'saveProfileDetails',
            // authorize credit card
            'submit form#finance_form': 'authorizeCreditCard',
            // bank details
            'submit form#bank_form': 'saveBankDetails',
            // open modal add portfolio
            'click a.add-portfolio': 'openModalPorfolio',
            // open modal add CV
            'click a.add-cv': 'openModalCv',
            // open modal change password
            'click a.change-password': 'openModalChangePW',
            // request confirm mail
            'click a.request-confirm': 'requestConfirmMail',
            // remove credit card
            //'click a.remove-cc': 'removeCreditCard'
        },
        // request a confirm email
        requestConfirmMail: function (e) {
            e.preventDefault();
            var $target = $(e.currentTarget),
                view = this;
            this.user.confirmMail({
                beforeSend: function () {
                    view.blockUi.block($target);
                },
                success: function (result, res, xhr) {
                    view.blockUi.unblock();
                    AE.pubsub.trigger('ae:notification', {
                        msg: res.msg,
                        notice_type: (res.success) ? 'success' : 'error',
                    });
                }
            });
        },
        /**
         * init view setup Block Ui and Model User
         */
        initialize: function () {
            this.initValidator();
            var view = this;
            this.blockUi = new Views.BlockUi();
            this.LoadingButtonNew = new Views.LoadingButtonNew();
            this.user = AE.App.user;
            //get id from the url
            var hash = window.location.hash;
            hash && $('ul.nav a[href="' + hash + '"]').tab('show');
            //set current profile
            if ($('#current_profile').length > 0) {
                this.profile = new Models.Profile(JSON.parse($('#current_profile').html()));
                //$('#current_profile').remove();
            } else {
                this.profile = new Models.Profile();
            }
            //new skills view     
            console.log('vaoo');
            new Views.Skill_Control({
                model: this.profile,
                el: view.$('.skill-profile-control'),
                name: 'skill'
            });
            // update value for post content editor
            if (typeof tinyMCE !== 'undefined') {
                tinymce.EditorManager.execCommand('mceAddEditor', true, "about_content");
                // tinymce.EditorManager.get('about_content').setContent(view.model.get('post_content'));
            }

            if ($('.edit-portfolio-container').length > 0) {
                var $container = $('.edit-portfolio-container');
                //portfolio list control
                if ($('.edit-portfolio-container').find('.postdata').length > 0) {
                    var postdata = JSON.parse($container.find('.postdata').html());
                    this.portfolios_collection = new Collections.Portfolios(postdata);
                } else {
                    this.portfolios_collection = new Collections.Portfolios();
                }
                /**
                 * init list portfolio view
                 */
                new ListPortfolios({
                    itemView: PortfolioItem,
                    collection: this.portfolios_collection,
                    el: $container.find('.list-item-portfolio')
                });
                /**
                 * init block control list blog
                 */
                new Views.BlockControl({
                    collection: this.portfolios_collection,
                    el: $container
                });
            }
            //button available
            var availableCheckbox = document.querySelector('.user-available');
            if ($('.user-available').length > 0) {
                availableCheckbox.onchange = function () {
                    if (availableCheckbox.checked) {
                        $('.switch-for-hide span.text').text('Yes').removeClass('no').addClass('yes');
                    } else {
                        $('.switch-for-hide span.text').text('No').removeClass('yes').addClass('no');
                    }
                    //alert(availableCheckbox.checked);
                    view.user.save('user_available', availableCheckbox.checked ? "on" : "off", {
                        beforeSend: function () {
                            view.blockUi.block(view.$('.switchery'));
                        },
                        success: function (res) {
                            view.blockUi.unblock();
                        }
                    });
                };
            }

            this.uploaderID = 'user_avatar';
            var $container = $("#user_avatar_container");
            //init avatar upload
            if (typeof this.avatar_uploader === "undefined") {
                this.avatar_uploader = new AE.Views.File_Uploader({
                    el: $container,
                    uploaderID: this.uploaderID,
                    thumbsize: 'thumbnail',
                    multipart_params: {
                        _ajax_nonce: $container.find('.et_ajaxnonce').attr('id'),
                        data: {
                            method: 'change_avatar',
                            author: view.user.get('ID')
                        },
                        imgType: this.uploaderID,
                    },
                    cbUploaded: function (up, file, res) {
                        if (res.success) {
                            $('#' + this.container).parents('.desc').find('.error').remove();
                        } else {
                            $('#' + this.container).parents('.desc').append('<div class="error">' + res.msg + '</div>');
                        }

                    },
                    beforeSend: function (ele) {
                        button = $(ele).find('.image');
                        view.blockUi.block(button);
                    },
                    success: function (res) {
                        if (res.success === false) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error',
                            });
                        } else {
                            //update avatar ... header menu
                            jQuery('.current_user_avatar img').attr('src', res.data.thumbnail[0])
                        }
                        view.blockUi.unblock();
                    }
                });
            }
            var moveIt = $(".user-status-text").remove();
            $(".switchery").append(moveIt);
            this.$('.cat_profile').chosen({
                width: '350px'
            });
            //handle change password in mobile
            if (ae_globals.ae_is_mobile) {
                this.modalChangePW = new Views.Modal_Change_Pass({
                    el: '#tab_change_pw'
                });
            }
            ;
            this.$('.sw_skill').chosen({
                max_selected_options: 10,
                inherit_select_classes: true,
                width: '95%',
            })
            // about_content
        },
        /**
         * init form validator rules
         * can override this function by using prototype
         */
        initValidator: function () {
            // login rule
            this.account_validator = $("form#account_form").validate({
                rules: {
                    display_name: "required",
                    user_email: {
                        required: true,
                        email: true
                    },
                    paypal: {
                        email: true
                    }

                },
                messages: {
                    //name: "Please specify your name",
                    paypal: {
                        email: "Email address seems invalid"
                    },
                    user_email: {
                        email: "Email address seems invalid"
                    }
                }
            });
            /**
             * register rule
             */
            this.profile_validator = $("form#profile_form").validate({
                rules: {
                    et_professional_title: "required",
                    country: "required",
                    hour_rate: {
                        required: true,
                        number: true
                    },
                    et_experience: {
                        number: true,
                        min: 0,
                        max: 30
                    }

                }
            });
            // credit card authorization

            this.credit_card_validator = $("form#finance_form").validate({

                rules: {

                    card_type: "required",

                    first_name: "required",

                    last_name: "required",

                    card_number: {
                        required: true,
                        number: true
                    },

                    exp_month: {
                        required: true,
                        number: true
                    },

                    exp_year: {
                        required: true,
                        number: true
                    },

                    card_cvv: {
                        required: true,
                        number: true
                    },

                    stree: "required",

                    city: "required",

                    user_country: "required",

                    zip_code: "required"

                }

            });

            // credit card authorization

            this.bank_details_validator = $("form#bank_form").validate({

                rules: {

                    account_type: "required",

                    bank_name: "required",

                    bank_country: "required",

                    routing_no: "required",

                    bank_address: "required",

                    bank_city: "required",

                    bank_state: "required",

                    bank_zipcode: "required",

                    account_holder_currency: "required",

                    account_holder_name: "required",

                    account_number: "required",

                    account_holder_address: "required",

                    account_holder_city: "required",

                    account_holder_country: "required",

                    account_holder_state: "required",

                    account_holder_zipcode: "required"
                }

            });

        },

        /**
         * user profile, catch event when user submit profile form
         */
        saveAccountDetails: function (event) {
            event.preventDefault();
            event.stopPropagation();
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
            form.find('input, textarea, select').each(function () {
                view.user.set($(this).attr('name'), $(this).val());
            })
            // check form validate and process sign-in
            if (this.account_validator.form() && !form.hasClass("processing")) {
                this.user.set('do', 'profile');
                this.user.request('update', {
                    beforeSend: function () {
                        view.LoadingButtonNew.loading(button);
                        form.addClass('processing');
                    },
                    success: function (profile, status, jqXHR) {
                        start_refresh_count();
                        view.LoadingButtonNew.finish(button);
                        form.removeClass('processing');
                        // trigger event process authentication
                        AE.pubsub.trigger('ae:user:account', profile, status, jqXHR);
                        // trigger event notification
                        if (status.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'success',
                            });
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'error',
                            });
                        }
                    }
                });
            }
        },
        /**
         * user profile, catch event when user submit profile form
         */
        saveProfileDetails: function (event) {
            event.preventDefault();
            event.stopPropagation();
            /**
             * call validator init
             */
            this.initValidator();
            var form = $(event.currentTarget),
                button = form.find('.btn-submit'),
                view = this,
                temp = new Array();
            ;
            /**
             * scan all fields in form and set the value to model user
             */
            form.find('input, textarea, select').each(function () {
                view.profile.set($(this).attr('name'), $(this).val());
            });
            /**
             * update input check box to model
             */
            form.find('input[type=checkbox]:checked').each(function () {
                var name = $(this).attr('name');
                if (typeof temp[name] !== 'object') {
                    temp[name] = new Array();
                }
                temp[name].push($(this).val());
                view.profile.set(name, temp[name]);
            });
            /**
             * update input radio to model
             */
            form.find('input[type=radio]:checked').each(function () {
                view.profile.set($(this).attr('name'), $(this).val());
            });
            // check form validate and process sign-in
            if (this.$('form#profile_form').valid() && !form.hasClass("processing")) {
                this.profile.save('', '', {
                    beforeSend: function () {
                        count = jQuery("iframe#about_content_ifr").contents().find('body').text().replace(/(<([^>]+)>)/ig,"").length
                        //count = count.replace(d, "");

                        if (count >= 250) {
                            jQuery('.post-content-error').html('');

                        } else {
                            jQuery('.post-content-error').html('<span class="message"><i class="fa fa-exclamation-triangle"></i> Description should be at least 250 symbols</span>');
                            jQuery("iframe#about_content_ifr").contents().bind("keyup change", function(e) {


                                if (jQuery("iframe#about_content_ifr").contents().find('body').text().replace(/(<([^>]+)>)/ig, "").length >= 250) {
                                    jQuery('.post-content-error').html('');
                                } else {
                                    jQuery('.post-content-error').html('<span class="message"><i class="fa fa-exclamation-triangle"></i> Description should be at least 250 symbols</span>');
                                }
                            })
                            return false;
                        }

                        view.LoadingButtonNew.loading(button);

                        form.addClass('processing');
                    },
                    success: function (profile, status, jqXHR) {
                        start_refresh_count();
                        view.LoadingButtonNew.finish(button);
                        form.removeClass('processing');
                        // trigger event process authentication
                        AE.pubsub.trigger('ae:user:profile', profile, status, jqXHR);
                        // trigger event notification
                        if (status.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'success',
                            });
                            //window.location.reload();
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'error',
                            });
                        }
                    }
                });
            }
        },
        openModalPorfolio: function (event) {
            event.preventDefault();
            var portfolio = new Models.Portfolio();
            if (typeof this.modalPortfolio === 'undefined') {
                this.modalPortfolio = new Views.Modal_Add_Portfolio({
                    el: '#modal_add_portfolio',
                    collection: this.portfolios_collection,
                    // model: portfolio
                });
            }
            this.modalPortfolio.setModel(portfolio);
            this.modalPortfolio.openModal();
        },
        openModalCv: function (event) {
            event.preventDefault();
            console.log('upload CV');
            this.modalCv = new Views.Modal_Change_Pass({
                el: '#cv_modal'
            });
            this.modalCv.openModal();
        },
        openModalChangePW: function (event) {
            event.preventDefault();
            console.log('change pass');
            this.modalChangePW = new Views.Modal_Change_Pass({
                el: '#modal_change_pass'
            });
            this.modalChangePW.openModal();
        },

        /**
         * Authorize credit card
         *
         */

        authorizeCreditCard: function (event) {

            event.preventDefault();

            event.stopPropagation();

            /**

             * call validator init

             */

            this.initValidator();

            var form = $(event.currentTarget),

                button = form.find('.btn-submit'),

                view = this,

                temp = new Array();

            if (this.credit_card_validator.form() && !form.hasClass("processing")) {

                var action = 'authorizecard';
                var strCardType = $('#card_type').val();
                var strCardNumber = $('#card_number').val();
                var strFirstName = $('#first_name').val();
                var strLastName = $('#last_name').val();
                var strCardMonth = $('#exp_month').val();
                var strCardYear = $('#exp_year').val();
                var strCardCVV = $('#card_cvv').val();
                var strStreet = $('#street').val();
                var strCity = $('#city').val();
                var strState = $('#state').val();
                var strCountry = $('#user_country').val();
                var strZipCode = $('#zip_code').val();
                var strDefaultPayment = false;

                if ($('#default_payment').prop("checked") == true) {
                    strDefaultPayment = true;
                }


                jQuery.ajax({

                    type: 'POST',
                    beforeSend: function () {
                        view.blockUi.block(button);

                        form.addClass('processing');
                    },
                    complete: function () {
                        view.blockUi.unblock();
                        form.removeClass('processing');
                    },
                    url: myAjax.ajaxurl,
                    data: {
                        action: action,
                        strCardType: strCardType,
                        strCardNumber: strCardNumber,
                        strFirstName: strFirstName,
                        strLastName: strLastName,
                        strCardMonth: strCardMonth,
                        strCardYear: strCardYear,
                        strCardCVV: strCardCVV,
                        strStreet: strStreet,
                        strCity: strCity,
                        strState: strState,
                        strCountry: strCountry,
                        strZipCode: strZipCode,
                        strDefaultPayment: strDefaultPayment
                    },
                    dataType: "json",
                    success: function (response) {
                        // trigger event notification
                        if (response.status == 'success') {
                            AE.pubsub.trigger('ae:notification', {
                                msg: response.msg,
                                notice_type: 'success',
                            });
                            form.trigger('reset');
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: response.msg,
                                notice_type: 'error',
                            });
                        }
                    }
                });

            }
        },

        saveBankDetails: function (event) {
            event.preventDefault();

            event.stopPropagation();

            /**

             * call validator init

             */

            this.initValidator();

            var form = $(event.currentTarget),

                button = form.find('.btn-submit'),

                view = this,

                temp = new Array();

            if (this.bank_details_validator.form() && !form.hasClass("processing")) {

                var action = 'savebankdetails';
                var strAccType = $('#account_type').val();
                var strBankName = $('#bank_name').val();
                var strBankCountry = $('#bank_country').val();
                var strRoutingNo = $('#routing_no').val();
                var strBankAdd = $('#bank_address').val();
                var strBankCity = $('#bank_city').val();
                var strBankState = $('#bank_state').val();
                var strBankZip = $('#bank_zipcode').val();
                var strAccHolderName = $('#account_holder_name').val();
                var strAccHolderCurr = $('#account_holder_currency').val();
                var strAccNo = $('#account_number').val();
                var strAccHolderAdd = $('#account_holder_address').val();
                var strAccHolderCity = $('#account_holder_city').val();
                var strAccHolderCountry = $('#account_holder_country').val();
                var strAccHolderState = $('#account_holder_state').val();
                var strAccHolderZipCode = $('#account_holder_zipcode').val();

                jQuery.ajax({

                    type: 'POST',
                    beforeSend: function () {
                        view.blockUi.block(button);

                        form.addClass('processing');
                    },
                    complete: function () {
                        view.blockUi.unblock();
                        form.removeClass('processing');
                    },
                    url: myAjax.ajaxurl,
                    data: {
                        action: action,
                        strAccType: strAccType,
                        strBankName: strBankName,
                        strBankCountry: strBankCountry,
                        strRoutingNo: strRoutingNo,
                        strBankAdd: strBankAdd,
                        strBankCity: strBankCity,
                        strBankState: strBankState,
                        strBankZip: strBankZip,
                        strAccHolderName: strAccHolderName,
                        strAccHolderCurr: strAccHolderCurr,
                        strAccNo: strAccNo,
                        strAccHolderAdd: strAccHolderAdd,
                        strAccHolderCity: strAccHolderCity,
                        strAccHolderCountry: strAccHolderCountry,
                        strAccHolderState: strAccHolderState,
                        strAccHolderZipCode: strAccHolderZipCode
                    },
                    dataType: "json",
                    success: function (response) {
                        // trigger event notification
                        if (response.status == 'success') {
                            AE.pubsub.trigger('ae:notification', {
                                msg: response.msg,
                                notice_type: 'success',
                            });
                            //form.trigger('reset');
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: response.msg,
                                notice_type: 'error',
                            });
                        }
                    }
                });

            }
        }

    });
    /*
     *
     * M O D A L  A D D  P O R T F O L I O  V I E W S
     *
     */
    Views.Modal_Add_Portfolio = Views.Modal_Box.extend({
        events: {
            // user register
            'submit form.create_portfolio': 'createPortfolio',
        },
        /**
         * init view setup Block Ui and Model User
         */
        initialize: function () {
            this.user = AE.App.user;
            this.blockUi = new Views.BlockUi();
            this.LoadingButtonNew = new Views.LoadingButtonNew();
            jQuery(".modal-skills").chosen({width: "95%"})
            this.initValidator();
            // upload file portfolio image
            this.uploaderID = 'portfolio_img';
            var $container = $("#portfolio_img_container"),
                view = this;
            //init chosen
            this.$(".modal-skills").chosen({width: "95%"})

            if (typeof this.portfolio_uploader === "undefined") {
                this.portfolio_uploader = new AE.Views.File_Uploader({
                    el: $container,
                    uploaderID: this.uploaderID,
                    drop_element: 'portfolio_img_container',
                    thumbsize: 'portfolio',
                    multipart_params: {
                        _ajax_nonce: $container.find('.et_ajaxnonce').attr('id'),
                        data: {
                            method: 'add_portfolio',
                            author: view.user.get('ID')
                        },
                        imgType: this.uploaderID,
                    },
                    cbUploaded: function (up, file, res) {
                        if (res.success) {
                            $('#' + this.container).find("input#post_thumbnail").val(res.data.attach_id);
                            $('#' + this.container).parents('.desc').find('.error').remove();
                        } else {
                            $('#' + this.container).parents('.desc').append('<div class="error">' + res.msg + '</div>');
                        }
                    },
                    beforeSend: function (ele) {
                        button = $(ele).find('.image');
                        view.blockUi.block(button);
                    },
                    success: function (res) {
                        if (res.success === false) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error',
                            });
                        }
                        view.blockUi.unblock();
                    }
                });
            }
        },
        setModel: function (model) {
            this.portfolio = model; //new Models.Portfolio();
            this.setupFields();
        },
        setupFields: function () {
            var view = this;
            view.$(".modal-skills").find('option').each(function () {
                jQuery(this).removeAttr('selected');
            });
            view.$("iframe#post_content_ifr").contents().find('body').html('');
            view.$(".modal-skills").trigger('chosen:updated');
            this.$('.form-group').find('input').each(function () {
                $(this).val(view.portfolio.get($(this).attr('name')));
            });
            view.$("#portfolio_img_thumbnail").html('');
        },
        resetUploader: function () {
            if (typeof this.portfolio_uploader === 'undefined') return;
            this.portfolio_uploader.controller.splice();
            this.portfolio_uploader.controller.refresh();
            this.portfolio_uploader.controller.destroy();
        },
        /**
         * init form validator rules
         * can override this function by using prototype
         */
        initValidator: function () {
            /**
             * register rule
             */
            this.portfolio_validator = $("form.create_portfolio").validate({
                rules: {
                    post_title: "required",
                    post_thumbnail: "required",
                }
            });
        },
        /**
         * user sign-up catch event when user submit form signup
         */
        createPortfolio: function (event) {
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
            form.find('input, textarea, select').each(function () {
                view.portfolio.set($(this).attr('name'), $(this).val());
            });
            // check if user has selected an image!
            if ($("#post_thumbnail").val() == "0") {
                AE.pubsub.trigger('ae:notification', {
                    msg: fre_fronts.portfolio_img,
                    notice_type: 'error'
                });
                return false;
            }
            // check form validate and process sign-up
            if (this.portfolio_validator.form() && !form.hasClass("processing")) {
                this.portfolio.save('', '', {
                    beforeSend: function () {
                        view.LoadingButtonNew.loading(button);
                        form.addClass('processing');
                    },
                    success: function (portfolio, status, jqXHR) {
                        view.LoadingButtonNew.finish(button);
                        form.removeClass('processing');
                        // trigger event process authentication
                        AE.pubsub.trigger('ae:portfolio:create', portfolio, status, jqXHR);
                        // add to collection
                        view.collection.add(portfolio, {
                            at: 0
                        });
                        if (status.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'success'
                            });
                            // close modal
                            view.closeModal();
                            // reset form
                            // form.reset();
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


    Views.Modal_Edit_Portfolio = Views.Modal_Box.extend({
        events: {
            // user register
            'submit form.edit_portfolio': 'editPortfolio',
        },
        /**
         * init view setup Block Ui and Model User
         */
        initialize: function () {
            this.user = AE.App.user;
            this.blockUi = new Views.BlockUi();
            this.LoadingButtonNew = new Views.LoadingButtonNew();
            this.initValidator();
            // upload file portfolio image
            this.uploaderID = 'portfolio_img_edit';
            this.$(".modal-skills").chosen({width: "95%"})
            var $container = $("#portfolio_img_edit_container"),
                view = this;
            //init chosen
            this.$('#skills').chosen({
                width: '330px'
            });
           // console.log(this);
            if (typeof Views.Profile.portfolio_uploader_edit === "undefined") {
                Views.Profile.portfolio_uploader_edit = new AE.Views.File_Uploader({
                    el: $container,
                    uploaderID: this.uploaderID,
                    drop_element: 'portfolio_img_edit_container',
                    thumbsize: 'portfolio',
                    multipart_params: {
                        _ajax_nonce: $container.find('.et_ajaxnonce').attr('id'),
                        data: {
                            method: 'add_portfolio',
                            author: view.user.get('ID')
                        },
                        imgType: this.uploaderID,
                    },
                    cbUploaded: function (up, file, res) {
                        if (res.success) {
                            $('#' + this.container).find("input#post_thumbnail").val(res.data.attach_id);
                            $('#' + this.container).parents('.desc').find('.error').remove();
                        } else {
                            $('#' + this.container).parents('.desc').append('<div class="error">' + res.msg + '</div>');
                        }
                    },
                    beforeSend: function (ele) {
                        button = $(ele).find('.image');
                        view.blockUi.block(button);
                    },
                    success: function (res) {
                        if (res.success === false) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error',
                            });
                        }
                        view.blockUi.unblock();
                    }
                });
            }
        },
        setModel: function (model) {
            this.portfolio = model; //new Models.Portfolio();
            this.setupFields();
        },
        setupFields: function () {
            var view = this;
            view.$(".modal-skills").find('option').each(function () {
                jQuery(this).removeAttr('selected');
            })
            this.$('.form-group').find('input').each(function () {
                $(this).val(view.portfolio.get($(this).attr('name')));
            });
            console.log(view.collection.model.attributes);
            view.$('#ID').val(view.collection.model.attributes.ID);
            var content = view.collection.model.attributes.post_content;
            view.$('#post_content').val(jQuery('<div>' + view.collection.model.attributes.post_content + '</div>').text());
            view.$('#portfolio_img_edit_thumb').attr('src', view.collection.model.attributes.the_post_thumbnail);
            view.$('#post_title').val(view.collection.model.attributes.post_title);
            view.$('#post_thumbnail').val(0);
            view.$("#portfolio_img_thumbnail").html('');
            jQuery.each(view.collection.model.attributes.skill, function (index, value) {
                view.$(".modal-skills").find("option[value='" + value + "']").attr('selected', true);
            });
            view.$(".modal-skills").trigger('chosen:updated');

        },
        resetUploader: function () {
            if (typeof Views.Profile.portfolio_uploader_edit === 'undefined') return;
            Views.Profile.portfolio_uploader_edit.controller.splice();
            Views.Profile.portfolio_uploader_edit.controller.refresh();
            Views.Profile.portfolio_uploader_edit.controller.destroy();
        },
        /**
         * init form validator rules
         * can override this function by using prototype
         */
        initValidator: function () {
            /**
             * register rule
             */
            this.portfolio_validator = $("form.edit_portfolio").validate({
                rules: {
                    post_title: "required",
                    post_thumbnail: "required",
                }
            });
        },
        /**
         * user sign-up catch event when user submit form signup
         */
        editPortfolio: function (event) {
            event.preventDefault();
            event.stopPropagation();
            /**
             * call validator init
             */
            this.$('.modal-skills').chosen({
                max_selected_options: 10,
                inherit_select_classes: true,
                width: '95%',
            })
            this.initValidator();
            var form = $(event.currentTarget),
                button = form.find('button.btn-submit'),
                view = this;
            /**
             * scan all fields in form and set the value to model user
             */
            form.find('input, textarea, select').each(function () {
                if ($(this).attr('name')) {
                    view.portfolio.set($(this).attr('name'), $(this).val());
                    console.log($(this).attr('name'), $(this).val());
                }
            });

            if (this.portfolio_validator.form() && !form.hasClass("processing")) {
                this.portfolio.save('', '', {
                    beforeSend: function () {
                        view.LoadingButtonNew.loading(button);
                        form.addClass('processing');
                    },
                    success: function (portfolio, status, jqXHR) {
                        view.LoadingButtonNew.finish(button);
                        form.removeClass('processing');

                        if (status.success) {
                            var model = view.collection.model.collection.get(portfolio.id);
                            if (model) {
                                for (var f in portfolio.attributes) {
                                    model.set(f, portfolio.attributes[f]);
                                }
                            }
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'success'
                            });
                            // close modal
                            view.closeModal();
                            // reset form
                            // form.reset();
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

// Views.Profile.prototype.initValidator = function() {
//     // login rule
//     this.account_validator = $("form#account_form").validate({
//         rules: {
//             display_name: "required",
//             user_email: {
//                 required: true,
//                 email: true
//             }
//         }
//     });
//     /**
//      * register rule
//      */
//     this.profile_validator = $("form#profile_form").validate({
//         rules: {
//             et_professional_title: "required",
//             country: "required",
//             // hour_rate: {
//             //     required: true,
//             //     number: true
//             // }, 
//             et_experience : {
//                 number : true
//             }
//         }
//     });
// }
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);

jQuery(document).ready(function () {
    if (window.location.pathname == '/profile/') {
        //setInterval("start()", 2500);
        setTimeout("start_refresh_count()", 3500);
        //start_refresh_count();
    }
});
function start_refresh_count() {
    if (jQuery('section').hasClass('freelancer')) {
        refreshcountcompleteFreelancer();
    }
    else {
        refreshcountcompleteEmployer();
    }
}
function focus_field(id, tab, container) {
    console.log(container);
    if (container === undefined) {
        current_field = jQuery("[id='" + id + "']").focus();
        if (jQuery("[href='#" + tab + "']").parent().hasClass('active')) {
            jQuery("html, body").delay(250).animate({scrollTop: current_field.offset().top / 2 - 100 }, 500);

            current_field.focus();
        } else {
            jQuery("[href='#" + tab + "']").click();
            setTimeout(function () {
                jQuery("html, body").delay(250).animate({scrollTop: current_field.offset().top / 2 - 100 }, 500);
                current_field.focus();
            }, 750);
        }
    }
    else {
        if (container == 'about') {
            if (jQuery("[href='#" + tab + "']").parent().hasClass('active')) {
                jQuery("html, body").delay(250).animate({scrollTop: jQuery('iframe#about_content_ifr').offset().top - jQuery('iframe#about_content_ifr').height() }, 500);
                jQuery('iframe#about_content_ifr').contents().find('body').focus();
                console.log('fsadfasdf2');

            } else {
                jQuery("[href='#" + tab + "']").click();
                setTimeout(function () {
                    jQuery("html, body").animate({scrollTop: jQuery('iframe#about_content_ifr').offset().top - jQuery('iframe#about_content_ifr').height() }, 500);
                    jQuery('iframe#about_content_ifr').contents().find('body').focus();
                }, 250);

            }

        }
    }
}
function refreshcountcompleteFreelancer() {
    var percent = 0;
    var curPerc = jQuery('div.profile-completion-status span').text().replace('%', '');
    var htmlIncludeDescription = '';
    tab_account = "'tab_account_details'";
    tab_profile = "'tab_profile_details'";

    if (jQuery('#display_name').val().trim() !== '') {
        percent += 10;
    }
    else {
        temp = "'" + jQuery('#display_name').attr('id') + "'";
        htmlIncludeDescription += '<p class="focus-field" onclick="focus_field(' + temp + ',' + tab_account + ')">Fill your full name (+10%)</p>';
    }
    if (jQuery('#location').val().trim() !== '') {
        percent += 10;
    }
    else {
        temp = "'" + jQuery('#location').attr('id') + "'";
        htmlIncludeDescription += '<p class="focus-field" onclick="focus_field(' + temp + ',' + tab_account + ')">Fill in the "Location" (+10%)</p>';
    }
    if (jQuery('#user_email').val().trim() !== '') {
        percent += 10;
    }
    else {
        temp = "'" + jQuery('#user_email').attr('id') + "'";
        htmlIncludeDescription += '<p class="focus-field" onclick="focus_field(' + temp + ',' + tab_account + ')">Fill in the "E-Mail" (+10%)</p>';
    }
    if (jQuery('#paypal').val().trim() !== '') {
        //percent += 10;
        percent += 20;
    }
    else {
        temp = "'" + jQuery('#paypal').attr('id') + "'";
        htmlIncludeDescription += '<p class="focus-field" onclick="focus_field(' + temp + ',' + tab_account + ')">Fill in the "Paypal Account" (+20%)</p>';
    }

    if (jQuery('#user_mobile').val().trim() !== '') {
        //percent += 10;
    }
    else {
        temp = "'" + jQuery('#user_mobile').attr('id') + "'";
        //htmlIncludeDescription += '<p class="focus-field"  onclick="focus_field(' + temp + ',' + tab_account + ')">Fill in the "Phone no" (+10%)</p>';
    }

    if (jQuery('[name = "et_professional_title"]').val().trim() !== '') {
        percent += 10;
    }
    else {
        temp = "'" + jQuery('[name = "et_professional_title"]').attr('id') + "'";
        htmlIncludeDescription += '<p class="focus-field" onclick="focus_field(' + temp + ',' + tab_profile + ')">Fill in the "Professional Title" (+10%)</p>';
    }

    if (jQuery('[name = "hour_rate"]').val().trim() !== '') {
        percent += 10;
    }
    else {
        temp = "'" + jQuery('[name = "hour_rate"]').attr('id') + "'";
        htmlIncludeDescription += '<p class="focus-field" onclick="focus_field(' + temp + ',' + tab_profile + ')">Fill in the "Hourly Rate" (+10%)</p>';
    }

    if (jQuery('ul.chosen-choices li').length > 1) {
        percent += 10;
    }
    else {
        temp = "'" + jQuery('[id="skill"]').attr('id') + "'";
        htmlIncludeDescription += '<p class="focus-field" onclick="focus_field(' + temp + ',' + tab_profile + ')">Fill in the "Skills" (+10%)</p>';
    }

    if (jQuery('div#country_chosen span').text() !== '') {
        percent += 10;
    }
    else {
        temp = "'" + jQuery('[id="country_chosen"]').attr('id') + "'";
        htmlIncludeDescription += '<p class="focus-field" onclick="focus_field(' + temp + ',' + tab_profile + ')">Fill in the "Country" (+10%)</p>';
    }

    if (jQuery("iframe#about_content_ifr").contents().find('body').text().trim() !== '' && jQuery("iframe#about_content_ifr").contents().find('body').text().trim().length > 250) {
        percent += 10;
    }
    else {
        temp = "'" + jQuery('iframe#about_content_ifr').attr('id') + "'";
        temp2 = "'about'"
        htmlIncludeDescription += '<p class="focus-field" onclick="focus_field(' + temp + ',' + tab_profile + ',' + temp2 + ')">Fill in the "About" (+10%)</p>';
    }
    if (htmlIncludeDescription != '') {
        htmlIncludeDescription = '<br>' + htmlIncludeDescription;
    }
    jQuery('#description-profile-completion-status').html(htmlIncludeDescription);
    if (curPerc != percent) {
        AnimRes(curPerc, percent);
    }
}
function refreshcountcompleteEmployer() {
    var percent = 0;
    var curPerc = jQuery('div.profile-completion-status span').text().replace('%', '');
    var htmlIncludeDescription = '';
    tab_account = "'tab_account_details'";
    tab_profile = "'tab_profile_details'";
    if (jQuery('#display_name').val().trim() !== '') {
        percent += 20;
    }
    else {
        temp = "'" + jQuery('#display_name').attr('id') + "'";

        htmlIncludeDescription += '<p class="focus-field" onclick="focus_field(' + temp + ',' + tab_account + ')">Fill your full name (+20%)</p>';
    }

    if (jQuery('#location').val().trim() !== '') {
        percent += 20;
    }
    else {
        temp = "'" + jQuery('#location').attr('id') + "'";
        htmlIncludeDescription += '<p class="focus-field" onclick="focus_field(' + temp + ',' + tab_account + ')">Fill in the "Location" (+20%)</p>';
    }

    if (jQuery('#user_email').val().trim() !== '') {
        percent += 20;
    }
    else {
        temp = "'" + jQuery('#user_email').attr('id') + "'";
        htmlIncludeDescription += '<p class="focus-field" onclick="focus_field(' + temp + ',' + tab_account + ')">Fill in the "E-Mail" (+20%)</p>';
    }

    if (jQuery('#paypal').val().trim() !== '') {
        //percent += 20;
        percent += 40;
    }
    else {
        temp = "'" + jQuery('#paypal').attr('id') + "'";
        htmlIncludeDescription += '<p class="focus-field" onclick="focus_field(' + temp + ',' + tab_account + ')">Fill in the "Paypal Account" (+40%)</p>';
    }

    if (jQuery('#user_mobile').val().trim() !== '') {
        //percent += 20;
    }
    else {
        //temp = "'" + jQuery('#user_mobile').attr('id') + "'";
        //htmlIncludeDescription += '<p class="focus-field" onclick="focus_field(' + temp + ',' + tab_account + ')">Fill in the "Phone no" (+20%)</p>';
    }
    if (htmlIncludeDescription != '') {
        htmlIncludeDescription = '<br>' + htmlIncludeDescription;
    }
    jQuery('#description-profile-completion-status').html(htmlIncludeDescription);

    if (curPerc != percent) {
        AnimRes(curPerc, percent);
    }
}
function AnimRes(currentPer, percent) {
    jQuery({someValue: currentPer}).stop().animate({someValue: percent}, {
        duration: 2000,
        easing: 'linear', // can be anything
        step: function () { // called on every step
            // Update the element's text with rounded-up value:
            jQuery('div.profile-completion-status span').text(Math.round(this.someValue) + "%");
        }
    });
    jQuery("div.profile-completion-status span").stop().animate({
        width: percent + "%"
    }, 2000);
}

jQuery(document).ready(function () {
    jQuery("iframe#about_content_ifr").contents().bind("keyup change", function(e) {


        if (jQuery("iframe#about_content_ifr").contents().find('body').text().replace(/(<([^>]+)>)/ig, "").length >= 250) {
            jQuery('.post-content-error').html('');
        } else {
            jQuery('.post-content-error').html('<span class="message"><i class="fa fa-exclamation-triangle"></i> Description should be at least 250 symbols</span>');
        }
    })

})

jQuery('#activate_without_interview').on('click',function(){
    var button = jQuery(this);
    button.attr('disabled','disabled');

    jQuery.ajax({
        type: "post",
        dataType: "json",
        url: ajaxurl,
        data: {
            action: 'activate_without_interview'
        },
        beforeSend: function () {

        },
        success: function (status) {
            if(status.status){
                button.fadeOut('slow');
                AE.pubsub.trigger('ae:notification', {
                    msg: status.msg,
                    notice_type: 'success'
                });
            } else {
                button.removeAttr('disabled');
                AE.pubsub.trigger('ae:notification', {
                    msg: status.msg,
                    notice_type: 'success'
                });
            }
        }
    });
});

/*cv upload js*/
jQuery('#create_cv').on('submit', function (e) {
    e.preventDefault();
    var formdata = new FormData(jQuery(this)[0]);
    var fileUploaded = jQuery('#cv_img_browse_button').val();
    if (fileUploaded == '') {
        alert('Error: Please select a file to upload first!');
        return false;
    }

    jQuery.ajax({
        url: ajaxurl,
        data: formdata,
        type: 'post',
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            jQuery('.loading-img').css('display', 'block');
        },
        success: function (result) {
            console.log(result);
            var src = result;
            jQuery('.loading-img').css('display', 'none');
            jQuery('#cv_modal').modal('hide');
            jQuery('.preview_cv_link').css('display', 'block');
            jQuery('.preview_cv_link').attr('href', src);
            jQuery('#modal_add_portfolio').css('display', 'none');
            jQuery('.add-cv').css('display', 'none');
            jQuery('#del_cv').css('display', 'block');
        }
    });
});
/* delete cv*/

jQuery('.add-porfolio-button').on('click', '#del_cv', function (e) {

    e.preventDefault();
    jQuery.ajax({
        url: ajaxurl,
        data: 'action=del_cv',
        type: 'post',
        cache: false,
        beforeSend: function () {
            jQuery('.error_cv').fadeIn('fast').html('<img src="http://www.perssistant.com/wp-content/themes/freelanceengine/includes/aecore/assets/img//loading.gif />');
        },
        success: function (result) {
            console.log(result);
            if (result == 'CV Updated Successfully') {
                jQuery('.error_cv').html(result).fadeIn();
                jQuery('.cv_preview').css('display', 'none');
                jQuery('#del_cv').css('display', 'none');
                jQuery('.preview_cv_link').css('display', 'none');
                jQuery('.add-cv_f').css('display', 'block');
            } else {
                jQuery('.error_cv').html(result).fadeIn('slow');
            }

            setTimeout(function () {
                jQuery('.error_cv').fadeOut().html('');
            }, 5000);
        }
    });
});
