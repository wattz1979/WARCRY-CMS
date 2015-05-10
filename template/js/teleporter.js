//define global variables
$CurrentMap = $('#tp-mainmap-container');
var Canvases = new Array();
var $SelectedPoint = false;
var $SelectedCharacter = false;

function HideCurrentMap()
{
	//queue the transition
	WarcryQueue('TELEPORTER').add(function()
	{
		//hide the map
		$CurrentMap.fadeOut('fast', function()
		{
			//continue the queue
			WarcryQueue('TELEPORTER').goNext();
		});
	});
}

function UpdateBackButton(stage)
{
	if (stage == 'mainmap')
	{
		if ($('#tp-back').parent().css('display') == 'none')
		{
			$('#tp-back').parent().fadeIn('slow');
		}
		
		$('#tp-back').on('click', function()
		{
			//clear the queue
			WarcryQueue('TELEPORTER').clear();
			//hide the main map
			HideCurrentMap();
			//queue the transition
			WarcryQueue('TELEPORTER').add(function()
			{
				//fade in the new map
				$('#tp-mainmap-container').fadeIn('fast', function()
				{
					//continue the queue
					WarcryQueue('TELEPORTER').goNext();
				});
			});
			//run the queue
			WarcryQueue('TELEPORTER').goNext();
			//save the currently opened map
			$CurrentMap = $('#tp-mainmap-container');
			//hide them button
			$('#tp-back').parent().fadeOut('fast');
			//remove the handler
			$('#tp-back').off('click');
			
			return false;
		});
	}
	else if (stage == 'continent')
	{
		//remove the handler
		$('#tp-back').off('click');
		//bind the new one
		$('#tp-back').on('click', function()
		{
			var container = $('.open-territory');
			var map = $CurrentMap.attr('mapname');
			var canvasId = $(this).attr('id');
			var $nameHolder = container.find('#name');
			var $levelHolder = container.find('#level');
			
			//hide the character select if open
			WarcryQueue('TELEPORTER').add(function()
			{
				if ($('.complete-tele-form').css('display') == 'block')
				{
					//hide the character select form
					$('.complete-tele-form').fadeOut('slow', function()
					{
						//continue the queue
						WarcryQueue('TELEPORTER').goNext();
					});
	
					//the point is already selected, deselect event
					$SelectedPoint = false;
				}
				else
				{
					//continue the queue
					WarcryQueue('TELEPORTER').goNext();
				}
			});
			
			WarcryQueue('TELEPORTER').add(function()
			{
				container.fadeOut('fast', function()
				{
					//cleanup
					container.find('.teleport-point').each(function()
					{
						$(this).detach();
					});
					$nameHolder.html('');
					$levelHolder.html('');
					//continue the queue
					WarcryQueue('TELEPORTER').goNext();
				});
			});
			//queue the transition
			WarcryQueue('TELEPORTER').add(function()
			{
				//resize the container
				$('.map-holder').animate({
					height: '671px',
				}, 'fast',
				function()
				{
					//continue the queue
					WarcryQueue('TELEPORTER').goNext();
				});
			});
			//queue the current map fadein
			WarcryQueue('TELEPORTER').add(function()
			{
				$CurrentMap.fadeIn('slow');
			});
			//run the queue
			WarcryQueue('TELEPORTER').goNext();
			//update the back button
			UpdateBackButton('mainmap');
			
			return false;
		});
	}
}
//On point selection
function PointClick(e)
{
	var pointId = $(e).attr('data-pointId');
	
	//check if the point is not yet selected
	if (!$(e).hasClass('active'))
	{
		//visual
		$(e).addClass('active');
		
		//bring up the character select form
		$('.complete-tele-form').fadeIn('slow');
		
		//save the point id
		$SelectedPoint = pointId;
	}
	else
	{
		//remove visual
		$(e).removeClass('active');
		
		//hide the character select form
		$('.complete-tele-form').fadeOut('slow');

		//the point is already selected, deselect event
		$SelectedPoint = false;
	}

	//find previously selected points
	$('.teleport-point.active').each(function()
	{
		//check if it's not the current point
		if ($(this).attr('data-pointId') != pointId)
		{
			$(this).removeClass('active');
		}
	});
			
	return false;
}

