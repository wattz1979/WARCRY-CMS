<?PHP
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

?>

<script src="template/js/shadowbox.js" type="text/javascript"></script>
<script>
function InitShadowbox()
{
	Shadowbox.init(
	{
		onFinish: function()
		{
			var obj = Shadowbox.getCurrent()

			var innerBody = $('#sb-body-inner');
			//append the new div element containing the description
			innerBody.append('<div id="sb-description" style="position: absolute; left: 0px; opacity: 0;">'+obj.description+'</div>');
			//get the description container
			var desctCont = $('#sb-description');
			//set width
			desctCont.css({ width: Shadowbox.dimensions.innerWidth + 'px' });
			desctCont.css({ top: (Shadowbox.dimensions.innerHeight - desctCont.outerHeight()) + 'px' });
			//fade it in
			desctCont.animate({ opacity: 1 }, 'slow');
			//handle window resize
			$(window).resize(function()
			{
				//get the description container
				var desctCont = $('#sb-description');
				//set width
				desctCont.css({ width: Shadowbox.dimensions.innerWidth + 'px' });
				desctCont.css({ top: (Shadowbox.dimensions.innerHeight - desctCont.outerHeight()) + 'px' });
				//do that again
				setTimeout(function()
				{
					//set width
					desctCont.css({ width: Shadowbox.dimensions.innerWidth + 'px' });
					desctCont.css({ top: (Shadowbox.dimensions.innerHeight - desctCont.outerHeight()) + 'px' });
				}, 100);
			});
		}
	});
	Shadowbox.onReady = function()
	{
		setTimeout(function()
		{
			//filter the titles
			for (var key in Shadowbox.cache)
			{
				var title = Shadowbox.cache[key].title;
				//split the title from the description
				var parts = title.split('{|}');
				//save the title and description
				if ($(parts).size() == 2)
				{
					Shadowbox.cache[key].title = parts[0];
					Shadowbox.cache[key].description = parts[1];
				}
				//remove the description part from the elements title attr
				$(Shadowbox.cache[key].link).attr('title', parts[0]);
			}
		}, 500);
	};
}
</script>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li><a href="index.php?page=media">Movies</a></li>
        <li><a href="index.php?page=movie-add">New Movie</a></li>
		<li class="current"><a href="index.php?page=screenshots">Screenshots</a></li>
	</ul>
</nav>

<?php
//check for permissions
if (!$CURUSER->getPermissions()->isAllowed(PERMISSION_MEDIA_SREENSHOTS))
{
	$CORE->ErrorBox('You do not have the required permissions.');
}
?>
   
<!-- The content -->
<section id="content">

<div class="tab" id="maintab">
	<h2>Screenshots Management</h2>
  	  
      <?php
	  
	  	if ($error = $ERRORS->DoPrint('deleteScreenshot'))
		{
			echo $error;
			unset($error);
		}			
 		if ($success = $ERRORS->successPrint('deleteScreenshot'))
		{
			echo $success;
			unset($success);
		}			

	  ?>
      
      <table class="datatable" id="datatable_screenshots">
      
        <thead>
          <tr>
            <th>Title</th>
            <th>Image</th>
            <th>Added</th>
            <th>Uploaded by</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        
        <tbody>
        
        <?php
		
		function get_StatusString($status)
		{
			switch ($status)
			{
				case SCREENSHOT_STATUS_PENDING:
					return 'Pending';
					break;
				case SCREENSHOT_STATUS_APPROVED:
					return 'Approved';
					break;
				case SCREENSHOT_STATUS_DENIED:
					return 'Denied';
					break;
				default:
					return 'Unknown';
			}
		}
		
		$res = $DB->prepare("SELECT * FROM `images` WHERE `type` = :type ORDER BY added DESC;");
		$res->bindValue(':type', TYPE_SCREENSHOT, PDO::PARAM_STR);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			while ($arr = $res->fetch())
			{
				//find the account
				$res2 = $DB->prepare("SELECT displayName FROM `account_data` WHERE `id` = :id LIMIT 1;");
				$res2->bindParam(':id', $arr['account'], PDO::PARAM_INT);
				$res2->execute();
				//if we found it
				if ($res2->rowCount() > 0)
				{
					$row = $res2->fetch();
					$arr['account'] = $row['displayName'] . ' [' . $arr['account'] . ']';
					unset($row);
				}
				unset($res2);
				
				echo '
				<tr id="screen-record-', $arr['id'], '">
					<td>', $arr['name'], '</td>
					<td><a class="sliding-image" href="', $config['BaseURL'], '/uploads/media/screenshots/', $arr['image'],'" rel="shadowbox" title="', $arr['name'], '{|}', $arr['descr'], '" style="display: block; height: 30px; overflow-y: hidden;"><img src="', $config['BaseURL'], '/uploads/media/screenshots/thumbs/', $arr['image'],'" height="100" /></a></td>
					<td>', $arr['added'], '</td>
					<td>', $arr['account'], '</td>
					<td>', get_StatusString($arr['status']), '</td>
					<td>
					  <span class="button-group">
						<a href="javascript: void(0);" style="', (($arr['status'] == SCREENSHOT_STATUS_PENDING or $arr['status'] == SCREENSHOT_STATUS_DENIED) ? 'display: block;' : 'display: none;'), '" class="button icon approve" onclick="return ApproveScreenshot('.$arr['id'].');">Approve</a>
						<a href="javascript: void(0);" style="', ($arr['status'] == SCREENSHOT_STATUS_APPROVED ? 'display: block;' : 'display: none;'), '" class="button icon arrowdown deny" onclick="return DenyScreenshot('.$arr['id'].');">Deny</a>
						<a href="execute.php?take=delete&action=screenshot&id=', $arr['id'], '" onclick="return deletecheck(\'Are you sure you want to delete this screenshot?\');" class="button icon remove danger">Remove</a>
					  </span>
					</td>
				</tr>';
			}
		}
		
		?>
           
        </tbody>
        
      </table>

