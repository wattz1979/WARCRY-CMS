// JavaScript Document

//Handle fading notifications
$(document).ready(function()
{
	var delay = 5000;
	
    $('.fading-notification').each(function()
	{
		$(this).delay(delay).fadeOut(500);
		
		delay = delay + 500;
	});
});

//create queue
var warcryQueues = [];
warcryQueues['internal'] = [];

var WarcryQueue = function(queueName)
{
    var add = function(fnc)
	{
		if (typeof queueName != 'undefined')
		{
			if (typeof warcryQueues[queueName] == 'undefined')
			{
				warcryQueues[queueName] = []
			}
			warcryQueues[queueName].push(fnc);
		}
		else
		{
        	warcryQueues['internal'].push(fnc);
		}
    };
		
    var goNext = function()
	{
		if (typeof queueName != 'undefined')
		{
			//check if we have some functions in the queue
			if ($(warcryQueues[queueName]).size() < 1)
			{
				return false;
			}
			
			var fnc = warcryQueues[queueName].shift();
			if (typeof fnc == 'function')
			{
        		fnc();
			}
			else
			{
				console.log('WarcryQueue: There are no other functions in the queue "'+queueName+'".');
			}
		}
		else
		{
 			//check if we have some functions in the queue
			if ($(warcryQueues['internal']).size() < 1)
			{
				return false;
			}
			
       		var fnc = warcryQueues['internal'].shift();
			if (typeof fnc == 'function')
			{
        		fnc();
			}
			else
			{
				console.log('WarcryQueue: There are no other functions in the queue.');
			}
		}
    };
		
	var clear = function()
	{
		if (typeof queueName != 'undefined')
		{
			warcryQueues[queueName] = []
		}
		else
		{
			warcryQueue['internal'] = []
		}
	};
		
	var size = function()
	{
		if (typeof queueName != 'undefined')
		{
			return $(warcryQueues[queueName]).size();
		}
		else
		{
			return $(warcryQueues['internal']).size();
		}
	};
			
    return {
        add: add,
        goNext: goNext,
		clear: clear,
		size: size,
    };
};		

//ajax update status
function updateRealmStatus(id)
{
	var $this = $('#realm-status-' + id);
	var $realm = id;
	
	$.get(
		$BaseURL + '/ajax.php?phase=19', 
		{ 
    		id: $realm,
		},
		function(data)
		{
			if (data == '1')
			{
				$this.addClass('online');
			}
			else
			{
				$this.addClass('offline');
			}
			//next in queue
			WarcryQueue('onload').goNext();
		}
	);	
}

function updateLogonStatus()
{
	var $this = $('#logon-status2');
	
	$.get(
		$BaseURL + '/ajax.php?phase=20', 
		function(data)
		{
			if (data == '1')
			{
				$this.addClass('online');
				$this.html('Online');
			}
			else
			{
				$this.addClass('offline');
				$this.html('Offline');
			}
			//next in queue
			WarcryQueue('onload').goNext();
		}
	);	
}

function updateTeamspeakStatus()
{
	var $this = $('#teeamspeak-status');
	
	//next in queue
	WarcryQueue('onload').goNext();
	return;
	
	$.get(
		"teamspeakStatus.php", 
		function(data)
		{
			if (data == '1')
			{
				$this.addClass('online');
				$this.html('Online');
			}
			else
			{
				$this.addClass('offline');
				$this.html('Offline');
			}
			//next in queue
			WarcryQueue('onload').goNext();
		}
	);	
}

