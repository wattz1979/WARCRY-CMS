<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Define the active movies so we can choose one of them as active for the page
$SWFMovies[] = 'cwc.swf';