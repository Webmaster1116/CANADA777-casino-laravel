EventDispatcher("GameProxy",
{
	GAME_LOADING_PROGRESS: 	"gameLoadingProgress",
	GAME_LOADING_ERROR:		"gameLoadingError",
	GAME_LOADING_COMPLETE:	"gameStarted",
	HIDE_GAME:				"hideGame",

	MSG_SUCCESS: "success",
	MSG_INSUFFICIENT_FUNDS:"insufficientFunds",
	MSG_REALITY_CHECK:"doRealityCheck",
	MSG_BET_LIMIT:"betLimitReached",
	MSG_LOSS_LIMIT:"lossLimitReached",
	MSG_TIME_LIMIT:"sessionTimeLimitReached",
	MSG_TIME_PROXIMITY_ALERT:"timeProximityAlert",
	MSG_CREDIT_LEFT_ALERT:"creditLeftAlert",
},
(function()
{
	var p =
	{
		init: function(socket, config, balance)
		{
			this._socket = socket;
			this._config = config;
			this._balance = balance;

			this._gameInterface = null;
			
			// TODO: maybe the two protected methods _onResponse ..... should be
			// connected to the socket here

			this._super();
		},
		
		dispose: function()
		{
			this._scoket = null;
			
			this._super();
		},
		
		_onResponseReceived: function(event) 
		{
			var response = event.data;

			checkForResponsibleGaming.call(this, response);
		},
		_onRequestTimeout: function(request) {}
	};

	function checkForResponsibleGaming(response)
	{
		switch(response.msg)
		{
			case GameProxy.MSG_REALITY_CHECK:
				var messageId = response.totalBet == -1 || response.totalWin == -1 ?
					"reality_check_time_passed_only_message" : "reality_check_message";

				createPopUp.call(this, response, "reality_check_title", messageId,
					"exit_text", "ok_text", goBack, closePopUp);
				sendResponsibleGaming.call(this, response.msg);
			break;
			case GameProxy.MSG_TIME_PROXIMITY_ALERT:
				createPopUp.call(this, response, "time_proximity_title", "time_proximity_message",
					"ok_text", null, closePopUp);
				sendResponsibleGaming.call(this, response.msg);
			break;
			case GameProxy.MSG_CREDIT_LEFT_ALERT:
				createPopUp.call(this, response, "credit_left_title", "credit_left_message",
					"ok_text", null, closePopUp);
				sendResponsibleGaming.call(this, response.msg);
			break;
			case GameProxy.MSG_BET_LIMIT:
				createPopUp.call(this, response, "bet_limit_title", "bet_limit_message",
					"ok_text", null, goBack);
				sendResponsibleGaming.call(this, response.msg);
			break;
			case GameProxy.MSG_LOSS_LIMIT:
				createPopUp.call(this, response, "loss_limit_title", "loss_limit_message",
					"ok_text", null, goBack);
				sendResponsibleGaming.call(this, response.msg);
			break;
			case GameProxy.MSG_TIME_LIMIT:
				createPopUp.call(this, response, "time_limit_title", "time_limit_message",
					"ok_text", null, goBack);
				sendResponsibleGaming.call(this, response.msg);
			break;
		}
	}

	function createPopUp(response, titleId, messageId, leftButtonTextId, rightButtonTextId,
		leftButtonHandler, rightButtonHandler)
	{
    		var title = PlatformSettings.localize(titleId);
    		var message = PlatformSettings.localize(messageId);
    		var leftButtonText = PlatformSettings.localize(leftButtonTextId);

    		if(rightButtonTextId)
    		{
    			var rightButtonText = PlatformSettings.localize(rightButtonTextId);
    		}

    		message = renderTemplate.call(this, message, response);

    		var popUp = new PopUp(title, message, leftButtonText, rightButtonText);
    		
	    	popUp.addEventListener(PopUp.BUTTON1_CLICK, leftButtonHandler, this);

	    	if(rightButtonHandler)
	    	{
	    		popUp.addEventListener(PopUp.BUTTON2_CLICK, rightButtonHandler, this);
	    	}
    		
    		$("#Game").append(popUp._containerDiv);
	}

	function renderTemplate(template, values)
	{
		var pattern = /\(\([A-z:]+\)\)/g;
		var matches = template.match(pattern);

		if(matches == null)
			return template;

		var length = matches.length;
		for(var i = 0;i < length;i++)
		{
			var param = matches[i].substr(2, matches[i].length-4);
			var splitResult = param.split(":");
			var paramName = splitResult[0];
			var paramType = splitResult[1];

			template = template.replace(matches[i], formatValue.call(this, values[paramName], paramType));
		}

		return template;
	}

	function formatValue(value, type)
	{
		if(type == null || type == "String")
		{
			return value;
		}
		else if(type == "Money")
		{
			return formatMoney.call(this, value);
		}
	}

	function formatMoney(value)
    {
    	var currency = this._balance.getCurrency();

		return Utils.formatNumber(value, 100, true) + " " + currency;
    }

	function closePopUp(event)
	{
		event.target.dispose(); 
		sendResponsibleGamingComplete.call(this);
	}

	function goBack(event)
	{
		this.dispatchEvent(new Event(GameProxy.HIDE_GAME, this._config.gin));
	}

	function sendResponsibleGaming(msg)
	{
		var data = {
    		command: "responsibleGaming",
    		msg: msg
    	};

    	this._gameInterface.setData(data);
	}

	function sendResponsibleGamingComplete()
	{
		var data = {
    		command: "responsibleGamingComplete"
    	};

    	this._gameInterface.setData(data);
	}
	
	return p;
})());