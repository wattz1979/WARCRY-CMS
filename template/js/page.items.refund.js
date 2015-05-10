/*
	A function to load item information for the Items Refund page
	- The function parameter is a jquery element of the item row
*/
function LoadItemInformation(row)
{
	var itemCont = row.find('.item-icon');
	var itemIcon = itemCont.find('img');
	var itemInfoCont = row.find('.item-info');
	var ItemNameCont = itemInfoCont.find('h2');
	var rel = itemCont.find('a').attr('rel');
	//let's get the item entry
	var $entry = parseInt(rel.replace('item=', ''));
	
	$.get($BaseURL + "/ajax.php?phase=1",
	{
		entry: $entry
	},
	function(data)
	{
		var $name = data.name;
		var $quality = data.quality_str.toLowerCase();
		var $icon = data.icon.toLowerCase();

		//set the icon
		itemIcon.css('background-image', 'url(http://wow.zamimg.com/images/wow/icons/large/'+$icon+'.jpg)');
		ItemNameCont.addClass($quality);
		ItemNameCont.html($name);
		
		//run the queue
		WarcryQueue('REFUND').goNext();
	});
}

/*
	A function called for item refunds
*/
function RefundItem(id)
{
	//prepare the ajax error handlers
	$.ajaxSetup(
	{
		error:function(x,e)
		{
			//prompt the error
			$.fn.WarcryAlertBox('open', '<p>The Item Refund System encountered server error, please try again.</p>');
			console.log('Ajax error: ' + x.status);
		}
	});
	
	//Run the refund
	$.post($BaseURL + "/execute.php?take=item_refund",
	{
		id: id
	},
	function(data)
	{
		if (data == 'OK')
		{
			document.location.reload(true);
		}
		else
		{
			//The errors are handled by the internal error system, so just refresh
			$.fn.WarcryAlertBox('open', '<p>'+data+'</p>');
		}
	});
	
	return false;
}

$(document).ready(function()
{
	//Update the items information
	$('.item-row').each(function(index, element)
	{
		WarcryQueue('REFUND').add(function()
		{
			LoadItemInformation($(element));
		});
	});
	//Run the queue
	WarcryQueue('REFUND').goNext();
});