
(function($){
ET_Slider_LoadingButton = Backbone.View.extend({
	dotCount : 3,
	isLoading : false,
	initialize : function(){
		if ( this.$el.length <= 0 ) return false;
		var dom = this.$el[0];
		//if ( this.$el[0].tagName != 'BUTTON' && (this.$el[0].tagName != 'INPUT') ) return false;

		if ( this.$el[0].tagName == 'INPUT' ){
			this.title = this.$el.val();
		}else {
			this.title = this.$el.html();
		}

		this.isLoading = false;
	},
	loopFunc : function(view){
		var dots = '';
		for(i = 0; i < view.dotCount; i++)
			dots = dots + '.';
		view.dotCount = (view.dotCount + 1) % 3;
		view.setTitle(et_globals.loading + dots);
	},
	setTitle: function(title){
		if ( this.$el[0].tagName === 'INPUT' ){
			this.$el.val( title );
		}else {
			this.$el.html( title );
		}
	},
	loading : function(){
		//if ( this.$el[0].tagName != 'BUTTON' && this.$el[0].tagName != 'A' && (this.$el[0].tagName != 'INPUT') ) return false;
		this.setTitle(et_globals.loading);
		
		this.$el.addClass('disabled');
		var view		= this;

		view.isLoading	= true;
		view.dots		= '...';
		view.setTitle(et_globals.loading + view.dots);

		this.loop = setInterval(function(){
			if ( view.dots === '...' ) view.dots = '';
			else if ( view.dots === '..' ) view.dots = '...';
			else if ( view.dots === '.' ) view.dots = '..';
			else view.dots = '.';
			view.setTitle(et_globals.loading + view.dots);
		}, 500);
	},
	finish : function(){
		var dom		= this.$el[0];
		this.isLoading	= false;
		clearInterval(this.loop);
		this.setTitle(this.title);
		this.$el.removeClass('disabled');
	}
});
/* end loading button */
//loading effect 
ET_BlockLoad = Backbone.View.extend({
	defaults : {
		image : et_globals.imgURL + '/loading.gif',
		opacity : '0.5',
		background_position : 'center center',
		background_color : '#ffffff'
	},

	isLoading : false,

	initialize : function(options){
		//var defaults = _.clone(this.defaults);
		options = _.extend( _.clone(this.defaults), options );

		var loadingImg = options.image;
		this.overlay = $('<div class="loading-blur loading"><div class="loading-overlay"></div><div class="loading-img"></div></div>');
		this.overlay.find('.loading-img').css({
			'background-image' : 'url(' + options.image + ')',
			'background-position' : options.background_position
			});

		this.overlay.find('.loading-overlay').css({
			'opacity'			: options.opacity,
			'filter'			: 'alpha(opacity=' + options.opacity*100 + ')',
			'background-color'	: options.background_color
			});
		this.$el.html( this.overlay );

		this.isLoading = false;
	},

	render : function(){
		this.$el.html( this.overlay );
		return this;
	},

	block: function(element){
		var $ele = $(element);
		// if ( $ele.css('position') !== 'absolute' || $ele.css('position') !== 'relative'){
		// 	$ele.css('position', 'relative');
		// }
		this.overlay.css({
			'position' 	: 'absolute',
			'top' 		: $ele.offset().top,
			'left' 		: $ele.offset().left,
			'width' 	: $ele.outerWidth(),
			'height' 	: $ele.outerHeight()
		});

		this.isLoading = true;

		this.render().$el.show().appendTo( $('body') );
	},

	unblock: function(){
		this.$el.remove();
		this.isLoading = false;
	},

	finish : function(){
		this.$el.fadeOut(500, function(){ $(this).remove();});
		this.isLoading = false;
	}
});
})(jQuery);