// JavaScript Document

(function($)
{
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
				search: '',
				quality: '-1',
			},
			isLoading: false,
			cartItems: new Array(),
			cartLastItemId: 0,
			cartTotalAmount: 
			{
				silver: 0,
				gold: 0,
			},
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
			if (typeof $this.data('WarcryStore') == 'undefined')
			{
				$this.data('WarcryStore', {config: null});

				//merge the defaults with the passed config				
				$this.data('WarcryStore').config = $.extend({}, methods.defaults, config);
			}
			else
			{
				//merge the old config with the passed one
				$this.data('WarcryStore').config = $.extend({}, $this.data('WarcryStore').config, config);
			}
		
			var config = $this.data('WarcryStore').config;
			
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
				$this.WarcryStore('loadPage', 'first');
				return false;
			});
			
			//prev page
			$('#pagination-nav-prev > a').on('click', function()
			{
				$this.WarcryStore('loadPage', 'prev');
				return false;
			});
								
			//next page
			$('#pagination-nav-next > a').on('click', function()
			{
				$this.WarcryStore('loadPage', 'next');
				return false;
			});
			
			//last page
			$('#pagination-nav-last > a').on('click', function()
			{
				$this.WarcryStore('loadPage', 'last');
				return false;
			});
			
			$('#store-empty-cart-btn').on('click', function()
			{
				$this.WarcryStore('shoppingCart').empty();
				return false;
			});
			
			//bind the on submit event for the search
			$('#store-search-form').on('submit', function()
			{
				var $search = $('#store-search-input').val();
				var $quality = '-1';
						
				//check the correct quality
				var selected = $('#store-quality-select').find('option:selected');
				if (selected.length > 0)
				{
					$quality = selected.val();
				}
												
				$this.WarcryStore('applyFilter', { search: $search, quality: $quality });
						
				return false;
			});
			
			//bind the on submit event for the completion form
			$('#store-complete-form').on('submit', function()
			{
				if (config.cartItems.length == 0)
				{
					//no items, fail
					$.fn.WarcryAlertBox('open', '<p>You did not select any items.</p>');
					return false;
				}
				
				//check for characters
				if ($(this).find('select[name="character"]').length == 0)
				{
					//no characters, fail
					$.fn.WarcryAlertBox('open', '<p>Unable to proceed, you dont have any characters.</p>');
					return false;
				}
				else
				{
					var select = $(this).find('select[name="character"]');
					//check if character is selected
					if (select.children('option[selected="selected"]').val().length == 0)
					{
						$.fn.WarcryAlertBox('open', '<p>Unable to proceed, please select a character.</p>');
						return false;
					}
				}
				
				//check if the server is online... Im gonna do that in verifyPayment		
				$this.WarcryStore('verifyAmount');
				
				return false;
			});

		},

		changeConfig : function(options)
		{
			var config = $(this).data('WarcryStore').config;
			
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
			if ($('#store-item-container').css('display') == 'block')
			{
				$('#store-item-container').fadeOut('fast', function()
				{
					$('#store_loading').LoadingBar('restart');
					$('#store_loading').LoadingBar('state1');
					$('#store_loading').fadeIn('slow', function()
					{
						WarcryQueue('STORE').goNext();
					});
				});
			}
			else
			{
				$('#store_loading').LoadingBar('restart');
				$('#store_loading').LoadingBar('state1');
				$('#store_loading').fadeIn('slow', function()
				{
					WarcryQueue('STORE').goNext();
				});
			}
		},
		
		applyFilter : function(filter)
		{
			var $this = $(this);
			var $config = $this.data('WarcryStore').config;
			var $filter = filter;
						
			//start the loading
			$this.WarcryStore('startLoading');
			
			//queue the Filter info ajax
			WarcryQueue('STORE').add(function()
			{
				//prepare the ajax error handlers
				$.ajaxSetup({
					error:function(x,e)
					{
						if(x.status==408)
						{
							$this.WarcryStore('applyFilter', $filter);
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
					"ajax.php?phase=3",
					{
						perPage: $config.perPage,
						search: $filter.search,
						quality: $filter.quality,
					},
					function(data)
					{
						var $totalPages = $(data).find('totalPages').text();
						var $totalRecords = $(data).find('totalRecords').text();
						
						//reset the whole thing
						$('#store-item-container').WarcryStore('changeConfig',
						{
							currentPage: 1, 
							totalPages: $totalPages, 
							totalRecords: $totalRecords, 
							filter: 
							{
								search: $filter.search, 
								quality: $filter.quality 
							}
						});
						
						//continue the queue
						WarcryQueue('STORE').goNext();
					}
				);
			});
			
			//queue the Page load
			WarcryQueue('STORE').add(function()
			{
				$this.WarcryStore('loadPage', 'first');
			});
			
			//run the queue
			WarcryQueue('STORE').goNext();
		},
		
		loadPage : function(page)
		{
			var $config = $(this).data('WarcryStore').config;
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
			
			WarcryQueue('STORE').add(function()
			{
				//start the loading
				$this.WarcryStore('startLoading');
			});
			
			//prepare the ajax error handlers
			$.ajaxSetup({
				error:function(x,e)
				{
					if(x.status==408)
					{
						$this.WarcryStore('loadPage', $page);
					}
					else
					{
						$config.isLoading = false;
					}
				},
				dataType: "xml",
			});
			
			WarcryQueue('STORE').add(function()
			{
				//get the page data	
				$.get(
					"ajax.php?phase=2",
					{
						page: $page,
						perPage: $config.perPage,
						search: $config.filter.search,
						quality: $config.filter.quality,
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
							$this.html('<p class="noresults">There are no items.</p>');
						}
						else
						{				
							//loop the items
							list.find('item').each(function(i, e)
							{
								var itemId = $(e).attr('id');
								var entry = $(e).find('entry').text();
								var order = $(e).find('order').text();
								var html = $(e).find('html').text();
								
								//append the HTML
								$this.append(html);
														
								//queue the load of the current item
								WarcryQueue('STORE').add(function()
								{
									$('#store-item-container').WarcryStore('loadItem', { itemId: itemId, entry: entry, order: order, source: $config.source, pageItems: count });
								});	
							});
						}
						
						//queue the loading hide
						WarcryQueue('STORE').add(function()
						{
							$('#store_loading').fadeOut('slow', function()
							{
								//fade in the item list
								$('#store-item-container').fadeIn('slow');
								//define that we are no longer loading
								$config.isLoading = false;
							});
						});
						
						//run the queue
						WarcryQueue('STORE').goNext();
						
						//update the pagination navs
						$('#store-item-container').WarcryStore('updatePagination', { page: $page, count: count });
						
						//update the current page
						$config.currentPage = $page;
					}
				);
			});
			
			//run the queue
			WarcryQueue('STORE').goNext();
		},
		
		updatePagination : function(options)
		{
			var $config = $(this).data('WarcryStore').config;
			
			var page = options.page;
			var count = options.count;
			var cont = $('#store-pagination');
			var firstBtn = $('#pagination-nav-first', cont);
			var prevBtn = $('#pagination-nav-prev', cont);
			var nextBtn = $('#pagination-nav-next', cont);
			var lastBtn = $('#pagination-nav-last', cont);
			var info = $('#pages', cont);
			var offset = Math.ceil((parseInt(page) - 1) * $config.perPage);
			
			//if it's not the first load
			if ($('#store-pagination').css('display') != 'none')
			{
				//queue the fadeOut func
				WarcryQueue('STORE_NAVS').add(function()
				{
					$('#store-pagination').fadeOut('fast', function()
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
				$('#store-pagination').fadeIn('slow', function()
				{
					//continue the queue
					WarcryQueue('STORE_NAVS').goNext();
				});				
			});
			
			//run the queue
			WarcryQueue('STORE_NAVS').goNext();
		},
			
		loadItem : function(options)
		{
			var $this = $(this);
			var $itemId = options.itemId;
			var $entry = options.entry;
			var $storeItem = options.order;
			var $lastRecord = options.pageItems;
			
			//handle undefiend source
			if (typeof options.source == 'undefined')
			{
				options.source = 'old';
			}			

			var $source = options.source;
				
			//prepare the ajax error handlers
			$.ajaxSetup({
				error:function(x,e)
				{
					if(x.status==408)
					{
						$this.WarcryStore('loadItem', $entry, $storeItem);
					}
					else if (x.status==200)
					{
						console.log('Parser Error.');
						//run the queue
						WarcryQueue('STORE').goNext();
					}
				},
				dataType: "json",
			});
					
			$.get(
				//$DBURL + "/ajax.php?item=" + $entry + "&json",
				"ajax.php?phase=1",
				{
					entry: $entry,
					source: 'old',
				},
				function(data)
				{
					var name = data.name;
					var quality = data.quality_str;
					var icon = data.icon;
					var subclass = data.subclass_str;
					var inventorySlot = data.InventoryType_str;
					
					var element = $('#store-item-' + $storeItem);
							
					//define that we dont have bonding as default
					var bonding = false;
					if (data.bonding_str != '')
					{
						if (data.bonding_str.indexOf('<br />') > -1)
						{
							data.bonding_str = data.bonding_str.replace('<br />', '');
						}
							
						bonding = data.bonding_str;
					}
								
					//compile the Item Info string
					var itemInfo = '';
					
					//add bonding
					if (bonding !== false)
					{
						itemInfo += bonding;
					}
		
					//add the item type string
					if (inventorySlot != '')
					{
						if (bonding !== false)
						{
							itemInfo += '&nbsp;&nbsp;|&nbsp;&nbsp;';
						}
						
						itemInfo += inventorySlot;
					}
		
					//add the item subclass string
					if (subclass != '')
					{
						if (itemInfo != '')
						{
							itemInfo += '&nbsp;&nbsp;|&nbsp;&nbsp;';
						}
						
						itemInfo += subclass;
					}
								
					//set the icon border color
					element.children('div#item-cont').addClass(quality.toLowerCase());
					//set the icon
					$('.item-ico a#icon', element).css('background', 'url(http://wow.zamimg.com/images/wow/icons/large/' + icon.toLowerCase() + '.jpg)');
					//set the name
					$('.item-info p', element).html(name);
					//set the item info
					$('.item-info span#info', element).html('<br>' + itemInfo);
					
					var $name = name;
					var $icon = icon.toLowerCase();
					var $quality = quality.toLowerCase();
					
					//bind the click event
					$('.item-ico a#icon', element).on('click', function()
					{
						$this.WarcryStore('shoppingCart').add({ itemId: $itemId, entry: $entry, name: $name, storeItem: $storeItem, icon: $icon, quality: $quality });
						return false;
					});
								
					//handle the loading animation
					if (typeof $lastRecord != 'undefined' && $storeItem == $lastRecord)
					{
						$('#store_loading').LoadingBar('state4');
					}
					else if ($storeItem == 2)
					{
						$('#store_loading').LoadingBar('state2');
					}
					else if ($storeItem == 4)
					{
						$('#store_loading').LoadingBar('state3');
					}
					else if ($storeItem == 6)
					{
						$('#store_loading').LoadingBar('state4');
					}
					
					//run the queue
					WarcryQueue('STORE').goNext();
				}
			);		
		},
		
		shoppingCart : function()
		{
			var $this = $(this);
			var $config = $this.data('WarcryStore').config;
			var items = $config.cartItems;
			var cartElement = $('#store-shopping-cart');
			var $totalAmount = $config.cartTotalAmount;
			
			var add = function(data)
			{
				var $itemId = data.itemId;
				var $entry = data.entry;
				var $name = data.name;
				var $storeItem = data.storeItem;
				var $icon = data.icon;
				var $quality = data.quality;
				
				//set default prices
				data.gold = 0;
				data.silver = 0;
														
				//remove the cart is empty message
				if (items.length == 0)
				{
					cartElement.html('');
					//fadeIn the Empty Cart button
					$('#store-empty-cart-btn').fadeIn('slow');
				}
				
				var $cartItemId = $config.cartLastItemId + 1;
											
				var $silver = $('#store-price-silver', '#store-item-' + $storeItem);
				var $gold = $('#store-price-gold', '#store-item-' + $storeItem);
				
				var $silverHTML = '';
				var $goldHTML = '';

				//check if the item is purchasable with silver
				if ($silver.length > 0)
				{
					data.silver = parseInt($silver.html());
					$silverHTML = '<a href="#">'+ $silver.html() +'</a>';
				}
				else
				{
					$silverHTML = '<span class="disabled">X</span>';
				}

				//check if the item is purchasable with gold
				if ($gold.length > 0)
				{
					data.gold = parseInt($gold.html());
					$goldHTML = '<a href="#">'+ $gold.html() +'</a>';
				}
				else
				{
					$goldHTML = '<span class="disabled">X</span>';
				}
								
				//append the new item HTML
				cartElement.append(
					'<ul class="selected-item-row" id="cart-item-'+ $cartItemId +'" style="display: none;">' +
                    	'<li class="sitem-icon" id="'+ $quality +'"><a href="http://old.wowhead.com/item='+ $entry +'" rel="item='+ $entry +'"><span style="background-image:url(\'http://wow.zamimg.com/images/wow/icons/large/'+ $icon +'.jpg\')"></span></a></li>' +
                        '<li class="sitem-name">'+ $name +'</li>' +
                        '<li class="sitem-s-coins">'+ $silverHTML +'</li>' +
                        '<li class="sitem-g-coins">'+ $goldHTML +'</li>' +
                        '<li class="remove"><a href="#">Remove</a></li>' +
                  	'</ul>');
								
				//bind the coins handlers
				if ($silver.length > 0)
				{
					$('.sitem-s-coins > a', '#cart-item-'+ $cartItemId).on('click', function()
					{
						$this.WarcryStore('shoppingCart').selectCurreny($cartItemId, 'silver');
						return false;
					});
				}
				if ($gold.length > 0)
				{
					$('.sitem-g-coins > a', '#cart-item-'+ $cartItemId).on('click', function()
					{
						$this.WarcryStore('shoppingCart').selectCurreny($cartItemId, 'gold');
						return false;
					});
				}
				
				//select the default currency
				if ($gold.length > 0)
				{
					data.selectedCurrency = 'gold';
					$this.WarcryStore('shoppingCart').selectCurreny($cartItemId, 'gold');
				}
				else
				{
					data.selectedCurrency = 'silver';
					$this.WarcryStore('shoppingCart').selectCurreny($cartItemId, 'silver');
				}
				
				//bind the remove handler
				$('#cart-item-'+ $cartItemId +' > .remove > a').on('click', function()
				{
					$this.WarcryStore('shoppingCart').remove($cartItemId);		
					return false;
				});

				//add the item to the array
				items.push([$cartItemId, data]);
				
				//update the total amount
				$this.WarcryStore('shoppingCart').updateTotal();
				
				//fade in the item
				$('#cart-item-'+ $cartItemId).fadeIn('slow');
				
				//update the last item id in the cart
				$config.cartLastItemId = $cartItemId;				
			};
			
			var remove = function(cartItemId)
			{
				var $cartItemId = cartItemId;				
				var $toBeRemoved = false;
				
				$.each(items, function(index, value)
				{
					//find the item we want to remove
					if (parseInt(value[0]) == parseInt($cartItemId))
					{
						//define the item to be removed
						$toBeRemoved = index;
					}					
				});
								
				//remove the item from the array
				if ($toBeRemoved !== false)
				{
					items.splice($toBeRemoved, 1);
					
					//remove the HTML for the item
					$('#cart-item-' + $cartItemId).fadeOut('fast', function()
					{
						$(this).detach();
						
						//check if there are no more items in the cart
						if (items.length == 0)
						{
							cartElement.append('<p>The cart is empty.</p>');
							//fadeOut the Empty Cart button
							$('#store-empty-cart-btn').fadeOut('slow');
						}
					});
					
					//update the total amount
					$this.WarcryStore('shoppingCart').updateTotal();
				}				
			};
			
			var empty = function()
			{
				//check if there are any items at all
				if (items.length == 0)
				{
					return false;
				}
				
				//null the items
				$config.cartItems = [];
				
				//null the last item id
				$config.cartLastItemId = 0;

				//fadeOut the Empty Cart button
				$('#store-empty-cart-btn').fadeOut('slow');
				
				//fadeOut the items
				$('ul', cartElement).fadeOut('slow', function()
				{
					//null the HTML
					cartElement.html('<p>The cart is empty.</p>');
				});

				//update the total amount
				$this.WarcryStore('shoppingCart').updateTotal();
			};
			
			var selectCurreny = function(cartItemId, currency)
			{
				var $cartItemId = cartItemId;	
				var $currency = currency;
				
				//update the selectedCurrency value
				$.each(items, function(index, value)
				{
					//find the item we want to change currency
					if (parseInt(value[0]) == parseInt($cartItemId))
					{
						value[1].selectedCurrency = $currency;
					}					
				});
				
				//remove the previusly selected currency class active
				if ($('.sitem-g-coins > a', '#cart-item-' + $cartItemId).length > 0)
				{
					$('.sitem-g-coins > a', '#cart-item-' + $cartItemId).removeClass('active');
				}
				if ($('.sitem-s-coins > a', '#cart-item-' + $cartItemId).length > 0)
				{
					$('.sitem-s-coins > a', '#cart-item-' + $cartItemId).removeClass('active');
				}
				
				//add the class active to the newly selected currency
				if (currency == 'gold')
				{
					$('.sitem-g-coins > a', '#cart-item-' + $cartItemId).addClass('active');
				}
				else
				{
					$('.sitem-s-coins > a', '#cart-item-' + $cartItemId).addClass('active');
				}

				//update the total amount
				$this.WarcryStore('shoppingCart').updateTotal();
			};
			
			var updateTotal = function()
			{
				var cont = $('#store-total-amount');
				var totalGold = 0;
				var totalSilver = 0;
				
				//update the total amount of the items
				$.each(items, function(index, value)
				{
					var currency = value[1].selectedCurrency;
					var priceGold = value[1].gold;
					var priceSilver = value[1].silver;
					
					//update the total amount by the selected currency
					if (currency == 'gold')
					{
						totalGold = totalGold + priceGold;
					}
					else
					{
						totalSilver = totalSilver + priceSilver;
					}
				});
				
				//update the config values
				$totalAmount.silver = totalSilver;
				$totalAmount.gold = totalGold;
				
				//update the text, if we have any price at all
				if (totalGold > 0 || totalSilver > 0)
				{
					//if we have both gold and silver
					if (totalGold > 0 && totalSilver > 0)
					{
						//display the gold
						$('#store-total-gold', cont).html(totalGold);
						//display the silver
						$('#store-total-silver', cont).html(totalSilver);
					}
					else if (totalGold > 0 && totalSilver == 0)
					{
						//display the gold
						$('#store-total-gold', cont).html(totalGold);
						//hide the silver
						$('#store-total-silver', cont).html('0');
					}
					else if (totalGold == 0 && totalSilver > 0)
					{
						//display the silver
						$('#store-total-silver', cont).html(totalSilver);
						//hide the gold
						$('#store-total-gold', cont).html('0');
					}
					
					//fadeIn the container
					cont.fadeIn('slow');
				}
				else
				{
					if (cont.css('display') != 'none')
					{
						cont.fadeOut('slow');
					}
				}
			};
			
			return {
				add: add,
				remove: remove,
				empty: empty,
				selectCurreny: selectCurreny,
				updateTotal: updateTotal,
			};
		},
		
		//this function is to simply preserv the shopping cart in case you dont have the amount, the values will be double checked by PHP
		verifyAmount : function()
		{
			var $this = $(this);
			var $config = $this.data('WarcryStore').config;
			var $amount = $config.cartTotalAmount;
			var $items = $config.cartItems;
			var $realm = $config.realm;
						
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
					silver: $amount.silver,
					gold: $amount.gold,
					realm: $realm,
				},
				function(data)
				{
					if (data == 'OK')
					{
						//remove all the old elements
						$('#store-complete-form').children('input[name*=items]').each(function(index, element)
						{	
                            $(this).detach();
                        });
						
						//get the items
						$.each($items, function(index, value)
						{
							var itemId = value[1].itemId;
							var currency = value[1].selectedCurrency;
							var icon = value[1].icon;
							
							//append the inputs
							$('#store-complete-form').append('<input type="hidden" name="items['+index+']" value="'+ itemId +','+ currency +','+ icon +'" />');
						});
						
						//restore the on submit function
						$('#store-complete-form').off('submit');
						$('#store-complete-form').submit();
					}
					else
					{
						//prompt the error
						$.fn.WarcryAlertBox('open', '<p>' + data + '</p>');
					}
				}
			);
		},	
	}
	
  	$.fn.WarcryStore = function(method)
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
      		$.error( 'Method ' +  method + ' does not exist on jQuery.WarcryStore');
    	}    
  	};

})(jQuery);