function css_browser_selector(u)
{
	var ua=u.toLowerCase();
	var is=function(t)
	{
		return ua.indexOf(t)>-1
	};
	
	var g='gecko',w='webkit',s='safari',o='opera',m='mobile';
	var h=document.documentElement;
	var b=[(!(/opera|webtv/i.test(ua))&&/msie\s(\d)/.test(ua))?('ie ie'+RegExp.$1):is('firefox/2')?g+' ff2':is('firefox/3.5')?g+' ff3 ff3_5':is('firefox/3.6')?g+' ff3 ff3_6':is('firefox/3')?g+' ff3':is('gecko/')?g:is('opera')?o+(/version\/(\d+)/.test(ua)?' '+o+RegExp.$1:(/opera(\s|\/)(\d+)/.test(ua)?' '+o+RegExp.$2:'')):is('konqueror')?'konqueror':is('blackberry')?m+' blackberry':is('android')?m+' android':is('chrome')?w+' chrome':is('iron')?w+' iron':is('applewebkit/')?w+' '+s+(/version\/(\d+)/.test(ua)?' '+s+RegExp.$1:''):is('mozilla/')?g:'',is('j2me')?m+' j2me':is('iphone')?m+' iphone':is('ipod')?m+' ipod':is('ipad')?m+' ipad':is('mac')?'mac':is('darwin')?'mac':is('webtv')?'webtv':is('win')?'win'+(is('windows nt 6.0')?' vista':''):is('freebsd')?'freebsd':(is('x11')||is('linux'))?'linux':'','js'];
	
	c = b.join(' ');
	h.className += ' '+c;
	
	return c;
}

$(function()
{
	css_browser_selector(navigator.userAgent);
});

function convertDateToUTC(date)
{ 
    return new Date(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(), date.getUTCHours(), date.getUTCMinutes(), date.getUTCSeconds()); 
}

// function to calculate local time
// in a different city
// given the city's UTC offset
function calcTime(city, offset)
{
    // create Date object for current location
    var d = new Date();

    // convert to msec
    // add local time zone offset
    // get UTC time in msec
    var utc = d.getTime() + (d.getTimezoneOffset() * 60000);

    // create new Date object for different city
    // using supplied offset
    var nd = new Date(utc + (3600000*parseInt(offset)));

    // return time as a string
    return nd.toString();
}

//Server time Cloack
function ServerTimeCloack()
{
	var currentTime = new Date(calcTime($TIMEZONE, $TIMEZONEOFFSET));
	
	var h = currentTime.getHours();
	var m = currentTime.getMinutes();
    var s = currentTime.getSeconds();
	
	setTimeout(function() { ServerTimeCloack(); }, 1000);

	if (h < 10)
	{
		h = "0" + h;
	}
	if (m < 10)
	{
		m = "0" + m;
	}
	if (s < 10)
	{
		s = "0" + s;
	}
	
    var myClock = document.getElementById('server-time-cloack');
	if (myClock)
	{
		myClock.textContent = h + ":" + m + ":" + s;
		myClock.innerText = h + ":" + m + ":" + s;
	}
}

//-----------------------------------------------------------------------//
//---------------- SelectTransform, jQuery plugin -----------------------//
//---------------------- Script by ChoMPi -------------------------------//
//-----------------------------------------------------------------------//

