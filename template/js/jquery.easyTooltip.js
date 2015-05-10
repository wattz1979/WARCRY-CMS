/*
 * 	Easy Tooltip 1.0 - jQuery plugin
 *	written by Alen Grakalic	
 *	http://cssglobe.com/post/4380/easy-tooltip--jquery-plugin
 *
 *	Copyright (c) 2009 Alen Grakalic (http://cssglobe.com)
 *	Dual licensed under the MIT (MIT-LICENSE.txt)
 *	and GPL (GPL-LICENSE.txt) licenses.
 *
 *	Built for jQuery library
 *	http://jquery.com
 *
 ###############################################
 ### Edited to suite the Teleporter by ChoMPi
 ###############################################
 */
 
(function($) {

	$.fn.TpTooltip = function(options){
	  
		// default configuration properties
		var defaults = {	
			xOffset: 15,		
			yOffset: 11,
			tooltipId: "TpTooltip",
			clickRemove: false,
			content: "",
			useElement: "",
			mapKey: "",
			name: false,
		}; 
			
		var options = $.extend(defaults, options);  
		var content;
				
		this.each(function() {  				
			var content = null;

			$(this).hover(function(e)
			{											 							   
				//content = (options.content != "") ? options.content : title;
				//content = (options.useElement != "") ? $("#" + options.useElement).html() : content;

				$("body").append("<div id='"+ options.tooltipId +"'>Loading...</div>");		
				$("#" + options.tooltipId)
					.css("position","absolute")
					.css("top",(e.pageY - options.yOffset) + "px")
					.css("left",(e.pageX + options.xOffset) + "px")						
					.css("display","none")
					.css("z-index", "200")
					.fadeIn("fast");

				//check if we have the data already
				if (options.name)
				{
					$("#" + options.tooltipId).html('<div class="tp-tooltip-inner"><div id="arrow"></div><p>'+options.name+'</p></div>');
				}
				else
				{
					//pull data by ajax
					$.get(
						"ajax.php?phase=7",
						{
							key: options.mapKey,
						},
						function(data)
						{
							//check for error
							if ($(data).find('error').length > 0)
							{
								console.log($(data).find('error').text());
							}
							
							var name = $(data).find('name').text();
							
							//save the data
							options.name = name;
							
							//fill the data
							$("#" + options.tooltipId).html('<div class="tp-tooltip-inner"><div id="arrow"></div><p>'+name+'</p></div>');
						}
					);
				}
			},
			function(){	
				$("#" + options.tooltipId).remove();
			});	
			$(this).mousemove(function(e){
				$("#" + options.tooltipId)
					.css("top",(e.pageY - options.yOffset) + "px")
					.css("left",(e.pageX + options.xOffset) + "px")					
			});	
			if(options.clickRemove){
				$(this).mousedown(function(e){
					$("#" + options.tooltipId).remove();
				});				
			}
		});
	  
	};

})(jQuery);

