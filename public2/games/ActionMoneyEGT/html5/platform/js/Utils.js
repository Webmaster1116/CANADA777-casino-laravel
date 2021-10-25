function Utils() {}
 
Utils.formatNumber = function(x, base, formatCurrency, noCoins)
{
	if(typeof(formatCurrency) === "undefined")
		formatCurrency = false;
		
	if(typeof(noCoins) === "undefined")
		noCoins = false;
		
	var str = formatCurrency ? (x / base).toFixed(noCoins ? 0 : 2) : Math.floor(x / base).toString();
	
	var arr = str.split(".");
	var n = arr[0];
	var prefix = n.split('');
	
	if (formatCurrency)
	{
		var counter = 3;
		var i = prefix.length;
		while (--i > 0) 
		{
			counter--;
			if (counter == 0)
			{
			 counter = 3;
			 prefix.splice(i , 0, ' ');
			}
		}
	}
	
	if(arr[1])
		return (prefix.join('') + "." + arr[1]);
	else
		return (prefix.join(''));
}