(function($)
{
	var $isListOpen = false;
	var $currentlyOpenList = null;
	var $lastScrollTimestamp = null;
	var $minTimeBetweenScroll = 200; //Time in miliseconds
	
	var methods =
	{
		listState : 'closed',
		
		defaults :
		{
			container: null,
			list: null,
			selected: null,
			arrow: null,
			scrollConfig: { scrollBy: 5, },
			searchQueue: null,
			isScrollable: false,
		},
		
		init : function(config)
		{
			//if we have the element
			if ($(this).length < 1)
			{
				return;
			}
			
			//If the init hasent been called yet
			if (typeof $(this).data('SelectTransform') == 'undefined')
			{
				$(this).data('SelectTransform', {config: null});

				//merge the defaults with the passed config				
				$(this).data('SelectTransform').config = $.extend({}, methods.defaults, config);
			}
			else
			{
				//merge the old config with the passed one
				$(this).data('SelectTransform').config = $.extend({}, $(this).data('SelectTransform').config, config);
			}
		
			var config = $(this).data('SelectTransform').config;

			//get the instance of the element
			var $element = $(this);
			
			//hide the select form
			$element.css({display: 'none'});
			
			//create new element which will represent the select
			var container = document.createElement('div');
			//check if the element has attribute ID			
			if (typeof $element.attr('id') != 'undefined')
			{
				$(container).attr('id', $element.attr('id'));
			}
			$(container).attr('class', 'js-select');
			//append the new element
			$element.after(container);
			//bind click event to open the dropdown list
			$(container).bind('click', function(event)
			{
				event.stopPropagation();
				$element.SelectTransform('clickEvent');
			});
			
			config.container = $(container);
			
			//create the div which will contain the selected option
			var selected = document.createElement('div');
			$(selected).attr('class', 'js-select-selected');
			$(container).append(selected);
			
			config.selected = $(selected);

			//create the div which will contain the arrow
			var arrow = document.createElement('div');
			$(arrow).attr('class', 'js-select-arrow');
			$(selected).after(arrow);
			
			config.arrow = $(arrow);
			
			//create new div which will be the container of the list
			var dropdownCont = document.createElement('div');
			$(dropdownCont).attr('class', 'js-select-list-container');
			$(dropdownCont).attr('id', 'js-list-container');
			$(dropdownCont).css({display: 'none', zIndex: 101});
			$(config.container).append(dropdownCont);
			
			config.listContainer = $(dropdownCont);

			//scrollbar manager
			var listItemCount = $element.find('option').length;
				
			//if the items are more than scrollBy variable, append scrollbar
			if (listItemCount > config.scrollConfig.scrollBy)
			{
				config.isScrollable = true;
				
				//create the scrollbar
				//create the top controller
				var topController = document.createElement('div');
				$(topController).attr('class', 'js-select-list-top-controller');
				$(topController).attr('id', 'js-list-top-controller');
				$(topController).attr('align', 'center');
				$(config.listContainer).append(topController);
				$(topController).append('<p></p>');
				$(topController).bind('click', function(event)
				{
					event.stopPropagation();
					$element.SelectTransform('ScrollUp');
				});

				config.topController = $(topController);
				
				//create new div which will be the scroller of the list
				var dropdownScroller = document.createElement('div');
				$(dropdownScroller).attr('class', 'js-select-list-scroller');
				$(dropdownScroller).attr('id', 'js-list-scroller');
				$(config.listContainer).append(dropdownScroller);
			
				config.listScroller = $(dropdownScroller);
							
				//Create new div which will be the container of the options
				var dropdown = document.createElement('div');
				$(dropdown).attr('class', 'js-select-list-scrollable');
				$(dropdown).attr('id', 'js-list');
				$(config.listScroller).append(dropdown);

				config.list = $(dropdown);
				
				//create the bottom controller
				var bottomController = document.createElement('div');
				$(bottomController).attr('class', 'js-select-list-bottom-controller');
				$(bottomController).attr('id', 'js-list-bottom-controller');
				$(bottomController).attr('align', 'center');
				$(config.listContainer).append(bottomController);
				$(bottomController).append('<p></p>');
				$(bottomController).bind('click', function(event)
				{
					event.stopPropagation();
					$element.SelectTransform('ScrollDown');
				});
				
				config.bottomController = $(bottomController);
			}
			else
			{
				//Create new div which will be the container of the options
				var dropdown = document.createElement('div');
				$(dropdown).attr('class', 'js-select-list');
				$(dropdown).attr('id', 'js-list');
				$(config.listContainer).append(dropdown);

				config.list = $(dropdown);
			}

			//append the options to the container
			$element.find('option').each(function(index, element)
			{
				var option = document.createElement('ul');
				$(option).attr('id', index);
				$(option).attr('class', 'js-select-list-option');
				$(option).html($(element).html());
				
				//check if HTML var is assigned
				if (typeof $(element).attr('getHtmlFrom') != 'undefined')
				{
					var htmlElement = $(element).attr('getHtmlFrom');
					
					//check if the assigned element exists
					if ($(htmlElement).length > 0)
					{
						$(option).html($(htmlElement).html());
					}
				}
				
				//copy the element style param
				if (typeof $(element).attr('style') != 'undefined')
				{
					$(option).attr('style', $(element).attr('style'));
				}
				
				//copy the element class param
				if (typeof $(element).attr('class') != 'undefined')
				{
					$(option).addClass($(element).attr('class'));
				}
				
				///bind the event handlers
				if(typeof $(element).attr('selected') != 'undefined')
				{
					//check if HTML var is assigned
					if (typeof $(element).attr('getHtmlFrom') != 'undefined')
					{
						var htmlElement = $(element).attr('getHtmlFrom');
						
						//check if the assigned element exists
						if ($(htmlElement).length > 0)
						{
							$(config.selected).html($(htmlElement).html());
						}
					}
					else
					{
						$(config.selected).html($(element).html());
					}
					
					$(option).addClass('js-select-list-option-selected');

					if(typeof $(element).attr('disabled') != 'undefined')
					{
						$(option).addClass('js-select-list-option-disabled');

						//wont bind anything on this one
						$(option).unbind('click');
						$(option).bind('click', function(event){ event.stopPropagation(); });
					}
					else
					{
						//bind click event to simply close the list
						$(option).bind('click', function(event)
						{ 
							event.stopPropagation();
							$element.SelectTransform('selectEvent', {option: element, index: index});
						});
					}
				}
				else
				{
					//bind the select handler
					if(typeof $(element).attr('disabled') != 'undefined')
					{
						$(option).addClass('js-select-list-option-disabled');
					
						//wont bind anything on this one
						$(option).bind('click', function(event)
						{ 
							event.stopPropagation();
						});
					}
					else
					{
						$(option).bind('click', function(event)
						{ 
							event.stopPropagation();
							$element.SelectTransform('selectEvent', {option: element, index: index});
						});
					}
				}
				
				//append the option to the container
            	$(config.list).append(option);
            });
			
		},
		
		clickEvent : function()
		{
			var config = $(this).data('SelectTransform').config;
			$element = $(this);
						
			//check if we have opened list
			if ($isListOpen)
			{
				//deactivate the arrow
				$currentlyOpenList.data('SelectTransform').config.arrow.removeClass('js-select-arrow-active');
				//close the option list
				$currentlyOpenList.SelectTransform('closeList');
				
				//unbind HTML click event
				$('html').unbind('click');
			}
			else					
			//if the list is closed
			{
				//activate the arrow
				$(config.arrow).addClass('js-select-arrow-active');
				//open the option list
				$(this).SelectTransform('openList');
				
				//bind click element to the HTML to close the dropdown list if the click is outside the select form
				$('html').bind('click', function(event)
				{
					event.stopPropagation();
					$element.SelectTransform('clickEvent');
				});
			}
		},
		
		openList : function()
		{
			var $element = $(this);
			var config = $(this).data('SelectTransform').config;
			
			//open the options list
			$(config.listContainer).slideDown('fast');
			
			//define that one list is already opened
			$isListOpen = true;
			$currentlyOpenList = $(this);
			
			//check if we have scrolling list
			if (config.isScrollable)
			{
				if (!$(config.listContainer).hasClass('js-select-list-container-scrollable'))
					$(config.listContainer).addClass('js-select-list-container-scrollable');
				
			  	//create the shearchbox element
				var searchbox = document.createElement('input');
				$(searchbox).css({ opacity: 0, position: 'fixed', top: '0px', left: '0px' });
				$(searchbox).attr('type', 'text');
				$(searchbox).attr('id', 'js-select-searchbox');
			
				$('body').append(searchbox);
				//focus the searchbox
				$(searchbox).focus();
			
				config.searchBox = $(searchbox);
			
				$(searchbox).on('keyup', function(event)
				{
					//prevent Enter key
					if (event.keyCode == '13')
					{
    	 				event.preventDefault();					
						return false;
					}
				
					//get the searchbox text
					var text = $(searchbox).val();
				
					$(config.list).children('ul').each(function(index, element)
					{
                    	var thisString = $(this).html();
					
						//convert both strings to lower case
						text = text.toLowerCase();
						thisString = thisString.toLowerCase();
					
						//if there is no text just go to the top
						if (text == '' || text == null)
						{
							clearTimeout(config.searchQueue);
							config.searchQueue = setTimeout(function(){ $element.SelectTransform('ScrollTo', 0); }, 300);
						}
						else if (thisString.indexOf(text) >= 0)
						{
							clearTimeout(config.searchQueue);
							config.searchQueue = setTimeout(function(){ $element.SelectTransform('ScrollTo', index); }, 300);
						}
                	});
            	});
				
				//Bind the mouse wheel events
				config.list.on('mousewheel', function(event, delta)
				{
					//stop the page from being scrolled
					event.preventDefault();
					
					//break if the last scroll was too soon
					if ($lastScrollTimestamp != null && (parseInt($lastScrollTimestamp) + $minTimeBetweenScroll) >= parseInt(event.timeStamp))
					{
						//console.log('Mouse wheel too soon.');
						return false;
					}
					
					//update the last scroll timestamp
					$lastScrollTimestamp = event.timeStamp;
					
					//get the direction			
					var dir = delta > 0 ? 'Up' : 'Down';
					
					//if scrolling up
					if (dir == 'Up')
					{
						$element.SelectTransform('ScrollUp');
					}
					else
					{
						$element.SelectTransform('ScrollDown');
					}
										
		            return false;
				});
			}			
		},
		
		closeList : function()
		{
			var config = $(this).data('SelectTransform').config;

			$isListOpen = false;
			$currentlyOpenList = null;

			//close the options list
			$(config.listContainer).slideUp('fast');
			
			//check if we have scrolling list
			if (config.isScrollable)
			{
				//destroy the searchbox
				$(config.searchBox).detach();
			}
			
			//off the mousewheel event
			config.list.off('mousewheel');
		},
		
		unselectOption: function()
		{
			var config = $(this).data('SelectTransform').config;
			$element = $(this);
			
			$option = $(this).find(':selected');
			
			//remove the selected
			$option.removeAttr('selected');
			
			//find the selected option in our custom list
			$selectedInList = $(config.list).find('.js-select-list-option-selected');
			$selectedInList.removeClass('js-select-list-option-selected');

			/*if (!$selectedInList.hasClass('js-select-list-option-disabled'))
			{
				//bind the select handler
				$selectedInList.unbind('click').bind('click', function(event)
				{ 
					event.stopPropagation();
					$element.SelectTransform('selectEvent', {option: $option, index: $option.index()});
				});
			}*/
		},
		
		selectOption: function(options)
		{
			var config = $(this).data('SelectTransform').config;
			$element = $(this);
						
			//set the option attr selected			
			$(options.option).attr('selected', 'selected');
			
			//find the option in our custom list
			$selectedInList = $(config.list).find('#'+options.index);
						
			//add class selected
			$selectedInList.addClass('js-select-list-option-selected');
					
			/*//bind click events
			$selectedInList.unbind('click').bind('click', function(event)
			{ 
				event.stopPropagation();
				$element.SelectTransform('clickEvent');
			});*/
		},
		
		selectEvent: function(options)
		{
			var config = $(this).data('SelectTransform').config;
			$element = $(this);
						
			var text = $(options.option).html();
			
			//remove the selected
			$(this).SelectTransform('unselectOption');
			
			//check if HTML var is assigned
			if (typeof $(options.option).attr('getHtmlFrom') != 'undefined')
			{
				var htmlElement = $(options.option).attr('getHtmlFrom');
						
				//check if the assigned element exists
				if ($(htmlElement).length > 0)
				{
					$(config.selected).html($(htmlElement).html());
				}
			}
			else
			{
				//update the selected text
				$(config.selected).html(text);
			}
			
			//select the option
			$(this).SelectTransform('selectOption', {option: options.option, index: options.index});
			
			//close the list
			$element.SelectTransform('clickEvent');
			
			//trigger change event
			$element.trigger('change');			
		},
		
		quickSelect: function(options)
		{
			var config = $(this).data('SelectTransform').config;
			$element = $(this);
						
			var text = $(options.option).html();
			
			//remove the selected
			$(this).SelectTransform('unselectOption');
			
			//check if HTML var is assigned
			if (typeof $(options.option).attr('getHtmlFrom') != 'undefined')
			{
				var htmlElement = $(options.option).attr('getHtmlFrom');
						
				//check if the assigned element exists
				if ($(htmlElement).length > 0)
				{
					$(config.selected).html($(htmlElement).html());
				}
			}
			else
			{
				//update the selected text
				$(config.selected).html(text);
			}
			
			//select the option
			$(this).SelectTransform('selectOption', {option: options.option, index: options.index});		
		},
		
		ScrollUp : function()
		{
			var config = $(this).data('SelectTransform').config;
			$element = $(this);
			
			//check if we need setup
			var isSetupDone = $(config.list).attr('isSetupDone');
			if (typeof isSetupDone == 'undefined' || isSetupDone != '1')
			{
				$element.SelectTransform('ScrollSetup');
			}

			//get some config variables
			var currentOffset = parseInt($(config.list).attr('currentOffset'));
			var totalOptions = parseInt($(config.list).attr('totalOptions'));
			
			var scrollToOffset = currentOffset - config.scrollConfig.scrollBy;
			
			if (scrollToOffset < 0)
			{
				scrollToOffset = 0;
			}
			
			//find the option we want to scroll to
			var $find = config.list.children('#' + scrollToOffset );
								
			$(config.list).stop(true,true).animate({ marginTop: '-' + ($find.position().top) + 'px' }, 400);
						
			//update the current offset
			$(config.list).attr('currentOffset', scrollToOffset);
					
			//focus the searchbox if exists
			if (typeof config.searchBox != 'undefined')
			{
				config.searchBox.attr('value', '');
				config.searchBox.focus();
			}
		},
		
		ScrollDown : function()
		{
			var config = $(this).data('SelectTransform').config;
			$element = $(this);
			
			//check if we need setup
			var isSetupDone = $(config.list).attr('isSetupDone');
			if (typeof isSetupDone == 'undefined' || isSetupDone != '1')
			{
				$element.SelectTransform('ScrollSetup');
			}
			
			//get some config variables
			var currentOffset = parseInt($(config.list).attr('currentOffset'));
			var totalOptions = parseInt($(config.list).attr('totalOptions'));
			
			var scrollToOffset = currentOffset + config.scrollConfig.scrollBy;
						
			//if the next scroll offset is greater than the total options
			if (scrollToOffset > (totalOptions - config.scrollConfig.scrollBy))
			{
				//null the next scroll offset to the total options - scroll by value
				scrollToOffset = totalOptions - config.scrollConfig.scrollBy;
			}

			//find the option we want to scroll to
			var $find = config.list.children('#' + scrollToOffset );
								
			$(config.list).stop(true,true).animate({ marginTop: '-' + ($find.position().top) + 'px' }, 400);
			
			//update the current offset
			$(config.list).attr('currentOffset', scrollToOffset);
			
			//focus the searchbox if exists
			if (typeof config.searchBox != 'undefined')
			{
				config.searchBox.attr('value', '');
				config.searchBox.focus();
			}
		},
		
		ScrollTo : function(index)
		{
			var config = $(this).data('SelectTransform').config;
			var $element = $(this);

			//check if we need setup
			var isSetupDone = $(config.list).attr('isSetupDone');
			if (typeof isSetupDone == 'undefined' || isSetupDone != '1')
			{
				$element.SelectTransform('ScrollSetup');
			}

			//get some config variables
			var currentOffset = parseInt($(config.list).attr('currentOffset'));
			var totalOptions = parseInt($(config.list).attr('totalOptions'));
									
			//if the next scroll offset is greater than the total options
			if (index > (totalOptions - config.scrollConfig.scrollBy))
			{
				//null the next scroll offset to the total options - scroll by value
				index = totalOptions - config.scrollConfig.scrollBy;
			}
			
			//find the option we want to scroll to
			var $find = config.list.children('#' + index );
								
			$(config.list).stop(true,true).animate({ marginTop: '-' + ($find.position().top) + 'px' }, 400);
			
			//update the current offset
			$(config.list).attr('currentOffset', index);		
			
		},
		
		ScrollSetup : function()
		{
			var config = $(this).data('SelectTransform').config;
			$element = $(this);
			
			//set some default values to the element directly
			$(config.list).attr('totalOptions', config.list.children('ul').length);
			$(config.list).attr('currentOffset', '0');
			
			$(config.list).attr('isSetupDone', '1');
		},
	}
	
  	$.fn.SelectTransform = function(method)
  	{
  		if (methods[method])
		{
     		return methods[method].apply(this, Array.prototype.slice.call( arguments, 1 ));
    	}
		else if (typeof method === 'object' || ! method)
		{
      		return methods.init.apply(this, arguments);
    	}
		else
		{
      		$.error( 'Method ' +  method + ' does not exist on jQuery.SelectTransform' );
    	}    
  	};

})(jQuery);

