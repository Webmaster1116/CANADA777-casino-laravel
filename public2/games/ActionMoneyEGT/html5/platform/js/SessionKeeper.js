$.Class("SessionKeeper",
{
	PING_TIMER_INTERVAL: 5000, // ms
	PING_TIME_LIMIT: 4000, // ms
	LOST_CONNECTION_TIME_LIMIT: 20000 // ms
},
(function()
{
	var p = 
	{
		init: function(socket, sessionKey)
		{
			this._socket = socket;
			this._sessionKey = sessionKey;
			this._pingTimer = 0;
			this._lastMessageTimestamp = 0;
			
			this._socket.addEventListener(Socket.SOCKET_CLOSED, onSocketClosed, this);
	        this._socket.addEventListener(Socket.RESPONSE_RECEIVED, onResponseReceived, this);
		},
		
		start: function()
		{
			if(this._pingTimer)
				return;
			
			var self = this;
			this._pingTimer = setInterval(function() { onPingTimer.call(self); }, SessionKeeper.PING_TIMER_INTERVAL);
		}
	};
	
	function sendPing()
	{
		this._socket.sendRequest(
        {
            command     : "ping",
            qName       : "jServer.gameManager.ping",
            sessionKey	: this._sessionKey
        }, 20);
	}
	
	function onResponseReceived(event)
	{
		var response = event.data;
		
		if(response.messageId != undefined && response.messageId.substring(0, 4) == "r-r_")
		{
			// this is a response to a request, save its time of arrival
			this._lastMessageTimestamp = (new Date()).getTime();
		}
	}
	
	function onSocketClosed()
	{
		if(this._pingTimer)
			this._pingTimer = clearInterval(this._pingTimer);
	}
	
	function onPingTimer()
	{
		var timeDiff = (new Date()).getTime() - this._lastMessageTimestamp;
		if(timeDiff <= SessionKeeper.LOST_CONNECTION_TIME_LIMIT)
		{
			if(timeDiff > SessionKeeper.PING_TIME_LIMIT)
				sendPing.call(this);
		}
		else
			// no connection was detected, close the socket
			this._socket.close();
	}
	
	return p;
})());