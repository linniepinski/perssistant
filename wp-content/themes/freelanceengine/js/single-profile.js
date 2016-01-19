(function($, Views, Models, Collections) {
	/*
	*
	* S I N G L E  P R O F I L E  V I E W S
	*
	*/
	Views.Single_Profile = Backbone.View.extend({
		el: 'body',
		events:{
		    //event open modal contact
            'click a.contact-me'        : 'openModalContact',
            'click a.invite-open' : 'openModalInvite'
		},
		initialize: function(){
			this.user = AE.App.user;
		},
		openModalContact: function(event){
			event.preventDefault();
			var $target = $(event.currentTarget);
			if( typeof Views.ContactModal !== "undefined" ){
    				this.modalContact = new Views.ContactModal({
    					el: '#modal_contact',
    					model: this.user,
    	                user_id: $target.attr('data-user')
    				});
				this.modalContact.user_id = $target.attr('data-user');
				this.modalContact.openModal();
			}
		},
		openModalInvite: function(event){
			event.preventDefault();
			var $target = $(event.currentTarget);
			if( typeof Views.InviteModal !== "undefined" ){
                if(typeof this.modalInvite === "undefined"){
    				this.modalInvite = new Views.InviteModal({
    					el: '#modal_invite',
    	                user_id: $target.attr('data-user')
    				});
                }
				this.modalInvite.user_id = $target.attr('data-user');
				this.modalInvite.openModal();
			}
		}, 
        
	});

	/**
     * modal invite jion a project
     */
    Views.InviteModal = AE.Views.Modal_Box.extend({
        events: {
            'submit form#submit_invite': 'sendInvite',
        },
        initialize: function(options) {
            AE.Views.Modal_Box.prototype.initialize.call();
            this.blockUi = new AE.Views.BlockUi();
            this.options = _.extend(this, options);
        },
        sendInvite: function(event) {
            event.preventDefault();
            this.submit_validator = $("form#submit_invite").validate({
                rules: {
                    'project_invites[]': "required"
                }
            });
            var form = $(event.currentTarget),
                $button = form.find(".btn-submit"),
                data = form.serializeObject(),
                view = this;
	        $.ajax({
                url: ae_globals.ajaxURL,
                type: 'post',
                data: {data : data, user_id: view.user_id ,action: 'ae-send-invite', },
                beforeSend: function() {
                	  view.blockUi.block($button);
                      form.addClass('processing');
                },
                success: function(resp) {
                	form.removeClass('processing');
                    if (resp.success) {
                        AE.pubsub.trigger('ae:notification', {
                            msg: resp.msg,
                            notice_type: 'success',
                        });
                        view.closeModal();
                    } else {
                        AE.pubsub.trigger('ae:notification', {
                            msg: resp.msg,
                            notice_type: 'error',
                        });
                        view.closeModal();
                        form.trigger('reset');     
                    }
                    view.blockUi.unblock();
                }
	        });
        }
    });
})(jQuery, AE.Views, AE.Models, AE.Collections);