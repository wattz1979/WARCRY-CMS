//Here we are going to do the structure of the selects with the categories
//Define the main categories
var BT_CAT_WEBSITE 		= 1;
var BT_CAT_WOTLK_CORE 	= 2;
//Define the last select main category
var LastdMainCat = false;
var LastCat = false;

//A function to manage main category change
function showCategories(e)
{
	var $this = $(e);
	//find the selected option
	var selected = $this.find('option[selected="selected"]');
	var mainCategory = selected.val();
	
	if (mainCategory == 0)
	{
		return false;
	}
	
	//FadeOut and destroy any old selects
	WarcryQueue('BUGTRACKER').add(function()
	{
		$('#category-select').fadeOut('fast', function()
		{
			$('#category-select').find('select').detach();
			$('#category-select').find('.js-select').detach();
			WarcryQueue('BUGTRACKER').goNext();
		});
		$('#subcategory-select').fadeOut('fast', function()
		{
			$('#subcategory-select').find('select').detach();
			$('#subcategory-select').find('.js-select').detach();
		});
		LastCat = false;
	});

	//Check if we have the category data
	WarcryQueue('BUGTRACKER').add(function()
	{
		//Set the opacity and display
		$('#category-select').css({ opacity: 0, display: 'inline-block' });

		if (typeof $(document).data('BTCats'+mainCategory) == 'undefined')
		{
			$.ajax({
				type: "GET",
				url: $BaseURL + "/ajax.php?phase=13",
				data: { category: mainCategory },
				dataType: 'json',
				cache: false,
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
				},
				success: function(data)
				{
				   $(document).data('BTCats'+mainCategory, data);
				   //continue the queue
				   WarcryQueue('BUGTRACKER').goNext();
				}
			});
		}
		else
		{
			//continue the queue
			WarcryQueue('BUGTRACKER').goNext();
		}
	});
	
	//FadeIn and add the new select
	WarcryQueue('BUGTRACKER').add(function()
	{
		var data = $(document).data('BTCats'+mainCategory);

		//append the new select
		var newSelect = $('<select id="sub-cat-select" name="category" onchange="return showSubCategories(this);"><option selected="selected" disabled="disabled">Select sub-category</option></select>');
		$('#category-select').append(newSelect);
		//append the options
		$.each(data, function(k, v)
		{
			//append each category to the new select
			$(newSelect).append('<option value="'+k+'">'+v.name+'</option>');
		});
		//do the select customization
		$(newSelect).SelectTransform();
		//fadein
		$('#category-select').animate({ opacity: 1 }, 'fast', function()
		{
			//continue the queue
			WarcryQueue('BUGTRACKER').goNext();
		});
	});

	//run the queue
	WarcryQueue('BUGTRACKER').goNext();
	
	LastdMainCat = mainCategory;
	
	return true;
}

//A function to manage category change
function showSubCategories(e)
{
	var $this = $(e);
	var mainCategory = LastdMainCat;
	//find the selected option
	var selected = $this.find('option[selected="selected"]');
	var category = selected.val();
	//the data must be already pulled
	var data = $(document).data('BTCats'+mainCategory);
	
	//Destroy old select
	WarcryQueue('BUGTRACKER').add(function()
	{
		$('#subcategory-select').fadeOut('fast', function()
		{
			$('#subcategory-select').find('select').detach();
			$('#subcategory-select').find('.js-select').detach();
			//continue the queue
			WarcryQueue('BUGTRACKER').goNext();
		});
		LastCat = false;
	});
	
	WarcryQueue('BUGTRACKER').add(function()
	{
		//Set the opacity and display
		$('#subcategory-select').css({ opacity: 0, display: 'inline-block' });
		//find our category in the data
		$.each(data, function(k, v)
		{
			//find the key we are looking for
			if (k == category)
			{
				//check if we have sub categories
				if (v.subcats !== false)
				{
					//append the new select
					var newSelect = $('<select id="select-class" name="subcategory"><option selected="selected" disabled="disabled">Select specifics</option></select>');
					$('#subcategory-select').append(newSelect);
					//append the options
					$.each(v.subcats, function(key, value)
					{
						//append each category to the new select
						$(newSelect).append('<option value="'+key+'">'+value+'</option>');
					});
					//do the select customization
					$(newSelect).SelectTransform();
					//fadein
					$('#subcategory-select').animate({ opacity: 1 }, 'fast');
				}
			}
		});
	});
	
	//run the queue
	WarcryQueue('BUGTRACKER').goNext();
	
	return true;
}
