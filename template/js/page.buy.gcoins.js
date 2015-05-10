//Any value changes in attempt to exploit in here wont matter because the coins received are based on the money payed
var $paymentMethod = 'paypal';
var $startingCoins = 10;
var $selectedCoins = $startingCoins;

function submitPaymentForm()
{
	var form = $('#' + $paymentMethod + '-form');
	form.submit();
	return false;
}

function changePaymentMethod(e)
{
	var $this = $(e);
	var value = $this.find('option:selected').val();
	
	$paymentMethod = value;
}

function updateInfoPane(val)
{
	//val is the coins currently selected
	var pane = $('#payment-infoPane-price');
	//update the pane
	pane.html(val);
	//update the selected coins var
	$selectedCoins = val;
	//apply the value to the payment form
	var form = $('#' + $paymentMethod + '-form');
	
	//check which payment method are we using
	if ($paymentMethod == 'paypal')
	{
		var input = form.find('input[name=quantity]');
		//apply
		input.val(val);
	}
}

function strpos(haystack, needle, offset)
{
	var i = (haystack+'').indexOf(needle, (offset || 0));
	return i === -1 ? false : i;
}

function updateProductId(value)
{
	var current = $('#' + $paymentMethod + '-product-id').val();
	var pos = strpos(current, 'WCC');
	var preKey = current.substr(0, pos + 3);
	var newProductId = preKey + value;
	
	//update the product id
	$('#' + $paymentMethod + '-product-id').val(newProductId);
}

$(document).ready(function()
{
	//limit the coins input to digits only
	$('#selected-coins-input').keydown(function(event)
	{
		// Allow: backspace, delete, tab and escape
		if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || 
			 // Allow: Ctrl+A
			(event.keyCode == 65 && event.ctrlKey === true) || 
			 // Allow: home, end, left, right
			(event.keyCode >= 35 && event.keyCode <= 39))
		{
				 //let it happen
				 return;
		}
		else {
			// Ensure that it is a number and stop the keypress
			if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
				event.preventDefault(); 
			}   
		}
	});
	
	//bind handler to update the info pane
	$('#selected-coins-input').keyup(function(event)
	{
		//get the current values
		var currentValue = parseInt($('#selected-coins-input').val());
				
		//check if the values are incorrect
		if (typeof currentValue == 'undefined' || currentValue == 0 || currentValue < 1)
		{
			currentValue = 1;
			$('#selected-coins-input').val(1);
		}
		else if (currentValue > 1000)
		{
			currentValue = 1000;
			$('#selected-coins-input').val(1000);
		}
		else if (isNaN(currentValue) || currentValue == '')
		{
			//update only the info pane
			currentValue = 1;
		}
		
		//call update
		updateInfoPane(currentValue);
		//update the product id
		updateProductId(currentValue);
		
		return true;
	});
	
	//bind the increase coins handler
	$('#payment-increase-coins').on('click', function(event)
	{
		//get the current values
		var currentValue = parseInt($('#selected-coins-input').val());
		var newCoinValue = parseInt(currentValue);
		
		//if there is no value set 1
		if (typeof currentValue == 'undefined' || currentValue == '' || isNaN(currentValue))
		{
			currentValue = 1;
		}

		//dont allow increase above 1000
		if (currentValue < 1000)
		{
			newCoinValue = currentValue + 1;
		}
		
		//update the coins input
		$('#selected-coins-input').val(newCoinValue);
		//update the info pane
		updateInfoPane(newCoinValue);
		//update the product id
		updateProductId(newCoinValue);
		
		//later discount script will e applied
		
		return false;
	});
	
	//bind the decrease coins handler
	$('#payment-decrease-coins').on('click', function(event)
	{
		//get the current values
		var currentValue = parseInt($('#selected-coins-input').val());
		var newCoinValue = parseInt(currentValue);
		
		//if there is no value set 1
		if (typeof currentValue == 'undefined' || currentValue == '' || isNaN(currentValue))
		{
			currentValue = 1;
			$('#selected-coins-input').val(1);
		}
		
		//dont allow decrease below 1
		if (currentValue > 1)
		{
			newCoinValue = currentValue - 1;
		}
		
		//update the coins input
		$('#selected-coins-input').val(newCoinValue);
		//update the info pane
		updateInfoPane(newCoinValue);
		//update the product id
		updateProductId(newCoinValue);
		
		//later discount script will e applied
		
		return false;
	});
});