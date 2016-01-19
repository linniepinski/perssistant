(function($, Models, Collections, Views) {
    $(document).ready(function() {
        AE.Models.emproject = Backbone.Model.extend({
            action: 'ae-project-sync',
            initialize: function() {}
        });
        Collections.Projects = Backbone.Collection.extend({
            model: AE.Models.emproject,
            action: 'ae-fetch-projects',
            initialize: function(project_id) {
                this.paged = 1;
            },
        });
        EmProjectItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'project-item',
            template: _.template($('#employer-project-item').html()),
            onItemBeforeRender: function() {
                // before render view
            },
            onItemRendered: function() {
                // after render view
            }
        });
        ListProject = Views.ListPost.extend({
            tagName: 'li',
            itemView: EmProjectItem,
            itemClass: 'project-item'
        });
        AE.Views.AuthorProfile = Backbone.View.extend({
            // action: 'ae-project-sync',
            el: 'body.author',
            initialize: function(arg) {
                if ($('body').find('.projectdata').length > 0) {
                    var projectdata = JSON.parse($('body').find('.projectdata').html());
                    this.collection_projects = new Collections.Projects(projectdata);
                } else {
                    this.collection_projects = new Collections.Projects();
                }
                new ListProject({
                    //itemView: BidItem,
                    collection: this.collection_projects,
                    el: $('.list-history-project')
                });
                if (typeof Views.BlockControl !== "undefined") {
                    //project control
                    new Views.BlockControl({
                        collection: this.collection_projects,
                        el: this.$(".wrapper-history"),
                        query: {
                            paginate: 'page'
                        },
                    });
                }
            },
        });
        new AE.Views.AuthorProfile();
    });
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);
(function($, Views, Models, Collections) {
    /*
     *
     * S I N G L E  P R O F I L E  V I E W S
     *
     */
    Views.Single_Profile = Backbone.View.extend({
        el: 'body',
        events: {
            //event open modal contact
            'click a.contact-me': 'openModalContact',
            'click a.invite-open': 'openModalInvite',
        },
        initialize: function() {
            this.user = AE.App.user;
        },
        openModalContact: function(event) {
            event.preventDefault();
            var $target = $(event.currentTarget);
            if (typeof Views.ContactModal !== "undefined") {
                this.modalContact = new Views.ContactModal({
                    el: '#modal_contact',
                    model: this.user,
                    user_id: $target.attr('data-user')
                });
                this.modalContact.user_id = $target.attr('data-user');
                this.modalContact.openModal();
            }
        },
        openModalInvite: function(event) {
            event.preventDefault();
            var $target = $(event.currentTarget);
            if (typeof Views.InviteModal !== "undefined") {
                if (typeof this.modalInvite === "undefined") {
                    this.modalInvite = new Views.InviteModal({
                        el: '#modal_invite',
                        user_id: $target.attr('data-user')
                    });
                }
                this.modalInvite.user_id = $target.attr('data-user');
                this.modalInvite.openModal();
            }
        }
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
                data: {
                    data: data,
                    user_id: view.user_id,
                    action: 'ae-send-invite',
                },
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
