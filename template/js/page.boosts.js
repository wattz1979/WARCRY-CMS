//Boosts page
var CURRENCY_SILVER = 1;
var CURRENCY_GOLD = 2;
	
var Boosts =
{
	RealmId: 1,
				
	BindHandlers: function()
	{
		//Boosts click handler
		$('.select_boost > li > a').click(function()
		{
			var BoostId = parseInt($(this).attr('data-boost-id'));
			var parent = $(this).parent();
			
			//Prevent selecting disabled  boost
			if (parent.hasClass('disabled'))
			{
				return false;
			}
			
			//Update the selected boost input
			$('#selected-boost-id').val(BoostId);
			//Deselect the previously selected
			$('.select_boost').find('li.selected').removeClass('selected');
			//Update the visuals
			parent.addClass('selected');
			
			return false;
		});
		
		//Duration change handler
		//update prices per duration
		$('input[name="duration"]').change(function()
		{
			var duration = $(this).val();
			
			$.get($BaseURL + "/ajax.php?phase=21",
			{
				id: duration
			},
			function(data)
			{
				if (typeof data.error != 'undefined')
				{
					$.fn.WarcryAlertBox('open', '<p>An unexpected error occured.</p>');
				}
				else
				{
					$('#select-currency').find('label').each(function(index, element)
					{
						var input = $(element).find('input');
						var Currency = input.val();
						
						//Update the visual text
						$(element).find('#price').html(data[Currency]);
						//Set data attribute with the price value
						input.attr('data-price-value', data[Currency]);
					});
				}
			});
		});
		
		//Completion form
		$('#boosts-complete-form').on('submit', function()
		{
			//Verify selected boost
			if ($('#selected-boost-id').val() == '0')
			{
				//no items, fail
				$.fn.WarcryAlertBox('open', '<p>You did not select any boost.</p>');
				return false;
			}
			
			//Find out the selected currency
			var $Currency = $('input[name="currency"]:checked').val();
			//Get the price value for that currency
			var $Price = $('input[name="currency"]:checked').attr('data-price-value');
			
			//predefine these
			var $Silver = 0;
			var $Gold = 0;
			
			//switch the currency types
			switch (parseInt($Currency))
			{
				case CURRENCY_SILVER:
					$Silver = $Price;
					break;
				case CURRENCY_GOLD:
					$Gold = $Price;
					break;
			}
			
			//check if the server is online... Im gonna do that in verifyPayment
			$.get($BaseURL + "/ajax.php?phase=4",
			{
				silver: $Silver,
				gold: $Gold,
				realm: Boosts.RealmId,
			},
			function(data)
			{
				if (data == 'OK')
				{
					$('#boosts-complete-form').off('submit');
					$('#boosts-complete-form').submit();
				}
				else
				{
					//prompt the error
					$.fn.WarcryAlertBox('open', '<p>' + data + '</p>');
				}
			});
			
			return false;
		});
	}
};

//Initialize
$(function()
{
	Boosts.BindHandlers();
});