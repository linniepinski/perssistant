/**
 * backend user, control user list in user manage list
 * search user by name
 * filter user by role
 * filter user by another data
 * use Collections.Users , View.UserItem, Models.User
 * use Views.BlockUi add view block loading
 */
(function(Models, Views, Collections, $, Backbone) {
	Views.UserList = Backbone.View.extend({
		events: {
			'click .load-more': 'loadMore',
			'change input.user-search': 'search',
			'change select.et-input': 'search',
			'submit .et-member-search form': 'submit'
		},

		initialize: function() {
			_.bindAll(this, 'addAll', 'addOne');

			var view = this;
			/**
			 * init collection data
			 */
			if ($('#ae_users_list').length > 0) {
				var users = JSON.parse($('#ae_users_list').html());
				this.Users = new Collections.Users(users.users);
				this.pages = users.pages;
				this.query = users.query;
			} else {
				this.Users = new Collections.Users();
				this.query = {};
			}
			this.paged = 2;

			this.user_view = [];
			/**
			 * init UserItem view
			 */
			this.Users.each(function(user, index, col) {
				var el = $('li.et-member').eq(index);
				view.user_view.push(new Views.UserItem({
					el: el,
					model: user
				}));
			});

			// bind event to collection users
			this.listenTo(this.Users, 'add', this.addOne);
			this.listenTo(this.Users, 'reset', this.addAll);
			this.listenTo(this.Users, 'all', this.render);

			this.blockUi = new Views.BlockUi();

		},
		/**
		 * add one
		 */
		addOne: function(user) {
			console.log('add one');
			var userItem = new Views.UserItem({
				model: user
			});
			this.user_view.push(userItem);

			this.$('ul.users-list').append(userItem.render().el);
		},

		/**
		 * add all
		 */
		addAll: function() {

			for (var i = 0; i < this.user_view.length - 1; i++) {
				// this.user_view[i].$el.remove();
				this.user_view[i].remove();
			}

			this.$('ul').html('');
			this.user_view = [];
			this.Users.each(this.addOne, this);
		},
		/**
		 * build ajax params for ajax
		 */
		buildParams: function(reset) {

			var view = this,
				keywork = this.$('input.user-search').val(),
				loadmore = view.$('.load-more'),
				role = this.$('select.et-input').val(),
				// get ajax params from AE globals
				ajaxParams = AE.ajaxParams;

			if (!reset) {
				$target = this.$('.load-more');
			} else {
				$target = this.$('ul');
			}

			ajaxParams.success = function(result, status, jqXHR) {
				var data = result.data;
				view.blockUi.unblock();
				if (result.pages < result.paged) {
					loadmore.hide();
				} else {
					loadmore.show();
				}

				if (reset) view.Users.reset();
				view.Users.set(data);

				if (data.length == 0) view.$('ul').append('<li class="user-not-found">' + result.msg + '</li>');

			}

			ajaxParams.beforeSend = function() {
				view.paged++;
				view.blockUi.block($target);
			}
			/**
			 * filter param
			 */
			ajaxParams.data = {
				search: keywork,
				paged: view.paged
			};

			_.extend(ajaxParams.data, view.query);

			if (role != '') ajaxParams.data.role = role;

			ajaxParams.data.action = 'ae-fetch-users';

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
			this.paged = 1;
			var ajaxParams = this.buildParams(true);
			$.ajax(ajaxParams);
		},
		/* Prevent enter key */
		submit: function(event) {
			event.preventDefault();
		}		

	});

})(window.AE.Models, window.AE.Views, window.AE.Collections, jQuery, Backbone);

jQuery(document).ready(function () {
    jQuery('.confirm-user').on('click', function (event) {
        //alert(ajaxurl);
        var button = jQuery(this);
        var user_id = jQuery(this).attr('data-user-id');
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: ajaxurl,
            data: {
                user_id: user_id,
                action: 'confirm-interview'
            },
            beforeSend: function () {

            },
            success: function (status) {

                if(status.status){
                   alert('confirmed');
                    button.attr('disabled','disabled')
                } else {
                   alert('error');
                }


            }
        });
    });

});