</div>

<script src="template/js/jquery.datatables.js" type="text/javascript"></script>

<script>
	var $screensTable = null;
	var $configURL = '<?php echo $config['BaseURL']; ?>';
	
	function ApproveScreenshot(id)
	{
		var $id = id;
		
		//get the row index
		var tr = $('#screen-record-' + $id);
		var index = tr.index();
		
		$.post(
			$configURL + "/admin/execute.php?take=approve_screenshot", 
			{ 
				id: $id,
			}, 
			function(data)
			{
	     		//check for errors
				if (data == 'OK')
				{
					new Notification('The screenshot has been successfully approved.', 'success');
					$screensTable.fnUpdate('Approved', index, 4); // Single cell
					//update the buttons
					$('.approve', tr).css('display', 'none');
					$('.deny', tr).css('display', 'block');
				}
				else
				{
					new Notification(data, 'error', 'urgent');
				}
	   		}
		);
		
		return false;
	}

	function DenyScreenshot(id)
	{
		var $id = id;
		
		//get the row index
		var tr = $('#screen-record-' + $id);
		var index = tr.index();
		
		$.post(
			$configURL + "/admin/execute.php?take=deny_screenshot", 
			{ 
				id: $id,
			}, 
			function(data)
			{
	     		//check for errors
				if (data == 'OK')
				{
					new Notification('The screenshot has been successfully denied.', 'success');
					$screensTable.fnUpdate('Denied', index, 4); // Single cell
					//update the buttons
					$('.deny', tr).css('display', 'none');
					$('.approve', tr).css('display', 'block');
				}
				else
				{
					new Notification(data, 'error', 'urgent');
				}
	   		}
		);
		
		return false;
	}
	
	$(document).ready(function()
	{
		if ($("#datatable_movies").length > 0)
		{
		 	var moviesTable = $("#datatable_movies").dataTable(
			{
				"bFilter": false,
				"aoColumnDefs": [ 
		      		{ "bSortable": false, "aTargets": [ 2 ] }
		    	]
			});
			//sort the table
			moviesTable.fnSort( [ [1, 'desc'] ] );
		}
		//screenshots datatable
		if ($("#datatable_screenshots").length > 0)
		{
		 	$screensTable = $("#datatable_screenshots").dataTable(
			{
				"bFilter": false,
				"aoColumnDefs": [ 
		      		{ "bSortable": false, "aTargets": [ 5 ] }
		    	],
				"fnDrawCallback": function()
				{
					InitShadowbox();
					//sliding-image
					$('.sliding-image').on('mouseover', function()
					{
						$(this).stop().animate({ height: 100 }, 'fast');
					});
					$('.sliding-image').on('mouseout', function()
					{
						$(this).stop().animate({ height: 30 }, 'fast');
					});
				}
			});
			//sort the table
			$screensTable.fnSort( [ [2, 'desc'] ] );
		}
    });
</script>    
