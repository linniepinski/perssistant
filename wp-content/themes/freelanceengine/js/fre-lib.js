/*!
 * classie - class helper functions
 * from bonzo https://github.com/ded/bonzo
 * 
 * classie.has( elem, 'my-class' ) -> true/false
 * classie.add( elem, 'my-new-class' )
 * classie.remove( elem, 'my-unwanted-class' )
 * classie.toggle( elem, 'my-class' )
 */

/*jshint browser: true, strict: true, undef: true */
/*global define: false */

( function( window ) {

'use strict';

// class helper functions from bonzo https://github.com/ded/bonzo

function classReg( className ) {
	return new RegExp("(^|\\s+)" + className + "(\\s+|$)");
}

// classList support for class management
// altho to be fair, the api sucks because it won't accept multiple classes at once
var hasClass, addClass, removeClass;

if ( 'classList' in document.documentElement ) {
	hasClass = function( elem, c ) {
		return elem.classList.contains( c );
	};
	addClass = function( elem, c ) {
		elem.classList.add( c );
	};
	removeClass = function( elem, c ) {
		elem.classList.remove( c );
	};
}
else {
	hasClass = function( elem, c ) {
		return classReg( c ).test( elem.className );
	};
	addClass = function( elem, c ) {
		if ( !hasClass( elem, c ) ) {
			elem.className = elem.className + ' ' + c;
		}
	};
	removeClass = function( elem, c ) {
		elem.className = elem.className.replace( classReg( c ), ' ' );
	};
}

function toggleClass( elem, c ) {
	var fn = hasClass( elem, c ) ? removeClass : addClass;
	fn( elem, c );
}

var classie = {
	// full names
	hasClass: hasClass,
	addClass: addClass,
	removeClass: removeClass,
	toggleClass: toggleClass,
	// short names
	has: hasClass,
	add: addClass,
	remove: removeClass,
	toggle: toggleClass
};

// transport
if ( typeof define === 'function' && define.amd ) {
	// AMD
	define( classie );
} else {
	// browser global
	window.classie = classie;
}

})( window );



// menu-door
(function($) {
	if( $( 'div.overlay' ).length == 0 )
		return false;
	var overlay = document.querySelector( 'div.overlay' ),
		closeBttn = overlay.querySelector( '.overlay-close' );
		transEndEventNames = {
			'WebkitTransition': 'webkitTransitionEnd',
			'MozTransition': 'transitionend',
			'OTransition': 'oTransitionEnd',
			'msTransition': 'MSTransitionEnd',
			'transition': 'transitionend'
		},
		transEndEventName = transEndEventNames[ Modernizr.prefixed( 'transition' ) ],
		support = { transitions : Modernizr.csstransitions };

	function toggleOverlay() {
		if( classie.has( overlay, 'open' ) ) {
			classie.remove( overlay, 'open' );
			classie.add( overlay, 'fre-close' );
			var onEndTransitionFn = function( ev ) {
				if( support.transitions ) {
					if( ev.propertyName !== 'visibility' ) return;
					this.removeEventListener( transEndEventName, onEndTransitionFn );
				}
				classie.remove( overlay, 'fre-close' );
			};
			if( support.transitions ) {
				overlay.addEventListener( transEndEventName, onEndTransitionFn );
			}
			else {
				onEndTransitionFn();
			}
		}
		else if( !classie.has( overlay, 'fre-close' ) ) {
			classie.add( overlay, 'open' );
		}
		

	}
	
	// $('.trigger-overlay, .overlay-fre-close').on('click',function() {
	//  toggleOverlay();
	// });

	$(document).keyup(function(e) {       
		if (e.which == 27) { 
			if( classie.has( overlay, 'open' ) ) {
				classie.remove( overlay, 'open' );
				classie.add( overlay, 'fre-close' );
				var onEndTransitionFn = function( ev ) {
					if( support.transitions ) {
						if( ev.propertyName !== 'visibility' ) return;
						this.removeEventListener( transEndEventName, onEndTransitionFn );
					}
					classie.remove( overlay, 'fre-close' );
				};
				if( support.transitions ) {
					overlay.addEventListener( transEndEventName, onEndTransitionFn );
				}
				else {
					onEndTransitionFn();
				}
				
			}
			$('body').removeClass('fre-menu-overflow');
			
		}  // esc   (does not work)
	});
		
	var menuBttn = document.querySelector( '.trigger-menu' );
	
	if(menuBttn) {
		menuBttn.addEventListener( 'click', toggleOverlay );  
	} 
	var searchBttn = document.querySelector( '.trigger-search' );
	if(searchBttn) {
		searchBttn.addEventListener( 'click', toggleOverlay );  
	}
	var notifyBtn = document.querySelector('.trigger-notification'),
		notifyBtn2 = document.querySelector('.trigger-notification-2');
	if(notifyBtn) {
		//notifyBtn.addEventListener( 'click', toggleOverlay );
	}
	if(notifyBtn2) {
		//notifyBtn2.addEventListener( 'click', toggleOverlay );
	}

	closeBttn.addEventListener( 'click', toggleOverlay );
})(jQuery);

// end menu door

