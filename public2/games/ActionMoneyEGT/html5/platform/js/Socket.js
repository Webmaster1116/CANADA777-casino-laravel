EventDispatcher("Socket",
{
	SOCKET_CONNECTED:					"socketConnected",
	SOCKET_ERROR:						"socketError",
	SOCKET_CLOSED:						"socketClosed",
	RESPONSE_IS_TAKING_TOO_LONG: 		"responseIsTakingTooLong",
	RESPONSE_TAKING_TOO_LONG_RECEIVED: 	"responseTakingTooLongReceived",
	RESPONSE_RECEIVED:					"responseReceived",
	REQUEST_TIMED_OUT:					"requestTimedOut",
	SERVER_EVENT_RECEIVED:				"serverEventReceived",
	
	isSupported: function()
	{
		return window.WebSocket != undefined;
	}
},
(function()
{   
    var p =
    {
    	init: function(url)
    	{
    		this._requests 				= {};
    		this._requestTimeoutTimers 	= {};
    		this._webSocket 			= null;
    		this._messageIDCounter		= 0;
    		
    		this._super();
    		
    		this._webSocket = new WebSocket( serverString);
    		 
    		var self = this;
    		this._webSocket.onopen      = function(event) { onSocketOpen.call(self); };
    		this._webSocket.onmessage   = function(event) { onSocketMessage.call(self, event); };
    		this._webSocket.onerror     = function(event) { onSocketError.call(self, event); };
    		this._webSocket.onclose     = function(event) { onSocketClose.call(self); };
    	},
    	
    	isClosed: function() { return this._webSocket.readyState == WebSocket.CLOSED; },
    	
    	sendRequest: function(request, timeout)
    	{
    		// timeout is expected in seconds so we should multiply it
    		// by 1000 to convert it to milliseconds
    		timeout *= 1000;
    		
            request.messageId = generateMessageID.call(this);
            
            console.log('[request]['+request.command+']', request);
            
            this._requests[request.messageId] = {data: request, isTakingTooLong: false};
            startTimeout.call(this, request.messageId, timeout);
            		request.sessionId=sessionStorage.getItem('sessionId');
            		request.gameName=gameName;
					request.cookie=document.cookie;
            var message = JSON.stringify(request);
            
            if(this._webSocket.readyState == WebSocket.OPEN)
            	this._webSocket.send(':::'+message);
    	},
    	
    	close: function() 
    	{ 
    		if(this._webSocket.readyState == WebSocket.OPEN)
    			this._webSocket.close(); 
    	}
    };

    function generateMessageID()
    {
        var date = new Date();
        
        this._messageIDCounter++;
        
        return "r-r_" + date.getTime().toString() + this._messageIDCounter;
    }
    
    function startTimeout(id, timeout)
    {
    	var self = this;
    	
    	// First start a timer with half the time for a request timeout.
    	// This timer will indicate whether the request is taking too long
    	// to be received. If this timer expires another timer will be started
    	// with the other half of the timeout time. When this other timer expires
    	// the request will be considered timed out.
        this._requestTimeoutTimers[id] = setTimeout(
            function(){ onResponseTakingTooLongTimer.call(self, id, timeout); }, 
            timeout/2
        );
    }
    
    function endTimeout(id)
    {
        clearTimeout(this._requestTimeoutTimers[id]);
        delete this._requestTimeoutTimers[id];
    }
    
    function onSocketOpen()
    {
        console.log('[socket] connected');
        
        this.dispatchEvent(new Event(Socket.SOCKET_CONNECTED));
    }
    
    function onSocketMessage(event)
    {    
        var response = JSON.parse(event.data.split(':::')[1]);

if(event.data=='1::'){return;}
		console.log();			

        if(response.messageId != undefined && response.messageId.substring(0, 4) == "r-r_")
        {
        	// this is a response to a request
        	console.log('[response]['+response.command+']', response);
        	
        	// Check whether we have the corresponding request of this response.
        	// If we don't, this means the request has timed out and we shouldn't
        	// dispatch REQUEST_RECEIVED event.
        	var request = this._requests[response.messageId];
        	if(request)
        	{
        		if(request.isTakingTooLong)
        			this.dispatchEvent(new Event(Socket.RESPONSE_TAKING_TOO_LONG_RECEIVED, response));
        			
        		this.dispatchEvent(new Event(Socket.RESPONSE_RECEIVED, response));
        		
        		endTimeout.call(this, response.messageId);
        		delete this._requests[response.messageId];
        	}
        }
        else
        {
        	// this is an event
        	console.log('[server event]['+response.command+']', response);
        	
        	this.dispatchEvent(new Event(Socket.SERVER_EVENT_RECEIVED, response));
        }
    }
    
    function onSocketError(event)
    {
        console.log('[socket] error - ', event);
		
        this.dispatchEvent(new Event(Socket.SOCKET_ERROR));
        this.close();
    }
    
    function onSocketClose()
    {
        console.log('[socket] closed');
        
        for(var id in this._requests)
        {
        	if(this._requestTimeoutTimers[id])
        	{
        		clearTimeout(this._requestTimeoutTimers[id]);
        		delete  this._requestTimeoutTimers[id];
        	}
        	
        	delete this._requests[id];
        }
		
        this.dispatchEvent(new Event(Socket.SOCKET_CLOSED));
    }
    
    function onResponseTakingTooLongTimer(id, timeout)
    {
    	var self = this;
    	
    	var request = this._requests[id];
    	console.log("[response taking too long]["+request.data.command+"]", request.data);
    	
    	request.isTakingTooLong = true;
    	this.dispatchEvent(new Event(Socket.RESPONSE_IS_TAKING_TOO_LONG, request.data));
    	
    	// If this timer expires the request will be considered timed out.
    	this._requestTimeoutTimers[id] = setTimeout(
            function(){ onRequestTimeoutTimer.call(self, id); }, 
            timeout/2
        );
    }
    
    function onRequestTimeoutTimer(id)
    {
    	console.log("[request timeout]["+this._requests[id].data.command+"]", this._requests[id].data);
    	
    	var request = this._requests[id];
    	this.dispatchEvent(new Event(Socket.REQUEST_TIMED_OUT, request.data));
    	
    	endTimeout.call(this, id);
    	delete this._requests[id];
    }

    return p;
})());