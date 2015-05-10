<?php
if (!defined('init_pages'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//check if we are allowed to display notification
if (!isset($_SESSION['AvailableNotification']) or $_SESSION['AvailableNotification'] === false)
{
	header("Refresh: 0; url=".$config['BaseURL']."/index.php");
	exit();
}
else
{
	$_SESSION['AvailableNotification'] = false;
}

//Set the title
$TPL->SetTitle('Notification');
//Print the header
$TPL->LoadHeader();

//It seems we have permission to be here let's try and get the first notification
if ($data = $NOTIFICATIONS->GetFirst())
{
	echo '
    <div class="sub-page-title">
        <div id="title"><h1>', $data['title'], '<p></p><span></span></h1></div>
    </div>
     
    <div class="container_2" align="center">
        <div class="vertical_center" align="center">
     
            <div class="container_3" align="center">
                
                <div class="login-success">
                    <h1>', $data['headline'], '</h1>
                    <p style="padding:0 50px 20px 50px; text-align: ', $data['textAlign'], ';">', $data['text'], '</p>
					', (!$data['autoContinue'] ? '<a href="'.$data['return'].'" style="padding-bottom: 20px; font-size: 12px;">Continue</a>' : ''), '
                </div>
            
            </div>
    
        </div>
    </div>';
	
	//check for auto continue
	if ($data['autoContinue'])
	{
		echo '<meta http-equiv="refresh" content="', $data['delay'], ';URL=\'', $data['return'], '\'">';
	}
}
unset($data);

$TPL->LoadFooter();

?>
 