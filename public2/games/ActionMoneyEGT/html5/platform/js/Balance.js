$.Class("Balance",
{
},
(function()
{
	var p =
	{
		init: function(balance, currency)
		{
			this.value  	= balance;
			this._currency 	= currency;
		},
		getCurrency: function() { return this._currency; }
	};
	
	return p;
})());