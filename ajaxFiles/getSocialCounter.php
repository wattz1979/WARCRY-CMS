<?PHP
if (!defined('init_ajax'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$SocialId = isset($_GET['sid']) ? (int)$_GET['sid'] : false;

switch ($SocialId)
{
	case APP_FACEBOOK:
	{
		if (($json = $CACHE->get('facebook_likes')) === false)
		{
			$CORE->load_CoreModule('facebook.api');
			
			$fbconfig = array();
			$fbconfig['appId'] = $config['FACEBOOK']['appId'];
			$fbconfig['secret'] = $config['FACEBOOK']['secret'];
			$fbconfig['fileUpload'] = false; // optional
			
			$facebook = new Facebook($fbconfig);
			//Pull the likes
			$response = $facebook->api('/'.$config['FACEBOOK']['pageID'].'?fields=likes', 'GET');
			//Print
			$json = json_encode($response);
			//Cache for 1 hour
    		$CACHE->store('facebook_likes', $json, strtotime('1 hour', 0));
			//mem
			unset($facebook);
		}
		break;
	}
	case APP_TWITTER:
	{
		if (($json = $CACHE->get('twitter_stats')) === false)
		{
			$response = $CORE->getRemotePage(array(
				'host'	=> 'api.twitter.com',
				'port'	=> 80,
				'page'	=> '/1/users/show.json?screen_name='.$config['TWITTER']['page'].'&include_entities=true'
			));
			$json = $response['body'];
			//Cache for 1 hour
    		$CACHE->store('twitter_stats', $json, strtotime('1 hour', 0));
		}
		break;
	}
}

//Set the json headers
header('Content-type: text/json');
header('Content-type: application/json');

echo $json;

?>