//Character selection trigger
function OnCharacterSelect(e)
{
	//find the selected character
	if ($(e).find('option[selected="selected"]').length > 0)
	{
		//check if the option has value
		if ($(e).find('option[selected="selected"]').val().length > 0)
		{
			//save the value
			$SelectedCharacter = $(e).find('option[selected="selected"]').val();
		}
	}
	
	return true;
}

//check shit up before submiting
function OnTeleportSubmit(e)
{
	var $this = $(e);
	
	//check if we have an selected point
	if (!$SelectedPoint)
	{
		$.fn.WarcryAlertBox('open', '<p>Unable to proceed, you did not select teleport location.</p>');
		return false;
	}
	//check for characters
	if (!$SelectedCharacter)
	{
		//no characters, fail
		$.fn.WarcryAlertBox('open', '<p>Unable to proceed, please select a character.</p>');
		return false;
	}
	
	//check if the character meets the level requirements
	$.get(
		"ajax.php?phase=8",
		{
			point: $SelectedPoint,
			character: $SelectedCharacter,
		},
		function(data)
		{
			//check for error
			if ($(data).find('error').length > 0)
			{
				console.log($(data).find('error').text());
			}
			else
			{
				var reqLevel = $(data).find('reqLevel').text();
				var charLevel = $(data).find('charLevel').text();
				
				//check if the character meets the req level
				if (parseInt(charLevel) < parseInt(reqLevel))
				{
					$.fn.WarcryAlertBox('open', '<p>The selected character does not meet the level requirement. The location requires a minimum of atleast ' + reqLevel + ' level.</p>');
				}
				else
				{
					//we are ready to proceed to PHP checks and stuff
					//append an input with the point id
					$this.append('<input type="hidden" name="point" value="'+$SelectedPoint+'" />');
					//submit the form
					$this.submit();
				}
			}
		}
	);

	return false;
}

function DrawCanvases()
{
	$('.canv-holder').each(function()
	{
		var map = $(this).parent().parent().attr('mapname');
		
        $(this).children('canvas').each(function()
		{
			var canvasId = $(this).attr('id');
			//draw
			Canvases[map][canvasId]($(this));
			//bind tooltip
			$(this).TpTooltip({ mapKey: canvasId, });
        });
    });
}

function DevelopmentBundle()
{
	$(".open-territory").click(function(e)
	{
	   	var parentOffset = $(this).offset(); 
	   	//or $(this).offset(); if you really just want the current element's offset
	   	var relX = e.pageX - parentOffset.left;
	   	var relY = e.pageY - parentOffset.top;
		
		var newPoint = document.createElement("a");
		newPoint.setAttribute("class", "teleport-point");
		newPoint.setAttribute("href", "#");
		newPoint.style.top = relY+'px';
		newPoint.style.left = relX+'px';
		
		//make it visible
		$(".open-territory").append(newPoint);
		//tooltips
		$(newPoint).hover(
			function()
			{
				$(".open-territory").append('<div id="point-info" style="top: 20px; left: 20px;">top:'+relY+'px; left: '+relX+'px;</div>');
			}, 
			function()
			{
				$('#point-info').detach();
			}
		);
		
		console.log('---------------------- CLICK ----------------------');
		console.log('top:'+relY+'px; left: '+relX+'px;');
	});
	//a function to clear the points added
	$(".open-territory").bind('contextmenu',function()
	{
		$(".open-territory").find('.teleport-point').each(function()
		{
			$(this).detach();
		});
		
		return false;
	});
}

/* #################################################### */
/* ######    On Document Ready Stuff    ############### */
/* #################################################### */

