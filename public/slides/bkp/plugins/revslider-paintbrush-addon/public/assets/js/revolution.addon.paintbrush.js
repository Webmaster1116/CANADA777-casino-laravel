/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

;(function() {
	
	jQuery('li[data-revaddonpaintbrush]').each(function() {
		
		var $this = jQuery(this);
		$this.children('img').attr({'data-bgposition': 'center center', 'data-kenburns': 'off'})
							 .data({bgposition: 'center center', kenburns: 'off'});
			
		if($this.attr('data-revaddonpaintbrushedges')) $this.attr('data-transition', 'fade').data('transition', 'fade');
		
	});
	
	jQuery('li[data-revaddonpaintbrushfallback]').each(function() {
		
		var $this = jQuery(this),
			img = $this.children('img'),
			fallback = $this.attr('data-revaddonpaintbrushfallback'),
			lazyload = img.attr('data-lazyload'),
			attr = lazyload ? 'data-lazyload' : 'src';

		img.attr(attr, fallback);
		if(lazyload) img.data('lazyload', fallback);
		
	});
	
	var $,
		touch = 'ontouchend' in document;
		
	window.RevSliderPaintBrush = function(_$, api) {
		
		$ = _$;
		if(!$) return;
		
		api.on('revolution.slide.onloaded', function() {
			
			var css = '',
				options = api[0].opt,
				levels = options.responsiveLevels,
				widths = options.gridwidth;
			
			if(!Array.isArray(levels)) levels = [levels];
			if(!Array.isArray(widths)) widths = [widths];
			
			api.find('.tp-revslider-slidesli[data-revaddonpaintbrush]').each(function() {
				
				var clas,
					edgeFix,
					fixEdges,
					scaleBlur,
					img = new Image(),
					$this = $(this).addClass('revaddon-paintbrush'),
					index = $this.attr('data-index'),
					slot = $this.find('.slotholder'),
					options = JSON.parse(this.getAttribute('data-revaddonpaintbrush'));
				
				if(options.blur) {
					
					clas = 'revaddonblurfilter_' + index;
					
					if(!options.scaleblur) {
						css += '.' + clas + ' .tp-bgimg, .' + clas + ' .slot {filter: blur(' + options.blurAmount + 'px);}';
					}
					else {
						scaleBlur = clas;
					}
					
					$this.addClass(clas);
					
				}
				
				if(options.fixedges && options.edgefix) {
					
					edgeFix = 1 + (options.edgefix * 0.01);
					fixEdges = edgeFix.toFixed(2);
					fixEdges = 'scale(' + fixEdges + ', ' + fixEdges + ')';
					slot.find('.tp-bgimg').css('transform', fixEdges);
					
					clas = 'revaddonblurfilterfix_' + index;
					css += '.' + clas + ' .tp-bgimg {transform: ' + fixEdges + ' !important}';
					$this.addClass(clas);
					
				}

				img.onload = function() {
					
					options.width = this.naturalWidth;
					options.height = this.naturalHeight;
					
					var brush = new Brush(api, options, $this, img, slot[0], levels, widths, fixEdges, edgeFix, scaleBlur);
					$this.data('revaddonbrush', brush);
					
					if(!brush.pause && brush.ready) {
						
						brush.pause = false;
						if(!brush.inited) brush.init();
						
					}
					
				};
				
				img.onerror = function() {
					
					console.log('PaintBrush Addon: background image could not be loaded');
					
				};

				img.src = options.image;
				
			});
			
			if(css) {
				
				var style = document.createElement('style');
				style.type = 'text/css';
				style.innerHTML = css;
				document.head.appendChild(style);
				
			}
			
		}).on('revolution.slide.onbeforeswap', function(e, data) {
			
			var brush = data.currentslide.data('revaddonbrush');
			if(brush && brush.canvas) brush.canvas.className = 'revaddonpaintbrush swapping';
			
		}).on('revolution.slide.onafterswap', function(e, data) {
		
			var brush;
			if(data.prevslide) {
				
				brush = data.prevslide.data('revaddonbrush');
				if(brush) {
					
					brush.pause = true;
					brush.reset();
					if(brush.canvas) brush.canvas.className = 'revaddonpaintbrush';
					
				}
				
			}
			
			brush = data.currentslide.data('revaddonbrush');
			if(!brush) return;
			
			brush.pause = false;
			brush.ready = true;
			if(!brush.inited) brush.init();
			
		});
		
	};
	
	function Brush(api, options, slide, img, slot, levels, widths, fixEdges, edgeFix, scaleBlur) {
		
		this.pause = true;
		this.options = options;
		this.slide = slide;
		this.img = img;
		this.slot = slot;
		this.levels = levels;
		this.widths = widths;
		this.slider = api;
		this.fixEdges = fixEdges;
		this.edgeFix = edgeFix;
		
		if(scaleBlur) {
			
			var style = document.createElement('style');
			style.type = 'text/css';
			document.head.appendChild(style);
			
			this.blurstyle = {sheet: style, css: '.' + scaleBlur + ' .tp-bgimg, .' + scaleBlur + ' .slot {filter: blur({{blur}}px);}'};
			this.resizeBlur();
			
			api.on('revolution.slide.afterdraw', this.blurSizer.bind(this));
			
		}
		
	}
	
	Brush.prototype = {
		
		init: function() {

			this.canvas = document.createElement('canvas');
			this.brush = document.createElement('canvas');
			this.canvas.className = 'revaddonpaintbrush';
			
			this.context = this.canvas.getContext('2d');
			this.ctx = this.brush.getContext('2d');
			
			this.slot.parentNode.insertBefore(this.canvas, this.slot.nextSibling);
			this.inited = true;
			this.steps = [];
			
			if(!this.options.carousel) this.start();
			else setTimeout(this.start.bind(this), 100);
			
		},
		
		start: function() {
			
			if(!this.options.carousel) this.slider.on('mousemove touchmove', this.onMove.bind(this));
			else this.slide.on('mousemove touchmove', this.onMove.bind(this));
			
			this.slider.on('revolution.slide.afterdraw', this.sizer.bind(this));
			this.resize();
			
		},
		
		onMove: function(e) {
			
			if(this.pause) return;
			if(touch) {
				
				e = e.originalEvent;
				e.preventDefault();
				if(e.touches) e = e.touches[0];
				
			}
			
			var rect = this.canvas.getBoundingClientRect();
			this.steps.unshift({time: Date.now(), x: e.clientX - rect.left, y: e.clientY - rect.top});
			this.draw();
			
		},
		
		updateSteps: function() {
			
			var time = Date.now();
			for(var i = 0; i < this.steps.length; i++) {
				
				if(time - this.steps[i].time > this.options.fade) this.steps.length = i;
				
			}
			
		},
		
		paint: function() {
			
			var total = this.steps.length,
				time = Date.now(),
				alpha,
				dif;

			for(var i = 1; i < total; i++) {
				
				dif = (time - this.steps[i].time) / this.options.fadetime;
				alpha = Math.max(1 - dif, 0);

				this.ctx.lineCap = this.options.style;
				this.ctx.strokeStyle = 'rgba(0, 0, 0, ' + alpha + ')';
				this.ctx.shadowBlur = this.options.strength;
				this.ctx.shadowColor = '#000000';
				this.ctx.lineWidth = this.options.size;
				
				this.ctx.beginPath();
				this.ctx.moveTo(this.steps[i - 1].x, this.steps[i - 1].y);
				this.ctx.lineTo(this.steps[i].x, this.steps[i].y);
				this.ctx.stroke();
				
			}
			
		},
		
		draw: function() {
			
			this.updateSteps();
			
			cancelAnimationFrame(this.frame);
			if(this.steps.length) this.frame = window.requestAnimationFrame(this.draw.bind(this));
			
			this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
			if(this.options.disappear) this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
			
			this.paint();
			
			this.context.drawImage(this.img, this.cx, this.cy, this.cw, this.ch, 0, 0, this.canvas.width, this.canvas.height);
			this.context.globalCompositeOperation = 'destination-in';
			
			this.context.drawImage(this.brush, 0, 0);
			this.context.globalCompositeOperation = 'source-over';
			
		},
		
		reset: function() {
			
			if(this.context) {
				
				cancelAnimationFrame(this.frame);
				this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
				this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
				
			}
			
		},
		
		sizer: function() {
			
			if(!this.options.carousel) {
				
				this.resize();
				
			}
			else {
				
				clearTimeout(this.timer);
				this.timer = setTimeout(this.resize.bind(this), 250);
				
			}
			
		},
		
		resize: function(getPerc) {
			
			if(!getPerc) this.reset();
			
			var w = this.slide.width(),
				h = this.slide.height();
				
			if(this.edgeFix) {
				
				w *= this.edgeFix;
				h *= this.edgeFix;
				
			}
			
			var perc = Math.min(w / this.options.width, h / this.options.height);
			if(getPerc) return perc;
			
			var wid = this.options.width * perc,
				high = this.options.height * perc,
				ratio = 1;
	  
			if(wid < w) ratio = w / wid;                             
			if(Math.abs(ratio - 1) < 1e-14 && high < h) ratio = h / high;

			this.cw = this.options.width / ((wid * ratio) / w);
			this.ch = this.options.height / ((high * ratio) / h);
			this.cx = (this.options.width - this.cw) * 0.5;
			this.cy = (this.options.height - this.ch) * 0.5;
			
			this.canvas.width = this.brush.width = w;
			this.canvas.height = this.brush.height = h;
			
			if(this.options.responsive) {
				
				var len = this.levels.length,
					level = 0;
				
				for(var i = 0; i < len; i++) {

					if(w < this.levels[i]) level = i;
					
				}
				
				var scale = Math.min(w / this.widths[level], 1);
				this.options.size = this.options.origsize * scale;
				
			}
			
		},
		
		blurSizer: function() {
			
			if(!this.options.carousel) {
				
				this.resizeBlur();
				
			}
			else {
				
				clearTimeout(this.blurTimer);
				this.blurTimer = setTimeout(this.resizeBlur.bind(this), 250);
				
			}
		
		},
		
		resizeBlur: function() {
			
			var blurstyle = this.blurstyle;	
			blurstyle.sheet.innerHTML = blurstyle.css.replace('{{blur}}', Math.max(Math.round(this.options.blurAmount * this.resize(true)), 1));
			
		}
		
	};
	
})();






























