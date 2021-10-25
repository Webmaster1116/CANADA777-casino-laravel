$.Class("LoadingHandler",
{
},
(function()
{
	var p =
	{
		init: function()
		{
			this._loadingDiv = $("#Loading");
			this._progress = this._loadingDiv.find('.progress');
			this._percent =  this._loadingDiv.find('.percent');
			this._fill = this._loadingDiv.find('.fill');
			this._star = this._loadingDiv.find('.star');
			this._text = this._loadingDiv.find('.text');
			this._progressShown = false;
			
			this._loadingDiv.show();
	        
			this._progress.children().hide();
			this._progress.removeClass('slider');
			this._text.show();
		},
		
		setProgress: function(progress)
		{
			if(!this._progressShown)
			{
				this._progress.addClass('slider');
				this._percent.html('0%').css('display', 'block');
				this._fill.width('0%').css('display', 'block');
				this._star.show();
				
				this._progressShown = true;
			}
			
			this._percent.html(progress+'%');
			this._fill.width(progress+'%');
			this._star.css("left", -20 + 488*(progress/100)+"px");
		},
		
		dispose: function()
		{
			this._loadingDiv.detach();
		}
	};
	
	return p;
})());