// switchery
(function(){function require(path,parent,orig){var resolved=require.resolve(path);if(null==resolved){orig=orig||path;parent=parent||"root";var err=new Error('Failed to require "'+orig+'" from "'+parent+'"');err.path=orig;err.parent=parent;err.require=true;throw err}var module=require.modules[resolved];if(!module._resolving&&!module.exports){var mod={};mod.exports={};mod.client=mod.component=true;module._resolving=true;module.call(this,mod.exports,require.relative(resolved),mod);delete module._resolving;module.exports=mod.exports}return module.exports}require.modules={};require.aliases={};require.resolve=function(path){if(path.charAt(0)==="/")path=path.slice(1);var paths=[path,path+".js",path+".json",path+"/index.js",path+"/index.json"];for(var i=0;i<paths.length;i++){var path=paths[i];if(require.modules.hasOwnProperty(path))return path;if(require.aliases.hasOwnProperty(path))return require.aliases[path]}};require.normalize=function(curr,path){var segs=[];if("."!=path.charAt(0))return path;curr=curr.split("/");path=path.split("/");for(var i=0;i<path.length;++i){if(".."==path[i]){curr.pop()}else if("."!=path[i]&&""!=path[i]){segs.push(path[i])}}return curr.concat(segs).join("/")};require.register=function(path,definition){require.modules[path]=definition};require.alias=function(from,to){if(!require.modules.hasOwnProperty(from)){throw new Error('Failed to alias "'+from+'", it does not exist')}require.aliases[to]=from};require.relative=function(parent){var p=require.normalize(parent,"..");function lastIndexOf(arr,obj){var i=arr.length;while(i--){if(arr[i]===obj)return i}return-1}function localRequire(path){var resolved=localRequire.resolve(path);return require(resolved,parent,path)}localRequire.resolve=function(path){var c=path.charAt(0);if("/"==c)return path.slice(1);if("."==c)return require.normalize(p,path);var segs=parent.split("/");var i=lastIndexOf(segs,"deps")+1;if(!i)i=0;path=segs.slice(0,i+1).join("/")+"/deps/"+path;return path};localRequire.exists=function(path){return require.modules.hasOwnProperty(localRequire.resolve(path))};return localRequire};require.register("abpetkov-transitionize/transitionize.js",function(exports,require,module){module.exports=Transitionize;function Transitionize(element,props){if(!(this instanceof Transitionize))return new Transitionize(element,props);this.element=element;this.props=props||{};this.init()}Transitionize.prototype.isSafari=function(){return/Safari/.test(navigator.userAgent)&&/Apple Computer/.test(navigator.vendor)};Transitionize.prototype.init=function(){var transitions=[];for(var key in this.props){transitions.push(key+" "+this.props[key])}this.element.style.transition=transitions.join(", ");if(this.isSafari())this.element.style.webkitTransition=transitions.join(", ")}});require.register("ftlabs-fastclick/lib/fastclick.js",function(exports,require,module){function FastClick(layer){"use strict";var oldOnClick,self=this;this.trackingClick=false;this.trackingClickStart=0;this.targetElement=null;this.touchStartX=0;this.touchStartY=0;this.lastTouchIdentifier=0;this.touchBoundary=10;this.layer=layer;if(!layer||!layer.nodeType){throw new TypeError("Layer must be a document node")}this.onClick=function(){return FastClick.prototype.onClick.apply(self,arguments)};this.onMouse=function(){return FastClick.prototype.onMouse.apply(self,arguments)};this.onTouchStart=function(){return FastClick.prototype.onTouchStart.apply(self,arguments)};this.onTouchMove=function(){return FastClick.prototype.onTouchMove.apply(self,arguments)};this.onTouchEnd=function(){return FastClick.prototype.onTouchEnd.apply(self,arguments)};this.onTouchCancel=function(){return FastClick.prototype.onTouchCancel.apply(self,arguments)};if(FastClick.notNeeded(layer)){return}if(this.deviceIsAndroid){layer.addEventListener("mouseover",this.onMouse,true);layer.addEventListener("mousedown",this.onMouse,true);layer.addEventListener("mouseup",this.onMouse,true)}layer.addEventListener("click",this.onClick,true);layer.addEventListener("touchstart",this.onTouchStart,false);layer.addEventListener("touchmove",this.onTouchMove,false);layer.addEventListener("touchend",this.onTouchEnd,false);layer.addEventListener("touchcancel",this.onTouchCancel,false);if(!Event.prototype.stopImmediatePropagation){layer.removeEventListener=function(type,callback,capture){var rmv=Node.prototype.removeEventListener;if(type==="click"){rmv.call(layer,type,callback.hijacked||callback,capture)}else{rmv.call(layer,type,callback,capture)}};layer.addEventListener=function(type,callback,capture){var adv=Node.prototype.addEventListener;if(type==="click"){adv.call(layer,type,callback.hijacked||(callback.hijacked=function(event){if(!event.propagationStopped){callback(event)}}),capture)}else{adv.call(layer,type,callback,capture)}}}if(typeof layer.onclick==="function"){oldOnClick=layer.onclick;layer.addEventListener("click",function(event){oldOnClick(event)},false);layer.onclick=null}}FastClick.prototype.deviceIsAndroid=navigator.userAgent.indexOf("Android")>0;FastClick.prototype.deviceIsIOS=/iP(ad|hone|od)/.test(navigator.userAgent);FastClick.prototype.deviceIsIOS4=FastClick.prototype.deviceIsIOS&&/OS 4_\d(_\d)?/.test(navigator.userAgent);FastClick.prototype.deviceIsIOSWithBadTarget=FastClick.prototype.deviceIsIOS&&/OS ([6-9]|\d{2})_\d/.test(navigator.userAgent);FastClick.prototype.needsClick=function(target){"use strict";switch(target.nodeName.toLowerCase()){case"button":case"select":case"textarea":if(target.disabled){return true}break;case"input":if(this.deviceIsIOS&&target.type==="file"||target.disabled){return true}break;case"label":case"video":return true}return/\bneedsclick\b/.test(target.className)};FastClick.prototype.needsFocus=function(target){"use strict";switch(target.nodeName.toLowerCase()){case"textarea":return true;case"select":return!this.deviceIsAndroid;case"input":switch(target.type){case"button":case"checkbox":case"file":case"image":case"radio":case"submit":return false}return!target.disabled&&!target.readOnly;default:return/\bneedsfocus\b/.test(target.className)}};FastClick.prototype.sendClick=function(targetElement,event){"use strict";var clickEvent,touch;if(document.activeElement&&document.activeElement!==targetElement){document.activeElement.blur()}touch=event.changedTouches[0];clickEvent=document.createEvent("MouseEvents");clickEvent.initMouseEvent(this.determineEventType(targetElement),true,true,window,1,touch.screenX,touch.screenY,touch.clientX,touch.clientY,false,false,false,false,0,null);clickEvent.forwardedTouchEvent=true;targetElement.dispatchEvent(clickEvent)};FastClick.prototype.determineEventType=function(targetElement){"use strict";if(this.deviceIsAndroid&&targetElement.tagName.toLowerCase()==="select"){return"mousedown"}return"click"};FastClick.prototype.focus=function(targetElement){"use strict";var length;if(this.deviceIsIOS&&targetElement.setSelectionRange&&targetElement.type.indexOf("date")!==0&&targetElement.type!=="time"){length=targetElement.value.length;targetElement.setSelectionRange(length,length)}else{targetElement.focus()}};FastClick.prototype.updateScrollParent=function(targetElement){"use strict";var scrollParent,parentElement;scrollParent=targetElement.fastClickScrollParent;if(!scrollParent||!scrollParent.contains(targetElement)){parentElement=targetElement;do{if(parentElement.scrollHeight>parentElement.offsetHeight){scrollParent=parentElement;targetElement.fastClickScrollParent=parentElement;break}parentElement=parentElement.parentElement}while(parentElement)}if(scrollParent){scrollParent.fastClickLastScrollTop=scrollParent.scrollTop}};FastClick.prototype.getTargetElementFromEventTarget=function(eventTarget){"use strict";if(eventTarget.nodeType===Node.TEXT_NODE){return eventTarget.parentNode}return eventTarget};FastClick.prototype.onTouchStart=function(event){"use strict";var targetElement,touch,selection;if(event.targetTouches.length>1){return true}targetElement=this.getTargetElementFromEventTarget(event.target);touch=event.targetTouches[0];if(this.deviceIsIOS){selection=window.getSelection();if(selection.rangeCount&&!selection.isCollapsed){return true}if(!this.deviceIsIOS4){if(touch.identifier===this.lastTouchIdentifier){event.preventDefault();return false}this.lastTouchIdentifier=touch.identifier;this.updateScrollParent(targetElement)}}this.trackingClick=true;this.trackingClickStart=event.timeStamp;this.targetElement=targetElement;this.touchStartX=touch.pageX;this.touchStartY=touch.pageY;if(event.timeStamp-this.lastClickTime<200){event.preventDefault()}return true};FastClick.prototype.touchHasMoved=function(event){"use strict";var touch=event.changedTouches[0],boundary=this.touchBoundary;if(Math.abs(touch.pageX-this.touchStartX)>boundary||Math.abs(touch.pageY-this.touchStartY)>boundary){return true}return false};FastClick.prototype.onTouchMove=function(event){"use strict";if(!this.trackingClick){return true}if(this.targetElement!==this.getTargetElementFromEventTarget(event.target)||this.touchHasMoved(event)){this.trackingClick=false;this.targetElement=null}return true};FastClick.prototype.findControl=function(labelElement){"use strict";if(labelElement.control!==undefined){return labelElement.control}if(labelElement.htmlFor){return document.getElementById(labelElement.htmlFor)}return labelElement.querySelector("button, input:not([type=hidden]), keygen, meter, output, progress, select, textarea")};FastClick.prototype.onTouchEnd=function(event){"use strict";var forElement,trackingClickStart,targetTagName,scrollParent,touch,targetElement=this.targetElement;if(!this.trackingClick){return true}if(event.timeStamp-this.lastClickTime<200){this.cancelNextClick=true;return true}this.cancelNextClick=false;this.lastClickTime=event.timeStamp;trackingClickStart=this.trackingClickStart;this.trackingClick=false;this.trackingClickStart=0;if(this.deviceIsIOSWithBadTarget){touch=event.changedTouches[0];targetElement=document.elementFromPoint(touch.pageX-window.pageXOffset,touch.pageY-window.pageYOffset)||targetElement;targetElement.fastClickScrollParent=this.targetElement.fastClickScrollParent}targetTagName=targetElement.tagName.toLowerCase();if(targetTagName==="label"){forElement=this.findControl(targetElement);if(forElement){this.focus(targetElement);if(this.deviceIsAndroid){return false}targetElement=forElement}}else if(this.needsFocus(targetElement)){if(event.timeStamp-trackingClickStart>100||this.deviceIsIOS&&window.top!==window&&targetTagName==="input"){this.targetElement=null;return false}this.focus(targetElement);if(!this.deviceIsIOS4||targetTagName!=="select"){this.targetElement=null;event.preventDefault()}return false}if(this.deviceIsIOS&&!this.deviceIsIOS4){scrollParent=targetElement.fastClickScrollParent;if(scrollParent&&scrollParent.fastClickLastScrollTop!==scrollParent.scrollTop){return true}}if(!this.needsClick(targetElement)){event.preventDefault();this.sendClick(targetElement,event)}return false};FastClick.prototype.onTouchCancel=function(){"use strict";this.trackingClick=false;this.targetElement=null};FastClick.prototype.onMouse=function(event){"use strict";if(!this.targetElement){return true}if(event.forwardedTouchEvent){return true}if(!event.cancelable){return true}if(!this.needsClick(this.targetElement)||this.cancelNextClick){if(event.stopImmediatePropagation){event.stopImmediatePropagation()}else{event.propagationStopped=true}event.stopPropagation();event.preventDefault();return false}return true};FastClick.prototype.onClick=function(event){"use strict";var permitted;if(this.trackingClick){this.targetElement=null;this.trackingClick=false;return true}if(event.target.type==="submit"&&event.detail===0){return true}permitted=this.onMouse(event);if(!permitted){this.targetElement=null}return permitted};FastClick.prototype.destroy=function(){"use strict";var layer=this.layer;if(this.deviceIsAndroid){layer.removeEventListener("mouseover",this.onMouse,true);layer.removeEventListener("mousedown",this.onMouse,true);layer.removeEventListener("mouseup",this.onMouse,true)}layer.removeEventListener("click",this.onClick,true);layer.removeEventListener("touchstart",this.onTouchStart,false);layer.removeEventListener("touchmove",this.onTouchMove,false);layer.removeEventListener("touchend",this.onTouchEnd,false);layer.removeEventListener("touchcancel",this.onTouchCancel,false)};FastClick.notNeeded=function(layer){"use strict";var metaViewport;var chromeVersion;if(typeof window.ontouchstart==="undefined"){return true}chromeVersion=+(/Chrome\/([0-9]+)/.exec(navigator.userAgent)||[,0])[1];if(chromeVersion){if(FastClick.prototype.deviceIsAndroid){metaViewport=document.querySelector("meta[name=viewport]");if(metaViewport){if(metaViewport.content.indexOf("user-scalable=no")!==-1){return true}if(chromeVersion>31&&window.innerWidth<=window.screen.width){return true}}}else{return true}}if(layer.style.msTouchAction==="none"){return true}return false};FastClick.attach=function(layer){"use strict";return new FastClick(layer)};if(typeof define!=="undefined"&&define.amd){define(function(){"use strict";return FastClick})}else if(typeof module!=="undefined"&&module.exports){module.exports=FastClick.attach;module.exports.FastClick=FastClick}else{window.FastClick=FastClick}});require.register("switchery/switchery.js",function(exports,require,module){var transitionize=require("transitionize"),fastclick=require("fastclick");module.exports=Switchery;var defaults={color:"#64bd63",secondaryColor:"#dfdfdf",className:"switchery",disabled:false,disabledOpacity:.5,speed:"0.4s"};function Switchery(element,options){if(!(this instanceof Switchery))return new Switchery(element,options);this.element=element;this.options=options||{};for(var i in defaults){if(this.options[i]==null){this.options[i]=defaults[i]}}if(this.element!=null&&this.element.type=="checkbox")this.init()}Switchery.prototype.hide=function(){this.element.style.display="none"};Switchery.prototype.show=function(){var switcher=this.create();this.insertAfter(this.element,switcher)};Switchery.prototype.create=function(){this.switcher=document.createElement("span");this.jack=document.createElement("small");this.switcher.appendChild(this.jack);this.switcher.className=this.options.className;return this.switcher};Switchery.prototype.insertAfter=function(reference,target){reference.parentNode.insertBefore(target,reference.nextSibling)};Switchery.prototype.isChecked=function(){return this.element.checked};Switchery.prototype.isDisabled=function(){return this.options.disabled||this.element.disabled};Switchery.prototype.setPosition=function(clicked){var checked=this.isChecked(),switcher=this.switcher,jack=this.jack;if(clicked&&checked)checked=false;else if(clicked&&!checked)checked=true;if(checked===true){this.element.checked=true;if(window.getComputedStyle)jack.style.left=parseInt(window.getComputedStyle(switcher).width)-parseInt(window.getComputedStyle(jack).width)+"px";else jack.style.left=parseInt(switcher.currentStyle["width"])-parseInt(jack.currentStyle["width"])+"px";if(this.options.color)this.colorize();this.setSpeed()}else{jack.style.left=0;this.element.checked=false;this.switcher.style.boxShadow="inset 0 0 0 0 "+this.options.secondaryColor;this.switcher.style.borderColor=this.options.secondaryColor;this.switcher.style.backgroundColor=this.options.secondaryColor!==defaults.secondaryColor?this.options.secondaryColor:"#fff";this.setSpeed()}};Switchery.prototype.setSpeed=function(){var switcherProp={},jackProp={left:this.options.speed.replace(/[a-z]/,"")/2+"s"};if(this.isChecked()){switcherProp={border:this.options.speed,"box-shadow":this.options.speed,"background-color":this.options.speed.replace(/[a-z]/,"")*3+"s"}}else{switcherProp={border:this.options.speed,"box-shadow":this.options.speed}}transitionize(this.switcher,switcherProp);transitionize(this.jack,jackProp)};Switchery.prototype.colorize=function(){this.switcher.style.backgroundColor=this.options.color;this.switcher.style.borderColor=this.options.color;this.switcher.style.boxShadow="inset 0 0 0 16px "+this.options.color};Switchery.prototype.handleOnchange=function(state){if(typeof Event==="function"||!document.fireEvent){var event=document.createEvent("HTMLEvents");event.initEvent("change",true,true);this.element.dispatchEvent(event)}else{this.element.fireEvent("onchange")}};Switchery.prototype.handleChange=function(){var self=this,el=this.element;if(el.addEventListener){el.addEventListener("change",function(){self.setPosition()})}else{el.attachEvent("onchange",function(){self.setPosition()})}};Switchery.prototype.handleClick=function(){var self=this,switcher=this.switcher,parent=self.element.parentNode.tagName.toLowerCase(),labelParent=parent==="label"?false:true;if(this.isDisabled()===false){fastclick(switcher);if(switcher.addEventListener){switcher.addEventListener("click",function(){self.setPosition(labelParent);self.handleOnchange(self.element.checked)})}else{switcher.attachEvent("onclick",function(){self.setPosition(labelParent);self.handleOnchange(self.element.checked)})}}else{this.element.disabled=true;this.switcher.style.opacity=this.options.disabledOpacity}};Switchery.prototype.markAsSwitched=function(){this.element.setAttribute("data-switchery",true)};Switchery.prototype.markedAsSwitched=function(){return this.element.getAttribute("data-switchery")};Switchery.prototype.init=function(){this.hide();this.show();this.setPosition();this.markAsSwitched();this.handleChange();this.handleClick()}});require.alias("abpetkov-transitionize/transitionize.js","switchery/deps/transitionize/transitionize.js");require.alias("abpetkov-transitionize/transitionize.js","switchery/deps/transitionize/index.js");require.alias("abpetkov-transitionize/transitionize.js","transitionize/index.js");require.alias("abpetkov-transitionize/transitionize.js","abpetkov-transitionize/index.js");require.alias("ftlabs-fastclick/lib/fastclick.js","switchery/deps/fastclick/lib/fastclick.js");require.alias("ftlabs-fastclick/lib/fastclick.js","switchery/deps/fastclick/index.js");require.alias("ftlabs-fastclick/lib/fastclick.js","fastclick/index.js");require.alias("ftlabs-fastclick/lib/fastclick.js","ftlabs-fastclick/index.js");require.alias("switchery/switchery.js","switchery/index.js");if(typeof exports=="object"){module.exports=require("switchery")}else if(typeof define=="function"&&define.amd){define(function(){return require("switchery")})}else{this["Switchery"]=require("switchery")}})();



