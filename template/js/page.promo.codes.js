var PCODE_REWARD_CURRENCY_S = 1;
var PCODE_REWARD_CURRENCY_G = 2;
var PCODE_REWARD_ITEM 		= 3;
var CURRENCY_SILVER = 1;
var CURRENCY_GOLD	= 2;

var PromoCodes =
{
	BindHandlers: function()
	{
		//Handle Promo-Code Input
		$('input#code').keydown(function(event)
		{
			var text = $(this).val();
			var key = event.which || event.keyCode || event.charCode;
			
			//Backspace || delete
			if (key == 8)
			{
				if (text.charAt(text.length - 1) == '-')
					$(this).val(text.substr(0, text.length - 1));
				
				//Reset the windows if needed
				if (text.length < 14)
					PromoCodes.Reset();
			}
			
			//listen for the enter key
			if (text.length == 14 && key == 13)
			{
				//Blur the input which will trigger the reward lookup
				$(this).blur();
				//Prevent the defailt action
				event.preventDefault();
				return false;
			}
			
			if ((text.length > 13 && key != 8) || key == 13)
			{
				//Prevent the defailt action
				event.preventDefault();
				return false;
			}
		});
		
		$('input#code').keyup(function(event)
		{
			var text = $(this).val();
			var clean = text.replace(/-/g, '');
			var len = text.length;
			var newText = '';

			for (var i = 0; i < clean.length; i++)
			{
				newText += clean.charAt(i);
				
				if (i == 3 || i == 7)
				{
					newText += '-';
				}
			}
			
			$(this).val(newText);
		});
		
		//Handle reward lookup
		$('input#code').blur(function()
		{
			if ($('input#code').val().length == 14)
			{
				PromoCodes.Show();
				//Visualize the code reward
				PromoCodes.LookupCode();
			}
			else
			{
				PromoCodes.Reset();
			}
		});
		
		$('input#code').focus(function()
		{
            PromoCodes.Reset();
        });
		
		//Character select handler
		$('#character-select').change(function(event)
		{
            var value = $(this).find('option:selected').val();
			
			//Make sure we have a character
			if (typeof value != 'undefined' && value != null && value.length > 0)
			{
				//put the value onto the real input
				$('#real-char-select').val(value);
				//Show the tip how to complete
				PromoCodes.CompletionTip(true);
				//Hook the submit
				PromoCodes.HookSubmit(true);
			}
        });
	},
	
	Reset: function()
	{
		//Hide the main window
		PromoCodes.Hide();
		//Hide the Tip
		PromoCodes.CompletionTip(false);
		//Hide the Error
		PromoCodes.Error(false);
		//Unbind the form
		PromoCodes.HookSubmit(false);
		//Hide the character select
		PromoCodes.CharacterSelect(false);
	},
	
	HookSubmit: function(bool)
	{
		if (bool)
		{
			$('#promo-code-form').off('submit');
			$('#promo-code-form').on('submit', function(){ return true; });
			//Global Enter Listener
			$(document).on('keydown', function(event)
			{
				var key = event.which || event.keyCode || event.charCode;
				
				if (key == 13)
				{
					$('#promo-code-form').submit();
				}
			});
		}
		else
		{
			$('#promo-code-form').off('submit');
			$('#promo-code-form').on('submit', function(){ return false; });
			$(document).off('keydown');
		}
	},
	
	Show: function()
	{
		WarcryQueue('PCODES').add(function()
		{
			//Fade in the reward container
			$('.reward_container').fadeIn('fast', function()
			{
				WarcryQueue('PCODES').goNext();
			});
			//Loading should be visible
			PromoCodes.Loading(true);
		});
		
		//Run the queue
		WarcryQueue('PCODES').goNext();
	},
	
	Hide: function()
	{
		//Fade out the reward container
		$('.reward_container').fadeOut('fast', function()
		{
			$('#reward-type-silver').css('display', 'none');
			$('#reward-type-gold').css('display', 'none');
			$('#reward-type-item').css('display', 'none');
			$('#reward_loading').css('display', 'block');
		});
	},
	
	Error: function(text)
	{
		if (text)
		{
			$('#invalid').html(text).fadeIn('fast', function()
			{
				WarcryQueue('PCODES').goNext();
			});
		}
		else
		{
			
			$('#invalid').html('').fadeOut('fast');
		}
	},
	
	CompletionTip: function(bool)
	{
		if (bool)
		{
			$('#enter').fadeIn('fast');
		}
		else
		{
			$('#enter').fadeOut('fast');
		}
	},
	
	Loading: function(bool)
	{
		if (bool)
		{
			$('.reward_loading').css('display', 'block');
		}
		else
		{
			$('.reward_loading').css('display', 'none');
		}
	},
	
	LookupCode: function()
	{
		var code = $('input#code').val();
		
		$.ajaxSetup(
		{
			error: function(xhr, status, error)
			{
				console.log("An AJAX error occured: " + status + "\nError: " + error);
			},
			dataType: "json"
		});
		
		//Get some info about the code
		$.get($BaseURL + '/ajax.php?phase=23', 
		{
			code: code
		},
		function(data)
		{
			//Check if we have an error
			if (typeof data.error != 'undefined')
			{
				PromoCodes.Error(data.error);
				PromoCodes.Hide();
			}
			else
			{
				//Hide the error
				PromoCodes.Error(false);
				//Visualize the reward
				PromoCodes.VisualizeReward(data);
			}
		});
	},
	
	VisualizeReward: function(data)
	{
		var rewardType = parseInt(data.reward_type);
		var rewardValue = parseInt(data.reward_value);
		
		switch (rewardType)
		{
			case PCODE_REWARD_CURRENCY_S:
				PromoCodes.VisualizeCurrency(CURRENCY_SILVER, rewardValue);
				break;
			case PCODE_REWARD_CURRENCY_G:
				PromoCodes.VisualizeCurrency(CURRENCY_GOLD, rewardValue);
				break;
			case PCODE_REWARD_ITEM:
				PromoCodes.VisualizeItem(rewardValue);
				break;
		}
	},
	
	VisualizeCurrency: function(currency, value)
	{
		//Hide the loading
		PromoCodes.Loading(false);
		
		switch (currency)
		{
			case CURRENCY_SILVER:
				$('#reward-type-silver #value').html(value);
				$('#reward-type-silver').stop().fadeIn('fast');
				break;
			case CURRENCY_GOLD:
				$('#reward-type-gold #value').html(value);
				$('#reward-type-gold').stop().fadeIn('fast');
				break;
		}
		
		//Show the tip how to complete
		PromoCodes.CompletionTip(true);
		
		//Hook the submit
		PromoCodes.HookSubmit(true);
	},
	
	VisualizeItem: function(entry)
	{
		//prepare the ajax error handlers
		$.ajaxSetup({
			error: function(xhr, status, error)
			{
				console.log("An AJAX error occured: " + status + "\nError: " + error);
			},
			dataType: "json",
		});
					
		$.get("ajax.php?phase=1",
		{
			entry: entry
		},
		function(data)
		{
			var name = data.name;
			var quality = data.quality_str;
			var icon = data.icon;
			var subclass = data.subclass_str;
			var inventorySlot = data.InventoryType_str;
			
			if (subclass == 'Pet')
				subclass = 'Companion';
			
			//set the icon
			$('#reward-type-item .ico').css('background', 'url(http://wow.zamimg.com/images/wow/icons/large/' + icon.toLowerCase() + '.jpg)');
			//set the name
			$('#reward-type-item #name').html(name);
			//set the subclass
			$('#reward-type-item #subclass').html(subclass);
			
			//Hide the loading
			PromoCodes.Loading(false);
			//Promt the character select
			PromoCodes.CharacterSelect(true);
			
			//Show the item container
			WarcryQueue('PCODES').add(function()
			{
				$('#reward-type-item').stop().fadeIn('fast', function()
				{
					WarcryQueue('PCODES').goNext();
				});
			});
			//Run it
			WarcryQueue('PCODES').goNext();
		});
	},
	
	CharacterSelect: function(bool)
	{
		if (bool)
		{
			WarcryQueue('PCODES').add(function()
			{
				$('.pcode-chat-select-cont').fadeIn('fast', function()
				{
					//Trigger the select change event
					//there might be a previusly selected char
					$('#character-select').change();
					//continue
					WarcryQueue('PCODES').goNext();
				});
			});
		}
		else
		{
			$('.pcode-chat-select-cont').fadeOut('fast');
		}
	}
};

//Initialize
$(function()
{
	PromoCodes.BindHandlers();
});