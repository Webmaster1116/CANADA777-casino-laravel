$.Class("GamePlatform",
{
	REALITY_CHECK: "realityCheck",
	LOSS_LIMIT: "lossLimit",
	BET_LIMIT: "betLimit",
	TIME_LIMIT: "timeLimit"
},
(function()
{
    var p =
    {
    	init: function(initVars)
    	{
    		this._initVars = initVars;
    		this._socket = null;
    		this._viewportHandler = null;
    		this.$document = $('body');
    		this.$waiting = $('#Waiting');
    	    this.$error = $('#Error');
    	    this._allowErrorPopUpOverride = true;
    	    this._loadingHandler = null;
    	    this._balance = null;
    	    this._gameList = null;
    	    this._gameProxy = null;
    	    this._sessionKeeper = null;
            this._loginResponse = null;
    	    
    	    // TODO: make #Game the main view
    		
    	    if (!window.console) window.console = {};
    	    if (!window.console.log) window.console.log = function () { };
    	    
//    	    window.console.log = function() { };
    	    
    		Device.detect();
    		
    		Loader.appendURL = "";

			PlatformSettings.language = this._initVars.lang;

    		this._viewportHandler = new ViewportHandler(1280, 720);

            if(!Device.isiOS && !Device.isAndroid) {
                showErrorPopUp.call(this, PlatformSettings.localize("unsupported_browser_error"), "HOME", goBack);
                return;
            }

    		// TODO: get these messages from an external file
    		if(!Socket.isSupported())
    		{
    			if(Device.isiOS)
    				showErrorPopUp.call(this, "No WebSocket the game is optimized for iOS 7", "HOME", goBack);
    			else
    				showErrorPopUp.call(this, "No WebSocket the game is optimized for Chrome 30", "HOME", goBack);
    		}
    		else
    			startGame.call(this);
    		
    		// TODO: add a global object which will request an animation frame
    		// and all other objects that need animation will add event listeners to this
    		// global object
    		window.requestAnimFrame = (function() {
				return window.requestAnimationFrame ||
					window.webkitRequestAnimationFrame ||
					window.mozRequestAnimationFrame ||
					window.oRequestAnimationFrame ||
					window.msRequestAnimationFrame ||
					function(callback, element) {
						window.setTimeout(callback, 1000/60);
					};
			})();

			var hostname = window.location.hostname.toLowerCase();
			var isLocalHost = hostname.indexOf("localhost") !== -1 || hostname.indexOf("127.0.0.1") !== -1;
			if (!isLocalHost) {
				sendStatisticUsage();
			}
    	}
    };

	function sendStatisticUsage() {
	
	}
    
    function startGame()
    {
    	console.log("START");
    	
    	var soundManager = SoundManager.getInstance();
    	if(soundManager.isSupported())
			Loader.audioContext = soundManager.getContext();
    	
    	this._loadingHandler = new LoadingHandler();
        this._viewportHandler.loadingStarted();

        // TODO: add session keeper class which takes care of the ping

        // TODO: handle socket closing (here and in game proxy)
        this._socket = new Socket(PlatformSettings.socketType+'://'+this._initVars.tcpHost+':'+this._initVars.tcpPort);
        this._socket.addEventListener(Socket.SOCKET_CONNECTED, onSocketConnected, this);
        this._socket.addEventListener(Socket.SOCKET_CLOSED, onSocketClosed, this);
        this._socket.addEventListener(Socket.RESPONSE_RECEIVED, onResponseReceived, this);
        this._socket.addEventListener(Socket.RESPONSE_IS_TAKING_TOO_LONG, onResponseTakingTooLong, this);
        this._socket.addEventListener(Socket.RESPONSE_TAKING_TOO_LONG_RECEIVED, onResponseTakingTooLongReceived, this);
        this._socket.addEventListener(Socket.REQUEST_TIMED_OUT, onRequestTimedOut, this);
        this._socket.addEventListener(Socket.SERVER_EVENT_RECEIVED, onServerEvent, this);
        
        this._sessionKeeper = new SessionKeeper(this._socket, this._initVars.sessionKey);
        
        //var self = this;
       //this.lostConnectionButton = $("<div class='lostConnectionButton'>");
		//this.lostConnectionButton.on("touchstart mousedown", function(event) { self._socket.close(); });
		//this.lostConnectionButton.css({position: "absolute", zIndex: "9999", width: "75px", height:"75px", background: "#f00"});
		//this.$document.append(this.lostConnectionButton);
        
//        var self = this;
//        this.debugTextField = $("<div id='debugField'>");
//    	this.$document.append(this.debugTextField);
    }
    
    function requestLogin()
    {    
        this._socket.sendRequest(
        {
            command     : "login",
            qName       : "jServer.gameManager.login",
            platformType: "mobile",
            sessionKey	: this._initVars.sessionKey
        }, 20);
    }
    
    function loadConfig()
    {
    	this.$error.hide();
    	
    	// TODO: config.xml should be removed and all settings for the game platform should
    	// be taken from PlatformSettings.xml
    	Loader.cacheTimestamp = (new Date()).getTime();
    	var configXMLLoader = new XMLLoader("config.xml");
        configXMLLoader.addEventListener(LoaderEvent.COMPLETE, onConfigXMLLoaded, this);
        configXMLLoader.addEventListener(LoaderEvent.ERROR, onConfigXMLError, this);
        configXMLLoader.load();
    }
    
    function showErrorPopUp(message, leftButtonText, leftButtonCallback, rightButtonText, rightButtonCallback, allowOverride)
    {
    	if(this.$error.is(":visible") && !this._allowErrorPopUpOverride)
    		return;
    	
    	if(allowOverride == undefined)
    		allowOverride = false;
    	
    	this._allowErrorPopUpOverride = allowOverride;
    	
        this.$error.css("display", "table");
        
        this.$error.find('.message').html(message);
        
        var leftButton = this.$error.find('.leftButton');
        var rightButton = this.$error.find('.rightButton');
        leftButton.removeClass("centeredErrorButton");
        rightButton.removeClass("centeredErrorButton");
        
        if(!leftButtonText || !leftButtonCallback)
        {
        	leftButton.hide();
        	leftButton = null;
        }
        if(!rightButtonText || !rightButtonCallback)
        {
        	rightButton.hide();
        	rightButton = null;
        }

        var self = this;
        if(leftButton)
        {
        	leftButton.html(leftButtonText);
	    	leftButton.show().bind('click touchend', function(e){
	            e.preventDefault();
	            leftButtonCallback.call(self);
	        });
	    	if(!rightButton)
	    		leftButton.addClass("centeredErrorButton");
        }
        if(rightButton)
        {
        	rightButton.html(rightButtonText);
	    	rightButton.show().bind('click touchend', function(e){
	            e.preventDefault();
	            rightButtonCallback.call(self);
	        });
	    	if(!leftButton)
	    		rightButton.addClass("centeredErrorButton");
        }
    }
    
    function goBack()
	{
		this._viewportHandler.exitFullscreen();
						document.body.style['display']='none';
					window.parent.postMessage('CloseGame',"*");
	}
	
	function reload()
	{
		this.$document.trigger('game.reload');
	}
    
    function onSocketConnected()
    {
    	this._socket.removeEventListener(Socket.SOCKET_CONNECTED, onSocketConnected, this);

        requestLogin.call(this);
    }
    
    function onSocketClosed()
    {
    	this.$waiting.hide();
    	
    	showErrorPopUp.call(this, PlatformSettings.localize("no_connecton"), "HOME", goBack);
    }

    function onResponseReceived(event)
    {    
    	var response = event.data;
    	
    	if(response.command == "login")
    	{
			this._loginResponse = response;
			
	    	if(response.msg != "success")
	    	{
	    		if(response.msg == "ipBlocked")
                    showErrorPopUp.call(this, PlatformSettings.localize("login_ip_blocked"), "HOME", goBack);
	    		else
	    			showErrorPopUp.call(this, PlatformSettings.localize("login_error"), "HOME", goBack);
	    		return;
	    	}
			
			if(response.minimumSpinTime == undefined)
				response.minimumSpinTime = 0;
			
			if(response.languages.indexOf(this._initVars.lang) < 0)
				PlatformSettings.language = "en"
	    	
	        this._balance = new Balance(response.balance, response.currency);

	        this._gameList = response.complex;

            if(!isCanvasSupported())
            {
                if(Device.isiOS)
                    showErrorPopUp.call(this, "No Canvas the game is optimized for iOS 7", "HOME", goBack, null, null, true);
                else
                    showErrorPopUp.call(this, "No Canvas the game is optimized for Chrome 30", "HOME", goBack, null, null, true);
            } else if(!SoundManager.getInstance().isSupported())
			{
				if(Device.isiOS)
					showErrorPopUp.call(this, "No Sound the game is optimized for iOS 7", "HOME", goBack, "CONTINUE", loadConfig, true);
				else
					showErrorPopUp.call(this, "No Sound the game is optimized for Chrome 30", "HOME", goBack, "CONTINUE", loadConfig, true);
			}
	        else
	        	loadConfig.call(this);
	        
	        this._sessionKeeper.start();
    	}
    	else
    	{
    		if(response.balance != undefined && response.balance != null)
    			this._balance.value = response.balance;
    	}
    }

    function isCanvasSupported(){
        var elem = document.createElement('canvas');
        return !!(elem && elem.getContext && elem.getContext('2d'));
    }
    
    function onResponseTakingTooLong(event)
    {
    	var response = event.data;
    	if(response.command != "bet")
	    	return;
    	
    	this.$waiting.show();
    }
    
    function onResponseTakingTooLongReceived(event)
    {
    	var response = event.data;
    	if(response.command != "bet")
    		return;
    	
    	this.$waiting.hide();
    }
    
    function onRequestTimedOut(event)
    {
    	var request = event.data;

    	if(request.command == "ping")
    		return;
    	
    	this._socket.close();
    }
    
    function onServerEvent(event)
    {
    	var eventData = event.data;
    	
    	if(eventData.reason == "override")
    		showErrorPopUp.call(this, PlatformSettings.localize("session_override"), "HOME", goBack);
    	else if(eventData.reason == "sessionTimeout")
    		showErrorPopUp.call(this, PlatformSettings.localize("session_timeout"), "HOME", goBack);
    	else if(eventData.reason == "connectionClosed")
    		showErrorPopUp.call(this, PlatformSettings.localize("connection_closed"), "HOME", goBack);
        else if(eventData.reason == "maintenance")
            showErrorPopUp.call(this, PlatformSettings.localize("maintenance"), "HOME", goBack);
    	else if(eventData.reason == "shutdown")
    	{
    		var popUp = new PopUp(PlatformSettings.localize("shutdown_title"), PlatformSettings.localize("shutdown_message"), "OK");
    		popUp.addEventListener(PopUp.BUTTON1_CLICK, function(event) 
    								{ 
    									event.target.dispose();
    								}, this);
    		
    		$("#Game").append(popUp._containerDiv);
    	}
    }
    
    function onConfigXMLLoaded(event)
    {
    	event.target.removeEventListener(LoaderEvent.COMPLETE, onConfigXMLLoaded, this);
    	event.target.removeEventListener(LoaderEvent.ERROR, onConfigXMLError, this);
    	
    	var xmlConfig = $(event.data);
    	
    	var gameTypeToStart = null;
		var gin = this._initVars.gameIdentificationNumber;
		
		if(gin == undefined)
		{
			gameTypeToStart = this._initVars.gameType;

			if (this._gameList[gameTypeToStart] != null) {
                gin = this._gameList[gameTypeToStart][0].gameIdentificationNumber;
            } else {
				gameTypeToStart = null;
			}
		}
		else 
		{
			for(var index in this._gameList) 
			{ 
				// TODO: shouldn't this check be removed
				if (this._gameList.hasOwnProperty(index)) 
				{
					var attr = this._gameList[index];
					var len = attr.length;
					for(var i = 0;i < len;i++)
					{
						if(attr[i].gameIdentificationNumber == gin)
						{
							gameTypeToStart = index;
							break;
						}
					}
				   
					if(gameTypeToStart)
						break;
				}
			}
		}

		if(gameTypeToStart == null) {
            showErrorPopUp.call(this, PlatformSettings.localize("game_filtered_error"), "HOME", goBack);

            return;
        }
		
		var enginesTag = xmlConfig.find("engines");
		var engineNodes = enginesTag.children();
		var engineType = undefined;

		var self = this;
		engineNodes.each(function()
		{
			var gameFound = false;
			$(this).children().each(function()
			{
				if($(this).attr("name") == gameTypeToStart)
				{
					gameFound = true;
					return false;
				}
			});
			
			if(gameFound)
			{
				engineType = $(this).attr("type");
				Loader.cacheTimestamp = $(this).attr("buildTimestamp");
				return false;
			}
		});
		
		if(!engineType)
		{
			if(this._gameList[gameTypeToStart][0].recovery != "norecovery")
			{
				showErrorPopUp.call(this, PlatformSettings.localize("game_unavailable_error"), "HOME", goBack); 
			}
			else
			{
				throw gameTypeToStart + " does not have a mobile version!";
			}
		}

        var commonTimestamp =  $(xmlConfig.find("common")).attr("buildTimestamp");
        var buildTimestamp =  $(xmlConfig.find(engineType)).attr("buildTimestamp");

		this._gameProxy = new SlotProxy(this._socket, {gameType: gameTypeToStart, 
			engineType: engineType, gin: gin, 
            sessionKey: this._initVars.sessionKey, language: PlatformSettings.language,
            login: this._loginResponse, commonTimestamp: commonTimestamp, buildTimestamp: buildTimestamp}, this._balance);
		this._gameProxy.addEventListener(GameProxy.GAME_LOADING_PROGRESS, onGameProgress, this);
		this._gameProxy.addEventListener(GameProxy.GAME_LOADING_COMPLETE, onGameLoaded, this);
		this._gameProxy.addEventListener(GameProxy.GAME_LOADING_ERROR, onGameError, this);
		this._gameProxy.addEventListener(GameProxy.HIDE_GAME, goBack, this);
    }
    
    function onConfigXMLError(event)
    {
    	event.target.removeEventListener(LoaderEvent.COMPLETE, onConfigXMLLoaded, this);
    	event.target.removeEventListener(LoaderEvent.ERROR, onConfigXMLError, this);
    }

    function onGameProgress(event)
    {
    	var percent = parseInt(100 * event.data);
    	this._loadingHandler.setProgress(percent);
    }
    
    function onGameLoaded(event)
    {
    	this._gameProxy.removeEventListener(GameProxy.GAME_LOADING_PROGRESS, onGameProgress, this);
		this._gameProxy.removeEventListener(GameProxy.GAME_LOADING_COMPLETE, onGameLoaded, this);
		this._gameProxy.removeEventListener(GameProxy.GAME_LOADING_ERROR, onGameError, this);
		
    	this._loadingHandler.dispose();
    	this._viewportHandler.loadingEnded();
    	
    	console.log("DONE");
    }
    
    function onGameError(event)
    {
    	this._gameProxy.removeEventListener(GameProxy.GAME_LOADING_PROGRESS, onGameProgress, this);
		this._gameProxy.removeEventListener(GameProxy.GAME_LOADING_COMPLETE, onGameLoaded, this);
		this._gameProxy.removeEventListener(GameProxy.GAME_LOADING_ERROR, onGameError, this);
		
		showErrorPopUp.call(this, PlatformSettings.localize("content_error"), "HOME", goBack);
		
    	console.log("GAME LOADING ERROR");
    }
    
    return p;
})());