// covervide video
var coverVid=function(a,b,c){function d(a,b){var c=null;return function(){var d=this,e=arguments;window.clearTimeout(c),c=window.setTimeout(function(){a.apply(d,e)},b)}}function e(){var d=a.parentNode.offsetHeight,e=a.parentNode.offsetWidth,f=b,g=c,h=d/g,i=e/f;i>h?(a.style.height="auto",a.style.width=e+"px"):(a.style.height=d+"px",a.style.width="auto")}document.addEventListener("DOMContentLoaded",e),window.onresize=function(){d(e(),50)},a.style.position="absolute",a.style.top="50%",a.style.left="50%",a.style["-webkit-transform"]="translate(-50%, -50%)",a.style["-ms-transform"]="translate(-50%, -50%)",a.style.transform="translate(-50%, -50%)",a.parentNode.style.overflow="hidden"};window.jQuery&&jQuery.fn.extend({coverVid:function(){return coverVid(this[0],arguments[0],arguments[1]),this}});


// odometer
(function() {
	var COUNT_FRAMERATE, COUNT_MS_PER_FRAME, DIGIT_FORMAT, DIGIT_HTML, DIGIT_SPEEDBOOST, DURATION, FORMAT_MARK_HTML, FORMAT_PARSER, FRAMERATE, FRAMES_PER_VALUE, MS_PER_FRAME, MutationObserver, Odometer, RIBBON_HTML, TRANSITION_END_EVENTS, TRANSITION_SUPPORT, VALUE_HTML, addClass, createFromHTML, fractionalPart, now, removeClass, requestAnimationFrame, round, transitionCheckStyles, trigger, truncate, wrapJQuery, _jQueryWrapped, _old, _ref, _ref1,
		__slice = [].slice;

	VALUE_HTML = '<span class="odometer-value"></span>';

	RIBBON_HTML = '<span class="odometer-ribbon"><span class="odometer-ribbon-inner">' + VALUE_HTML + '</span></span>';

	DIGIT_HTML = '<span class="odometer-digit"><span class="odometer-digit-spacer">8</span><span class="odometer-digit-inner">' + RIBBON_HTML + '</span></span>';

	FORMAT_MARK_HTML = '<span class="odometer-formatting-mark"></span>';

	DIGIT_FORMAT = '(,ddd).dd';

	FORMAT_PARSER = /^\(?([^)]*)\)?(?:(.)(d+))?$/;

	FRAMERATE = 30;

	DURATION = 2000;

	COUNT_FRAMERATE = 20;

	FRAMES_PER_VALUE = 2;

	DIGIT_SPEEDBOOST = .5;

	MS_PER_FRAME = 1000 / FRAMERATE;

	COUNT_MS_PER_FRAME = 1000 / COUNT_FRAMERATE;

	TRANSITION_END_EVENTS = 'transitionend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd';

	transitionCheckStyles = document.createElement('div').style;

	TRANSITION_SUPPORT = (transitionCheckStyles.transition != null) || (transitionCheckStyles.webkitTransition != null) || (transitionCheckStyles.mozTransition != null) || (transitionCheckStyles.oTransition != null);

	requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;

	MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;

	createFromHTML = function(html) {
		var el;
		el = document.createElement('div');
		el.innerHTML = html;
		return el.children[0];
	};

	removeClass = function(el, name) {
		return el.className = el.className.replace(new RegExp("(^| )" + (name.split(' ').join('|')) + "( |$)", 'gi'), ' ');
	};

	addClass = function(el, name) {
		removeClass(el, name);
		return el.className += " " + name;
	};

	trigger = function(el, name) {
		var evt;
		if (document.createEvent != null) {
			evt = document.createEvent('HTMLEvents');
			evt.initEvent(name, true, true);
			return el.dispatchEvent(evt);
		}
	};

	now = function() {
		var _ref, _ref1;
		return (_ref = (_ref1 = window.performance) != null ? typeof _ref1.now === "function" ? _ref1.now() : void 0 : void 0) != null ? _ref : +(new Date);
	};

	round = function(val, precision) {
		if (precision == null) {
			precision = 0;
		}
		if (!precision) {
			return Math.round(val);
		}
		val *= Math.pow(10, precision);
		val += 0.5;
		val = Math.floor(val);
		return val /= Math.pow(10, precision);
	};

	truncate = function(val) {
		if (val < 0) {
			return Math.ceil(val);
		} else {
			return Math.floor(val);
		}
	};

	fractionalPart = function(val) {
		return val - round(val);
	};

	_jQueryWrapped = false;

	(wrapJQuery = function() {
		var property, _i, _len, _ref, _results;
		if (_jQueryWrapped) {
			return;
		}
		if (window.jQuery != null) {
			_jQueryWrapped = true;
			_ref = ['html', 'text'];
			_results = [];
			for (_i = 0, _len = _ref.length; _i < _len; _i++) {
				property = _ref[_i];
				_results.push((function(property) {
					var old;
					old = window.jQuery.fn[property];
					return window.jQuery.fn[property] = function(val) {
						var _ref1;
						if ((val == null) || (((_ref1 = this[0]) != null ? _ref1.odometer : void 0) == null)) {
							return old.apply(this, arguments);
						}
						return this[0].odometer.update(val);
					};
				})(property));
			}
			return _results;
		}
	})();

	setTimeout(wrapJQuery, 0);

	Odometer = (function() {
		function Odometer(options) {
			var e, k, property, v, _base, _i, _len, _ref, _ref1, _ref2,
				_this = this;
			this.options = options;
			this.el = this.options.el;
			if (this.el.odometer != null) {
				return this.el.odometer;
			}
			this.el.odometer = this;
			_ref = Odometer.options;
			for (k in _ref) {
				v = _ref[k];
				if (this.options[k] == null) {
					this.options[k] = v;
				}
			}
			if ((_base = this.options).duration == null) {
				_base.duration = DURATION;
			}
			this.MAX_VALUES = ((this.options.duration / MS_PER_FRAME) / FRAMES_PER_VALUE) | 0;
			this.resetFormat();
			this.value = this.cleanValue((_ref1 = this.options.value) != null ? _ref1 : '');
			this.renderInside();
			this.render();
			try {
				_ref2 = ['innerHTML', 'innerText', 'textContent'];
				for (_i = 0, _len = _ref2.length; _i < _len; _i++) {
					property = _ref2[_i];
					if (this.el[property] != null) {
						(function(property) {
							return Object.defineProperty(_this.el, property, {
								get: function() {
									var _ref3;
									if (property === 'innerHTML') {
										return _this.inside.outerHTML;
									} else {
										return (_ref3 = _this.inside.innerText) != null ? _ref3 : _this.inside.textContent;
									}
								},
								set: function(val) {
									return _this.update(val);
								}
							});
						})(property);
					}
				}
			} catch (_error) {
				e = _error;
				this.watchForMutations();
			}
			this;
		}

		Odometer.prototype.renderInside = function() {
			this.inside = document.createElement('div');
			this.inside.className = 'odometer-inside';
			this.el.innerHTML = '';
			return this.el.appendChild(this.inside);
		};

		Odometer.prototype.watchForMutations = function() {
			var e,
				_this = this;
			if (MutationObserver == null) {
				return;
			}
			try {
				if (this.observer == null) {
					this.observer = new MutationObserver(function(mutations) {
						var newVal;
						newVal = _this.el.innerText;
						_this.renderInside();
						_this.render(_this.value);
						return _this.update(newVal);
					});
				}
				this.watchMutations = true;
				return this.startWatchingMutations();
			} catch (_error) {
				e = _error;
			}
		};

		Odometer.prototype.startWatchingMutations = function() {
			if (this.watchMutations) {
				return this.observer.observe(this.el, {
					childList: true
				});
			}
		};

		Odometer.prototype.stopWatchingMutations = function() {
			var _ref;
			return (_ref = this.observer) != null ? _ref.disconnect() : void 0;
		};

		Odometer.prototype.cleanValue = function(val) {
			var _ref;
			if (typeof val === 'string') {
				val = val.replace((_ref = this.format.radix) != null ? _ref : '.', '<radix>');
				val = val.replace(/[.,]/g, '');
				val = val.replace('<radix>', '.');
				val = parseFloat(val, 10) || 0;
			}
			return round(val, this.format.precision);
		};

		Odometer.prototype.bindTransitionEnd = function() {
			var event, renderEnqueued, _i, _len, _ref, _results,
				_this = this;
			if (this.transitionEndBound) {
				return;
			}
			this.transitionEndBound = true;
			renderEnqueued = false;
			_ref = TRANSITION_END_EVENTS.split(' ');
			_results = [];
			for (_i = 0, _len = _ref.length; _i < _len; _i++) {
				event = _ref[_i];
				_results.push(this.el.addEventListener(event, function() {
					if (renderEnqueued) {
						return true;
					}
					renderEnqueued = true;
					setTimeout(function() {
						_this.render();
						renderEnqueued = false;
						return trigger(_this.el, 'odometerdone');
					}, 0);
					return true;
				}, false));
			}
			return _results;
		};

		Odometer.prototype.resetFormat = function() {
			var format, fractional, parsed, precision, radix, repeating, _ref, _ref1;
			format = (_ref = this.options.format) != null ? _ref : DIGIT_FORMAT;
			format || (format = 'd');
			parsed = FORMAT_PARSER.exec(format);
			if (!parsed) {
				throw new Error("Odometer: Unparsable digit format");
			}
			_ref1 = parsed.slice(1, 4), repeating = _ref1[0], radix = _ref1[1], fractional = _ref1[2];
			precision = (fractional != null ? fractional.length : void 0) || 0;
			return this.format = {
				repeating: repeating,
				radix: radix,
				precision: precision
			};
		};

		Odometer.prototype.render = function(value) {
			var classes, cls, digit, match, newClasses, theme, wholePart, _i, _j, _len, _len1, _ref;
			if (value == null) {
				value = this.value;
			}
			this.stopWatchingMutations();
			this.resetFormat();
			this.inside.innerHTML = '';
			theme = this.options.theme;
			classes = this.el.className.split(' ');
			newClasses = [];
			for (_i = 0, _len = classes.length; _i < _len; _i++) {
				cls = classes[_i];
				if (!cls.length) {
					continue;
				}
				if (match = /^odometer-theme-(.+)$/.exec(cls)) {
					theme = match[1];
					continue;
				}
				if (/^odometer(-|$)/.test(cls)) {
					continue;
				}
				newClasses.push(cls);
			}
			newClasses.push('odometer');
			if (!TRANSITION_SUPPORT) {
				newClasses.push('odometer-no-transitions');
			}
			if (theme) {
				newClasses.push("odometer-theme-" + theme);
			} else {
				newClasses.push("odometer-auto-theme");
			}
			this.el.className = newClasses.join(' ');
			this.ribbons = {};
			this.digits = [];
			wholePart = !this.format.precision || !fractionalPart(value) || false;
			_ref = value.toString().split('').reverse();
			for (_j = 0, _len1 = _ref.length; _j < _len1; _j++) {
				digit = _ref[_j];
				if (digit === '.') {
					wholePart = true;
				}
				this.addDigit(digit, wholePart);
			}
			return this.startWatchingMutations();
		};

		Odometer.prototype.update = function(newValue) {
			var diff,
				_this = this;
			newValue = this.cleanValue(newValue);
			if (!(diff = newValue - this.value)) {
				return;
			}
			removeClass(this.el, 'odometer-animating-up odometer-animating-down odometer-animating');
			if (diff > 0) {
				addClass(this.el, 'odometer-animating-up');
			} else {
				addClass(this.el, 'odometer-animating-down');
			}
			this.stopWatchingMutations();
			this.animate(newValue);
			this.startWatchingMutations();
			setTimeout(function() {
				_this.el.offsetHeight;
				return addClass(_this.el, 'odometer-animating');
			}, 0);
			return this.value = newValue;
		};

		Odometer.prototype.renderDigit = function() {
			return createFromHTML(DIGIT_HTML);
		};

		Odometer.prototype.insertDigit = function(digit, before) {
			if (before != null) {
				return this.inside.insertBefore(digit, before);
			} else if (!this.inside.children.length) {
				return this.inside.appendChild(digit);
			} else {
				return this.inside.insertBefore(digit, this.inside.children[0]);
			}
		};

		Odometer.prototype.addSpacer = function(chr, before, extraClasses) {
			var spacer;
			spacer = createFromHTML(FORMAT_MARK_HTML);
			spacer.innerHTML = chr;
			if (extraClasses) {
				addClass(spacer, extraClasses);
			}
			return this.insertDigit(spacer, before);
		};

		Odometer.prototype.addDigit = function(value, repeating) {
			var chr, digit, resetted, _ref;
			if (repeating == null) {
				repeating = true;
			}
			if (value === '-') {
				return this.addSpacer(value, null, 'odometer-negation-mark');
			}
			if (value === '.') {
				return this.addSpacer((_ref = this.format.radix) != null ? _ref : '.', null, 'odometer-radix-mark');
			}
			if (repeating) {
				resetted = false;
				while (true) {
					if (!this.format.repeating.length) {
						if (resetted) {
							throw new Error("Bad odometer format without digits");
						}
						this.resetFormat();
						resetted = true;
					}
					chr = this.format.repeating[this.format.repeating.length - 1];
					this.format.repeating = this.format.repeating.substring(0, this.format.repeating.length - 1);
					if (chr === 'd') {
						break;
					}
					this.addSpacer(chr);
				}
			}
			digit = this.renderDigit();
			digit.querySelector('.odometer-value').innerHTML = value;
			this.digits.push(digit);
			return this.insertDigit(digit);
		};

		Odometer.prototype.animate = function(newValue) {
			if (!TRANSITION_SUPPORT || this.options.animation === 'count') {
				return this.animateCount(newValue);
			} else {
				return this.animateSlide(newValue);
			}
		};

		Odometer.prototype.animateCount = function(newValue) {
			var cur, diff, last, start, tick,
				_this = this;
			if (!(diff = +newValue - this.value)) {
				return;
			}
			start = last = now();
			cur = this.value;
			return (tick = function() {
				var delta, dist, fraction;
				if ((now() - start) > _this.options.duration) {
					_this.value = newValue;
					_this.render();
					trigger(_this.el, 'odometerdone');
					return;
				}
				delta = now() - last;
				if (delta > COUNT_MS_PER_FRAME) {
					last = now();
					fraction = delta / _this.options.duration;
					dist = diff * fraction;
					cur += dist;
					_this.render(Math.round(cur));
				}
				if (requestAnimationFrame != null) {
					return requestAnimationFrame(tick);
				} else {
					return setTimeout(tick, COUNT_MS_PER_FRAME);
				}
			})();
		};

		Odometer.prototype.getDigitCount = function() {
			var i, max, value, values, _i, _len;
			values = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
			for (i = _i = 0, _len = values.length; _i < _len; i = ++_i) {
				value = values[i];
				values[i] = Math.abs(value);
			}
			max = Math.max.apply(Math, values);
			return Math.ceil(Math.log(max + 1) / Math.log(10));
		};

		Odometer.prototype.getFractionalDigitCount = function() {
			var i, parser, parts, value, values, _i, _len;
			values = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
			parser = /^\-?\d*\.(\d*?)0*$/;
			for (i = _i = 0, _len = values.length; _i < _len; i = ++_i) {
				value = values[i];
				values[i] = value.toString();
				parts = parser.exec(values[i]);
				if (parts == null) {
					values[i] = 0;
				} else {
					values[i] = parts[1].length;
				}
			}
			return Math.max.apply(Math, values);
		};

		Odometer.prototype.resetDigits = function() {
			this.digits = [];
			this.ribbons = [];
			this.inside.innerHTML = '';
			return this.resetFormat();
		};

		Odometer.prototype.animateSlide = function(newValue) {
			var boosted, cur, diff, digitCount, digits, dist, end, fractionalCount, frame, frames, i, incr, j, mark, numEl, oldValue, start, _base, _i, _j, _k, _l, _len, _len1, _len2, _m, _ref, _results;
			oldValue = this.value;
			fractionalCount = this.getFractionalDigitCount(oldValue, newValue);
			if (fractionalCount) {
				newValue = newValue * Math.pow(10, fractionalCount);
				oldValue = oldValue * Math.pow(10, fractionalCount);
			}
			if (!(diff = newValue - oldValue)) {
				return;
			}
			this.bindTransitionEnd();
			digitCount = this.getDigitCount(oldValue, newValue);
			digits = [];
			boosted = 0;
			for (i = _i = 0; 0 <= digitCount ? _i < digitCount : _i > digitCount; i = 0 <= digitCount ? ++_i : --_i) {
				start = truncate(oldValue / Math.pow(10, digitCount - i - 1));
				end = truncate(newValue / Math.pow(10, digitCount - i - 1));
				dist = end - start;
				if (Math.abs(dist) > this.MAX_VALUES) {
					frames = [];
					incr = dist / (this.MAX_VALUES + this.MAX_VALUES * boosted * DIGIT_SPEEDBOOST);
					cur = start;
					while ((dist > 0 && cur < end) || (dist < 0 && cur > end)) {
						frames.push(Math.round(cur));
						cur += incr;
					}
					if (frames[frames.length - 1] !== end) {
						frames.push(end);
					}
					boosted++;
				} else {
					frames = (function() {
						_results = [];
						for (var _j = start; start <= end ? _j <= end : _j >= end; start <= end ? _j++ : _j--){ _results.push(_j); }
						return _results;
					}).apply(this);
				}
				for (i = _k = 0, _len = frames.length; _k < _len; i = ++_k) {
					frame = frames[i];
					frames[i] = Math.abs(frame % 10);
				}
				digits.push(frames);
			}
			this.resetDigits();
			_ref = digits.reverse();
			for (i = _l = 0, _len1 = _ref.length; _l < _len1; i = ++_l) {
				frames = _ref[i];
				if (!this.digits[i]) {
					this.addDigit(' ', i >= fractionalCount);
				}
				if ((_base = this.ribbons)[i] == null) {
					_base[i] = this.digits[i].querySelector('.odometer-ribbon-inner');
				}
				this.ribbons[i].innerHTML = '';
				if (diff < 0) {
					frames = frames.reverse();
				}
				for (j = _m = 0, _len2 = frames.length; _m < _len2; j = ++_m) {
					frame = frames[j];
					numEl = document.createElement('div');
					numEl.className = 'odometer-value';
					numEl.innerHTML = frame;
					this.ribbons[i].appendChild(numEl);
					if (j === frames.length - 1) {
						addClass(numEl, 'odometer-last-value');
					}
					if (j === 0) {
						addClass(numEl, 'odometer-first-value');
					}
				}
			}
			if (start < 0) {
				this.addDigit('-');
			}
			mark = this.inside.querySelector('.odometer-radix-mark');
			if (mark != null) {
				mark.parent.removeChild(mark);
			}
			if (fractionalCount) {
				return this.addSpacer(this.format.radix, this.digits[fractionalCount - 1], 'odometer-radix-mark');
			}
		};

		return Odometer;

	})();

	Odometer.options = (_ref = window.odometerOptions) != null ? _ref : {};

	setTimeout(function() {
		var k, v, _base, _ref1, _results;
		if (window.odometerOptions) {
			_ref1 = window.odometerOptions;
			_results = [];
			for (k in _ref1) {
				v = _ref1[k];
				_results.push((_base = Odometer.options)[k] != null ? (_base = Odometer.options)[k] : _base[k] = v);
			}
			return _results;
		}
	}, 0);

	Odometer.init = function() {
		var el, elements, _i, _len, _ref1, _results;
		if (document.querySelectorAll == null) {
			return;
		}
		elements = document.querySelectorAll(Odometer.options.selector || '.odometer');
		_results = [];
		for (_i = 0, _len = elements.length; _i < _len; _i++) {
			el = elements[_i];
			_results.push(el.odometer = new Odometer({
				el: el,
				value: (_ref1 = el.innerText) != null ? _ref1 : el.textContent
			}));
		}
		return _results;
	};

	if ((((_ref1 = document.documentElement) != null ? _ref1.doScroll : void 0) != null) && (document.createEventObject != null)) {
		_old = document.onreadystatechange;
		document.onreadystatechange = function() {
			if (document.readyState === 'complete' && Odometer.options.auto !== false) {
				Odometer.init();
			}
			return _old != null ? _old.apply(this, arguments) : void 0;
		};
	} else {
		document.addEventListener('DOMContentLoaded', function() {
			if (Odometer.options.auto !== false) {
				return Odometer.init();
			}
		}, false);
	}

	if (typeof define === 'function' && define.amd) {
		define(['jquery'], function() {
			return Odometer;
		});
	} else if (typeof exports === !'undefined') {
		module.exports = Odometer;
	} else {
		window.Odometer = Odometer;
	}

}).call(this);