//-----------------------------------------------------------------------//
//-------------------- Loading Bar, jQuery plugin -----------------------//
//---------------------- Script by ChoMPi -------------------------------//
//-----------------------------------------------------------------------//

(function($)
{
	var methods =
	{
		init : function()
		{
			//if we have the element
			if ($(this).length < 1)
			{
				return;
			}
			
			//get the instance of the element
			var $element = $(this);
			
			//create the bar
			$element.append('<div class="loading-bar" align="left"><span id="bar"></span></div>');
			
			//run to state 1
            $element.LoadingBar('state1'); 
		},
		
		state1 : function(callback)
		{
			var $element = $(this);

			$element.find('#bar').stop(true,true).animate({ width: '100px' }, 1000, function()
			{
				$(this).css('width', '100px');
				
				if (typeof callback == 'function')
				{
					callback();
				}
			});
		},
		
		state2 : function(callback)
		{
			var $element = $(this);
			
			$element.find('#bar').stop(true,true).animate({ width: '200px' }, 500, function()
			{
				$(this).css('width', '200px');
				
				if (typeof callback == 'function')
				{
					callback();
				}
			});
		},
		
		state3 : function(callback)
		{
			var $element = $(this);

			$element.find('#bar').stop(true,true).animate({ width: '300px' }, 500, function()
			{
				$(this).css('width', '300px');

				if (typeof callback == 'function')
				{
					callback();
				}
			});
		},
		
		state4 : function(callback)
		{
			var $element = $(this);

			$element.find('#bar').stop(true,true).animate({ width: '400px' }, 500, function()
			{
				$(this).css('width', '400px');
				
				if (typeof callback == 'function')
				{
					callback();
				}
			});
		},
		
		restart : function()
		{
			var $element = $(this);
			
			$element.find('#bar').css('width', '0px');
		},
	}
	
  	$.fn.LoadingBar = function(method)
  	{
  		if (methods[method])
		{
     		return methods[method].apply(this, Array.prototype.slice.call( arguments, 1 ));
    	}
		else if (typeof method === 'object' || ! method)
		{
      		return methods.init.apply(this, arguments);
    	}
		else
		{
      		$.error( 'Method ' +  method + ' does not exist on jQuery.LoadingBar');
    	}    
  	};

})(jQuery);

