/*!
* jQuery Featured; build: v20131005
* http://jquery.malsup.com/cycle22/
* Copyright (c) 2013 M. Alsup; Dual licensed: MIT/GPL
*/
/*! core engine; version: 20131003 */
(function(e){"use strict";function t(e){return(e||"").toLowerCase()}var i="20131003";e.fn.featured=function(i){var n;return 0!==this.length||e.isReady?this.each(function(){var n,s,o,c,r=e(this),l=e.fn.featured.log;if(!r.data("featured.opts")){(r.data("featured-log")===!1||i&&i.log===!1||s&&s.log===!1)&&(l=e.noop),l("--c2 init--"),n=r.data();for(var a in n)n.hasOwnProperty(a)&&/^featured[A-Z]+/.test(a)&&(c=n[a],o=a.match(/^featured(.*)/)[1].replace(/^[A-Z]/,t),l(o+":",c,"("+typeof c+")"),n[o]=c);s=e.extend({},e.fn.featured.defaults,n,i||{}),s.timeoutId=0,s.paused=s.paused||!1,s.container=r,s._maxZ=s.maxZ,s.API=e.extend({_container:r},e.fn.featured.API),s.API.log=l,s.API.trigger=function(e,t){return s.container.trigger(e,t),s.API},r.data("featured.opts",s),r.data("featured.API",s.API),s.API.trigger("featured-bootstrap",[s,s.API]),s.API.addInitialSlides(),s.API.preInitSlideshow(),s.slides.length&&s.API.initSlideshow()}}):(n={s:this.selector,c:this.context},e.fn.featured.log("requeuing slideshow (dom not ready)"),e(function(){e(n.s,n.c).featured(i)}),this)},e.fn.featured.API={opts:function(){return this._container.data("featured.opts")},addInitialSlides:function(){var t=this.opts(),i=t.slides;t.slideCount=0,t.slides=e(),i=i.jquery?i:t.container.find(i),t.random&&i.sort(function(){return Math.random()-.5}),t.API.add(i)},preInitSlideshow:function(){var t=this.opts();t.API.trigger("featured-pre-initialize",[t]);var i=e.fn.featured.transitions[t.fx];i&&e.isFunction(i.preInit)&&i.preInit(t),t._preInitialized=!0},postInitSlideshow:function(){var t=this.opts();t.API.trigger("featured-post-initialize",[t]);var i=e.fn.featured.transitions[t.fx];i&&e.isFunction(i.postInit)&&i.postInit(t)},initSlideshow:function(){var t,i=this.opts(),n=i.container;i.API.calcFirstSlide(),"static"==i.container.css("position")&&i.container.css("position","relative"),e(i.slides[i.currSlide]).css("opacity",1).show(),i.API.stackSlides(i.slides[i.currSlide],i.slides[i.nextSlide],!i.reverse),i.pauseOnHover&&(i.pauseOnHover!==!0&&(n=e(i.pauseOnHover)),n.hover(function(){i.API.pause(!0)},function(){i.API.resume(!0)})),i.timeout&&(t=i.API.getSlideOpts(i.currSlide),i.API.queueTransition(t,t.timeout+i.delay)),i._initialized=!0,i.API.updateView(!0),i.API.trigger("featured-initialized",[i]),i.API.postInitSlideshow()},pause:function(t){var i=this.opts(),n=i.API.getSlideOpts(),s=i.hoverPaused||i.paused;t?i.hoverPaused=!0:i.paused=!0,s||(i.container.addClass("featured-paused"),i.API.trigger("featured-paused",[i]).log("featured-paused"),n.timeout&&(clearTimeout(i.timeoutId),i.timeoutId=0,i._remainingTimeout-=e.now()-i._lastQueue,(0>i._remainingTimeout||isNaN(i._remainingTimeout))&&(i._remainingTimeout=void 0)))},resume:function(e){var t=this.opts(),i=!t.hoverPaused&&!t.paused;e?t.hoverPaused=!1:t.paused=!1,i||(t.container.removeClass("featured-paused"),0===t.slides.filter(":animated").length&&t.API.queueTransition(t.API.getSlideOpts(),t._remainingTimeout),t.API.trigger("featured-resumed",[t,t._remainingTimeout]).log("featured-resumed"))},add:function(t,i){var n,s=this.opts(),o=s.slideCount,c=!1;"string"==e.type(t)&&(t=e.trim(t)),e(t).each(function(){var t,n=e(this);i?s.container.prepend(n):s.container.append(n),s.slideCount++,t=s.API.buildSlideOpts(n),s.slides=i?e(n).add(s.slides):s.slides.add(n),s.API.initSlide(t,n,--s._maxZ),n.data("featured.opts",t),s.API.trigger("featured-slide-added",[s,t,n])}),s.API.updateView(!0),c=s._preInitialized&&2>o&&s.slideCount>=1,c&&(s._initialized?s.timeout&&(n=s.slides.length,s.nextSlide=s.reverse?n-1:1,s.timeoutId||s.API.queueTransition(s)):s.API.initSlideshow())},calcFirstSlide:function(){var e,t=this.opts();e=parseInt(t.startingSlide||0,10),(e>=t.slides.length||0>e)&&(e=0),t.currSlide=e,t.reverse?(t.nextSlide=e-1,0>t.nextSlide&&(t.nextSlide=t.slides.length-1)):(t.nextSlide=e+1,t.nextSlide==t.slides.length&&(t.nextSlide=0))},calcNextSlide:function(){var e,t=this.opts();t.reverse?(e=0>t.nextSlide-1,t.nextSlide=e?t.slideCount-1:t.nextSlide-1,t.currSlide=e?0:t.nextSlide+1):(e=t.nextSlide+1==t.slides.length,t.nextSlide=e?0:t.nextSlide+1,t.currSlide=e?t.slides.length-1:t.nextSlide-1)},calcTx:function(t,i){var n,s=t;return i&&s.manualFx&&(n=e.fn.featured.transitions[s.manualFx]),n||(n=e.fn.featured.transitions[s.fx]),n||(n=e.fn.featured.transitions.fade,s.API.log('Transition "'+s.fx+'" not found.  Using fade.')),n},prepareTx:function(e,t){var i,n,s,o,c,r=this.opts();return 2>r.slideCount?(r.timeoutId=0,void 0):(!e||r.busy&&!r.manualTrump||(r.API.stopTransition(),r.busy=!1,clearTimeout(r.timeoutId),r.timeoutId=0),r.busy||(0!==r.timeoutId||e)&&(n=r.slides[r.currSlide],s=r.slides[r.nextSlide],o=r.API.getSlideOpts(r.nextSlide),c=r.API.calcTx(o,e),r._tx=c,e&&void 0!==o.manualSpeed&&(o.speed=o.manualSpeed),r.nextSlide!=r.currSlide&&(e||!r.paused&&!r.hoverPaused&&r.timeout)?(r.API.trigger("featured-before",[o,n,s,t]),c.before&&c.before(o,n,s,t),i=function(){r.busy=!1,r.container.data("featured.opts")&&(c.after&&c.after(o,n,s,t),r.API.trigger("featured-after",[o,n,s,t]),r.API.queueTransition(o),r.API.updateView(!0))},r.busy=!0,c.transition?c.transition(o,n,s,t,i):r.API.doTransition(o,n,s,t,i),r.API.calcNextSlide(),r.API.updateView()):r.API.queueTransition(o)),void 0)},doTransition:function(t,i,n,s,o){var c=t,r=e(i),l=e(n),a=function(){l.animate(c.animIn||{opacity:1},c.speed,c.easeIn||c.easing,o)};l.css(c.cssBefore||{}),r.animate(c.animOut||{},c.speed,c.easeOut||c.easing,function(){r.css(c.cssAfter||{}),c.sync||a()}),c.sync&&a()},queueTransition:function(t,i){var n=this.opts(),s=void 0!==i?i:t.timeout;return 0===n.nextSlide&&0===--n.loop?(n.API.log("terminating; loop=0"),n.timeout=0,s?setTimeout(function(){n.API.trigger("featured-finished",[n])},s):n.API.trigger("featured-finished",[n]),n.nextSlide=n.currSlide,void 0):(s&&(n._lastQueue=e.now(),void 0===i&&(n._remainingTimeout=t.timeout),n.paused||n.hoverPaused||(n.timeoutId=setTimeout(function(){n.API.prepareTx(!1,!n.reverse)},s))),void 0)},stopTransition:function(){var e=this.opts();e.slides.filter(":animated").length&&(e.slides.stop(!1,!0),e.API.trigger("featured-transition-stopped",[e])),e._tx&&e._tx.stopTransition&&e._tx.stopTransition(e)},advanceSlide:function(e){var t=this.opts();return clearTimeout(t.timeoutId),t.timeoutId=0,t.nextSlide=t.currSlide+e,0>t.nextSlide?t.nextSlide=t.slides.length-1:t.nextSlide>=t.slides.length&&(t.nextSlide=0),t.API.prepareTx(!0,e>=0),!1},buildSlideOpts:function(i){var n,s,o=this.opts(),c=i.data()||{};for(var r in c)c.hasOwnProperty(r)&&/^featured[A-Z]+/.test(r)&&(n=c[r],s=r.match(/^featured(.*)/)[1].replace(/^[A-Z]/,t),o.API.log("["+(o.slideCount-1)+"]",s+":",n,"("+typeof n+")"),c[s]=n);c=e.extend({},e.fn.featured.defaults,o,c),c.slideNum=o.slideCount;try{delete c.API,delete c.slideCount,delete c.currSlide,delete c.nextSlide,delete c.slides}catch(l){}return c},getSlideOpts:function(t){var i=this.opts();void 0===t&&(t=i.currSlide);var n=i.slides[t],s=e(n).data("featured.opts");return e.extend({},i,s)},initSlide:function(t,i,n){var s=this.opts();i.css(t.slideCss||{}),n>0&&i.css("zIndex",n),isNaN(t.speed)&&(t.speed=e.fx.speeds[t.speed]||e.fx.speeds._default),t.sync||(t.speed=t.speed/2),i.addClass(s.slideClass)},updateView:function(e,t){var i=this.opts();if(i._initialized){var n=i.API.getSlideOpts(),s=i.slides[i.currSlide];!e&&t!==!0&&(i.API.trigger("featured-update-view-before",[i,n,s]),0>i.updateView)||(i.slideActiveClass&&i.slides.removeClass(i.slideActiveClass).eq(i.currSlide).addClass(i.slideActiveClass),e&&i.hideNonActive&&i.slides.filter(":not(."+i.slideActiveClass+")").hide(),i.API.trigger("featured-update-view",[i,n,s,e]),e&&i.API.trigger("featured-update-view-after",[i,n,s]))}},getComponent:function(t){var i=this.opts(),n=i[t];return"string"==typeof n?/^\s*[\>|\+|~]/.test(n)?i.container.find(n):e(n):n.jquery?n:e(n)},stackSlides:function(t,i,n){var s=this.opts();t||(t=s.slides[s.currSlide],i=s.slides[s.nextSlide],n=!s.reverse),e(t).css("zIndex",s.maxZ);var o,c=s.maxZ-2,r=s.slideCount;if(n){for(o=s.currSlide+1;r>o;o++)e(s.slides[o]).css("zIndex",c--);for(o=0;s.currSlide>o;o++)e(s.slides[o]).css("zIndex",c--)}else{for(o=s.currSlide-1;o>=0;o--)e(s.slides[o]).css("zIndex",c--);for(o=r-1;o>s.currSlide;o--)e(s.slides[o]).css("zIndex",c--)}e(i).css("zIndex",s.maxZ-1)},getSlideIndex:function(e){return this.opts().slides.index(e)}},e.fn.featured.log=function(){window.console&&console.log&&console.log("[featured2] "+Array.prototype.join.call(arguments," "))},e.fn.featured.version=function(){return"Featured: "+i},e.fn.featured.transitions={custom:{},none:{before:function(e,t,i,n){e.API.stackSlides(i,t,n),e.cssBefore={opacity:1,display:"block"}}},fade:{before:function(t,i,n,s){var o=t.API.getSlideOpts(t.nextSlide).slideCss||{};t.API.stackSlides(i,n,s),t.cssBefore=e.extend(o,{opacity:0,display:"block"}),t.animIn={opacity:1},t.animOut={opacity:0}}},fadeout:{before:function(t,i,n,s){var o=t.API.getSlideOpts(t.nextSlide).slideCss||{};t.API.stackSlides(i,n,s),t.cssBefore=e.extend(o,{opacity:1,display:"block"}),t.animOut={opacity:0}}},scrollHorz:{before:function(e,t,i,n){e.API.stackSlides(t,i,n);var s=e.container.css("overflow","hidden").width();e.cssBefore={left:n?s:-s,top:0,opacity:1,display:"block"},e.cssAfter={zIndex:e._maxZ-2,left:0},e.animIn={left:0},e.animOut={left:n?-s:s}}}},e.fn.featured.defaults={allowWrap:!0,autoSelector:".featured-slideshow[data-featured-auto-init!=false]",delay:0,easing:null,fx:"fade",hideNonActive:!0,loop:0,manualFx:void 0,manualSpeed:void 0,manualTrump:!0,maxZ:100,pauseOnHover:!1,reverse:!1,slideActiveClass:"featured-slide-active",slideClass:"featured-slide",slideCss:{position:"absolute",top:0,left:0},slides:"> img",speed:500,startingSlide:0,sync:!0,timeout:4e3,updateView:-1},e(document).ready(function(){e(e.fn.featured.defaults.autoSelector).featured()})})(jQuery),/*! Featured autoheight plugin; Copyright (c) M.Alsup, 2012; version: 20130304 */
function(e){"use strict";function t(t,n){var s,o,c,r=n.autoHeight;if("container"==r)o=e(n.slides[n.currSlide]).outerHeight(),n.container.height(o);else if(n._autoHeightRatio)n.container.height(n.container.width()/n._autoHeightRatio);else if("calc"===r||"number"==e.type(r)&&r>=0){if(c="calc"===r?i(t,n):r>=n.slides.length?0:r,c==n._sentinelIndex)return;n._sentinelIndex=c,n._sentinel&&n._sentinel.remove(),s=e(n.slides[c].cloneNode(!0)),s.removeAttr("id name rel").find("[id],[name],[rel]").removeAttr("id name rel"),s.css({position:"static",visibility:"hidden",display:"block"}).prependTo(n.container).addClass("featured-sentinel featured-slide").removeClass("featured-slide-active"),s.find("*").css("visibility","hidden"),n._sentinel=s}}function i(t,i){var n=0,s=-1;return i.slides.each(function(t){var i=e(this).height();i>s&&(s=i,n=t)}),n}function n(t,i,n,s){var o=e(s).outerHeight(),c=i.sync?i.speed/2:i.speed;i.container.animate({height:o},c)}function s(i,o){o._autoHeightOnResize&&(e(window).off("resize orientationchange",o._autoHeightOnResize),o._autoHeightOnResize=null),o.container.off("featured-slide-added featured-slide-removed",t),o.container.off("featured-destroyed",s),o.container.off("featured-before",n),o._sentinel&&(o._sentinel.remove(),o._sentinel=null)}e.extend(e.fn.featured.defaults,{autoHeight:0}),e(document).on("featured-initialized",function(i,o){function c(){t(i,o)}var r,l=o.autoHeight,a=e.type(l),d=null;("string"===a||"number"===a)&&(o.container.on("featured-slide-added featured-slide-removed",t),o.container.on("featured-destroyed",s),"container"==l?o.container.on("featured-before",n):"string"===a&&/\d+\:\d+/.test(l)&&(r=l.match(/(\d+)\:(\d+)/),r=r[1]/r[2],o._autoHeightRatio=r),"number"!==a&&(o._autoHeightOnResize=function(){clearTimeout(d),d=setTimeout(c,50)},e(window).on("resize orientationchange",o._autoHeightOnResize)),setTimeout(c,30))})}(jQuery),/*! caption plugin for Featured;  version: 20130306 */
function(e){"use strict";e.extend(e.fn.featured.defaults,{caption:"> .featured-caption",captionTemplate:"{{slideNum}} / {{slideCount}}",overlay:"> .featured-overlay",overlayTemplate:"<div>{{title}}</div><div>{{desc}}</div>",captionModule:"caption"}),e(document).on("featured-update-view",function(t,i,n,s){"caption"===i.captionModule&&e.each(["caption","overlay"],function(){var e=this,t=n[e+"Template"],o=i.API.getComponent(e);o.length&&t?(o.html(i.API.tmpl(t,n,i,s)),o.show()):o.hide()})}),e(document).on("featured-destroyed",function(t,i){var n;e.each(["caption","overlay"],function(){var e=this,t=i[e+"Template"];i[e]&&t&&(n=i.API.getComponent("caption"),n.empty())})})}(jQuery),/*! command plugin for Featured;  version: 20130707 */
function(e){"use strict";var t=e.fn.featured;e.fn.featured=function(i){var n,s,o,c=e.makeArray(arguments);return"number"==e.type(i)?this.featured("goto",i):"string"==e.type(i)?this.each(function(){var r;return n=i,o=e(this).data("featured.opts"),void 0===o?(t.log('slideshow must be initialized before sending commands; "'+n+'" ignored'),void 0):(n="goto"==n?"jump":n,s=o.API[n],e.isFunction(s)?(r=e.makeArray(c),r.shift(),s.apply(o.API,r)):(t.log("unknown command: ",n),void 0))}):t.apply(this,arguments)},e.extend(e.fn.featured,t),e.extend(t.API,{next:function(){var e=this.opts();if(!e.busy||e.manualTrump){var t=e.reverse?-1:1;e.allowWrap===!1&&e.currSlide+t>=e.slideCount||(e.API.advanceSlide(t),e.API.trigger("featured-next",[e]).log("featured-next"))}},prev:function(){var e=this.opts();if(!e.busy||e.manualTrump){var t=e.reverse?1:-1;e.allowWrap===!1&&0>e.currSlide+t||(e.API.advanceSlide(t),e.API.trigger("featured-prev",[e]).log("featured-prev"))}},destroy:function(){this.stop();var t=this.opts(),i=e.isFunction(e._data)?e._data:e.noop;clearTimeout(t.timeoutId),t.timeoutId=0,t.API.stop(),t.API.trigger("featured-destroyed",[t]).log("featured-destroyed"),t.container.removeData(),i(t.container[0],"parsedAttrs",!1),t.retainStylesOnDestroy||(t.container.removeAttr("style"),t.slides.removeAttr("style"),t.slides.removeClass(t.slideActiveClass)),t.slides.each(function(){e(this).removeData(),i(this,"parsedAttrs",!1)})},jump:function(e){var t,i=this.opts();if(!i.busy||i.manualTrump){var n=parseInt(e,10);if(isNaN(n)||0>n||n>=i.slides.length)return i.API.log("goto: invalid slide index: "+n),void 0;if(n==i.currSlide)return i.API.log("goto: skipping, already on slide",n),void 0;i.nextSlide=n,clearTimeout(i.timeoutId),i.timeoutId=0,i.API.log("goto: ",n," (zero-index)"),t=i.currSlide<i.nextSlide,i.API.prepareTx(!0,t)}},stop:function(){var t=this.opts(),i=t.container;clearTimeout(t.timeoutId),t.timeoutId=0,t.API.stopTransition(),t.pauseOnHover&&(t.pauseOnHover!==!0&&(i=e(t.pauseOnHover)),i.off("mouseenter mouseleave")),t.API.trigger("featured-stopped",[t]).log("featured-stopped")},reinit:function(){var e=this.opts();e.API.destroy(),e.container.featured()},remove:function(t){for(var i,n,s=this.opts(),o=[],c=1,r=0;s.slides.length>r;r++)i=s.slides[r],r==t?n=i:(o.push(i),e(i).data("featured.opts").slideNum=c,c++);n&&(s.slides=e(o),s.slideCount--,e(n).remove(),t==s.currSlide?s.API.advanceSlide(1):s.currSlide>t?s.currSlide--:s.currSlide++,s.API.trigger("featured-slide-removed",[s,t,n]).log("featured-slide-removed"),s.API.updateView())}}),e(document).on("click.featured","[data-featured-cmd]",function(t){t.preventDefault();var i=e(this),n=i.data("featured-cmd"),s=i.data("featured-context")||".featured-slideshow";e(s).featured(n,i.data("featured-arg"))})}(jQuery),/*! hash plugin for Featured;  version: 20130905 */
function(e){"use strict";function t(t,i){var n;return t._hashFence?(t._hashFence=!1,void 0):(n=window.location.hash.substring(1),t.slides.each(function(s){if(e(this).data("featured-hash")==n){if(i===!0)t.startingSlide=s;else{var o=s>t.currSlide;t.nextSlide=s,t.API.prepareTx(!0,o)}return!1}}),void 0)}e(document).on("featured-pre-initialize",function(i,n){t(n,!0),n._onHashChange=function(){t(n,!1)},e(window).on("hashchange",n._onHashChange)}),e(document).on("featured-update-view",function(e,t,i){i.hash&&"#"+i.hash!=window.location.hash&&(t._hashFence=!0,window.location.hash=i.hash)}),e(document).on("featured-destroyed",function(t,i){i._onHashChange&&e(window).off("hashchange",i._onHashChange)})}(jQuery),/*! loader plugin for Featured;  version: 20130307 */
function(e){"use strict";e.extend(e.fn.featured.defaults,{loader:!1}),e(document).on("featured-bootstrap",function(t,i){function n(t,n){function o(t){var o;"wait"==i.loader?(r.push(t),0===a&&(r.sort(c),s.apply(i.API,[r,n]),i.container.removeClass("featured-loading"))):(o=e(i.slides[i.currSlide]),s.apply(i.API,[t,n]),o.show(),i.container.removeClass("featured-loading"))}function c(e,t){return e.data("index")-t.data("index")}var r=[];if("string"==e.type(t))t=e.trim(t);else if("array"===e.type(t))for(var l=0;t.length>l;l++)t[l]=e(t[l])[0];t=e(t);var a=t.length;a&&(t.hide().appendTo("body").each(function(t){function c(){0===--l&&(--a,o(d))}var l=0,d=e(this),u=d.is("img")?d:d.find("img");return d.data("index",t),u=u.filter(":not(.featured-loader-ignore)").filter(':not([src=""])'),u.length?(l=u.length,u.each(function(){this.complete?c():e(this).load(function(){c()}).error(function(){0===--l&&(i.API.log("slide skipped; img not loaded:",this.src),0===--a&&"wait"==i.loader&&s.apply(i.API,[r,n]))})}),void 0):(--a,r.push(d),void 0)}),a&&i.container.addClass("featured-loading"))}var s;i.loader&&(s=i.API.add,i.API.add=n)})}(jQuery),/*! pager plugin for Featured;  version: 20130525 */
function(e){"use strict";function t(t,i,n){var s,o=t.API.getComponent("pager");o.each(function(){var o=e(this);if(i.pagerTemplate){var c=t.API.tmpl(i.pagerTemplate,i,t,n[0]);s=e(c).appendTo(o)}else s=o.children().eq(t.slideCount-1);s.on(t.pagerEvent,function(e){e.preventDefault(),t.API.page(o,e.currentTarget)})})}function i(e,t){var i=this.opts();if(!i.busy||i.manualTrump){var n=e.children().index(t),s=n,o=s>i.currSlide;i.currSlide!=s&&(i.nextSlide=s,i.API.prepareTx(!0,o),i.API.trigger("featured-pager-activated",[i,e,t]))}}e.extend(e.fn.featured.defaults,{pager:"> .featured-pager",pagerActiveClass:"featured-pager-active",pagerEvent:"click.featured",pagerTemplate:"<span>&bull;</span>"}),e(document).on("featured-bootstrap",function(e,i,n){n.buildPagerLink=t}),e(document).on("featured-slide-added",function(e,t,n,s){t.pager&&(t.API.buildPagerLink(t,n,s),t.API.page=i)}),e(document).on("featured-slide-removed",function(t,i,n){if(i.pager){var s=i.API.getComponent("pager");s.each(function(){var t=e(this);e(t.children()[n]).remove()})}}),e(document).on("featured-update-view",function(t,i){var n;i.pager&&(n=i.API.getComponent("pager"),n.each(function(){e(this).children().removeClass(i.pagerActiveClass).eq(i.currSlide).addClass(i.pagerActiveClass)}))}),e(document).on("featured-destroyed",function(e,t){var i=t.API.getComponent("pager");i&&(i.children().off(t.pagerEvent),t.pagerTemplate&&i.empty())})}(jQuery),/*! prevnext plugin for Featured;  version: 20130709 */
function(e){"use strict";e.extend(e.fn.featured.defaults,{next:"> .featured-next",nextEvent:"click.featured",disabledClass:"disabled",prev:"> .featured-prev",prevEvent:"click.featured",swipe:!1}),e(document).on("featured-initialized",function(e,t){if(t.API.getComponent("next").on(t.nextEvent,function(e){e.preventDefault(),t.API.next()}),t.API.getComponent("prev").on(t.prevEvent,function(e){e.preventDefault(),t.API.prev()}),t.swipe){var i=t.swipeVert?"swipeUp.featured":"swipeLeft.featured swipeleft.featured",n=t.swipeVert?"swipeDown.featured":"swipeRight.featured swiperight.featured";t.container.on(i,function(){t.API.next()}),t.container.on(n,function(){t.API.prev()})}}),e(document).on("featured-update-view",function(e,t){if(!t.allowWrap){var i=t.disabledClass,n=t.API.getComponent("next"),s=t.API.getComponent("prev"),o=t._prevBoundry||0,c=void 0!==t._nextBoundry?t._nextBoundry:t.slideCount-1;t.currSlide==c?n.addClass(i).prop("disabled",!0):n.removeClass(i).prop("disabled",!1),t.currSlide===o?s.addClass(i).prop("disabled",!0):s.removeClass(i).prop("disabled",!1)}}),e(document).on("featured-destroyed",function(e,t){t.API.getComponent("prev").off(t.nextEvent),t.API.getComponent("next").off(t.prevEvent),t.container.off("swipeleft.featured swiperight.featured swipeLeft.featured swipeRight.featured swipeUp.featured swipeDown.featured")})}(jQuery),/*! progressive loader plugin for Featured;  version: 20130315 */
function(e){"use strict";e.extend(e.fn.featured.defaults,{progressive:!1}),e(document).on("featured-pre-initialize",function(t,i){if(i.progressive){var n,s,o=i.API,c=o.next,r=o.prev,l=o.prepareTx,a=e.type(i.progressive);if("array"==a)n=i.progressive;else if(e.isFunction(i.progressive))n=i.progressive(i);else if("string"==a){if(s=e(i.progressive),n=e.trim(s.html()),!n)return;if(/^(\[)/.test(n))try{n=e.parseJSON(n)}catch(d){return o.log("error parsing progressive slides",d),void 0}else n=n.split(RegExp(s.data("featured-split")||"\n")),n[n.length-1]||n.pop()}l&&(o.prepareTx=function(e,t){var s,o;return e||0===n.length?(l.apply(i.API,[e,t]),void 0):(t&&i.currSlide==i.slideCount-1?(o=n[0],n=n.slice(1),i.container.one("featured-slide-added",function(e,t){setTimeout(function(){t.API.advanceSlide(1)},50)}),i.API.add(o)):t||0!==i.currSlide?l.apply(i.API,[e,t]):(s=n.length-1,o=n[s],n=n.slice(0,s),i.container.one("featured-slide-added",function(e,t){setTimeout(function(){t.currSlide=1,t.API.advanceSlide(-1)},50)}),i.API.add(o,!0)),void 0)}),c&&(o.next=function(){var e=this.opts();if(n.length&&e.currSlide==e.slideCount-1){var t=n[0];n=n.slice(1),e.container.one("featured-slide-added",function(e,t){c.apply(t.API),t.container.removeClass("featured-loading")}),e.container.addClass("featured-loading"),e.API.add(t)}else c.apply(e.API)}),r&&(o.prev=function(){var e=this.opts();if(n.length&&0===e.currSlide){var t=n.length-1,i=n[t];n=n.slice(0,t),e.container.one("featured-slide-added",function(e,t){t.currSlide=1,t.API.advanceSlide(-1),t.container.removeClass("featured-loading")}),e.container.addClass("featured-loading"),e.API.add(i,!0)}else r.apply(e.API)})}})}(jQuery),/*! tmpl plugin for Featured;  version: 20121227 */
function(e){"use strict";e.extend(e.fn.featured.defaults,{tmplRegex:"{{((.)?.*?)}}"}),e.extend(e.fn.featured.API,{tmpl:function(t,i){var n=RegExp(i.tmplRegex||e.fn.featured.defaults.tmplRegex,"g"),s=e.makeArray(arguments);return s.shift(),t.replace(n,function(t,i){var n,o,c,r,l=i.split(".");for(n=0;s.length>n;n++)if(c=s[n]){if(l.length>1)for(r=c,o=0;l.length>o;o++)c=r,r=r[l[o]]||i;else r=c[i];if(e.isFunction(r))return r.apply(c,s);if(void 0!==r&&null!==r&&r!=i)return r}return i})}})}(jQuery);


/* Plugin for Featured; Copyright (c) 2012 M. Alsup; v20140114 */
(function(e){"use strict";e.event.special.swipe=e.event.special.swipe||{scrollSupressionThreshold:10,durationThreshold:1e3,horizontalDistanceThreshold:30,verticalDistanceThreshold:75,setup:function(){var t=e(this);t.bind("touchstart",function(i){function n(t){if(r){var i=t.originalEvent.touches?t.originalEvent.touches[0]:t;s={time:(new Date).getTime(),coords:[i.pageX,i.pageY]},Math.abs(r.coords[0]-s.coords[0])>e.event.special.swipe.scrollSupressionThreshold&&t.preventDefault()}}var s,o=i.originalEvent.touches?i.originalEvent.touches[0]:i,r={time:(new Date).getTime(),coords:[o.pageX,o.pageY],origin:e(i.target)};t.bind("touchmove",n).one("touchend",function(){t.unbind("touchmove",n),r&&s&&s.time-r.time<e.event.special.swipe.durationThreshold&&Math.abs(r.coords[0]-s.coords[0])>e.event.special.swipe.horizontalDistanceThreshold&&Math.abs(r.coords[1]-s.coords[1])<e.event.special.swipe.verticalDistanceThreshold&&r.origin.trigger("swipe").trigger(r.coords[0]>s.coords[0]?"swipeleft":"swiperight"),r=s=void 0})})}},e.event.special.swipeleft=e.event.special.swipeleft||{setup:function(){e(this).bind("swipe",e.noop)}},e.event.special.swiperight=e.event.special.swiperight||e.event.special.swipeleft})(jQuery);

// featured slider

(function($) {
	jQuery.fn.featuredSlider=function(args){
		var defaults={
				width		:1000,
				origLw		:500,
				origLh		:400,
				origSw		:250,
				origSh		:200,
				listblock	: 0,
				subtitlehtm	:'h2'
		}
		options=jQuery.extend({},defaults,args);
		return this.each(function(idx,elm){
			var excerpt=jQuery(this).find(".featured_excerpt"),
			sub_excerpt=jQuery(this).find(".featured_slide_sub_excerpt"),
			id=jQuery(this).attr('id'),
			eshortcode=jQuery(this).find('.featured_slider_eshortcode'),
			events_excerpt=jQuery(this).find(".eventwrap"),
			ecom_excerpt=jQuery(this).find(".ecomwrap"),
			self=this,
			sldrH=jQuery(this).height();
			var ht=excerpt.height();
			excerpt.height(0);
			var ecom_ht=ecom_excerpt.height();
			ecom_excerpt.height(0);
			var event_ht = events_excerpt.height();
			events_excerpt.height(0);
			
			jQuery(this).find(".featured_slide_left").hover(
				function(){
					excerpt.stop(true,true).animate({height: ht+"px"}, 400, "easeInSine");
					var contentSpan=excerpt.find(".featured_slide_content_span");
					var content = contentSpan.data('anim');
					if( content != undefined && content != '' ) {
						contentSpan.removeClass("featured-animated featured-"+content); 
						contentSpan.hide();
						setTimeout(function() {
							contentSpan.show();
							contentSpan.addClass("featured-animated featured-"+content);
						}, 1 );
					}
				},
				function(){excerpt.animate({height: "0px"}, 300, "easeOutSine");}
			);
			jQuery(this).find(".featured_slide_left").hover(
				function(){events_excerpt.stop(true,true).animate({height: event_ht+"px", overflow:"visible"}, 400, "easeInSine");},
				function(){events_excerpt.animate({height: "0px", overflow:"hidden"}, 300, "easeOutSine");}
			);
			jQuery(this).find(".featured_slide_left").hover(
				function(){ecom_excerpt.stop(true,true).animate({height: ecom_ht+"px", overflow:"visible"}, 400, "easeInSine");},
				function(){ecom_excerpt.animate({height: "0px", overflow:"hidden"}, 300, "easeOutSine");}
			);
			var ht_sub=sub_excerpt.height();
			sub_excerpt.height(0);
			jQuery(this).find(".featured_slide_sub_right").hover(function(){jQuery(this).find(".featured_slide_sub_excerpt").stop(true,true).animate({height: ht_sub+"px"}, 400, "easeInSine");},
				function(){jQuery(this).find(".featured_slide_sub_excerpt").animate({height: "0px"}, 300, "easeOutSine");});
			this.featuredSliderSize=function(){
				var largeSlide=jQuery(this).find('.featured_slide_left'),
					smallSlideContainer=jQuery(this).find('.featured_slide_right'),
					smallSlide=jQuery(this).find('.featured_slide_sub_right'),
					wrapWidth=jQuery(this).width(),
					ht=0,
					lsW=largeSlide.outerWidth(true);
				if(wrapWidth <= 0 && jQuery(".featured_preview").width() != undefined ) {
					wrapWidth = jQuery(".featured_preview").width();
				}
				if(wrapWidth<=options.origLw){
					largeSlide.css({'float':'none','width':'100%'});
					smallSlideContainer.css({'float':'none','width':'100%'});
					ht=(options.origLh*wrapWidth)/options.origLw; 
					jQuery(this).height(ht*2);
					largeSlide.height(ht);
					smallSlideContainer.height(ht);
					if(options.listblock == 4){	
						smallSlide.height(ht/3);
					}
					else {
						smallSlide.height(ht/2);
					}
				}
				else {
					var lswidth=(options.origLw * 100 / options.width),
					sswidth=(options.origSw * 100 / options.width);
					largeSlide.css({'float':'','width':lswidth+'%'});
					smallSlideContainer.css({'float':'','width':sswidth+'%'});
					ht=(options.origLh*wrapWidth)/options.width;
					jQuery(this).height(ht);
					largeSlide.height(ht);
					smallSlideContainer.height(ht);
					if(options.listblock == 4){	
						smallSlide.height(ht/3);
					}
					else {
						smallSlide.height(ht/2);
					}
				}
				//video css
				if(eshortcode.length > 0)
				{
					if(eshortcode.parents(".featured_slide_left").length > 0)
					{ 	jQuery(this).find(".featured_slide_left .featured_slider_eshortcode").height(ht);
						jQuery(this).find(".featured_slide_left .featured_slider_eshortcode").css("width","100%");
					}
					if(eshortcode.parents(".featured_slide_right").length > 0)
					{
						if(options.listblock == 4){	
							jQuery(this).find(".featured_slide_right .featured_slider_eshortcode").height(ht/3);
						}
						else {
							jQuery(this).find(".featured_slide_right .featured_slider_eshortcode").height(ht/2);
						}
						jQuery(this).find(".featured_slide_right .featured_slider_eshortcode").css("width","100%");
					}
				}
				//Make fonts of side slides smaller
				var featuredRw=options.origSw;
				var rItemW=jQuery(this).find('.featured_slide_sub_right').width();
				var fRw=(featuredRw*35)/100;
				if(rItemW <= fRw)
				{
					jQuery(this).find(".featured_slideri").each(function(idx,el){
						var sideh2=jQuery(el).find(".featured_slide_sub_right .featured_slide_sub_content h"+options.subtitlehtm);
						sideh2.addClass('featuredSmallF');
					});
				}
				else{
					jQuery(this).find(".featured_slideri").each(function(idx,el){
						var sideh2=jQuery(el).find(".featured_slide_sub_right .featured_slide_sub_content h"+options.subtitlehtm);
						sideh2.removeClass('featuredSmallF');
					});
				}
				//--
				
				// Start For Transition
				jQuery(this).on( "featured-before", function( event, optionHash, outgoingSlideEl, incomingSlideEl, forwardFlag ) {
					var imgs = jQuery(incomingSlideEl).find(".featured_slider_thumbnail");
					imgs.each( function(index, elm) {
						var img = jQuery(elm).data('anim');
						if( img != undefined && img != '' ) {
							jQuery(elm).removeClass("featured-animated featured-"+img); 
							setTimeout(function() {
								jQuery(elm).addClass("featured-animated featured-"+img);
							} , 1); 
						}
					});
					var stitles = jQuery(incomingSlideEl).find(".slider_stitle");
					stitles.each( function(index, elm) {
						var stitle = jQuery(elm).data('anim');
						if( stitle != undefined && stitle != '' ) {
							jQuery(elm).removeClass("featured-animated featured-"+stitle); 

							setTimeout(function() {
								jQuery(elm).addClass("featured-animated featured-"+stitle);
							} , 1); 
						}
					});

					var titles = jQuery(incomingSlideEl).find(".slider_htitle");
					titles.each( function(index, elm) {
						var title = jQuery(elm).data('anim');
						if( title != undefined && title != '' ) {
							jQuery(elm).removeClass("featured-animated featured-"+title); 
							setTimeout(function() {
								jQuery(elm).addClass("featured-animated featured-"+title);
							} , 1); 

						}
					});
					// End For Transitions 	
				}); 	// End before 
				/* ----------------------------------------------------
					Code for Iframe Auto Pause when moves to next slide
				---------------------------------------------------- */
				jQuery(this).on( 'featured-after', function(event, optionHash, outgoingSlideEl, incomingSlideEl, forwardFlag) {
					var allIframes = jQuery(outgoingSlideEl).find(".featured_slideri iframe");
					jQuery(allIframes).each(function(index, elm) {
						var iframeHtml, prnt;
						iframeHtml = jQuery(this)[0].outerHTML.replace(/<iframe/,"<iframe").replace("</iframe>","</iframe>");
						prnt = jQuery(elm).parent();
						jQuery(elm).remove();
						prnt.html(iframeHtml);			
					});
					/* ----------------------------------------------------
					END - Code for Iframe fix
					---------------------------------------------------- */
				}); 
				//var self=this;
				jQuery(this).find(".featured_slideri iframe").on("click", function(e){
					var allIframes = jQuery(self).find(".featured_slideri iframe").not(this);
					jQuery(allIframes).each(function(index, elm) {
						var iframeHtml, prnt;
						iframeHtml = jQuery(this)[0].outerHTML.replace(/<iframe/,"<iframe").replace("</iframe>","</iframe>");
						prnt = jQuery(elm).parent();
						jQuery(elm).remove();
						prnt.html(iframeHtml);			
					});
				});
				
				
				//--
				return this;
			};
			this.featuredSliderSize();
			//On Window Resize
			jQuery(window).resize(function() { 
				self.featuredSliderSize();
			});
		});		
	}
})(jQuery);

// featured slider blocks

(function($) {
	jQuery.fn.featuredBlock=function(args){
		var defaults={
				width		:1000,
				origLw		:500,
				origLh		:400,
				origSw		:250,
				origSh		:200,
				listblock	: 0,
				subtitlehtm	:'h2'
		}
		options=jQuery.extend({},defaults,args);

		return this.each(function(idx,elm){
			var excerpt=jQuery(this).find(".featured_excerpt"),
			sub_excerpt=jQuery(this).find(".featured_slide_sub_excerpt"),
			id=jQuery(this).attr('id'),
			eshortcode=jQuery(this).find('.featured_slider_eshortcode'),
			events_excerpt=jQuery(this).find(".eventwrap"),
			ecom_excerpt=jQuery(this).find(".ecomwrap"),
			self=this,
			sldrH=jQuery(this).height();
			var ht=excerpt.height();
			excerpt.height(0);
			var ecom_ht=ecom_excerpt.height();
			ecom_excerpt.height(0);
			var event_ht = events_excerpt.height();
			events_excerpt.height(0);
			
			jQuery(this).find(".featured_slide_left").hover(
				function(){excerpt.stop(true,true).animate({height: ht+"px"}, 400, "easeInSine");},
				function(){excerpt.animate({height: "0px"}, 300, "easeOutSine");}
			);
			jQuery(this).find(".featured_slide_left").hover(
				function(){events_excerpt.stop(true,true).animate({height: event_ht+"px", overflow:"visible"}, 400, "easeInSine");},
				function(){events_excerpt.animate({height: "0px", overflow:"hidden"}, 300, "easeOutSine");}
			);
			jQuery(this).find(".featured_slide_left").hover(
				function(){ecom_excerpt.stop(true,true).animate({height: ecom_ht+"px", overflow:"visible"}, 400, "easeInSine");},
				function(){ecom_excerpt.animate({height: "0px", overflow:"hidden"}, 300, "easeOutSine");}
			);
			var ht_sub=sub_excerpt.height();
			sub_excerpt.height(0);
			jQuery(this).find(".featured_slide_sub_right").hover(function(){jQuery(this).find(".featured_slide_sub_excerpt").stop(true,true).animate({height: ht_sub+"px"}, 400, "easeInSine");},
				function(){jQuery(this).find(".featured_slide_sub_excerpt").animate({height: "0px"}, 300, "easeOutSine");});
			this.featuredSliderSize=function(){
				var largeSlide=jQuery(this).find('.featured_slide_left'),
					smallSlideContainer=jQuery(this).find('.featured_slide_right'),
					smallSlide=jQuery(this).find('.featured_slide_sub_right'),
					wrapWidth=jQuery(this).width(),
					ht=0,
					lsW=largeSlide.outerWidth(true);
				if(wrapWidth <= 0 && jQuery(".featured_preview").width() != undefined ) {
					wrapWidth = jQuery(".featured_preview").width();
				}
				if(wrapWidth<=options.origLw){
					largeSlide.css({'float':'none','width':'100%'});
					smallSlideContainer.css({'float':'none','width':'100%'});
					ht=(options.origLh*wrapWidth)/options.origLw; 
					jQuery(this).height(ht*2);
					largeSlide.height(ht);
					smallSlideContainer.height(ht);
					if(options.listblock == 4){	
						smallSlide.height(ht/3);
					}
					else {
						smallSlide.height(ht/2);
					}
				}
				else{
					var lswidth=(options.origLw * 100 / options.width),
					sswidth=(options.origSw * 100 / options.width);
					largeSlide.css({'float':'','width':lswidth+'%'});
					smallSlideContainer.css({'float':'','width':sswidth+'%'});
					ht=(options.origLh*wrapWidth)/options.width;
					jQuery(this).height(ht);
					largeSlide.height(ht);
					smallSlideContainer.height(ht);
					if(options.listblock == 4){	
						smallSlide.height(ht/3);
					}
					else {
						smallSlide.height(ht/2);
					}
				}
				//video css
				if(eshortcode.length > 0)
				{
					if(eshortcode.parents(".featured_slide_left").length > 0)
					{ 	jQuery(this).find(".featured_slide_left .featured_slider_eshortcode").height(ht);
						jQuery(this).find(".featured_slide_left .featured_slider_eshortcode").css("width","100%");
					}
					if(eshortcode.parents(".featured_slide_right").length > 0)
					{
						if(options.listblock == 4){	
							jQuery(this).find(".featured_slide_right .featured_slider_eshortcode").height(ht/3);
						}
						else {
							jQuery(this).find(".featured_slide_right .featured_slider_eshortcode").height(ht/2);
						}
						jQuery(this).find(".featured_slide_right .featured_slider_eshortcode").css("width","100%");
					}
				}
				//Make fonts of side slides smaller
				var featuredRw=options.origSw;
				var rItemW=jQuery(this).find('.featured_slide_sub_right').width();
				var fRw=(featuredRw*35)/100;
				if(rItemW <= fRw)
				{
					jQuery(this).find(".featured_slideri").each(function(idx,el){
						var sideh2=jQuery(el).find(".featured_slide_sub_right .featured_slide_sub_content h"+options.subtitlehtm);
						sideh2.addClass('featuredSmallF');
					});
				}
				else{
					jQuery(this).find(".featured_slideri").each(function(idx,el){
						var sideh2=jQuery(el).find(".featured_slide_sub_right .featured_slide_sub_content  h"+options.subtitlehtm);
						sideh2.removeClass('featuredSmallF');
					});
				}
				return this;
			};
			this.featuredSliderSize();
			//On Window Resize
			jQuery(window).resize(function() { 
				self.featuredSliderSize();
			});
		});		
	}
})(jQuery);
