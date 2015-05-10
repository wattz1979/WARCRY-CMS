// JavaScript Document

function restoreFormData(formName, data)
{
	var thisForm = $(document).find('form[name="'+formName+'"]');
	
	//loop the data	
	$.each(data, function(key, value)
	{ 
		//find the element
  		var element = $(thisForm).find('[name="'+key+'"]');
		
		if (typeof value == 'object')
		{
			//handle post arrays
			restoreFormData_withKey(formName, value, key);
			return;
		}
				
		//if we found our element get the type
		if (element.length > 0)
		{		
			var elementType = element.get(0).tagName;
		}
		else
		{
			//try another serach
  			element = $(thisForm).find('[name*="'+key+'"]');
			
			//check if we got some elements
			if (element.length > 0)
			{
				var elementType = element.get(0).tagName;
			}
		}
				
		if (elementType == 'INPUT')
		{
			if ($(element).attr('type') == 'checkbox')
			{
				element.each(function(i, e)
				{
					//###############################################################################//
					// Handle checkboxes
					//###############################################################################//
					if ($(e).attr('type') == 'checkbox')
					{
						//handle multiple checks
						if (typeof value == 'object')
						{
							$.each(value, function(k, v)
							{
								//if the element is not already checked
								if (e.checked == false)
								{
									//if our element is supposed to be checked, match the element name with the current value
									if ($(e).attr('name').indexOf(k) > -1)
									{
										e.checked = true;
										$(e).trigger('change');
									}
								}
							});
						}
						else
						{
							//if the element is not already checked
							if (e.checked == false)
							{
								//if our element is supposed to be checked, match the element name with the current value
								if ($(e).attr('name').indexOf(value) > -1)
								{
									e.checked = true;
									$(e).trigger('change');
								}
							}
						}
					}
					//###############################################################################//
					//###############################################################################//
                });
			}
			else if ($(element).attr('type') == 'radio')
			{
				var $radioElements = $(element);
				var $radioValue = value;
				
				$(document).ready(function()
				{
					$radioElements.each(function(i, e)
					{					
						//check if the current radio is the selected one
                    	if ($(e).val() == $radioValue)
						{
							if (e.checked == false)
							{
								$(e).attr('checked', true);
								setTimeout(function(){ $(e).trigger('click');}, 1000);
							}
						}
						else
						{
							$(e).removeAttr("checked");
						}
                	});    
                });
			}
			else if ($(element).attr('type') == 'text' || $(element).attr('type') == 'hidden')
			{
				element.val(value);
				element.trigger('change');
			}
			else
			{
				console.log("Form Data Restoration: Undefined Input type \""+$(element).attr('type')+"\".");
			}
		}
		else if (elementType == 'TEXTAREA')
		{
			element.html(value);
			$(element).trigger('change');
		}
		else if (elementType == 'SELECT')
		{
			//find the default selected option
			$(element).find('option[selected="selected"]').attr('selected', null);
			//apply the new one
			var option = $(element).find('option[value="'+value+'"]');
			option.attr("selected", "selected");
			$(element).trigger('change');
		}
		else
		{
			console.log("Form Data Restoration: Undefined element type \""+elementType+"\", field name: \""+key+"\".");
		}
		
	});
}

function restoreFormData_withKey(formName, data, prefix)
{
	var thisForm = $(document).find('form[name="'+formName+'"]');
	
	//loop the data	
	$.each(data, function(key, value)
	{ 
		//find the element
  		var element = $(thisForm).find('[name="'+prefix+'['+key+']"]');
						
		//if we found our element get the type
		if (element.length > 0)
		{		
			var elementType = element.get(0).tagName;
		}
		else
		{
			//try another serach
  			element = $(thisForm).find('[name*="'+key+'"]');
			
			//check if we got some elements
			if (element.length > 0)
			{
				var elementType = element.get(0).tagName;
			}
		}
				
		if (elementType == 'INPUT')
		{
			if ($(element).attr('type') == 'checkbox')
			{
				element.each(function(i, e)
				{
					//###############################################################################//
					// Handle checkboxes
					//###############################################################################//
					if ($(e).attr('type') == 'checkbox')
					{
						//handle multiple checks
						if (typeof value == 'object')
						{
							$.each(value, function(k, v)
							{
								//if the element is not already checked
								if (e.checked == false)
								{
									//if our element is supposed to be checked, match the element name with the current value
									if ($(e).attr('name').indexOf(k) > -1)
									{
										e.checked = true;
									}
								}
							});
						}
						else
						{
							//if the element is not already checked
							if (e.checked == false)
							{
								//if our element is supposed to be checked, match the element name with the current value
								if ($(e).attr('name').indexOf(value) > -1)
								{
									e.checked = true;
								}
							}
						}
					}
					//###############################################################################//
					//###############################################################################//
                });
			}
			else if ($(element).attr('type') == 'radio')
			{
				var $radioElements = $(element);
				var $radioValue = value;
				
				$(document).ready(function()
				{
					$radioElements.each(function(i, e)
					{					
						//check if the current radio is the selected one
                    	if ($(e).val() == $radioValue)
						{
							if (e.checked == false)
							{
								$(e).attr('checked', true);
								console.log("checking input: "+$radioValue);
							}
						}
						else
						{
							$(e).removeAttr("checked");
						}
                	});    
                });
			}
			else if ($(element).attr('type') == 'text' || $(element).attr('type') == 'hidden')
			{
				element.val(value);
				element.trigger('change');
			}
			else
			{
				console.log("Form Data Restoration: Undefined Input type \""+$(element).attr('type')+"\".");
			}
		}
		else if (elementType == 'TEXTAREA')
		{
			element.html(value);
		}
		else if (elementType == 'SELECT')
		{
			var option = $(element).find('option[value="'+value+'"]');
			option.attr("selected", "selected");
		}
		else
		{
			console.log("Form Data Restoration: Undefined element type \""+elementType+"\".");
		}
		
	});
}