// end odemeter

// Generated by CoffeeScript 1.6.2
/*!
jQuery Waypoints - v2.0.5
Copyright (c) 2011-2014 Caleb Troughton
Licensed under the MIT license.
https://github.com/imakewebthings/jquery-waypoints/blob/master/licenses.txt
*/
(function(){var t=[].indexOf||function(t){for(var e=0,n=this.length;e<n;e++){if(e in this&&this[e]===t)return e}return-1},e=[].slice;(function(t,e){if(typeof define==="function"&&define.amd){return define("waypoints",["jquery"],function(n){return e(n,t)})}else{return e(t.jQuery,t)}})(window,function(n,r){var i,o,l,s,f,u,c,a,h,d,p,y,v,w,g,m;i=n(r);a=t.call(r,"ontouchstart")>=0;s={horizontal:{},vertical:{}};f=1;c={};u="waypoints-context-id";p="resize.waypoints";y="scroll.waypoints";v=1;w="waypoints-waypoint-ids";g="waypoint";m="waypoints";o=function(){function t(t){var e=this;this.$element=t;this.element=t[0];this.didResize=false;this.didScroll=false;this.id="context"+f++;this.oldScroll={x:t.scrollLeft(),y:t.scrollTop()};this.waypoints={horizontal:{},vertical:{}};this.element[u]=this.id;c[this.id]=this;t.bind(y,function(){var t;if(!(e.didScroll||a)){e.didScroll=true;t=function(){e.doScroll();return e.didScroll=false};return r.setTimeout(t,n[m].settings.scrollThrottle)}});t.bind(p,function(){var t;if(!e.didResize){e.didResize=true;t=function(){n[m]("refresh");return e.didResize=false};return r.setTimeout(t,n[m].settings.resizeThrottle)}})}t.prototype.doScroll=function(){var t,e=this;t={horizontal:{newScroll:this.$element.scrollLeft(),oldScroll:this.oldScroll.x,forward:"right",backward:"left"},vertical:{newScroll:this.$element.scrollTop(),oldScroll:this.oldScroll.y,forward:"down",backward:"up"}};if(a&&(!t.vertical.oldScroll||!t.vertical.newScroll)){n[m]("refresh")}n.each(t,function(t,r){var i,o,l;l=[];o=r.newScroll>r.oldScroll;i=o?r.forward:r.backward;n.each(e.waypoints[t],function(t,e){var n,i;if(r.oldScroll<(n=e.offset)&&n<=r.newScroll){return l.push(e)}else if(r.newScroll<(i=e.offset)&&i<=r.oldScroll){return l.push(e)}});l.sort(function(t,e){return t.offset-e.offset});if(!o){l.reverse()}return n.each(l,function(t,e){if(e.options.continuous||t===l.length-1){return e.trigger([i])}})});return this.oldScroll={x:t.horizontal.newScroll,y:t.vertical.newScroll}};t.prototype.refresh=function(){var t,e,r,i=this;r=n.isWindow(this.element);e=this.$element.offset();this.doScroll();t={horizontal:{contextOffset:r?0:e.left,contextScroll:r?0:this.oldScroll.x,contextDimension:this.$element.width(),oldScroll:this.oldScroll.x,forward:"right",backward:"left",offsetProp:"left"},vertical:{contextOffset:r?0:e.top,contextScroll:r?0:this.oldScroll.y,contextDimension:r?n[m]("viewportHeight"):this.$element.height(),oldScroll:this.oldScroll.y,forward:"down",backward:"up",offsetProp:"top"}};return n.each(t,function(t,e){return n.each(i.waypoints[t],function(t,r){var i,o,l,s,f;i=r.options.offset;l=r.offset;o=n.isWindow(r.element)?0:r.$element.offset()[e.offsetProp];if(n.isFunction(i)){i=i.apply(r.element)}else if(typeof i==="string"){i=parseFloat(i);if(r.options.offset.indexOf("%")>-1){i=Math.ceil(e.contextDimension*i/100)}}r.offset=o-e.contextOffset+e.contextScroll-i;if(r.options.onlyOnScroll&&l!=null||!r.enabled){return}if(l!==null&&l<(s=e.oldScroll)&&s<=r.offset){return r.trigger([e.backward])}else if(l!==null&&l>(f=e.oldScroll)&&f>=r.offset){return r.trigger([e.forward])}else if(l===null&&e.oldScroll>=r.offset){return r.trigger([e.forward])}})})};t.prototype.checkEmpty=function(){if(n.isEmptyObject(this.waypoints.horizontal)&&n.isEmptyObject(this.waypoints.vertical)){this.$element.unbind([p,y].join(" "));return delete c[this.id]}};return t}();l=function(){function t(t,e,r){var i,o;if(r.offset==="bottom-in-view"){r.offset=function(){var t;t=n[m]("viewportHeight");if(!n.isWindow(e.element)){t=e.$element.height()}return t-n(this).outerHeight()}}this.$element=t;this.element=t[0];this.axis=r.horizontal?"horizontal":"vertical";this.callback=r.handler;this.context=e;this.enabled=r.enabled;this.id="waypoints"+v++;this.offset=null;this.options=r;e.waypoints[this.axis][this.id]=this;s[this.axis][this.id]=this;i=(o=this.element[w])!=null?o:[];i.push(this.id);this.element[w]=i}t.prototype.trigger=function(t){if(!this.enabled){return}if(this.callback!=null){this.callback.apply(this.element,t)}if(this.options.triggerOnce){return this.destroy()}};t.prototype.disable=function(){return this.enabled=false};t.prototype.enable=function(){this.context.refresh();return this.enabled=true};t.prototype.destroy=function(){delete s[this.axis][this.id];delete this.context.waypoints[this.axis][this.id];return this.context.checkEmpty()};t.getWaypointsByElement=function(t){var e,r;r=t[w];if(!r){return[]}e=n.extend({},s.horizontal,s.vertical);return n.map(r,function(t){return e[t]})};return t}();d={init:function(t,e){var r;e=n.extend({},n.fn[g].defaults,e);if((r=e.handler)==null){e.handler=t}this.each(function(){var t,r,i,s;t=n(this);i=(s=e.context)!=null?s:n.fn[g].defaults.context;if(!n.isWindow(i)){i=t.closest(i)}i=n(i);r=c[i[0][u]];if(!r){r=new o(i)}return new l(t,r,e)});n[m]("refresh");return this},disable:function(){return d._invoke.call(this,"disable")},enable:function(){return d._invoke.call(this,"enable")},destroy:function(){return d._invoke.call(this,"destroy")},prev:function(t,e){return d._traverse.call(this,t,e,function(t,e,n){if(e>0){return t.push(n[e-1])}})},next:function(t,e){return d._traverse.call(this,t,e,function(t,e,n){if(e<n.length-1){return t.push(n[e+1])}})},_traverse:function(t,e,i){var o,l;if(t==null){t="vertical"}if(e==null){e=r}l=h.aggregate(e);o=[];this.each(function(){var e;e=n.inArray(this,l[t]);return i(o,e,l[t])});return this.pushStack(o)},_invoke:function(t){this.each(function(){var e;e=l.getWaypointsByElement(this);return n.each(e,function(e,n){n[t]();return true})});return this}};n.fn[g]=function(){var t,r;r=arguments[0],t=2<=arguments.length?e.call(arguments,1):[];if(d[r]){return d[r].apply(this,t)}else if(n.isFunction(r)){return d.init.apply(this,arguments)}else if(n.isPlainObject(r)){return d.init.apply(this,[null,r])}else if(!r){return n.error("jQuery Waypoints needs a callback function or handler option.")}else{return n.error("The "+r+" method does not exist in jQuery Waypoints.")}};n.fn[g].defaults={context:r,continuous:true,enabled:true,horizontal:false,offset:0,triggerOnce:false};h={refresh:function(){return n.each(c,function(t,e){return e.refresh()})},viewportHeight:function(){var t;return(t=r.innerHeight)!=null?t:i.height()},aggregate:function(t){var e,r,i;e=s;if(t){e=(i=c[n(t)[0][u]])!=null?i.waypoints:void 0}if(!e){return[]}r={horizontal:[],vertical:[]};n.each(r,function(t,i){n.each(e[t],function(t,e){return i.push(e)});i.sort(function(t,e){return t.offset-e.offset});r[t]=n.map(i,function(t){return t.element});return r[t]=n.unique(r[t])});return r},above:function(t){if(t==null){t=r}return h._filter(t,"vertical",function(t,e){return e.offset<=t.oldScroll.y})},below:function(t){if(t==null){t=r}return h._filter(t,"vertical",function(t,e){return e.offset>t.oldScroll.y})},left:function(t){if(t==null){t=r}return h._filter(t,"horizontal",function(t,e){return e.offset<=t.oldScroll.x})},right:function(t){if(t==null){t=r}return h._filter(t,"horizontal",function(t,e){return e.offset>t.oldScroll.x})},enable:function(){return h._invoke("enable")},disable:function(){return h._invoke("disable")},destroy:function(){return h._invoke("destroy")},extendFn:function(t,e){return d[t]=e},_invoke:function(t){var e;e=n.extend({},s.vertical,s.horizontal);return n.each(e,function(e,n){n[t]();return true})},_filter:function(t,e,r){var i,o;i=c[n(t)[0][u]];if(!i){return[]}o=[];n.each(i.waypoints[e],function(t,e){if(r(i,e)){return o.push(e)}});o.sort(function(t,e){return t.offset-e.offset});return n.map(o,function(t){return t.element})}};n[m]=function(){var t,n;n=arguments[0],t=2<=arguments.length?e.call(arguments,1):[];if(h[n]){return h[n].apply(null,t)}else{return h.aggregate.call(null,n)}};n[m].settings={resizeThrottle:100,scrollThrottle:30};return i.on("load.waypoints",function(){return n[m]("refresh")})})}).call(this);



