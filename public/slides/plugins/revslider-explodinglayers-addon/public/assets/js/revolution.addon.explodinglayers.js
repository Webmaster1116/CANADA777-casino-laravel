/*
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

;(function() {

	"use strict";

	if(typeof jQuery === 'undefined') return;
	function setAttributes() {

		var $this = jQuery(this),
			frams = $this.attr('data-frames'),
			hover = frams.split('{"frame":"hover"'),
			transition = JSON.parse(frams);

		hover = hover.length > 1 ? ',{"frame":"hover"' + hover[1].replace(']', '') : '';
		if(!transition || !transition.length || transition.length < 2) return;

		var start = transition[0],
			end = transition[1];

		start = start.hasOwnProperty('delay') ? start.delay : 1000;
		end = end.hasOwnProperty('delay') ? end.delay : 1000;

		var anime = '[{"delay":"' + start + '","speed":1000,"frame":"0","to":"o:1;","ease":"Power2.easeOut"},{"delay":"' + end + '","speed":1000,"frame":"999","ease":"Power2.easeOut"}' + hover + ']';
		if($this.attr('data-explodinglayersin')) $this.addClass('revaddonexplayerhide');
		$this.addClass('revaddonexplayer').attr('data-frames', anime).data('frames', JSON.parse(anime));

	}

	jQuery('.tp-caption[data-explodinglayersin]', '.tp-caption[data-explodinglayersin]').each(function() {setAttributes.call(this);});

	window.ExplodingLayersAddOn = function($, slider) {

		if(!$ || !slider) return;

		var sizer,
			levels,
			win = $(window),
			numerals = {'padding': 0, 'size': 1, 'speed': 0, 'density': 1, 'power': 0, 'duration': 300};

		slider.find('.tp-caption[data-explodinglayersin]', '.tp-caption[data-explodinglayersin]').each(function() {
			if(this.className.search('revaddonexplayer') === -1) setAttributes.call(this);
		});

		slider.on('revolution.slide.onloaded', function() {

			levels = slider[0].opt.responsiveLevels;
			if(levels) {

				if(!Array.isArray(levels)) levels = [levels];
				while(levels.length < 4) levels[levels.length] = levels[levels.length - 1];
				for(var i = 0; i < 4; i++) levels[i] = parseInt(levels[i], 10);

			}

			slider.find('.tp-caption[data-explodinglayersin]').each(function() {

				var options = this.getAttribute('data-explodinglayersin');
				if(!options) return;

				options = JSON.parse(options);
				if(options) setOptions.apply(this, ['in', options]);

			});

			slider.find('.tp-caption[data-explodinglayersout]').each(function() {

				var options = this.getAttribute('data-explodinglayersout');
				if(!options) return;

				options = JSON.parse(options);
				if(options) setOptions.apply(this, ['out', options]);

			});

			win.on('resize', function() {

				clearTimeout(sizer);
				sizer = setTimeout(onResize, 50);

			});

		}).on('revolution.slide.onbeforeswap revolution.slide.onafterswap', function(e, data) {

			if(e.namespace.search('before') !== -1) {
				if(data.nextslide && data.nextslide.length) resetEffect(data.nextslide);
			}
			else {
				if(data.prevslide && data.prevslide.length) resetEffect(data.prevslide);
			}

		}).on('revolution.slide.layeraction', function(e, data) {

			var explode,
				animation,
				effect = data.layer.data('revaddonexpeffect'),
				isStatic = data.layer.hasClass('tp-static-layer'),
				isSpecial = isStatic && !data.layer.hasClass('revaddonexpstatic');

			if(!effect) return;
			if(data.eventtype === 'enterstage' || isSpecial) {

				if(isStatic) data.layer.addClass('revaddonexpstatic');
				animation = data.layer.data('revaddonexplayerin');
				explode = false;

			}
			else if(data.eventtype === 'leavestage') {

				animation = data.layer.data('revaddonexplayerout');
				explode = true;

			}

			if(animation) {

				animation.options = $.extend({}, animation.orig);
				effect.o = animation.options;
				processOptions(animation.orig, animation.options, effect, explode);

			}

		});

		function onResizeReset() {

			resetEffect($(this), true);

		}

		function onResize() {

			slider.find('.tp-revslider-slidesli').each(onResizeReset);

		}

		function resetEffect(slide, resize) {

			slide.find('.tp-caption[data-explodinglayersin], .tp-caption[data-explodinglayersout]').each(function() {

				var $this = $(this),
					effect = $this.data('revaddonexpeffect');

				if(!effect) return;
				resize = resize && slide.attr('class').search(/processing-revslide|active-revslide/) !== -1;

				var method = !resize ? 'addClass' : effect.disintegrating ? 'addClass' : 'removeClass';
				effect.reset(resize);

				if($this.attr('data-explodinglayersin')) $this[method]('revaddonexplayerhide');

			});

		}

		function setOptions(direction, options) {

			for(var prop in options) {

				if(!options.hasOwnProperty(prop)) continue;
				options[prop.replace('_' + direction, '')] = options[prop];
				delete options[prop];

			}

			var $this = $(this);
			options.effect = $this.data('revaddonexpeffect') || new RevAddonExpBtn(this, options);

			var obj = {revaddonexpeffect: options.effect},
				orig = $.extend({}, options);

			obj['revaddonexplayer' + direction] = {orig: orig, options: options, direction: direction};
			$this.data(obj);

		}

		function checkValue(prop, value) {

			if(numerals.hasOwnProperty(prop)) {

				value = Math.max(parseFloat(value), numerals[prop]);

			}
			else if(prop === 'easing') {

				var val = value.split('.');
				if(val.length === 2) value = punchgs[val[0]][val[1]];
				else value = punchgs.hasOwnProperty(value) ? punchgs[value] : punchgs.Power3.easeOut;

			}

			return value;

		}

		function getValue(prop, val, level) {

			if(!val) return false;
			if(level === 0) return checkValue(prop, val[level]);

			var minus = level,
				value = val[level];

			while(value === 'inherit') {

				minus--;
				if(minus > -1) value = val[minus];
				else value = val[0];

			}

			return checkValue(prop, value);

		}

		function checkRandom(tpe, options, val) {

			if(options['random' + tpe] === 'on') {

				var min = Math.max(Math.round(val * 0.5), 1),
					max = Math.round(val * 2);

				options[tpe] = function() {

					return Math.floor(Math.random() * max) + min;

				};

			}

		}

		function processOptions(orig, options, effect, explode) {

			var prev,
				levl,
				level = 0,
				width = win.width();

			if(levels) {

				var len = levels.length;
				for(var i = 0; i < len; i++) {

					levl = levels[i];
					if(prev === levl) continue;
					if(width < levl) level = i;
					prev = levl;

				}

			}

			for(var prop in orig) {

				if(!orig.hasOwnProperty(prop) || prop === 'effect') continue;
				options[prop] = getValue(prop, options[prop], level);

			}

			checkRandom('size', options, options.size);
			checkRandom('speed', options, options.speed);
			options.sync = options.sync === 'on';

			var color = processColor(options.color),
				fill,
				defs;

			if(!color[1]) {

				fill = color[0];
				defs = '';

			}
			else {

				var gradient = drawFill(color);
				fill = gradient[0];
				defs = gradient[1];

			}

			var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">' + defs,
				tagStart,
				tagEnd;

			if(options.type !== 'circle') {

				tagStart = '<path ';
				tagEnd = '></path>';

			}
			else {

				tagStart = '<circle cx="12" cy="12" r="12" ';
				tagEnd = ' />';

			}

			if(options.style === 'fill') {
				svg += tagStart + 'fill="' + fill + '" d="' + options.type + '"' + tagEnd;
			}
			else {
				svg += tagStart + 'fill="transparent" d="' + options.type + '" stroke="' + fill + '" stroke-width="1"' + tagEnd;
			}

			svg += '</svg>';

			var img = new Image(),
				url = 'data:image/svg+xml;base64,' + btoa(svg),
				canvas = document.createElement('canvas'),
				ctx = canvas.getContext('2d');

			canvas.width = canvas.height = 24;
			img.onload = function() {

				ctx.drawImage(this, 0, 0);
				options.type = ctx.canvas;
				effect.run(explode);

			};

			img.src = url;

		}

	};

	/*
		COLORS PROCESSING
	*/
	function sanitizeGradient(obj) {

		var colors = obj.colors,
			len = colors.length,
			ar = [],
			prev;


		for(var i = 0; i < len; i++) {

			var cur = colors[i];
			delete cur.align;

			if(prev) {
				if(JSON.stringify(cur) !== JSON.stringify(prev)) ar[ar.length] = cur;
			}
			else {
				ar[ar.length] = cur;
			}

			prev = cur;

		}

		obj.colors = ar;
		return obj;

	}

	function processColor(clr) {

		if(clr.trim() === 'transparent') {

			return ['#ffffff', false];

		}
		else if(clr.search(/\[\{/) !== -1) {

			try {
				clr = JSON.parse(clr.replace(/\&/g, '"'));
				clr = sanitizeGradient(clr);
				return [clr, true];
			}
			catch(e) {
				return ['#ffffff', false];
			}

		}
		else if(clr.search('#') !== -1) {
			return [clr, false];
		}
		else if(clr.search('rgba') !== -1) {
			return [clr.replace(/\s/g, '').replace(/false/g, '1'), false];
		}
		else if(clr.search('rgb') !== -1) {
			return [clr.replace(/\s/g, ''), false];
		}
		else {
			return /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(clr) ? [clr, false] : ['#ffffff', false];
		}

	}

	function radialGradient(colors) {

		var len = colors.length,
			gradient,
			color;

		var id = 'explodinglayers' + Math.floor(Math.random() * 10000),
			st = '<defs><radialGradient id="' + id + '">',
			pos;

		for(var i = 0; i < len; i++) {

			color = colors[i];
			pos = parseInt(color.position, 10);
			st += '<stop offset="' + pos + '%" style="stop-color: rgb(' + color.r + ',' + color.g + ',' + color.b + '); stop-opacity: ' + color.a + '" />';

		}

		st += '</radialGradient></defs>';
		gradient = ['url(#' + id + ')', st];

		return gradient;

	}

	function linearGradient(colors, angle) {

		angle = parseInt(angle, 10) / 180 * Math.PI;

		var segment = Math.floor(angle / Math.PI * 2) + 2,
			diagonal =  (1/2 * segment + 1/4) * Math.PI,
			op = Math.cos(Math.abs(diagonal - angle)) * Math.sqrt(2),
			x = op * Math.cos(angle),
			y = op * Math.sin(angle);

		var points = [x < 0 ? 1 : 0, y < 0 ? 1 : 0, x >= 0 ? x : x + 1, y >= 0 ? y : y + 1],
			len = colors.length,
			gradient,
			color,
			pos,
			i;

		var id = 'explodinglayers' + Math.floor(Math.random() * 10000),
			st = '<defs><linearGradient id="' + id + '" x1="' + points[0] + '" y1="' + points[1] + '" x2="' + points[2] + '" y2="' + points[3] + '">';

		for(i = 0; i < len; i++) {

			color = colors[i];
			pos = parseInt(color.position, 10);
			st += '<stop offset="' + pos + '%" style="stop-color: rgb(' + color.r + ',' + color.g + ',' + color.b + '); stop-opacity: ' + color.a + '" />';

		}

		st += '</linearGradient></defs>';
		gradient = ['url(#' + id + ')', st];

		return gradient;

	}

	function drawFill(color) {

		 if(color[1]) {

			 color = color[0];
			 if(color.type === 'radial') return radialGradient(color.colors);
			 else return linearGradient(color.colors, color.angle);

		 }
		 else {
			 return color[0];
		 }

	  }

	/* ******************** */
	/* begin particle magic */
	/* ******************** */
    function RevAddonExpBtn(el, options) {

        this.el = el;
        this.o = options;
        this.init();

    }

    RevAddonExpBtn.prototype = {
        init: function () {
            this.particles = [];
            this.frame = null;
            this.canvas = document.createElement('canvas');
            this.ctx = this.canvas.getContext('2d');
            this.canvas.className = 'revaddon-explayer-canvas';
            this.canvas.style = 'display:none;';
            this.wrapper = document.createElement('div');
            this.wrapper.className = 'revaddon-explayer-wrapper';
            this.el.parentNode.insertBefore(this.wrapper, this.el);
            this.wrapper.appendChild(this.el);
            this.parentWrapper = document.createElement('div');
            this.parentWrapper.className = 'revaddon-explayer';
            this.wrapper.parentNode.insertBefore(this.parentWrapper, this.wrapper);
            this.parentWrapper.appendChild(this.wrapper);
            this.parentWrapper.appendChild(this.canvas);
        },
        loop: function () {
            this.updateRevAddonExpBtn();
            this.renderRevAddonExpBtn();
            if (this.isAnimating()) {
                this.frame = requestAnimationFrame(this.loop.bind(this));
            }
        },
        updateRevAddonExpBtn: function () {

            var p;
            for (var i = 0; i < this.particles.length; i++) {
                p = this.particles[i];
                if (p.life > p.death) {

					if(this.total === false) this.total = this.particles.length;
                    this.particles.splice(i, 1);
					if(this.o.sync) this.updateTransform(this.particles.length);

                } else {
                    p.x += p.speed;
                    p.y = this.o.power * Math.sin(p.counter * p.increase);
                    p.life++;
                    p.counter += this.disintegrating ? 1 : -1;
                }
            }
            if (!this.particles.length) {
                this.pause();
                this.canvas.style.display = 'none';
            }
        },
        renderRevAddonExpBtn: function () {
            this.ctx.clearRect(0, 0, this.width, this.height);
            var p;
            for (var i = 0; i < this.particles.length; i++) {
                p = this.particles[i];
                if (p.life < p.death) {
                    this.ctx.translate(p.startX, p.startY);
                    this.ctx.rotate(p.angle * Math.PI / 180);
                    this.ctx.globalAlpha = this.disintegrating ? 1 - p.life / p.death : p.life / p.death;
					this.ctx.drawImage(this.o.type, Math.round(p.x), Math.round(p.y), Math.round(p.size), Math.round(p.size));
                    this.ctx.globalAlpha = 1;
                    this.ctx.rotate(-p.angle * Math.PI / 180);
                    this.ctx.translate(-p.startX, -p.startY);
                }
            }
        },
        play: function () {
            this.frame = requestAnimationFrame(this.loop.bind(this));
        },
        pause: function () {
            cancelAnimationFrame(this.frame);
			this.ctx.clearRect(0, 0, this.width, this.height);
            this.frame = null;
        },
        addParticle: function (options) {
            var frames = this.o.duration * 60 / 1000;
            var speed = is.fnc(this.o.speed) ? this.o.speed() : this.o.speed;
            this.particles.push({
                startX: options.x,
                startY: options.y,
                x: this.disintegrating ? 0 : speed * -frames,
                y: 0,
                angle: rand(360),
                counter: this.disintegrating ? 0 : frames,
                increase: Math.PI * 2 / 100,
                life: 0,
                death: this.disintegrating ? (frames - 20) + Math.random() * 40 : frames,
                speed: speed,
                size: is.fnc(this.o.size) ? this.o.size() : this.o.size
            });
        },
        addRevAddonExpBtn: function (rect, progress) {
            var progressDiff = this.disintegrating ? progress - this.lastProgress : this.lastProgress - progress;
            this.lastProgress = progress;
            var x = this.o.padding;
            var y = this.o.padding;
            var progressValue = (this.isHorizontal() ? rect.width : rect.height) * progress + progressDiff * (this.disintegrating ? 100 : this.o.duration);
            if (this.isHorizontal()) {
                x += this.o.direction === 'left' ? progressValue : rect.width - progressValue;
            } else {
                y += this.o.direction === 'top' ? progressValue : rect.height - progressValue;
            }
            var i = Math.floor(this.o.density * (progressDiff * 100 + 1));
            if (i > 0) {
                while (i--) {
                    this.addParticle({
                        x: x + (this.isHorizontal() ? 0 : rect.width * Math.random()),
                        y: y + (this.isHorizontal() ? rect.height * Math.random() : 0)
                    });
                }
            }
            if (!this.isAnimating()) {
                this.canvas.style.display = 'block';
                this.play();
            }
        },

        addTransforms: function (value) {

            var translateProperty = this.isHorizontal() ? 'translateX' : 'translateY';
            var translateValue = this.o.direction === 'left' || this.o.direction === 'top' ? value : -value;
            this.wrapper.style[transformString] = translateProperty + '('+ translateValue +'%)';
            this.el.style[transformString] = translateProperty + '('+ -translateValue +'%)';

			if(!this.changed) {

				this.el.className = this.el.className.replace('revaddonexplayerhide', '');
				this.wrapper.style.visibility = 'visible';
				this.changed = true;

			}

        },

		updateTransform: function(num) {

			var value = (num / this.total) * 100;
			this.addTransforms(value);

		},

		update: function() {

			var value;
			if(this.disintegrating) {

				value = this.tween.value;
				this.addTransforms(value);

			}
			else {

				value = 100 - this.tween.value;
				if(!this.o.sync) {

					var _ = this;
					this.timers[this.timers.length] = setTimeout(function() {

						_.addTransforms(value);

					}, this.o.duration);

				}

			}

			this.addRevAddonExpBtn(this.rect, value / 100);

		},

		run: function(explode) {

			this.reset();
			this.disintegrating = explode;
			this.lastProgress = explode ? 0 : 1;
			this.rect = this.el.getBoundingClientRect();
			this.width = this.canvas.width = this.o.width || this.rect.width + this.o.padding * 2;
			this.height = this.canvas.height = this.o.height || this.rect.height + this.o.padding * 2;
			this.changed = false;
			this.timers = [];
			this.animate(this.update.bind(this));

		},

		setDisplay: function(resize) {

			this.canvas.style.display = 'none';
			this.wrapper.style.visibility = !resize ? 'hidden' : 'visible';
			this.wrapper.style[transformString] = 'none';
			this.el.style[transformString] = 'none';

		},
		reset: function(resize) {

			this.pause();
			this.particles = [];
			this.total = false;

			if(this.tween) {

				punchgs.TweenLite.killTweensOf(this.tween);
				delete this.tween;

			}
			if(this.timers) {

				while(this.timers.length) {

					clearTimeout(this.timers[0]);
					this.timers.shift();

				}

				delete this.timers;

			}

			if(!resize) {
				this.setDisplay();
			}
			else {
				var _ = this;
				requestAnimationFrame(function() {_.setDisplay(true);});
			}

		},
        animate: function (update) {

            var _ = this;
			this.tween = {value: 0};

			return punchgs.TweenLite.to(

				this.tween,
				this.o.duration * 0.001,
				{
					value: 100,
					ease: this.o.easing,
					onUpdate: update,
					onComplete: function() {
						if (_.disintegrating) {
							_.wrapper.style.visibility = 'hidden';
						}
					}

				}

			);

        },
        isAnimating: function () {
            return !!this.frame;
        },
        isHorizontal: function () {
            return this.o.direction === 'left' || this.o.direction === 'right';
        }
    };


    // Utils

    var is = {
        arr: function (a) { return Array.isArray(a); },
        str: function (a) { return typeof a === 'string'; },
        fnc: function (a) { return typeof a === 'function'; }
    };

    function stringToHyphens(str) {
        return str.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
    }

    function getCSSValue(el, prop) {
        if (prop in el.style) {
            return getComputedStyle(el).getPropertyValue(stringToHyphens(prop)) || '0';
        }
    }

	var t = 'transform',
		transformString;
	jQuery(document).ready(function() {
		transformString = (getCSSValue(document.body, t) ? t : '-webkit-' + t);
	});

    function rand(value) {
        return Math.random() * value - value / 2;
    }

})();