$(document).ready(function()
{
    //spawn the small canvases
	if (!$OnCooldown)
	{
		DrawCanvases();
	}
	
	//development tool
	//DevelopmentBundle();
	
	//kalimdor click event
	$('#tp-btn-kalimdor').on('click', function()
	{
		//hide the main map
		HideCurrentMap();
		//queue the transition
		WarcryQueue('TELEPORTER').add(function()
		{
			//fade in the new map
			$('#tp-kalimdor-container').fadeIn('fast', function()
			{
				//continue the queue
				WarcryQueue('TELEPORTER').goNext();
			});
		});
		//run the queue
		WarcryQueue('TELEPORTER').goNext();
		//save the currently opened map
		$CurrentMap = $('#tp-kalimdor-container');
		//update the back button
		UpdateBackButton('mainmap');
		
		return false;
	});
	
	//eastern kingdoms click event
	$('#tp-btn-eastern-kingdoms').on('click', function()
	{
		//hide the main map
		HideCurrentMap();
		//queue the transition
		WarcryQueue('TELEPORTER').add(function()
		{
			//fade in the new map
			$('#tp-eastern-kingdoms-container').fadeIn('fast', function()
			{
				//continue the queue
				WarcryQueue('TELEPORTER').goNext();
			});
		});
		//run the queue
		WarcryQueue('TELEPORTER').goNext();
		//save the currently opened map
		$CurrentMap = $('#tp-eastern-kingdoms-container');
		//update the back button
		UpdateBackButton('mainmap');
		
		return false;
	});
	
	//northrend click event
	$('#tp-btn-northrend').on('click', function()
	{
		//hide the main map
		HideCurrentMap();
		//queue the transition
		WarcryQueue('TELEPORTER').add(function()
		{
			//fade in the new map
			$('#tp-northrend-container').fadeIn('fast', function()
			{
				//continue the queue
				WarcryQueue('TELEPORTER').goNext();
			});
		});
		//run the queue
		WarcryQueue('TELEPORTER').goNext();
		//save the currently opened map
		$CurrentMap = $('#tp-northrend-container');
		//update the back button
		UpdateBackButton('mainmap');
		
		return false;
	});
	
	//bind the open territory click event
	$('.map-holder canvas').on('click', function()
	{
		var container = $('.open-territory');
		var map = $CurrentMap.attr('mapname');
		var canvasId = $(this).attr('id');
		var $nameHolder = container.find('#name');
		var $levelHolder = container.find('#level');
		
		//hide the main map
		HideCurrentMap();
		//queue the transition
		WarcryQueue('TELEPORTER').add(function()
		{
			//start he loading
			$('.TP_loading_cont').css('display', 'block');
			$('#TP_loading').LoadingBar('restart');
			$('#TP_loading').LoadingBar('state1');
			$('#TP_loading').fadeIn('slow');
			//resize the container
			$('.map-holder').animate({
				height: '548px',
			}, 'fast',
			function()
			{
				//continue the queue
				WarcryQueue('TELEPORTER').goNext();
			});
		});
		
		//queue the ajax to get the map info
		WarcryQueue('TELEPORTER').add(function()
		{
			$.get(
				"ajax.php?phase=7",
				{
					key: canvasId,
				},
				function(data)
				{
					//check for error
					if ($(data).find('error').length > 0)
					{
						console.log($(data).find('error').text());
					}
					
					var name = $(data).find('name').text();
					var minLevel = $(data).find('minLevel').text();
					var maxLevel = $(data).find('maxLevel').text();
					var type = $(data).find('type').text();
					var points = $(data).find('points');
					var zone = $(data).find('zone').text();
					
					//fill in the data
					$nameHolder.html(name);
					
					//resolve some level related stuff
					if (minLevel == maxLevel)
					{
						$levelHolder.html(minLevel + ' Level');
					}
					else
					{
						$levelHolder.html(minLevel + ' Level - ' + maxLevel + ' Level');
					}
										
					//set the map image
					$('.open-territory').css('background-image', 'url(template/style/images/maps/'+zone+'.jpg)');
					
					//add the points
					if (parseInt(points.attr('count')) > 0)
					{
						points.find('point').each(function(index, element)
						{
							var top = $(element).attr('styleTop');
							var left = $(element).attr('styleLeft');
							var pointId = $(element).attr('pointId');
							//append the point
                            container.append('<a class="teleport-point" style="top: '+top+'px; left: '+left+'px;" data-pointId="'+pointId+'" href="#"> lol</a>');
                        });
					}
					
					//bind the point click events
					$('.teleport-point').click(function()
					{
						return PointClick($(this));
					});
					
					//continue the queue
					WarcryQueue('TELEPORTER').goNext();
				}
			);
		});
		
		//finish loading and fade in
		WarcryQueue('TELEPORTER').add(function()
		{
			$('#TP_loading').LoadingBar('state4', function()
			{
				$('.open-territory').fadeIn('slow', function()
				{
					//hide the loading bar
					$('.TP_loading_cont').css('display', 'none');
					$('#TP_loading').css('display', 'none');
				});
			});
		});
		
		//run the queue
		WarcryQueue('TELEPORTER').goNext();

		//update the back button
		UpdateBackButton('continent');
	});	
});
