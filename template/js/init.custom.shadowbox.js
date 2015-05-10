/* 
	Custom initialization of shadowbox
	- Enables image captions
*/
Shadowbox.init(
{
	onFinish: function()
	{
		var obj = Shadowbox.getCurrent()

		var innerBody = $('#sb-body-inner');
		//append the new div element containing the description
		innerBody.append('<div id="sb-description" style="position: absolute; left: 0px; opacity: 0;">'+obj.description+'</div>');
		//get the description container
		var desctCont = $('#sb-description');
		//set width
		desctCont.css({ width: Shadowbox.dimensions.innerWidth + 'px' });
		desctCont.css({ top: (Shadowbox.dimensions.innerHeight - desctCont.outerHeight()) + 'px' });
		//fade it in
		desctCont.animate({ opacity: 1 }, 'slow');
		//handle window resize
		$(window).resize(function()
		{
			//get the description container
			var desctCont = $('#sb-description');
			//set width
			desctCont.css({ width: Shadowbox.dimensions.innerWidth + 'px' });
			desctCont.css({ top: (Shadowbox.dimensions.innerHeight - desctCont.outerHeight()) + 'px' });
			//do that again
			setTimeout(function()
			{
				//set width
				desctCont.css({ width: Shadowbox.dimensions.innerWidth + 'px' });
				desctCont.css({ top: (Shadowbox.dimensions.innerHeight - desctCont.outerHeight()) + 'px' });
			}, 100);
		});
	}
});

Shadowbox.onReady = function()
{
	setTimeout(function()
	{
		//filter the titles
		for (var key in Shadowbox.cache)
		{
			var title = Shadowbox.cache[key].title;
			//split the title from the description
			var parts = title.split('{|}');
			//save the title and description
			if ($(parts).size() == 2)
			{
				Shadowbox.cache[key].title = parts[0];
				Shadowbox.cache[key].description = parts[1];
			}
			//remove the description part from the elements title attr
			$(Shadowbox.cache[key].link).attr('title', parts[0]);
		}
	}, 500);
};