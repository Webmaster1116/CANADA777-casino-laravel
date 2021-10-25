$.Class("ViewportHandler",
{
},
(function()
{
	var p =
	{
		init: function(width, height)
		{
			this._width 				= width;
			this._height 				= height;
			this._aspectRatio			= width/height;
			this._isFullscreen			= undefined;
			this._isLoadingInProgress 	= true;
			this._window				= $(window);
			this._body					= $("body");
			this._game          		= $("#Game");
			this._viewport 				= $("#GameViewport");
			this._orientation   		= $("#Orientation");
			this._fullscreen    		= $("#FullScreen");
			this._loading       		= $("#Loading");
			this._windowWidth, this._windowHeight = 0;
			this._gameWrapper = $("<div id='GameWrapper'>");
			this._hideFullscreenTimeout = -1;
			Device.topOffset = 158;

			if(Device.isSafari) {
                this._game.remove();
                this._gameWrapper.append(this._game);
                this._body.prepend(this._gameWrapper);
            }
			
			var fullscreenFunction = undefined;
			if(document.documentElement.requestFullscreen)
	    		fullscreenFunction = document.documentElement.requestFullscreen;
	    	else if (document.documentElement.mozRequestFullScreen)
	    		fullscreenFunction = document.documentElement.mozRequestFullScreen;
	    	else if (document.documentElement.webkitRequestFullScreen)
	    		fullscreenFunction = document.documentElement.webkitRequestFullScreen;
			
			var exitFullscreenFunction = undefined;
			if(document.exitFullscreen) {
				exitFullscreenFunction = document.exitFullscreen;
			} else if(document.mozCancelFullScreen) {
				exitFullscreenFunction = document.mozCancelFullScreen;
			} else if(document.webkitExitFullscreen) {
				exitFullscreenFunction = document.webkitExitFullscreen;
			}
			
			document.documentElement.requestFullscreen = fullscreenFunction;
			document.exitFullscreen = exitFullscreenFunction;

            if(Device.isSafari && !isInIframe.call(this)) {
                document.documentElement.style.paddingBottom = "79px";
                this._fullscreenDiv = $("<div id='Fullscreen'>");
                this._body.append(this._fullscreenDiv);
                this._fullscreenDiv.hide();
                this._fullscreen.hide();
            }

			if(Device.isSafari)
			    this._fullscreen.find('.hand_slide').show();
			else
				this._fullscreen.hide();

			getWindowSize.call(this);

            handleOrientation.call(this);
            handleViewport.call(this);
			
			var self = this;
			this._window.on("resize", function()
			{
                getWindowSize.call(self);

				if(self._isLoadingInProgress)
					handleLoadingSize.call(self);
                else {
                    handleOrientation.call(self);
                }

                handleViewport.call(self);
			});

            this._window.on("gesturestart", preventDefault);
            this._window.on("gesturechange", preventDefault);
            this._window.on("gestureend", preventDefault);

			this._window.on("mousedown touchstart", function(event)
			{ 
				if(!Device.isSafari || self._isFullscreen || self._isLoadingInProgress) {
                    preventDefault(event);
                }
			});

            this._window.on("mouseend touchend", function(event)
            {
                setTimeout(function() {
                    window.scrollTo(0, 0);
                }, 300);
            });
		},
		
		loadingStarted: function()
		{
			handleLoadingSize.call(this);
			this._orientation.hide();
			this._isLoadingInProgress = true;
		},
		
		loadingEnded: function()
		{
			this._isLoadingInProgress = false;

            handleOrientation.call(this);
			handleViewport.call(this);
			
			if(Device.isSafari)
			{
				var self = this;
				this._window.on("scroll", function() {
				    handleViewport.call(self);
				});
			}
			else
			{
				var self = this;
				$("#SoundPopUpYesButton").on('touchend mouseup', function(event) { goFullscreen.call(self); });
				$("#SoundPopUpNoButton").on('touchend mouseup', function(event) { goFullscreen.call(self); });
				$("#OverlayStopButton").on('touchend mouseup', function(event) 
				{ 
					if(goFullscreen.call(self))
					{
						$("#OverlayStopButton").hide(); 
						setTimeout(function() {$("#OverlayStopButton").show(); }, 50);
					}
				});
			}
		},
		
		exitFullscreen: function()
		{
			if(document.exitFullscreen)
				document.exitFullscreen();
		}
	};

	function getWindowSize() {
	    if(window.navigator.standalone || Device.isCriOS) {
	        this._windowWidth = this._window.width();
	        this._windowHeight = this._window.height();
        } else {
            this._windowWidth = window.innerWidth;
            this._windowHeight = window.innerHeight;
        }
    }

    function isInIframe () {
        try {
            return window.self !== window.top;
        } catch (e) {
            return true;
        }
    }

    function preventDefault(event) {
        event.preventDefault();
        event.stopPropagation();
    }
	
	function handleLoadingSize()
	{
        var aspectRatio = this._windowWidth / this._windowHeight;

        var scale = 0;
        
        var css = new Object();
        
        if(aspectRatio > 1){
            scale       = this._windowHeight / this._loading.outerHeight();
            css.left    = (this._windowWidth - this._loading.outerWidth()*scale)/2 + 'px';
            css.top     = 0;
            
        }
        else
        {
        	// TODO: get this 600 in a meaningful way (now its not clear what it is)
            scale       = this._windowWidth / (this._loading.outerWidth()-600);
            css.left    = (this._windowWidth - this._loading.outerWidth()*scale)/2 + 'px';
            css.top     = (this._windowHeight - this._loading.outerHeight()*scale)/2 + 'px';

        }
        
        this._loading.css({
            'transform': 'scale('+scale+')',
            '-ms-transform': 'scale('+scale+')',
            '-webkit-transform': 'scale('+scale+')'
        }).css(css); 
    }
	
	function handleViewport()
	{
	    if(Device.isSafari) {
            this._gameWrapper.css({
                width: this._windowWidth + "px",
                height: this._windowHeight + "px"
            });
        }

        if(Device.isSafari) 
            handleViewportSafari.call(this);
        else
            handleViewportFullscreenAPI.call(this);

        var windowHeight = this._windowHeight/Device.scale + "px";

        if(Device.isPortrait) {
            $("#Error").css({
                top: -Device.topOffset + "px",
                height: windowHeight
            });
        } else {
            $("#Error").css({
                top: "0px",
                height: "100%"
            });
        }
    }
	
	function handleViewportSafari()
    {
		var aspectRatio = this._windowWidth / this._windowHeight;

        var scale = this._windowWidth / this._width;

        var widthCompensation = Device.isPortrait ? 130 : 0;

        if(aspectRatio > this._aspectRatio)
        {
            scale = this._windowHeight / this._height;
        }
        else
        {
            scale = this._windowWidth / (this._width - widthCompensation);
        }

        // if(!this._isLoadingInProgress)
        //     checkFullscreenIOS.call(this);
        this._fullscreen.hide();
        this._isFullscreen = true;
		
		Device.scale = scale;

        this._game.css({
            'transform': 'scale3d('+scale+','+scale+',1)',
            '-ms-transform': 'scale3d('+scale+','+scale+',1)',
            '-webkit-transform': 'scale3d('+scale+','+scale+',1)',
            'left': (this._windowWidth - this._game.outerWidth()*scale)/2 + 'px',
            'top': (Device.isPortrait ? Device.topOffset : 0)*scale + 'px'
        });

        window.scrollTo(0,0);
    }

    function checkFullscreenIOS() {
        var width, height;
        if (Device.isPortrait) {
            width = Math.min(window.screen.width, window.screen.height);
            height = Math.max(window.screen.width, window.screen.height)
        } else {
            width = Math.max(window.screen.width, window.screen.height);
            height = Math.min(window.screen.width, window.screen.height)
        }

        if(height === this._windowHeight || this._windowHeight / height >= 0.9) {
            if(this._isFullscreen === false || this._isFullscreen == undefined) {
                this._fullscreenDiv.hide();
                this._fullscreen.hide();
                this._isFullscreen = true;

                clearTimeout(this._hideFullscreenTimeout);
            }
        } else {
            if(this._isFullscreen === true || this._isFullscreen == undefined) {
                var self = this;
                this._hideFullscreenTimeout = setTimeout(function () {
                    self._fullscreenDiv.hide();
                    self._fullscreen.hide();
                    self._isFullscreen = true;
                }, 5000);

                this._fullscreenDiv.show();
                this._fullscreen.show();
                this._isFullscreen = false;

                setTimeout(function() {
                    window.scrollTo(0, 0);
                }, 100);
            }
        }
    }
	    
    function handleViewportFullscreenAPI()
    {
        var aspectRatio = this._windowWidth / this._windowHeight;

        var scale = 0;

        var widthCompensation = Device.isPortrait ? 130 : 0;
                
        // TODO: why here scale is calculated using either width or height
        // and in handleViewportSafari it is calculated using only width
        if(aspectRatio > this._aspectRatio)
        {
            scale = this._windowHeight / this._height;
            
            // this._body.css({
            //     width: this._width*scale,
            //     height: this._windowHeight
            // });
        }
        else
        {
            scale = this._windowWidth / (this._width - widthCompensation);
            
            // this._body.css({
            //     width: this._windowWidth,
            //     height: this._height*scale
            // });
        }
		
		Device.scale = scale;
        
        this._game.css({
            'transform': 'scale('+scale+')',
            '-ms-transform': 'scale('+scale+')',
            '-webkit-transform': 'scale('+scale+')',
            'left': (this._windowWidth - this._game.outerWidth()*scale)/2 + 'px',
			'top': (Device.isPortrait ? Device.topOffset : 0)*scale + 'px'
        });
        
//        if(isFullscreenApiAvailable.call(this))
//        {
//	        if(this._isFullscreen)
//	            this._fullscreen.hide();
//	        else
//	            this._fullscreen.show();
//
//	        window.scrollTo(0,0);
//        }

        setTimeout(handleViewportFullscreenAPI.bind(this), 0);
    }
	    
    function handleOrientation()
    {	
        if(this._window.height() > this._window.width()) {
            this._orientation.hide();
            Device.isPortrait = true;
        }
        else {
            this._orientation.hide();
            Device.isPortrait = false;
        }
    }
    
    function goFullscreen() 
    { 
    	if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement)
    	{
    		document.documentElement.requestFullscreen();
    		return true;
    	}
    	
    	return false;
    }
    
    function isFullscreenApiAvailable()
    {
    	if(document.documentElement.requestFullscreen)
    		return true;
    	
    	return false;
    }
    
	return p;
})());