/*!
	Autosize 1.18.14
	license: MIT
	http://www.jacklmoore.com/autosize
*/
!function(e){var t,o={className:"autosizejs",id:"autosizejs",append:"\n",callback:!1,resizeDelay:10,placeholder:!0},i='<textarea tabindex="-1" style="position:absolute; top:-999px; left:0; right:auto; bottom:auto; border:0; padding: 0; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; box-sizing:content-box; word-wrap:break-word; height:0 !important; min-height:0 !important; overflow:hidden; transition:none; -webkit-transition:none; -moz-transition:none;"/>',n=["fontFamily","fontSize","fontWeight","fontStyle","letterSpacing","textTransform","wordSpacing","textIndent","whiteSpace"],s=e(i).data("autosize",!0)[0];s.style.lineHeight="99px","99px"===e(s).css("lineHeight")&&n.push("lineHeight"),s.style.lineHeight="",e.fn.autosize=function(i){return this.length?(i=e.extend({},o,i||{}),s.parentNode!==document.body&&e(document.body).append(s),this.each(function(){function o(){var t,o=window.getComputedStyle?window.getComputedStyle(u,null):!1;o?(t=u.getBoundingClientRect().width,(0===t||"number"!=typeof t)&&(t=parseInt(o.width,10)),e.each(["paddingLeft","paddingRight","borderLeftWidth","borderRightWidth"],function(e,i){t-=parseInt(o[i],10)})):t=p.width(),s.style.width=Math.max(t,0)+"px"}function a(){var a={};if(t=u,s.className=i.className,s.id=i.id,d=parseInt(p.css("maxHeight"),10),e.each(n,function(e,t){a[t]=p.css(t)}),e(s).css(a).attr("wrap",p.attr("wrap")),o(),window.chrome){var r=u.style.width;u.style.width="0px";{u.offsetWidth}u.style.width=r}}function r(){var e,n;t!==u?a():o(),s.value=!u.value&&i.placeholder?p.attr("placeholder")||"":u.value,s.value+=i.append||"",s.style.overflowY=u.style.overflowY,n=parseInt(u.style.height,10),s.scrollTop=0,s.scrollTop=9e4,e=s.scrollTop,d&&e>d?(u.style.overflowY="scroll",e=d):(u.style.overflowY="hidden",c>e&&(e=c)),e+=w,n!==e&&(u.style.height=e+"px",s.className=s.className,f&&i.callback.call(u,u),p.trigger("autosize.resized"))}function l(){clearTimeout(h),h=setTimeout(function(){var e=p.width();e!==g&&(g=e,r())},parseInt(i.resizeDelay,10))}var d,c,h,u=this,p=e(u),w=0,f=e.isFunction(i.callback),z={height:u.style.height,overflow:u.style.overflow,overflowY:u.style.overflowY,wordWrap:u.style.wordWrap,resize:u.style.resize},g=p.width(),y=p.css("resize");p.data("autosize")||(p.data("autosize",!0),("border-box"===p.css("box-sizing")||"border-box"===p.css("-moz-box-sizing")||"border-box"===p.css("-webkit-box-sizing"))&&(w=p.outerHeight()-p.height()),c=Math.max(parseInt(p.css("minHeight"),10)-w||0,p.height()),p.css({overflow:"hidden",overflowY:"hidden",wordWrap:"break-word"}),"vertical"===y?p.css("resize","none"):"both"===y&&p.css("resize","horizontal"),"onpropertychange"in u?"oninput"in u?p.on("input.autosize keyup.autosize",r):p.on("propertychange.autosize",function(){"value"===event.propertyName&&r()}):p.on("input.autosize",r),i.resizeDelay!==!1&&e(window).on("resize.autosize",l),p.on("autosize.resize",r),p.on("autosize.resizeIncludeStyle",function(){t=null,r()}),p.on("autosize.destroy",function(){t=null,clearTimeout(h),e(window).off("resize",l),p.off("autosize").off(".autosize").css(z).removeData("autosize")}),r())})):this}}(jQuery||$);

