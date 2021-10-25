$.Class("Device", 
{
	isiOS: false,
	isAndroid: false,
	isSafari: false,
	isChrome: false,
	isCriOS: false,
	isFirefox: false,
	scale: 1,
	
	detect: function()
	{
		if(navigator.userAgent.match(/Android/i))
			Device.isAndroid = true;
		else if(navigator.userAgent.match(/iPhone/i))
			Device.isiOS = true;
		else if(navigator.userAgent.match(/iPad/i))
			Device.isiOS = true;
		else if(navigator.userAgent.match(/iPod/i))
			Device.isiOS = true;
  
//		if(navigator.userAgent.match(/BlackBerry/i))
//			return self.set('blackberry');
//		  
//		if(navigator.userAgent.match(/webOS/i))
//			return self.set('webos');
//		  
//		if(navigator.userAgent.match(/Opera Mini/i))
//			return self.set('operamini');
//		  
//		if(navigator.userAgent.match(/Windows Phone/i))
//			return self.set('windows');
		
		if(Device.isiOS)
		{
			if(navigator.userAgent.match(/CriOS/i))
				Device.isCriOS = true;
			else if(navigator.userAgent.match(/Chrome/i))
				Device.isChrome = true;
			else if(navigator.userAgent.match(/Safari/i))
				Device.isSafari = true;
		}
		else
		{
			if(navigator.userAgent.match(/Chrome/i))
				Device.isChrome = true;
			else if(navigator.userAgent.match(/Firefox/i))
				Device.isFirefox = true;
		}
	}
},
{});