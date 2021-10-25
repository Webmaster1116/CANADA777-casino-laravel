/*
 2017 ThemePunch
 http://www.themepunch.com/
 @version   1.0.0
*/
;(function() { 
	
	window.RsParticlesAddOn = function(slider) {
		
		// bounce if showDoubleJqueryError
		if(!slider) return;
		
		var options = slider[0].opt.particles,
			startSlide = options.startSlide,
			endSlide = options.endSlide,
			particl = options.particles,
			lines = particl.line_linked,
			shape = particl.shape,
			color = particl.color,
			stroke = shape.stroke,
			bWidth = stroke.width,
			id = slider[0].id,
			swapAdded,
			showTimer,
			swapTimer,
			isPlaying,
			instance,
			sibling,
			started,
			inited,
			canvas,
			inter,
			svg;
			
		
		canvas = id + '-rs-particles';
		color.value = color.value.split(',');
		
		if(!bWidth) stroke.color = stroke.color.split(',');
		else stroke.color = toRGBA(stroke.color.split(','), stroke.opacity);
		
		inter = options.interactivity.events.onhover;
		if(lines.enable || (inter.enable && inter.mode === 'grab')) {
			
			var ar = lines.color = lines.color.split(','),
				i = ar.length;
				
			while(i--) ar[i] = hexToRgb(ar[i]);
			
		}
		
		if(shape.type === 'image') {
			
			svg = shape.image;
			svg.src = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="{{viewbox}}">' + 
					  '<path fill="#ffffff" stroke="{{stroke-color}}" stroke-width="{{stroke-width}}" d="' + svg.src + '"></path></svg>';
			
			if(!bWidth) {
				
				svg.src = svg.src.replace('{{viewbox}}', '0 0 24 24').replace('{{stroke-width}}', 0);
				
			}
			else {
				
				var size = (bWidth * 2) + 24;
				svg.src = svg.src.replace('{{stroke-width}}', bWidth)
								 .replace('{{viewbox}}', -bWidth +  ' ' + -bWidth + ' ' + size + ' ' + size);
				
			}
			
		}
		
		slider.one('revolution.slide.onchange', function() {
			
			var linkFound,
				zIndex = options.zIndex,
				ids = id + '-tp-particles-',
				events = options.interactivity.events;
				events = events.onhover.enable || events.onclick.enable;
			
			// determine where the canvas should be placed
			slider.find('.tp-revslider-slidesli').each(function(i) {

				if(events) {
					
					var $this = jQuery(this),
						slideLink = $this.find('.slidelink');
					
					// particles are interactive, with no slidelink present
					if(!slideLink.length) {
						
						this.className = this.className + ' rs-particles-interactive';
						
					}
					// particles are interactive, and slide also has slidelink
					else {
						
						linkFound = true;
						if($this.data('slideindex') !== 'back') zIndex = 999;
						this.className = this.className + ' rs-particles-slidelink';
						
					}
					
				}
				
				this.className = this.className + ' ' + ids + (i + 1);
				this.setAttribute('data-particles-index', zIndex);
				
			});
			
			// for compatibility between particle interactivity and .slidelink
			if(linkFound) {
				
				jQuery('body').off('click.rsparticles')
							   .on('click.rsparticles', '.rs-particles-canvas', function() {
					
					var a = jQuery(this).prev('.tp-parallax-wrap').find('.slidelink a');
					if(!a.length) return;
					
					// navigate to url
					if(a[0].href) {
						
						if(a[0].target !== '_blank') window.location = a[0].href;
						else window.open(a[0].href);
						
					}
					else {
							
						// navigate to slide
						a.click();
						
					}
					
				});
				
			}
			
			onSwap(false, false)
			
		});
		
		function onSwap(event, data) {
			
			clearTimeout(swapTimer);
			clearTimeout(showTimer);
			
			var index = data ? data.nextslide.index() + 1 : 1;
			if(startSlide === 'first') startSlide = 1;
			if(endSlide === 'last') endSlide = slider.revmaxslide();
			
			// particles should play
			if(index >= startSlide && index <= endSlide) {
				
				sibling = '.' + id + '-tp-particles-' + index;
				
				// init
				if(!started) {
					
					started = true;
					instance = pJSDomRs.length;
					particlesJSRs(sibling, options, id, canvas, slider);
					
				}
				// replay
				else if(!isPlaying) {
					
					pJSDomRs[instance].pJS.fn.vendors.draw();
					
				}
				
				// briefly wait for the next slide's visibility to be switched on
				swapTimer = setTimeout(switchSlides, 100);
				isPlaying = true;
				
			}
			// particles should pause
			else if(started) {
				
				rspCancelAnimFrame(pJSDomRs[instance].pJS.fn.drawAnimFrame);
				document.getElementById(canvas).style.opacity = 0;
				isPlaying = false;
				
			}
			
			if(!swapAdded) {
				
				swapAdded = true;
				slider.on('revolution.slide.onbeforeswap', onSwap);
				
			}
			
		}
		
		function showParticles() {
		
			document.getElementById(canvas).style.opacity = 1;
			
		}
		
		// allows for particle interactivity
		function switchSlides() {
			
			var effect = document.getElementById(canvas),
				container = document.querySelector(sibling);
			
			if(inited) container.appendChild(effect);
			else inited = true;
			
			effect.style.zIndex = parseInt(container.getAttribute('data-particles-index'), 10);
			showTimer = setTimeout(showParticles, 250);
		
		}
		
	};
	
	function toRGBA(colors, opacity) {
		
		var hex,
			color,
			i = colors.length;
		
		while(i--) {
		
			hex = colors[i].replace('#', '');
			color = 'rgba(' + 
					    parseInt(hex.substring(0, 2), 16) + ',' + 
					    parseInt(hex.substring(2, 4), 16) + ',' + 
					    parseInt(hex.substring(4, 6), 16);
						
			if(opacity) color +=  ',' + opacity.toFixed(2) + ')';
			else color +=  ')';
			colors[i] = color;
		
		}
		
		return colors;
		
	}
	
	// optional argument = revapi reference
	window.RsDestroyParticles = function(revapi) {
		
		if(!pJSDomRs) return;
		var events = 'revolution.slide.onbeforeswap revolution.slide.onloaded .rsparticles';
		
		// destroy particles for specific slider
		if(!revapi) {
		
			while(pJSDomRs.length) {
				
				jQuery('#' + PJSDomRs[0].tpId).off(events);
				pJSDomRs[0].pJS.fn.vendors.destroypJS();
				pJSDomRs.shift();
				
			}
			
		}
		// destroy all particle instances on the page
		else {
			
			var id = revapi[0].id,
				len = pJSDomRs.length;
			
			revapi.off(events);
			for(var i = 0; i < len; i++) {
				
				if(PJSDomRs[i].tpId === id) {
					
					pJSDomRs[i].pJS.fn.vendors.destroypJS();
					pJSDomRs.splice(i, 1);
					break;
					
				}
				
			}
			
		}
		
	};
	
	
	/* the magic, modified slightly for RevSlider */

	/* -----------------------------------------------
	/* Author : Vincent Garreau  - vincentgarreau.com
	/* MIT license: http://opensource.org/licenses/MIT
	/* Demo / Generator : vincentgarreau.com/particles.js
	/* GitHub : github.com/VincentGarreau/particles.js
	/* How to use? : Check the GitHub README
	/* v2.0.0
	/* ----------------------------------------------- */

	var pJS = function(canvas_el, tag_id, params, tp_id, slider){

	  // var canvas_el = document.querySelector('#'+tag_id+' > .rs-particles-canvas');
	  this.tpId = tp_id;
	  
	  /* particles.js variables with default values */
	  this.pJS = {
		canvas: {
		  el: canvas_el,
		  w: canvas_el.offsetWidth,
		  h: canvas_el.offsetHeight
		},
		particles: {
		  number: {
			value: 400,
			density: {
			  enable: true,
			  value_area: 800
			}
		  },
		  color: {
			value: '#fff'
		  },
		  shape: {
			type: 'circle',
			stroke: {
			  width: 0,
			  color: '#ff0000'
			},
			polygon: {
			  nb_sides: 5
			},
			image: {
			  src: '',
			  width: 100,
			  height: 100
			}
		  },
		  opacity: {
			value: 1,
			random: false,
			min: 0.1,
			anim: {
			  enable: false,
			  speed: 2,
			  opacity_min: 0,
			  sync: false
			}
		  },
		  size: {
			value: 20,
			random: false,
			min: 1,
			anim: {
			  enable: false,
			  speed: 20,
			  size_min: 0,
			  sync: false
			}
		  },
		  line_linked: {
			enable: false,
			distance: 100,
			color: '#fff',
			opacity: 1,
			width: 1
		  },
		  move: {
			enable: true,
			speed: 2,
			direction: 'none',
			random: false,
			min_speed: 1,
			straight: false,
			out_mode: 'out',
			bounce: false,
			attract: {
			  enable: false,
			  rotateX: 3000,
			  rotateY: 3000
			}
		  },
		  array: []
		},
		interactivity: {
		  detect_on: 'canvas',
		  events: {
			onhover: {
			  enable: true,
			  mode: 'grab'
			},
			onclick: {
			  enable: true,
			  mode: 'push'
			},
			resize: true
		  },
		  modes: {
			grab:{
			  distance: 100,
			  line_linked:{
				opacity: 1
			  }
			},
			bubble:{
			  distance: 200,
			  size: 80,
			  duration: 0.4
			},
			repulse:{
			  distance: 200,
			  duration: 0.4
			},
			push:{
			  particles_nb: 4
			},
			remove:{
			  particles_nb: 2
			}
		  },
		  mouse:{}
		},
		retina_detect: false,
		offset: slider.offset(),
		fn: {
		  interact: {},
		  modes: {},
		  vendors:{}
		},
		tmp: {}
	  };

	  var pJS = this.pJS;

	  /* params settings */
	  if(params){
		Object.deepExtend(pJS, params);
	  }
	  
	  this.size_value = pJS.particles.size.value;

	  pJS.tmp.obj = {
		size_value: pJS.particles.size.value,
		size_anim_speed: pJS.particles.size.anim.speed,
		move_speed: pJS.particles.move.speed,
		line_linked_distance: pJS.particles.line_linked.distance,
		line_linked_width: pJS.particles.line_linked.width,
		mode_grab_distance: pJS.interactivity.modes.grab.distance,
		mode_bubble_distance: pJS.interactivity.modes.bubble.distance,
		mode_bubble_size: pJS.interactivity.modes.bubble.size,
		mode_repulse_distance: pJS.interactivity.modes.repulse.distance
	  };
		
	  // https://github.com/VincentGarreau/particles.js/issues/133
	  pJS.tmp.count_svg = 0;

	  pJS.fn.retinaInit = function(){

		if(pJS.retina_detect && window.devicePixelRatio > 1){
		  pJS.canvas.pxratio = window.devicePixelRatio; 
		  pJS.tmp.retina = true;
		} 
		else{
		  pJS.canvas.pxratio = 1;
		  pJS.tmp.retina = false;
		}

		pJS.canvas.w = pJS.canvas.el.offsetWidth * pJS.canvas.pxratio;
		pJS.canvas.h = pJS.canvas.el.offsetHeight * pJS.canvas.pxratio;

		pJS.particles.size.value = pJS.tmp.obj.size_value * pJS.canvas.pxratio;
		pJS.particles.size.anim.speed = pJS.tmp.obj.size_anim_speed * pJS.canvas.pxratio;
		pJS.particles.move.speed = pJS.tmp.obj.move_speed * pJS.canvas.pxratio;
		pJS.particles.line_linked.distance = pJS.tmp.obj.line_linked_distance * pJS.canvas.pxratio;
		pJS.interactivity.modes.grab.distance = pJS.tmp.obj.mode_grab_distance * pJS.canvas.pxratio;
		pJS.interactivity.modes.bubble.distance = pJS.tmp.obj.mode_bubble_distance * pJS.canvas.pxratio;
		pJS.particles.line_linked.width = pJS.tmp.obj.line_linked_width * pJS.canvas.pxratio;
		pJS.interactivity.modes.bubble.size = pJS.tmp.obj.mode_bubble_size * pJS.canvas.pxratio;
		pJS.interactivity.modes.repulse.distance = pJS.tmp.obj.mode_repulse_distance * pJS.canvas.pxratio;

	  };



	  /* ---------- pJS functions - canvas ------------ */

	  pJS.fn.canvasInit = function(){
		pJS.canvas.ctx = pJS.canvas.el.getContext('2d');
	  };

	  pJS.fn.canvasSize = function(){

		pJS.canvas.el.width = pJS.canvas.w;
		pJS.canvas.el.height = pJS.canvas.h;

		if(pJS && pJS.interactivity.events.resize){

		  window.addEventListener('resize', function(){
			  
			  pJS.interactivity.offset = slider.offset();
			  
			  pJS.canvas.w = pJS.canvas.el.offsetWidth;
			  pJS.canvas.h = pJS.canvas.el.offsetHeight;

			  /* resize canvas */
			  if(pJS.tmp.retina){
				pJS.canvas.w *= pJS.canvas.pxratio;
				pJS.canvas.h *= pJS.canvas.pxratio;
			  }

			  pJS.canvas.el.width = pJS.canvas.w;
			  pJS.canvas.el.height = pJS.canvas.h;

			  /* repaint canvas on anim disabled */
			  if(!pJS.particles.move.enable){
				pJS.fn.particlesEmpty();
				pJS.fn.particlesCreate();
				pJS.fn.particlesDraw();
				pJS.fn.vendors.densityAutoParticles();
			  }

			/* density particles enabled */
			pJS.fn.vendors.densityAutoParticles();

		  });

		}

	  };


	  pJS.fn.canvasPaint = function(){
		pJS.canvas.ctx.fillRect(0, 0, pJS.canvas.w, pJS.canvas.h);
	  };

	  pJS.fn.canvasClear = function(){
		pJS.canvas.ctx.clearRect(0, 0, pJS.canvas.w, pJS.canvas.h);
	  };


	  /* --------- pJS functions - particles ----------- */

	  pJS.fn.particle = function(color, opacity, position){

		/* size */
		// this.radius = (pJS.particles.size.random ? Math.random() : 1) * pJS.particles.size.value;
		var rds = pJS.particles.size.value;

		if(pJS.particles.size.random) {
			var mrds = pJS.particles.size.min;
			rds = Math.random() * (rds - mrds) + mrds;
			if(rds === 0) rds = 1;
		}

		this.radius = rds;
		this.osize = rds;
		
		if(pJS.particles.size.anim.enable){
		  this.size_status = false;
		  this.vs = pJS.particles.size.anim.speed / 100;
		  if(!pJS.particles.size.anim.sync){
			this.vs = this.vs * Math.random();
		  }
		}

		/* position */
		this.x = position ? position.x : Math.random() * pJS.canvas.w;
		this.y = position ? position.y : Math.random() * pJS.canvas.h;

		/* check position  - into the canvas */
		if(this.x > pJS.canvas.w - this.radius*2) this.x = this.x - this.radius;
		else if(this.x < this.radius*2) this.x = this.x + this.radius;
		if(this.y > pJS.canvas.h - this.radius*2) this.y = this.y - this.radius;
		else if(this.y < this.radius*2) this.y = this.y + this.radius;

		/* check position - avoid overlap */
		if(pJS.particles.move.bounce){
		  pJS.fn.vendors.checkOverlap(this, position);
		}
		
		/* speed */
		var spd = pJS.particles.move.speed,
		mspd = pJS.particles.move.min_speed;
		
		if(pJS.particles.move.random) {
			spd = Math.round(Math.random() * (spd - mspd) + mspd);
			if(spd < 1) spd = 1;
		}
		
		this.spd = spd;

		/* color */
		this.color = {};
		if(typeof(color.value) == 'object'){

		  if(color.value instanceof Array){
			var color_selected = color.value[Math.floor(Math.random() * pJS.particles.color.value.length)];
			this.color.rgb = hexToRgb(color_selected);
		  }else{
			if(color.value.r != undefined && color.value.g != undefined && color.value.b != undefined){
			  this.color.rgb = {
				r: color.value.r,
				g: color.value.g,
				b: color.value.b
			  }
			}
			if(color.value.h != undefined && color.value.s != undefined && color.value.l != undefined){
			  this.color.hsl = {
				h: color.value.h,
				s: color.value.s,
				l: color.value.l
			  }
			}
		  }

		}
		else if(color.value == 'random'){
		  this.color.rgb = {
			r: (Math.floor(Math.random() * (255 - 0 + 1)) + 0),
			g: (Math.floor(Math.random() * (255 - 0 + 1)) + 0),
			b: (Math.floor(Math.random() * (255 - 0 + 1)) + 0)
		  }
		}
		else if(typeof(color.value) == 'string'){
		  this.color = color;
		  this.color.rgb = hexToRgb(this.color.value);
		}
		
		/* variable border colors */
		var bColor = pJS.particles.shape.stroke.color;
		this.strokeColor = bColor[Math.floor(Math.random() * bColor.length)];
		
		/* variable line colors */
		var lColor = pJS.particles.line_linked.color;
		this.lineColor = lColor[Math.floor(Math.random() * lColor.length)];

		/* opacity */
		// this.opacity = (pJS.particles.opacity.random ? Math.random() : 1) * pJS.particles.opacity.value;
		
		var opacit = pJS.particles.opacity.value,
		mopc = pJS.particles.opacity.min;
		
		if(pJS.particles.opacity.random) {
			opacit = Math.random() * (opacit - mopc) + mopc;
		}
	
		this.opc = opacit;
		this.opacity = opacit;
		
		if(pJS.particles.opacity.anim.enable){
		  this.opacity_status = false;
		  this.vo = pJS.particles.opacity.anim.speed / 100;
		  if(!pJS.particles.opacity.anim.sync){
			this.vo = this.vo * Math.random();
		  }
		}

		/* animation - velocity for speed */
		var velbase = {}
		switch(pJS.particles.move.direction){
		  case 'top':
			velbase = { x:0, y:-1 };
		  break;
		  case 'top-right':
			velbase = { x:0.5, y:-0.5 };
		  break;
		  case 'right':
			velbase = { x:1, y:-0 };
		  break;
		  case 'bottom-right':
			velbase = { x:0.5, y:0.5 };
		  break;
		  case 'bottom':
			velbase = { x:0, y:1 };
		  break;
		  case 'bottom-left':
			velbase = { x:-0.5, y:1 };
		  break;
		  case 'left':
			velbase = { x:-1, y:0 };
		  break;
		  case 'top-left':
			velbase = { x:-0.5, y:-0.5 };
		  break;
		  default:
			velbase = { x:0, y:0 };
		  break;
		}

		if(pJS.particles.move.straight){
		  this.vx = velbase.x;
		  this.vy = velbase.y;
		  /*
		  if(pJS.particles.move.random){
			this.vx = this.vx * (Math.random());
			this.vy = this.vy * (Math.random());
		  }
		  */
		}else{
		  this.vx = velbase.x + Math.random()-0.5;
		  this.vy = velbase.y + Math.random()-0.5;
		}

		// var theta = 2.0 * Math.PI * Math.random();
		// this.vx = Math.cos(theta);
		// this.vy = Math.sin(theta);

		this.vx_i = this.vx;
		this.vy_i = this.vy;

		

		/* if shape is image */

		var shape_type = pJS.particles.shape.type;
		if(typeof(shape_type) == 'object'){
		  if(shape_type instanceof Array){
			var shape_selected = shape_type[Math.floor(Math.random() * shape_type.length)];
			this.shape = shape_selected;
		  }
		}else{
		  this.shape = shape_type;
		}

		if(this.shape == 'image'){
		  var sh = pJS.particles.shape;
		  this.img = {
			src: sh.image.src,
			ratio: sh.image.width / sh.image.height
		  }
		  if(!this.img.ratio) this.img.ratio = 1;
		  if(pJS.tmp.img_type == 'svg' && pJS.tmp.source_svg != undefined){
			
			pJS.fn.vendors.createSvgImg(this);
			if(pJS.tmp.pushing){
			  this.img.loaded = false;
			}
			
		  }
		}

		

	  };
	  
	  pJS.fn.particle.prototype.drawSVG = function(img_obj, radius) {
		  
		  pJS.canvas.ctx.drawImage(
			img_obj,
			this.x-radius,
			this.y-radius,
			radius*2,
			radius*2 / this.img.ratio
		  );
		  
	  }

	  pJS.fn.particle.prototype.draw = function() {

		var p = this, svg, radius, opacity, color_value;

		if(p.radius_bubble != undefined){
		  radius = p.radius_bubble; 
		}else{
		  radius = p.radius;
		}

		if(p.opacity_bubble != undefined){
		  opacity = p.opacity_bubble;
		}else{
		  opacity = p.opacity;
		}

		if(p.color.rgb){
		  color_value = 'rgba('+p.color.rgb.r+','+p.color.rgb.g+','+p.color.rgb.b+','+opacity+')';
		}else{
		  color_value = 'hsla('+p.color.hsl.h+','+p.color.hsl.s+'%,'+p.color.hsl.l+'%,'+opacity+')';
		}

		pJS.canvas.ctx.fillStyle = color_value;
		pJS.canvas.ctx.beginPath();

		switch(p.shape){

		  case 'circle':
			pJS.canvas.ctx.arc(p.x, p.y, radius, 0, Math.PI * 2, false);
		  break;

		  case 'edge':
			pJS.canvas.ctx.rect(p.x-radius, p.y-radius, radius*2, radius*2);
		  break;

		  case 'triangle':
			pJS.fn.vendors.drawShape(pJS.canvas.ctx, p.x-radius, p.y+radius / 1.66, radius*2, 3, 2);
		  break;

		  case 'polygon':
			pJS.fn.vendors.drawShape(
			  pJS.canvas.ctx,
			  p.x - radius / (pJS.particles.shape.polygon.nb_sides/3.5), // startX
			  p.y - radius / (2.66/3.5), // startY
			  radius*2.66 / (pJS.particles.shape.polygon.nb_sides/3), // sideLength
			  pJS.particles.shape.polygon.nb_sides, // sideCountNumerator
			  1 // sideCountDenominator
			);
		  break;

		  case 'star':
			pJS.fn.vendors.drawShape(
			  pJS.canvas.ctx,
			  p.x - radius*2 / (pJS.particles.shape.polygon.nb_sides/4), // startX
			  p.y - radius / (2*2.66/3.5), // startY
			  radius*2*2.66 / (pJS.particles.shape.polygon.nb_sides/3), // sideLength
			  pJS.particles.shape.polygon.nb_sides, // sideCountNumerator
			  2 // sideCountDenominator
			);
		  break;

		  case 'image':
			
			pJS.canvas.ctx.globalAlpha = opacity;
			
			function draw(){
				
			  pJS.canvas.ctx.drawImage(
				img_obj,
				p.x-radius,
				p.y-radius,
				radius*2,
				radius*2 / p.img.ratio
			  );
			}

			if(pJS.tmp.img_type == 'svg'){
			  var img_obj = p.img.obj;
			}else{
			  var img_obj = pJS.tmp.img_obj;
			}

			if(img_obj){
			  p.drawSVG(img_obj, radius);
			}
			
			pJS.canvas.ctx.globalAlpha = 1.0;
			svg = true;

		  break;

		}

		pJS.canvas.ctx.closePath();
		
		if(!svg) {
		
			if(pJS.particles.shape.stroke.width > 0){
			  
			  pJS.canvas.ctx.strokeStyle = p.strokeColor;
			  pJS.canvas.ctx.lineWidth = pJS.particles.shape.stroke.width;
			  pJS.canvas.ctx.stroke();
			}
			
			pJS.canvas.ctx.fill();
			
		}
		
	  };


	  pJS.fn.particlesCreate = function(){
		  
		var len = pJS.particles.number.value,
			ar = pJS.particles.array;
		
		for(var i = 0; i < len; i++) {
		  ar[ar.length] = new pJS.fn.particle(pJS.particles.color, pJS.particles.opacity.value);
		}
	  };

	  pJS.fn.particlesUpdate = function(){
		
		var len = pJS.particles.array.length;
		
		for(var i = 0; i < len; i++){

		  /* the particle */
		  var p = pJS.particles.array[i];

		  // var d = ( dx = pJS.interactivity.mouse.click_pos_x - p.x ) * dx + ( dy = pJS.interactivity.mouse.click_pos_y - p.y ) * dy;
		  // var f = -BANG_SIZE / d;
		  // if ( d < BANG_SIZE ) {
		  //     var t = Math.atan2( dy, dx );
		  //     p.vx = f * Math.cos(t);
		  //     p.vy = f * Math.sin(t);
		  // }

		  /* move the particle */
		  if(pJS.particles.move.enable){
			
			var ms = p.spd/2;
			p.x += p.vx * ms;
			p.y += p.vy * ms;
			
		  }

		  /* change opacity status */
		  if(pJS.particles.opacity.anim.enable) {
			if(p.opacity_status == true) {
			  if(p.opacity >= p.opc) p.opacity_status = false;
			  p.opacity += p.vo;
			}else {
			  if(p.opacity <= pJS.particles.opacity.anim.opacity_min) p.opacity_status = true;
			  p.opacity -= p.vo;
			}
			if(p.opacity < 0) p.opacity = 0;
		  }

		  /* change size */
		  if(pJS.particles.size.anim.enable){
			if(p.size_status == true){
			  if(p.radius >= pJS.particles.size.value) p.size_status = false;
			  p.radius += p.vs;
			}else{
			  if(p.radius <= pJS.particles.size.anim.size_min) p.size_status = true;
			  p.radius -= p.vs;
			}
			if(p.radius < 0) p.radius = 0;
		  }

		  /* change particle position if it is out of canvas */
		  if(pJS.particles.move.out_mode == 'bounce'){
			var new_pos = {
			  x_left: p.radius,
			  x_right:  pJS.canvas.w,
			  y_top: p.radius,
			  y_bottom: pJS.canvas.h
			}
		  }else{
			var new_pos = {
			  x_left: -p.radius,
			  x_right: pJS.canvas.w + p.radius,
			  y_top: -p.radius,
			  y_bottom: pJS.canvas.h + p.radius
			}
		  }

		  if(p.x - p.radius > pJS.canvas.w){
			p.x = new_pos.x_left;
			p.y = Math.random() * pJS.canvas.h;
		  }
		  else if(p.x + p.radius < 0){
			p.x = new_pos.x_right;
			p.y = Math.random() * pJS.canvas.h;
		  }
		  if(p.y - p.radius > pJS.canvas.h){
			p.y = new_pos.y_top;
			p.x = Math.random() * pJS.canvas.w;
		  }
		  else if(p.y + p.radius < 0){
			p.y = new_pos.y_bottom;
			p.x = Math.random() * pJS.canvas.w;
		  }

		  /* out of canvas modes */
		  switch(pJS.particles.move.out_mode){
			case 'bounce':
			  if (p.x + p.radius > pJS.canvas.w) p.vx = -p.vx;
			  else if (p.x - p.radius < 0) p.vx = -p.vx;
			  if (p.y + p.radius > pJS.canvas.h) p.vy = -p.vy;
			  else if (p.y - p.radius < 0) p.vy = -p.vy;
			break;
		  }

		  /* events */
		  if(isInArray('grab', pJS.interactivity.events.onhover.mode)){
			pJS.fn.modes.grabParticle(p);
		  }

		  if(isInArray('bubble', pJS.interactivity.events.onhover.mode) || isInArray('bubble', pJS.interactivity.events.onclick.mode)){
			pJS.fn.modes.bubbleParticle(p);
		  }

		  if(isInArray('repulse', pJS.interactivity.events.onhover.mode) || isInArray('repulse', pJS.interactivity.events.onclick.mode)){
			pJS.fn.modes.repulseParticle(p);
		  }

		  /* interaction auto between particles */
		  if(pJS.particles.line_linked.enable || pJS.particles.move.attract.enable){
			  
			var leg =  pJS.particles.array.length;
			  
			for(var j = i + 1; j < leg; j++){
			  var p2 = pJS.particles.array[j];

			  /* link particles */
			  if(pJS.particles.line_linked.enable){
				pJS.fn.interact.linkParticles(p,p2);
			  }

			  /* attract particles */
			  if(pJS.particles.move.attract.enable){
				pJS.fn.interact.attractParticles(p,p2);
			  }

			  /* bounce particles */
			  if(pJS.particles.move.bounce){
				pJS.fn.interact.bounceParticles(p,p2);
			  }

			}
		  }


		}

	  };

	  pJS.fn.particlesDraw = function(){

		/* clear canvas */
		pJS.canvas.ctx.clearRect(0, 0, pJS.canvas.w, pJS.canvas.h);

		/* update each particles param */
		pJS.fn.particlesUpdate();

		/* draw each particle */
		var len = pJS.particles.array.length;
		for(var i = 0; i < len; i++){
		  var p = pJS.particles.array[i];
		  p.draw();
		}

	  };

	  pJS.fn.particlesEmpty = function(){
		pJS.particles.array = [];
	  };

	  pJS.fn.particlesRefresh = function(){

		/* init all */
		rspCancelAnimFrame(pJS.fn.checkAnimFrame);
		rspCancelAnimFrame(pJS.fn.drawAnimFrame);
		pJS.tmp.source_svg = undefined;
		pJS.tmp.img_obj = undefined;
		pJS.tmp.count_svg = 0;
		pJS.fn.particlesEmpty();
		pJS.fn.canvasClear();
		
		/* restart */
		pJS.fn.vendors.start();

	  };


	  /* ---------- pJS functions - particles interaction ------------ */

	  pJS.fn.interact.linkParticles = function(p1, p2){

		var dx = p1.x - p2.x,
			dy = p1.y - p2.y,
			dist = Math.sqrt(dx*dx + dy*dy);

		/* draw a line between p1 and p2 if the distance between them is under the config distance */
		if(dist <= pJS.particles.line_linked.distance){

		  var opacity_line = pJS.particles.line_linked.opacity - (dist / (1/pJS.particles.line_linked.opacity)) / pJS.particles.line_linked.distance;

		  if(opacity_line > 0){        
			
			/* style */
			var color_line = p1.lineColor;
			pJS.canvas.ctx.strokeStyle = 'rgba('+color_line.r+','+color_line.g+','+color_line.b+','+opacity_line+')';
			pJS.canvas.ctx.lineWidth = pJS.particles.line_linked.width;
			//pJS.canvas.ctx.lineCap = 'round'; /* performance issue */
			
			/* path */
			pJS.canvas.ctx.beginPath();
			pJS.canvas.ctx.moveTo(p1.x, p1.y);
			pJS.canvas.ctx.lineTo(p2.x, p2.y);
			pJS.canvas.ctx.stroke();
			pJS.canvas.ctx.closePath();

		  }

		}

	  };


	  pJS.fn.interact.attractParticles  = function(p1, p2){

		/* condensed particles */
		var dx = p1.x - p2.x,
			dy = p1.y - p2.y,
			dist = Math.sqrt(dx*dx + dy*dy);

		if(dist <= pJS.particles.line_linked.distance){

		  var ax = dx/(pJS.particles.move.attract.rotateX*1000),
			  ay = dy/(pJS.particles.move.attract.rotateY*1000);

		  p1.vx -= ax;
		  p1.vy -= ay;

		  p2.vx += ax;
		  p2.vy += ay;

		}
		

	  }


	  pJS.fn.interact.bounceParticles = function(p1, p2){

		var dx = p1.x - p2.x,
			dy = p1.y - p2.y,
			dist = Math.sqrt(dx*dx + dy*dy),
			dist_p = p1.radius+p2.radius;

		if(dist <= dist_p){
		  p1.vx = -p1.vx;
		  p1.vy = -p1.vy;

		  p2.vx = -p2.vx;
		  p2.vy = -p2.vy;
		}

	  }


	  /* ---------- pJS functions - modes events ------------ */

	  pJS.fn.modes.pushParticles = function(nb, pos){
		
		nb = nb | 0;
		pJS.tmp.pushing = true;
		
		for(var i = 0; i < nb; i++){
			
		  pJS.particles.array.push(
			new pJS.fn.particle(
			  pJS.particles.color,
			  pJS.particles.opacity.value,
			  {
				'x': pos ? pos.pos_x : Math.random() * pJS.canvas.w,
				'y': pos ? pos.pos_y : Math.random() * pJS.canvas.h
			  }
			)
		  )

		}
		
		if(!pJS.particles.move.enable){
		  pJS.fn.particlesDraw();
		}
		pJS.tmp.pushing = false;

	  };


	  pJS.fn.modes.removeParticles = function(nb){

		pJS.particles.array.splice(0, nb);
		if(!pJS.particles.move.enable){
		  pJS.fn.particlesDraw();
		}

	  };


	  pJS.fn.modes.bubbleParticle = function(p){
	
		/* on hover event */
		if(pJS.interactivity.events.onhover.enable && isInArray('bubble', pJS.interactivity.events.onhover.mode)){

		  var dx_mouse = p.x - pJS.interactivity.mouse.pos_x,
			  dy_mouse = p.y - pJS.interactivity.mouse.pos_y,
			  dist_mouse = Math.sqrt(dx_mouse*dx_mouse + dy_mouse*dy_mouse),
			  ratio = 1 - dist_mouse / pJS.interactivity.modes.bubble.distance;
			
		  /*	
		  function init(){
			p.opacity_bubble = p.opacity;
			p.radius_bubble = p.radius;
		  }
		  */

		  /* mousemove - check ratio */
		  if(dist_mouse <= pJS.interactivity.modes.bubble.distance){

			if(ratio >= 0 && pJS.interactivity.status == 'mousemove'){
			  
			  /* size */
			  if(pJS.interactivity.modes.bubble.size != p.radius){

				if(pJS.interactivity.modes.bubble.size > p.radius){
				  var size = p.radius + (pJS.interactivity.modes.bubble.size*ratio);
				  if(size >= 0){
					p.radius_bubble = size;
				  }
				}else{
				  var dif = p.radius - pJS.interactivity.modes.bubble.size,
					  size = p.radius - (dif*ratio);
				  if(size > 0){
					p.radius_bubble = size;
				  }else{
					p.radius_bubble = 0;
				  }
				}

			  }

			  /* opacity */
			  if(pJS.interactivity.modes.bubble.opacity != p.opc){

				if(pJS.interactivity.modes.bubble.opacity > p.opc){
				  var opacity = pJS.interactivity.modes.bubble.opacity*ratio;
				  if(opacity > p.opacity && opacity <= pJS.interactivity.modes.bubble.opacity){
					p.opacity_bubble = opacity;
				  }
				}else{
				  var opacity = p.opacity - (p.opc-pJS.interactivity.modes.bubble.opacity)*ratio;
				  if(opacity < p.opacity && opacity >= pJS.interactivity.modes.bubble.opacity){
					p.opacity_bubble = opacity;
				  }
				}

			  }

			}

		  }else{
			// init();
			p.opacity_bubble = p.opacity;
			p.radius_bubble = p.radius;
		  }


		  /* mouseleave */
		  if(pJS.interactivity.status == 'mouseleave'){
			// init();
			p.opacity_bubble = p.opacity;
			p.radius_bubble = p.radius;
		  }
		
		}

		/* on click event */
		else if(pJS.interactivity.events.onclick.enable && isInArray('bubble', pJS.interactivity.events.onclick.mode)){


		  if(pJS.tmp.bubble_clicking){
			var dx_mouse = p.x - pJS.interactivity.mouse.click_pos_x,
				dy_mouse = p.y - pJS.interactivity.mouse.click_pos_y,
				dist_mouse = Math.sqrt(dx_mouse*dx_mouse + dy_mouse*dy_mouse),
				time_spent = (new Date().getTime() - pJS.interactivity.mouse.click_time)/1000;

			if(time_spent > pJS.interactivity.modes.bubble.duration){
			  pJS.tmp.bubble_duration_end = true;
			}

			if(time_spent > pJS.interactivity.modes.bubble.duration*2){
			  pJS.tmp.bubble_clicking = false;
			  pJS.tmp.bubble_duration_end = false;
			}
		  }

		  /*
		  function process(bubble_param, particles_param, p_obj_bubble, p_obj, id){

			if(bubble_param != particles_param){

			  if(!pJS.tmp.bubble_duration_end){
				if(dist_mouse <= pJS.interactivity.modes.bubble.distance){
				  if(p_obj_bubble != undefined) var obj = p_obj_bubble;
				  else var obj = p_obj;
				  if(obj != bubble_param){
					var value = p_obj - (time_spent * (p_obj - bubble_param) / pJS.interactivity.modes.bubble.duration);
					if(id == 'size') p.radius_bubble = value;
					if(id == 'opacity') p.opacity_bubble = value;
				  }
				}else{
				  if(id == 'size') p.radius_bubble = undefined;
				  if(id == 'opacity') p.opacity_bubble = undefined;
				}
			  }else{
				if(p_obj_bubble != undefined){
				  var value_tmp = p_obj - (time_spent * (p_obj - bubble_param) / pJS.interactivity.modes.bubble.duration),
					  dif = bubble_param - value_tmp;
					  value = bubble_param + dif;
				  if(id == 'size') p.radius_bubble = value;
				  if(id == 'opacity') p.opacity_bubble = value;
				}
			  }

			}

		  }
		  */

		  if(pJS.tmp.bubble_clicking){
			  
			/* size */
			// process(pJS.interactivity.modes.bubble.size, pJS.particles.size.value, p.radius_bubble, p.radius, 'size');
			/* opacity */
			// process(pJS.interactivity.modes.bubble.opacity, pJS.particles.opacity.value, p.opacity_bubble, p.opacity, 'opacity');
			
			processBubble(p, dist_mouse, time_spent, pJS.interactivity.modes.bubble.size, p.osize, p.radius_bubble, p.radius, 'size');
			processBubble(p, dist_mouse, time_spent, pJS.interactivity.modes.bubble.opacity, p.opc, p.opacity_bubble, p.opacity, 'opacity');
			
		  }

		}

	  };
		
	  function processBubble(p, dist_mouse, time_spent, bubble_param, particles_param, p_obj_bubble, p_obj, id){

		if(bubble_param != particles_param){

		  if(!pJS.tmp.bubble_duration_end){
			if(dist_mouse <= pJS.interactivity.modes.bubble.distance){
			  var obj;
			  if(p_obj_bubble != undefined) obj = p_obj_bubble;
			  else obj = p_obj;
			  
			  if(obj != bubble_param){
				var value = p_obj - (time_spent * (p_obj - bubble_param) / pJS.interactivity.modes.bubble.duration);
				if(id == 'size') p.radius_bubble = value;
				if(id == 'opacity') p.opacity_bubble = value;
			  }
			}else{
			  if(id == 'size') p.radius_bubble = undefined;
			  if(id == 'opacity') p.opacity_bubble = undefined;
			}
		  }else{
			if(p_obj_bubble != undefined){
			  var value_tmp = p_obj - (time_spent * (p_obj - bubble_param) / pJS.interactivity.modes.bubble.duration),
				  dif = bubble_param - value_tmp;
				  value = bubble_param + dif;
			  if(id == 'size') p.radius_bubble = value;
			  if(id == 'opacity') p.opacity_bubble = value;
			}
		  }

		}

	  }	

	  pJS.fn.modes.repulseParticle = function(p){

		if(pJS.interactivity.events.onhover.enable && isInArray('repulse', pJS.interactivity.events.onhover.mode) && pJS.interactivity.status == 'mousemove') {

		  var dx_mouse = p.x - pJS.interactivity.mouse.pos_x,
			  dy_mouse = p.y - pJS.interactivity.mouse.pos_y,
			  dist_mouse = Math.sqrt(dx_mouse*dx_mouse + dy_mouse*dy_mouse);

		  var normVec = {x: dx_mouse/dist_mouse, y: dy_mouse/dist_mouse},
			  repulseRadius = pJS.interactivity.modes.repulse.distance,
			  velocity = 100,
			  repulseFactor = clamp((1/repulseRadius)*(-1*Math.pow(dist_mouse/repulseRadius,2)+1)*repulseRadius*velocity, 0, 50);
		  
		  var pos = {
			x: p.x + normVec.x * repulseFactor,
			y: p.y + normVec.y * repulseFactor
		  }

		  if(pJS.particles.move.out_mode == 'bounce'){
			if(pos.x - p.radius > 0 && pos.x + p.radius < pJS.canvas.w) p.x = pos.x;
			if(pos.y - p.radius > 0 && pos.y + p.radius < pJS.canvas.h) p.y = pos.y;
		  }else{
			p.x = pos.x;
			p.y = pos.y;
		  }
		
		}


		else if(pJS.interactivity.events.onclick.enable && isInArray('repulse', pJS.interactivity.events.onclick.mode)) {

		  if(!pJS.tmp.repulse_finish){
			pJS.tmp.repulse_count++;
			if(pJS.tmp.repulse_count == pJS.particles.array.length){
			  pJS.tmp.repulse_finish = true;
			}
		  }

		  if(pJS.tmp.repulse_clicking){

			var repulseRadius = Math.pow(pJS.interactivity.modes.repulse.distance/6, 3);

			var dx = pJS.interactivity.mouse.click_pos_x - p.x,
				dy = pJS.interactivity.mouse.click_pos_y - p.y,
				d = dx*dx + dy*dy;

			var force = -repulseRadius / d * 1;
			
			/*
			function process(){

			  var f = Math.atan2(dy,dx);
			  p.vx = force * Math.cos(f);
			  p.vy = force * Math.sin(f);

			  if(pJS.particles.move.out_mode == 'bounce'){
				var pos = {
				  x: p.x + p.vx,
				  y: p.y + p.vy
				}
				if (pos.x + p.radius > pJS.canvas.w) p.vx = -p.vx;
				else if (pos.x - p.radius < 0) p.vx = -p.vx;
				if (pos.y + p.radius > pJS.canvas.h) p.vy = -p.vy;
				else if (pos.y - p.radius < 0) p.vy = -p.vy;
			  }

			}
			*/

			// default
			if(d <= repulseRadius){
				
			  // process();
			  
			  var f = Math.atan2(dy,dx);
			  p.vx = force * Math.cos(f);
			  p.vy = force * Math.sin(f);

			  if(pJS.particles.move.out_mode == 'bounce'){
				var pos = {
				  x: p.x + p.vx,
				  y: p.y + p.vy
				}
				if (pos.x + p.radius > pJS.canvas.w) p.vx = -p.vx;
				else if (pos.x - p.radius < 0) p.vx = -p.vx;
				if (pos.y + p.radius > pJS.canvas.h) p.vy = -p.vy;
				else if (pos.y - p.radius < 0) p.vy = -p.vy;
			  }
			  
			}

			// bang - slow motion mode
			// if(!pJS.tmp.repulse_finish){
			//   if(d <= repulseRadius){
			//     process();
			//   }
			// }else{
			//   process();
			// }
			

		  }else{

			if(pJS.tmp.repulse_clicking == false){

			  p.vx = p.vx_i;
			  p.vy = p.vy_i;
			
			}

		  }

		}

	  }


	  pJS.fn.modes.grabParticle = function(p){

		if(pJS.interactivity.events.onhover.enable && pJS.interactivity.status == 'mousemove'){

		  var dx_mouse = p.x - pJS.interactivity.mouse.pos_x,
			  dy_mouse = p.y - pJS.interactivity.mouse.pos_y,
			  dist_mouse = Math.sqrt(dx_mouse*dx_mouse + dy_mouse*dy_mouse);

		  /* draw a line between the cursor and the particle if the distance between them is under the config distance */
		  if(dist_mouse <= pJS.interactivity.modes.grab.distance){

			var opacity_line = pJS.interactivity.modes.grab.line_linked.opacity - (dist_mouse / (1/pJS.interactivity.modes.grab.line_linked.opacity)) / pJS.interactivity.modes.grab.distance;

			if(opacity_line > 0){

			  /* style */
			  var color_line = p.lineColor;
			  pJS.canvas.ctx.strokeStyle = 'rgba('+color_line.r+','+color_line.g+','+color_line.b+','+opacity_line+')';
			  pJS.canvas.ctx.lineWidth = pJS.particles.line_linked.width;
			  //pJS.canvas.ctx.lineCap = 'round'; /* performance issue */
			  
			  /* path */
			  pJS.canvas.ctx.beginPath();
			  pJS.canvas.ctx.moveTo(p.x, p.y);
			  pJS.canvas.ctx.lineTo(pJS.interactivity.mouse.pos_x, pJS.interactivity.mouse.pos_y);
			  pJS.canvas.ctx.stroke();
			  pJS.canvas.ctx.closePath();

			}

		  }

		}

	  };



	  /* ---------- pJS functions - vendors ------------ */

	  pJS.fn.vendors.eventsListeners = function(){

		/* events target element */
		if(pJS.interactivity.detect_on == 'window'){
		  pJS.interactivity.el = window;
		}else{
		  pJS.interactivity.el = pJS.canvas.el;
		}

		/* detect mouse pos - on hover / click event */
		if(pJS.interactivity.events.onhover.enable || pJS.interactivity.events.onclick.enable){
		  
		  slider.on('mousemove.rsparticles', function(e){
			
			/*
			if(pJS.interactivity.el == window){
			  var pos_x = e.clientX,
				  pos_y = e.clientY;
			}
			else{
			  var pos_x = e.offsetX || e.clientX,
				  pos_y = e.offsetY || e.clientY;
			}
			*/
			
			/*
			var pos_x = e.pageX - pJS.offset.left,
				pos_y = e.pageY - pJS.offset.top;
			
			if(pJS.tmp.retina){
			  pJS.interactivity.mouse.pos_x *= pJS.canvas.pxratio;
			  pJS.interactivity.mouse.pos_y *= pJS.canvas.pxratio;
			}
			*/
			
			pJS.interactivity.mouse.pos_x = e.pageX - pJS.offset.left;
			pJS.interactivity.mouse.pos_y = e.pageY - pJS.offset.top;

			pJS.interactivity.status = 'mousemove';

		  });

		  /* el on onmouseleave */
		  slider[0].addEventListener('mouseleave', function(e){
			
			pJS.interactivity.mouse.pos_x = null;
			pJS.interactivity.mouse.pos_y = null;
			pJS.interactivity.status = 'mouseleave';

		  });

		}

		/* on click event */
		if(pJS.interactivity.events.onclick.enable){

		  pJS.interactivity.el.addEventListener('click', function(){

			pJS.interactivity.mouse.click_pos_x = pJS.interactivity.mouse.pos_x;
			pJS.interactivity.mouse.click_pos_y = pJS.interactivity.mouse.pos_y;
			pJS.interactivity.mouse.click_time = new Date().getTime();

			if(pJS.interactivity.events.onclick.enable){

			  switch(pJS.interactivity.events.onclick.mode){

				case 'push':
				  if(pJS.particles.move.enable){
					pJS.fn.modes.pushParticles(pJS.interactivity.modes.push.particles_nb, pJS.interactivity.mouse);
				  }else{
					if(pJS.interactivity.modes.push.particles_nb == 1){
					  pJS.fn.modes.pushParticles(pJS.interactivity.modes.push.particles_nb, pJS.interactivity.mouse);
					}
					else if(pJS.interactivity.modes.push.particles_nb > 1){
					  pJS.fn.modes.pushParticles(pJS.interactivity.modes.push.particles_nb);
					}
				  }
				break;

				case 'remove':
				  pJS.fn.modes.removeParticles(pJS.interactivity.modes.remove.particles_nb);
				break;

				case 'bubble':
				  pJS.tmp.bubble_clicking = true;
				break;

				case 'repulse':
				  pJS.tmp.repulse_clicking = true;
				  pJS.tmp.repulse_count = 0;
				  pJS.tmp.repulse_finish = false;
				  setTimeout(function(){
					pJS.tmp.repulse_clicking = false;
				  }, pJS.interactivity.modes.repulse.duration*1000)
				break;

			  }

			}

		  });
			
		}


	  };

	  pJS.fn.vendors.densityAutoParticles = function(){

		if(pJS.particles.number.density.enable){

		  /* calc area */
		  var area = pJS.canvas.el.width * pJS.canvas.el.height / 1000;
		  if(pJS.tmp.retina){
			area = area/(pJS.canvas.pxratio*2);
		  }

		  /* calc number of particles based on density area */
		  var nb_particles = area * pJS.particles.number.value / pJS.particles.number.density.value_area;

		  /* add or remove X particles */
		  var missing_particles = pJS.particles.array.length - nb_particles;
		  
		  if(missing_particles < 0) {
			  pJS.fn.modes.pushParticles(Math.abs(missing_particles)); 
		  }
		  else {
			  pJS.fn.modes.removeParticles(missing_particles);
		  }

		}

	  };


	  pJS.fn.vendors.checkOverlap = function(p1, position){
		  
		var len = pJS.particles.array.length;
		for(var i = 0; i < len; i++){
		  var p2 = pJS.particles.array[i];

		  var dx = p1.x - p2.x,
			  dy = p1.y - p2.y,
			  dist = Math.sqrt(dx*dx + dy*dy);

		  if(dist <= p1.radius + p2.radius){
			p1.x = position ? position.x : Math.random() * pJS.canvas.w;
			p1.y = position ? position.y : Math.random() * pJS.canvas.h;
			pJS.fn.vendors.checkOverlap(p1);
		  }
		}
	  };
	  
	  function rgbHexReplace() {
			
		  var color_value;
			
		  if(this.color.rgb){
			  color_value = 'rgba('+this.color.rgb.r+','+this.color.rgb.g+','+this.color.rgb.b+','+this.opacity.toFixed(2)+')';
		  }else{
			  color_value = 'hsla('+this.color.hsl.h+','+this.color.hsl.s+'%,'+this.color.hsl.l+'%,'+this.opacity.toFixed(2)+')';
		  }
		  return color_value;
	  }

	  pJS.fn.vendors.createSvgImg = function(p){
		
		/* set color to svg element */
		var svgXml = pJS.tmp.source_svg,
			rgbHex = /#([0-9A-F]{3,6})/gi,
			coloredSvgXml = svgXml.replace(rgbHex, rgbHexReplace.call(p)).replace('{{stroke-color}}', p.strokeColor);

		/* prepare to create img with colored svg */
		/*
		var svg = new Blob([coloredSvgXml], {type: 'image/svg+xml;charset=utf-8'}),
			DOMURL = window.URL || window.webkitURL || window,
			url = DOMURL.createObjectURL(svg);
		*/
		
		/* create particle img obj */
		var img = new Image(),
		url = 'data:image/svg+xml;base64,' + btoa(coloredSvgXml);
		
		img.addEventListener('load', function(){
		  p.img.obj = img;
		  p.img.loaded = true;
		  // DOMURL.revokeObjectURL(url);
		  pJS.tmp.count_svg++;
		});
		img.src = url;

	  };


	  pJS.fn.vendors.destroypJS = function(){
		cancelAnimationFrame(pJS.fn.drawAnimFrame);
		canvas_el.remove();
		// pJSDomRs = null;
	  };


	  pJS.fn.vendors.drawShape = function(c, startX, startY, sideLength, sideCountNumerator, sideCountDenominator){

		// By Programming Thomas - https://programmingthomas.wordpress.com/2013/04/03/n-sided-shapes/
		var sideCount = sideCountNumerator * sideCountDenominator;
		var decimalSides = sideCountNumerator / sideCountDenominator;
		var interiorAngleDegrees = (180 * (decimalSides - 2)) / decimalSides;
		var interiorAngle = Math.PI - Math.PI * interiorAngleDegrees / 180; // convert to radians
		c.save();
		c.beginPath();
		c.translate(startX, startY);
		c.moveTo(0,0);
		for (var i = 0; i < sideCount; i++) {
		  c.lineTo(sideLength,0);
		  c.translate(sideLength,0);
		  c.rotate(interiorAngle);
		}
		//c.stroke();
		c.fill();
		c.restore();

	  };

	  /*
	  pJS.fn.vendors.exportImg = function(){
		window.open(pJS.canvas.el.toDataURL('image/png'), '_blank');
	  };
	*/

	  pJS.fn.vendors.loadImg = function(type, svg){
		
		/*
		pJS.tmp.img_error = undefined;

		if(pJS.particles.shape.image.src != ''){

		  if(type == 'svg'){
			
			var xhr = new XMLHttpRequest();
			xhr.open('GET', pJS.particles.shape.image.src);
			xhr.onreadystatechange = function (data) {
			  if(xhr.readyState == 4){
				if(xhr.status == 200){
				  pJS.tmp.source_svg = data.currentTarget.response;
				  pJS.fn.vendors.checkBeforeDraw();
				}else{
				  console.log('Error pJS - Image not found');
				  pJS.tmp.img_error = true;
				}
			  }
			}
			xhr.send();

		  }else{

			var img = new Image();
			img.addEventListener('load', function(){
			  pJS.tmp.img_obj = img;
			  pJS.fn.vendors.checkBeforeDraw();
			});
			img.src = pJS.particles.shape.image.src;
			
		  }

		}else{
		  console.log('Error pJS - No image.src');
		  pJS.tmp.img_error = true;
		}
		*/
		
		pJS.tmp.source_svg = svg;
		pJS.fn.vendors.checkBeforeDraw();

	  };


	  pJS.fn.vendors.draw = function(){

		if(pJS.particles.shape.type == 'image'){

		  if(pJS.tmp.img_type == 'svg'){

			if(pJS.tmp.count_svg >= pJS.particles.number.value){
			  pJS.fn.particlesDraw();
			  if(!pJS.particles.move.enable) rspCancelAnimFrame(pJS.fn.drawAnimFrame);
			  else pJS.fn.drawAnimFrame = rspRequestAnimFrame(pJS.fn.vendors.draw);
			}else{
			  // console.log('still loading...');
			  if(!pJS.tmp.img_error) pJS.fn.drawAnimFrame = rspRequestAnimFrame(pJS.fn.vendors.draw);
			}

		  }else{

			if(pJS.tmp.img_obj != undefined){
			  pJS.fn.particlesDraw();
			  if(!pJS.particles.move.enable) rspCancelAnimFrame(pJS.fn.drawAnimFrame);
			  else pJS.fn.drawAnimFrame = rspRequestAnimFrame(pJS.fn.vendors.draw);
			}else{
			  if(!pJS.tmp.img_error) pJS.fn.drawAnimFrame = rspRequestAnimFrame(pJS.fn.vendors.draw);
			}

		  }

		}else{
		  pJS.fn.particlesDraw();
		  if(!pJS.particles.move.enable) rspCancelAnimFrame(pJS.fn.drawAnimFrame);
		  else pJS.fn.drawAnimFrame = rspRequestAnimFrame(pJS.fn.vendors.draw);
		}

	  };


	  pJS.fn.vendors.checkBeforeDraw = function(){

		// if shape is image
		if(pJS.particles.shape.type == 'image'){

		  if(pJS.tmp.img_type == 'svg' && pJS.tmp.source_svg == undefined){
			pJS.tmp.checkAnimFrame = rspRequestAnimFrame(check);
		  }else{
			//console.log('images loaded! cancel check');
			rspCancelAnimFrame(pJS.tmp.checkAnimFrame);
			if(!pJS.tmp.img_error){
			  pJS.fn.vendors.init();
			  pJS.fn.vendors.draw();
			}
			
		  }

		}else{
		  pJS.fn.vendors.init();
		  pJS.fn.vendors.draw();
		}

	  };


	  pJS.fn.vendors.init = function(){

		/* init canvas + particles */
		pJS.fn.retinaInit();
		pJS.fn.canvasInit();
		pJS.fn.canvasSize();
		pJS.fn.canvasPaint();
		pJS.fn.particlesCreate();
		pJS.fn.vendors.densityAutoParticles();

	  };


	  pJS.fn.vendors.start = function(){

		if(isInArray('image', pJS.particles.shape.type)){
		  pJS.tmp.img_type = 'svg';
		  pJS.fn.vendors.loadImg(pJS.tmp.img_type, pJS.particles.shape.image.src);
		}else{
		  pJS.fn.vendors.checkBeforeDraw();
		}

	  };




	  /* ---------- pJS - start ------------ */


	  pJS.fn.vendors.eventsListeners();

	  pJS.fn.vendors.start();
	  


	};

	/* ---------- global functions - vendors ------------ */

	Object.deepExtend = function(destination, source) {
	  for (var property in source) {
		if (source[property] && source[property].constructor &&
		 source[property].constructor === Object) {
		  destination[property] = destination[property] || {};
		  arguments.callee(destination[property], source[property]);
		} else {
		  destination[property] = source[property];
		}
	  }
	  return destination;
	};

	window.rspRequestAnimFrame = (function(){
	  return  window.requestAnimationFrame ||
		window.webkitRequestAnimationFrame ||
		window.mozRequestAnimationFrame    ||
		window.oRequestAnimationFrame      ||
		window.msRequestAnimationFrame     ||
		function(callback){
		  window.setTimeout(callback, 1000 / 60);
		};
	})();

	window.rspCancelAnimFrame = ( function() {
	  return window.cancelAnimationFrame         ||
		window.webkitCancelRequestAnimationFrame ||
		window.mozCancelRequestAnimationFrame    ||
		window.oCancelRequestAnimationFrame      ||
		window.msCancelRequestAnimationFrame     ||
		clearTimeout
	} )();

	function hexToRgb(hex){
	  // By Tim Down - http://stackoverflow.com/a/5624139/3493650
	  // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
	  var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
	  hex = hex.replace(shorthandRegex, function(m, r, g, b) {
		 return r + r + g + g + b + b;
	  });
	  var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
	  return result ? {
		  r: parseInt(result[1], 16),
		  g: parseInt(result[2], 16),
		  b: parseInt(result[3], 16)
	  } : null;
	};

	function clamp(number, min, max) {
	  return Math.min(Math.max(number, min), max);
	};

	function isInArray(value, array) {
	  return array.indexOf(value) > -1;
	}


	/* ---------- particles.js functions - start ------------ */

	window.pJSDomRs = [];

	window.particlesJSRs = function(tag_id, params, tp_id, rs_id, slider){

	  //console.log(params);

	  /* no string id? so it's object params, and set the id with default id */
	  /*
	  if(typeof(tag_id) != 'string'){
		params = tag_id;
		tag_id = 'particles-js';
	  }
	  */
	  /* no id? set the id to default id */
	  /*
	  if(!tag_id){
		tag_id = 'particles-js';
	  }
	  */

	  /* pJS elements */
	  var pJS_tag = document.querySelector(tag_id);
	  if(pJS_tag == null) {
		  console.log('slide with particles removed from DOM');
	  }
	  
	  /*
	  var pJS_canvas_class = 'rs-particles-canvas',
		  exist_canvas = pJS_tag.getElementsByClassName(pJS_canvas_class);
      */
	  /* remove canvas if exists into the pJS target tag */
	  /*
	  if(exist_canvas.length){
		while(exist_canvas.length > 0){
		  pJS_tag.removeChild(exist_canvas[0]);
		}
	  }
	  */

	  /* create canvas element */
	  var canvas_el = document.createElement('canvas');
	  canvas_el.className = 'rs-particles-canvas';
	  canvas_el.id = rs_id;

	  /* set size canvas */
	  /*
	  canvas_el.style.width = "100%";
	  canvas_el.style.height = "100%";
	  */	
		
	  /* append canvas */
	  pJS_tag.appendChild(canvas_el);

	  /* launch particle.js */
	  //if(container != null){
	  pJSDomRs.push(new pJS(canvas_el, tag_id, params, tp_id, slider));
	  //}

	};

	/*
	window.particlesJS.load = function(tag_id, path_config_json, callback){

	  // load json config 
	  var xhr = new XMLHttpRequest();
	  xhr.open('GET', path_config_json);
	  xhr.onreadystatechange = function (data) {
		if(xhr.readyState == 4){
		  if(xhr.status == 200){
			var params = JSON.parse(data.currentTarget.response);
			window.particlesJS(tag_id, params);
			if(callback) callback();
		  }else{
			console.log('Error pJS - XMLHttpRequest status: '+xhr.status);
			console.log('Error pJS - File config not found');
		  }
		}
	  };
	  xhr.send();

	};
	*/

})(); /* END CLOSURE */