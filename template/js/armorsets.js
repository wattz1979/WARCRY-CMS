// JavaScript Document

(function($)
{
	var $lastClickedArmorset = 0;
	var $lastSelectedCharacter = 0;
	var $IsReadyToComplete = false;
	var $SelectedArmorsetPrice = 0;
	
	var methods =
	{
		defaults :
		{
			currentPage: 0,
			totalPages: 0,
			perPage: 6,
			totalRecords: 0,
			realm: 1,
			source: 'old',
			filter: 
			{
				category: 0,
				character: ''
			},
			isLoading: false,
			alertBox : 
			{
				isLoaded: false,
			},
		},

		init : function(options)
		{
			var $this = $(this);
			
			//if we have the element
			if ($this.length < 1)
			{
				return;
			}
			
			//If the init hasent been called yet
			if (typeof $this.data('WarcryArmorsets') == 'undefined')
			{
				$this.data('WarcryArmorsets', {config: null});

				//merge the defaults with the passed config				
				$this.data('WarcryArmorsets').config = $.extend({}, methods.defaults, config);
			}
			else
			{
				//merge the old config with the passed one
				$this.data('WarcryArmorsets').config = $.extend({}, $this.data('WarcryArmorsets').config, config);
			}
		
			var config = $this.data('WarcryArmorsets').config;
			
			//setup the config
			if (typeof options.currentPage != 'undefined')
			{			
			  	config.currentPage = options.currentPage;
			}
			if (typeof options.totalPages != 'undefined')
			{			
			  	config.totalPages = options.totalPages;
			}
			if (typeof options.perPage != 'undefined')
			{			
			  	config.perPage = options.perPage;	
			}
			if (typeof options.totalRecords != 'undefined')
			{			
			  	config.totalRecords = options.totalRecords;
			}
			if (typeof options.filter != 'undefined')
			{
				config.filter = options.filter;
			}
			if (typeof options.realm != 'undefined')
			{			
			  	config.realm = options.realm;
			}
			if (typeof options.source != 'undefined')
			{			
			  	config.source = options.source;
			}
			
			//first page
			$('#pagination-nav-first > a').on('click', function()
			{
				$this.WarcryArmorsets('loadPage', 'first');
				return false;
			});
			
			//prev page
			$('#pagination-nav-prev > a').on('click', function()
			{
				$this.WarcryArmorsets('loadPage', 'prev');
				return false;
			});
								
			//next page
			$('#pagination-nav-next > a').on('click', function()
			{
				$this.WarcryArmorsets('loadPage', 'next');
				return false;
			});
			
			//last page
			$('#pagination-nav-last > a').on('click', function()
			{
				$this.WarcryArmorsets('loadPage', 'last');
				return false;
			});
			
			//bind character select handler
			$('#character-select').on('change', function()
			{
				//find the selected category
				var selected = $('#character-select').find('option:selected');
				if (selected.length > 0)
				{
					if (selected.val() != '' && selected.val() != '0')
					{
						$lastSelectedCharacter = selected.val();
						$('#selected-character').val($lastSelectedCharacter);
					}
				}
				
				if ($lastSelectedCharacter != '' && $lastSelectedCharacter != 0)
				{
					//queue the fade out
					WarcryQueue('ARMORSETS').add(function()
					{
						if ($('.armor-set-prepurchase-info').css('display') == 'none')
						{
							$('#armorsets-starting-message').fadeOut('fast', function()
							{
								$('.armor-set-prepurchase-info').fadeIn('fast');
								WarcryQueue('ARMORSETS').goNext();
							});
						}
						else
						{
							WarcryQueue('ARMORSETS').goNext();
						}
					});
					//queue the page load
					WarcryQueue('ARMORSETS').add(function()
					{
						$this.WarcryArmorsets('applyFilter', { category: config.filter.category, character: $lastSelectedCharacter });
					});
					//run the queue
					WarcryQueue('ARMORSETS').goNext();
				}
			});
			
			//bind the filter handler
			$('#armors-filter-select').on('change', function()
			{
				var $filter = 0;
				
				//find the selected category
				var selected = $('#armors-filter-select').find('option:selected');
				if (selected.length > 0)
				{
					$filter = selected.val();
				}
				
				//get the current address query
				var currentQuery = window.location.pathname + window.location.search;
				
				//check if the filter is already in the query
				if (currentQuery.toString().indexOf('filter=') > -1)
				{
					var query_arg_regex;
				    query_arg_regex = new RegExp('&filter=[^(?:&|$)]*', 'ig');
				    currentQuery = currentQuery.replace(query_arg_regex, '&filter=' + $filter);
				}
				else
				{
					currentQuery = currentQuery + '&filter=' + $filter;
				}
				
				//update the address bar
				window.history.replaceState('Object', 'Title', currentQuery);
				
				if ($lastSelectedCharacter != '' && $lastSelectedCharacter != 0)
				{						
					$this.WarcryArmorsets('applyFilter', { category: $filter, character: config.filter.character });
				}
			});
			
			//bind the form handler
			$('#armorset-purchase-form').submit(function()
			{
				var charValue = $(this).children('#selected-character').val();
				var armorsetValue = $(this).children('#selected-armorset').val();
				
				if (charValue.length == 0 || charValue == '')
				{
					//no characters, fail
					$.fn.WarcryAlertBox('open', '<p>Unable to proceed, please select character.</p>');
					return false;
				}
				
				if (armorsetValue.length == 0 || parseInt(armorsetValue) == 0)
				{
					//no characters, fail
					$.fn.WarcryAlertBox('open', '<p>Unable to proceed, please select armor set.</p>');
					return false;
				}
				
				$this.WarcryArmorsets('verifyAmount');
				
				return $IsReadyToComplete;
			});
		},

		changeConfig : function(options)
		{
			var config = $(this).data('WarcryArmorsets').config;
			
			//setup the config
			if (typeof options.currentPage != 'undefined')
			{			
			  	config.currentPage = options.currentPage;
			}
			if (typeof options.totalPages != 'undefined')
			{			
			  	config.totalPages = options.totalPages;
			}
			if (typeof options.perPage != 'undefined')
			{			
			  	config.perPage = options.perPage;	
			}
			if (typeof options.totalRecords != 'undefined')
			{			
			  	config.totalRecords = options.totalRecords;
			}
			if (typeof options.filter != 'undefined')
			{
				config.filter = options.filter;
			}
			if (typeof options.realm != 'undefined')
			{			
			  	config.realm = options.realm;
			}
			if (typeof options.source != 'undefined')
			{			
			  	config.source = options.source;
			}
		},
		
		startLoading : function()
		{
			//check if the item container is visible
			if ($('#armorsets-container').css('display') == 'block')
			{
				$('#armorsets-container').fadeOut('fast', function()
				{
					$('#armorsets_loading').LoadingBar('restart');
					$('#armorsets_loading').LoadingBar('state1');
					$('#armorsets_loading').fadeIn('slow', function()
					{
						WarcryQueue('ARMORSETS').goNext();
					});
				});
			}
			else
			{
				$('#armorsets_loading').LoadingBar('restart');
				$('#armorsets_loading').LoadingBar('state1');
				$('#armorsets_loading').fadeIn('slow', function()
				{
					WarcryQueue('ARMORSETS').goNext();
				});
			}
		},
		
		applyFilter : function(filter)
		{
			var $this = $(this);
			var $config = $this.data('WarcryArmorsets').config;
			var $category = filter.category;
			var $character = filter.character;
			
			WarcryQueue('ARMORSETS').add(function()
			{			
				//start the loading
				$this.WarcryArmorsets('startLoading');
			});
			
			//queue the Filter info ajax
			WarcryQueue('ARMORSETS').add(function()
			{
				//prepare the ajax error handlers
				$.ajaxSetup({
					error:function(x,e)
					{
						if(x.status==408)
						{
							$this.WarcryArmorsets('applyFilter', filter);
						}
						else
						{
							console.log('Ajax error: ' + x.status);
						}
					},
					dataType: "xml",
				});
				
				//get the page data	
				$.get(
					"ajax.php?phase=6",
					{
						perPage: $config.perPage,
						category: $category,
						character: $character,
						realm: $config.realm,
					},
					function(data)
					{
						var $totalPages = $(data).find('totalPages').text();
						var $totalRecords = $(data).find('totalRecords').text();
						
						//reset the whole thing
						$('#armorsets-container').WarcryArmorsets('changeConfig',
						{
							currentPage: 1, 
							totalPages: $totalPages, 
							totalRecords: $totalRecords,
							filter: 
							{
								category: $category,
								character: $character
							},
						});
						
						//continue the queue
						WarcryQueue('ARMORSETS').goNext();
					}
				);
			});
			
			//queue the Page load
			WarcryQueue('ARMORSETS').add(function()
			{
				$this.WarcryArmorsets('loadPage', 'first');
			});
			
			//run the queue
			WarcryQueue('ARMORSETS').goNext();
		},
		
		loadPage : function(page)
		{
			var $config = $(this).data('WarcryArmorsets').config;
			var $this = $(this);
						
			//break the script if already loading
			if ($config.isLoading)
			{
				return false;
			}
			
			//define that we are now starting to load
			$config.isLoading = true;
			
			//convert page string to number
			if (page == 'first')
			{
				page = 1;
			}
			else if (page == 'prev')
			{
				page = $config.currentPage - 1;
			}
			else if (page == 'next')
			{
				page = $config.currentPage + 1;
			}
			else if (page == 'last')
			{
				page = $config.totalPages;
			}
			var $page = page;
			
			//queue the start loading func
			WarcryQueue('ARMORSETS').add(function()
			{
				//start the loading
				$this.WarcryArmorsets('startLoading');
			});
			
			//prepare the ajax error handlers
			$.ajaxSetup({
				error:function(x,e)
				{
					if(x.status==408)
					{
						$this.WarcryArmorsets('loadPage', $page);
					}
					else
					{
						$config.isLoading = false;
					}
				},
				dataType: "xml",
			});
			
			//queue the start loading func
			WarcryQueue('ARMORSETS').add(function()
			{
				//get the page data	
				$.get(
					"ajax.php?phase=5",
					{
						page: $page,
						perPage: $config.perPage,
						category: $config.filter.category,
						character: $config.filter.character,
						realm: $config.realm,
					},
					function(data)
					{
						var list = $(data).find('itemlist');
						var count = list.attr('count');
						
						//null the HTML
						$this.html('');
	
						//check the count
						if (parseInt(count) == 0)
						{
							$this.html('<p class="noresults">There are no armor sets.</p>');
						}
						else
						{				
							//loop the items
							list.find('armorset').each(function(i, e)
							{
								var armorsetId = $(e).attr('id');
								var order = $(e).find('order').text();
								var html = $(e).find('html').text();
								
								//append the HTML
								$this.append(html);
								
								//check if this armorset is previusly selected
								if (armorsetId == $lastClickedArmorset)
								{
									$('#armor-set-'+armorsetId+'-items').parent().addClass('armor-set-active');
								}
								
								//set the id on display
								$('#armor-set-'+armorsetId+'-items').parent().attr('armorset', armorsetId);
								//bind the select handler
								$('#armor-set-'+armorsetId+'-items').parent().click(function()
								{
									$this.WarcryArmorsets('clickEvent', $(this));
								});
												
								//queue the load of the current item
								WarcryQueue('ARMORSETS').add(function()
								{
									$('#armorsets-container').WarcryArmorsets('loadItems', { armorset: armorsetId, order: order, source: $config.source, pageItems: count });
								});
							});
						}
						
						//queue the loading hide
						WarcryQueue('ARMORSETS').add(function()
						{
							$('#armorsets_loading').fadeOut('slow', function()
							{
								//fade in the item list
								$('#armorsets-container').fadeIn('slow');
								//define that we are no longer loading
								$config.isLoading = false;
							});
						});
						
						//update the pagination navs
						$('#armorsets-container').WarcryArmorsets('updatePagination', { page: $page, count: count });
						
						//update the current page
						$config.currentPage = $page;
						
						//continue the queue
						WarcryQueue('ARMORSETS').goNext();
					}
				);
			});
			
			//run the queue
			WarcryQueue('ARMORSETS').goNext();
		},
					
		loadItems : function(options)
		{
			var $this = $(this);
			
			var $armorsetId = options.armorset;
			var $order = options.order;
			var $count = options.pageItems;
			
			//handle undefiend source
			if (typeof options.source == 'undefined')
			{
				options.source = 'old';
			}			

			var $source = options.source;
			
			//get the items string
			var itemsStr = $('#armor-set-'+$armorsetId+'-items').html();
			//remove whitespaces
			itemsStr = itemsStr.replace(/\s/g, "");
			//make item array
			var items = itemsStr.split(',');
			
			//null the html
			$('#armor-set-'+$armorsetId+'-items').html("");
			
			//prepare the ajax error handlers
			$.ajaxSetup({
				error:function(x,e)
				{
					console.log('Parser Error.');
				},
				dataType: "json",
			});
			
			var $armset_itemsloop_i = 0;
			//loop the items
			for (i = 0; i < items.length; i++)
			{
				//get the entry
				var $items = items;
				
				//load the items in queue
				WarcryQueue('ARMORSETS_'+$armorsetId).add(function()
				{
					var $entry = $items[$armset_itemsloop_i];
					
					//load item
					$.get(
						$BaseURL + "/ajax.php?phase=1",
						{
							entry: $entry
						},
						function(data)
						{
							var quality = data.quality_str;
							var icon = data.icon;
							
							$('#armor-set-'+$armorsetId+'-items').append('<a class="'+quality.toLowerCase()+'" href="http://old.wowhead.com/item='+$entry+'" rel="item='+$entry+'" style="background-image:url(http://wow.zamimg.com/images/wow/icons/medium/'+icon.toLowerCase()+'.jpg);" onclick="return false;"></a>');
							
							//continue the queue
							WarcryQueue('ARMORSETS_'+$armorsetId).goNext();
						}
					);
					
					$armset_itemsloop_i++;
				});
			}
			
			//items have been loaded
			WarcryQueue('ARMORSETS_'+$armorsetId).add(function()
			{
				//handle the loading animation
				if (typeof $count != 'undefined' && $order == $count)
				{
					$('#armorsets_loading').LoadingBar('state4');
				}
				else if ($order == 1)
				{
					$('#armorsets_loading').LoadingBar('state2');
				}
				else if ($order == 3)
				{
					$('#armorsets_loading').LoadingBar('state3');
				}
				else if ($order == 5)
				{
					$('#armorsets_loading').LoadingBar('state4');
				}
				
				//continue the main queue
				WarcryQueue('ARMORSETS').goNext();
			});
			
			//run the queue
			WarcryQueue('ARMORSETS_'+$armorsetId).goNext();
		},

		updatePagination : function(options)
		{
			var $config = $(this).data('WarcryArmorsets').config;
			
			var page = options.page;
			var count = options.count;
			var cont = $('#armorsets-pagination');
			var firstBtn = $('#pagination-nav-first', cont);
			var prevBtn = $('#pagination-nav-prev', cont);
			var nextBtn = $('#pagination-nav-next', cont);
			var lastBtn = $('#pagination-nav-last', cont);
			var info = $('#pages', cont);
			var offset = Math.ceil((parseInt(page) - 1) * $config.perPage);
			
			//if it's not the first load
			if ($('#armorsets-pagination').css('display') != 'none')
			{
				//queue the fadeOut func
				WarcryQueue('STORE_NAVS').add(function()
				{
					$('#armorsets-pagination').fadeOut('fast', function()
					{
						//continue the queue
						WarcryQueue('STORE_NAVS').goNext();
					});
				});
			}
						
			//queue the nav buttons handler
			WarcryQueue('STORE_NAVS').add(function()
			{
				//define defaults
				var leftSep = false;
				var rightSep = false;
				
				//check if we've got no records
				if (page == 1 && count == 0)
				{
					return;
				}
				
				//hide first page BTN if need to
				if (parseInt(page) < 3)
				{
					firstBtn.css('display', 'none');
				}
				else
				{
					firstBtn.css('display', 'inline-block');
					leftSep = true;
				}
			
				//hide prev page BTN if need to
				if (parseInt(page) == 1)
				{
					prevBtn.css('display', 'none');
				}
				else
				{
					prevBtn.css('display', 'inline-block');
					leftSep = true;
				}
				
				//hide next page BTN if need to
				if (parseInt(page) == $config.totalPages)
				{
					nextBtn.css('display', 'none');
				}
				else
				{
					nextBtn.css('display', 'inline-block');
					rightSep = true;
				}
				
				//hide last page BTN if need to
				if (parseInt(page) == $config.totalPages || (parseInt(page) + 1) == $config.totalPages)
				{
					lastBtn.css('display', 'none');
				}
				else
				{
					lastBtn.css('display', 'inline-block');
					rightSep = true;
				}
				
				//update the info pane
				info.html((leftSep ? '<p>|&nbsp;&nbsp;</p>' : '') + offset + '-' + (parseInt(offset) + parseInt(count)) + ' of ' + $config.totalRecords + (rightSep ? '<p>&nbsp;&nbsp;|</p>' : ''));
				
				//continue the queue
				WarcryQueue('STORE_NAVS').goNext();
			});

			//queue the fadeIn func
			WarcryQueue('STORE_NAVS').add(function()
			{
				$('#armorsets-pagination').fadeIn('slow', function()
				{
					//continue the queue
					WarcryQueue('STORE_NAVS').goNext();
				});				
			});
			
			//run the queue
			WarcryQueue('STORE_NAVS').goNext();
		},
		
		clickEvent : function(element)
		{
			var ArmorSetId = $(element).attr('armorset');
									
			//check if we are clicking the same armor set
			if (ArmorSetId == $lastClickedArmorset)
			{
				$(element).removeClass('armor-set-active');
				$lastClickedArmorset = 0;
				$('#selected-armorset').val($lastClickedArmorset);
				$('#armorsets-info-title').html('<b>Please select a armor set.</b>');
				return;
			}
			else
			{
				$(this).find('.armor-set-active').removeClass('armor-set-active');
			}
			
			$(element).addClass('armor-set-active');
			$lastClickedArmorset = ArmorSetId;
			$('#selected-armorset').val($lastClickedArmorset);
			//get the armorset price
			var price = $(element).find('#price').children('p').html();
			//define the price for the form check
			$SelectedArmorsetPrice = parseInt(price);
			
			//check if character is selected
			if ($lastSelectedCharacter == 0)
			{
				$('#armorsets-info-title').html('<b>Please select a character.</b>');
			}
			else
			{
				$('#armorsets-info-title').html('<b>Ready to complete.</b>');
			}
		},
		
		//this function is to simply preserv the shopping cart in case you dont have the amount, the values will be double checked by PHP
		verifyAmount : function()
		{
			var $this = $(this);
			var $config = $this.data('WarcryArmorsets').config;
			var $realm = $config.realm;
			var $gold = $SelectedArmorsetPrice;
					
			//prepare the ajax error handlers
			$.ajaxSetup({
				error:function(x,e)
				{
					if(x.status==408)
					{
						$this.WarcryStore('verifyAmount');
					}
				},
				dataType: "html",
			});
			
			//get the results
			$.get(
				"ajax.php?phase=4",
				{
					silver: 0,
					gold: $gold,
					realm: $realm,
				},
				function(data)
				{
					if (data == 'OK')
					{
						$IsReadyToComplete = true;
						$('#armorset-purchase-form').submit();
					}
					else
					{
						$IsReadyToComplete = false;
						//prompt the error
						$.fn.WarcryAlertBox('open', '<p>' + data + '</p>');
					}
				}
			);
		},	
	}
	
  	$.fn.WarcryArmorsets = function(method)
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
      		$.error( 'Method ' +  method + ' does not exist on jQuery.WarcryArmorsets');
    	}    
  	};

})(jQuery);