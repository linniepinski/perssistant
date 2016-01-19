window.fbAsyncInit = function() {
	// init the FB JS SDK
	FB.init({
		appId      : facebook_auth.appID, //'167287143479594',                        // App ID from the app dashboard
		//channelUrl : '//192.168.10.112/channel.html', // Channel file for x-domain comms
		status     : true,                                 // Check Facebook Login status
		xfbml      : true                                  // Look for social plugins on the page
	});

	// // Here we subscribe to the auth.authResponseChange JavaScript event. This event is fired
	// // for any authentication related change, such as login, logout or session refresh. This means that
	// // whenever someone who was previously logged out tries to log in again, the correct case below 
	// // will be handled. 
	// FB.Event.subscribe('auth.authResponseChange', function(response) {
	// 	// Here we specify what we do with the response anytime this event occurs. 
	// 	if (response.status === 'connected') {
	// 		// The response object is returned with a status field that lets the app know the current
	// 		// login status of the person. In this case, we're handling the situation where they 
	// 		// have logged in to the app.
	// 		testAPI();
	// 	} else if (response.status === 'not_authorized') {
	// 		// In this case, the person is logged into Facebook, but not into the app, so we call
	// 		// FB.login() to prompt them to do so. 
	// 		// In real-life usage, you wouldn't want to immediately prompt someone to login 
	// 		// like this, for two reasons:
	// 		// (1) JavaScript created popup windows are blocked by most browsers unless they 
	// 		// result from direct interaction from people using the app (such as a mouse click)
	// 		// (2) it is a bad experience to be continually prompted to login upon page load.
	// 		FB.login();
	// 	} else {
	// 		// In this case, the person is not logged into Facebook, so we call the login() 
	// 		// function to prompt them to do so. Note that at this stage there is no indication
	// 		// of whether they are logged into the app. If they aren't then they'll see the Login
	// 		// dialog right after they log in to Facebook. 
	// 		// The same caveats as above apply to the FB.login() call here.
	// 		FB.login();
	// 	}
	// });
};

// Load the SDK asynchronously
(function(d, s, id){
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/all.js";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

(function($){
	$('.facebook_auth_btn').click(function(event){
		event.preventDefault();
		if ( FB ){
			FB.login(function(response) {
				if (response.authResponse) {
					access_token = response.authResponse.accessToken; //get access token
					user_id = response.authResponse.userID; //get FB UID

					FB.api('/me', function(response) {
						user_email = response.email; //get user email
						// you can store this data into your database
						var params = {
							url 	: ae_globals.ajaxURL,
							type 	: 'post',
							data 	: {
								action: 'et_facebook_auth',
								content: response,
								fb_token: access_token
							},
							beforeSend: function(){
							},
							success: function(resp){
								if ( resp.success && typeof resp.data.redirect_url != 'undefined' ){
									window.location = resp.data.redirect_url;
								}
								else if ( resp.success && typeof resp.data.user != 'undefined' ){
									if(!is_mobile){
										// assign current user
										var model = new AE.Models.User(resp.data.user);
										AE.App.currentUser = model;
										// trigger events
										var view 	= AE.App.authModal;
										if(typeof view != 'undefined'){
											view.trigger('response:login', resp);
											AE.pubsub.trigger('ae:response:login', model);
											AE.pubsub.trigger('ae:notification', {
												msg: resp.msg,
												notice_type: 'success',
											});

											view.$el.on('hidden.bs.modal', function(){
												AE.pubsub.trigger('ae:auth:afterLogin', model);
												view.trigger('afterLogin', model);
												// if ( view.options.enableRefresh == true){
													window.location.reload(true);
												// } else {
												// }
											});	

											view.closeModal();
										}
										else{
											AE.pubsub.trigger('ae:notification', {
												msg: resp.msg,
												notice_type: 'success',
											});
											window.location.reload(true);
										}
									}
									else{
										window.location.reload(true);
									}
								} else if ( resp.msg ) {
                                    AE.pubsub.trigger('ae:notification', {
                                        msg: resp.msg,
                                        notice_type: 'error',
                                    });
                                    alert(resp.msg);
								}
							},
							complete: function(){
								//$('#facebook_auth_btn').loader('unload');
								//this.blockUi.unblock();
							}
						}
						jQuery.ajax(params);

					});

				} else {
					//user hit cancel button
					//console.log('User cancelled login or did not fully authorize.');
					alert('User cancelled login or did not fully authorize.');
				}
			}, {
				scope: 'email,user_about_me'
			});
		}
	});
})(jQuery);