/* jQuery tubular plugin
|* by Sean McCambridge
|* http://www.seanmccambridge.com/tubular
|* version: 1.0
|* updated: October 1, 2012
|* since 2010
|* licensed under the MIT License
|* Enjoy.
|* 
|* Thanks,
|* Sean */
;(function(e,t){var n={ratio:16/9,videoId:"ZCAnLxRvNNc",mute:true,repeat:true,width:e(t).width(),wrapperZIndex:99,playButtonClass:"tubular-play",pauseButtonClass:"tubular-pause",muteButtonClass:"tubular-mute",volumeUpClass:"tubular-volume-up",volumeDownClass:"tubular-volume-down",increaseVolumeBy:10,start:0};var r=function(r,i){var i=e.extend({},n,i),s=e("body"),o=e(r);var u='<div id="tubular-container" style="width: 100%; height: 100%"><div id="tubular-player" style="position: absolute"></div></div><div id="tubular-shield" style="width: 100%; height: 100%; z-index: 2; position: absolute; left: 0; top: 0;"></div>';e("html,body").css({width:"100%",height:"100%"});o.prepend(u);o.css({position:"relative","z-index":i.wrapperZIndex,overflow:"hidden"});t.player;t.onYouTubeIframeAPIReady=function(){player=new YT.Player("tubular-player",{width:i.width,height:Math.ceil(i.width/i.ratio),videoId:i.videoId,playerVars:{controls:0,showinfo:0,modestbranding:1,wmode:"transparent"},events:{onReady:onPlayerReady,onStateChange:onPlayerStateChange}})};t.onPlayerReady=function(e){a();if(i.mute)e.target.mute();e.target.seekTo(i.start);e.target.playVideo()};t.onPlayerStateChange=function(e){if(e.data===0&&i.repeat){player.seekTo(i.start)}};var a=function(){var n=e(t).width(),r,s=e(t).height(),o,u=e("#tubular-player");if(n/i.ratio<s){r=Math.ceil(s*i.ratio);u.width(r).height(s).css({left:(n-r)/2,top:0})}else{o=Math.ceil(n/i.ratio);u.width(n).height(o).css({left:0,top:(s-o)/2})}};e(t).on("resize.tubular",function(){a()});e("body").on("click","."+i.playButtonClass,function(e){e.preventDefault();player.playVideo()}).on("click","."+i.pauseButtonClass,function(e){e.preventDefault();player.pauseVideo()}).on("click","."+i.muteButtonClass,function(e){e.preventDefault();player.isMuted()?player.unMute():player.mute()}).on("click","."+i.volumeDownClass,function(e){e.preventDefault();var t=player.getVolume();if(t<i.increaseVolumeBy)t=i.increaseVolumeBy;player.setVolume(t-i.increaseVolumeBy)}).on("click","."+i.volumeUpClass,function(e){e.preventDefault();if(player.isMuted())player.unMute();var t=player.getVolume();if(t>100-i.increaseVolumeBy)t=100-i.increaseVolumeBy;player.setVolume(t+i.increaseVolumeBy)})};var i=document.createElement("script");i.src="//www.youtube.com/iframe_api";var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(i,s);e.fn.tubular=function(t){return this.each(function(){if(!e.data(this,"tubular_instantiated")){e.data(this,"tubular_instantiated",r(this,t))}})}})(jQuery,window)