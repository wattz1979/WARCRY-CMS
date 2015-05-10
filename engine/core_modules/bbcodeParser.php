<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class bbcodeParser
{
 var $strip_html = true; 
 
 var $include_li = true;
 var $include_bold = true;
 var $include_italic = true;
 var $include_underline = true;
 var $include_imgs = true;
 var $include_colors = true;
 var $include_urls = true;
 var $include_size = true;
 var $include_fonts = true;
 var $include_quotes = true;
 var $include_code = false;
 var $include_list = true;
 var $include_nl2br = true;
 var $include_pre = true;
 var $include_spacing = true;
  
 private function _urls($str)
 {
  return preg_replace(
    	"/(\A|[^=\]'\"a-zA-Z0-9])((http|ftp|https|ftps|irc):\/\/[^()<>\s]+)/i",
	    "\\1<a href=\"\\2\">\\2</a>", $str);
 }

 private function _quotes($str)
 {
  $s = '';
  
  while ($s != $str)
  {
  	$s = $str;

	  $close = strpos($str, "[/quote]");
	  
	  if ($close === false)
	  	return $str;

	  $open = strripos(substr($str,0,$close), "[quote");
	  
	  if ($open === false)
	    return $str;

	  $quote = substr($str,$open,$close - $open + 8);

	  //[quote]Text[/quote]
	  $quote = preg_replace(
	    "/\[quote\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i",
	    "<p style='margin: 4px; color: #bcbcbc;'><b>Quote:</b></p><table width='100%' class='main' border='0' cellspacing='0' cellpadding='10'><tr><td style='border: 1px #b9b9b9 dotted; color: #8d8d8d;'>\\1</td></tr></table><br />", $quote);

	  //[quote=Author]Text[/quote]
	  $quote = preg_replace(
	    "/\[quote=(.+?)\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i",
	    "<p style='margin: 4px; color: #bcbcbc;'><b>\\1 wrote:</b></p><table width='100%' class='main' border='0' cellspacing='0' cellpadding='10'><tr><td style='border: 1px #b9b9b9 dotted; color: #8d8d8d;'>\\2</td></tr></table><br />", $quote);

	  $str = substr($str,0,$open) . $quote . substr($str,$close + 8);
  }

  return $str;
 }

 private function _code($str)
 {
  $s = '';
  
  while ($s != $str)
  {
  	$s = $str;

	  $close = strpos($str, "[/code]");
	  
	  if ($close === false)
	  	return $str;

	  $open = strripos(substr($str,0,$close), "[code");
	  
	  if ($open === false)
	    return $str;

	  $code = substr($str,$open,$close - $open + 8);

	  //[code]Text[/code]
	  $code = preg_replace(
	    "/\[code\]\s*((\s|.)+?)\s*\[\/code\]\s*/i",
	    "<p style='margin: 4px; color: #bcbcbc;'><b>Code:</b></p><table width='100%' class='main' border='0' cellspacing='0' cellpadding='10'><tr><td style='border: 1px #b9b9b9 dotted; color: #8d8d8d;'><pre>\\1</pre></td></tr></table><br />", $code);

	  $str = substr($str,0,$open) . $code . substr($str,$close + 8);
  }

  return $str;
 }

 private function _f_list($str)
 {
  $s = '';
  
  while ($s != $str)
  {
  	$s = $str;

	  $close = strpos($str, "[/list]");
	  
	  if ($close === false)
	  	return $str;

	  $open = strripos(substr($str,0,$close), "[list");
	  
	  if ($open === false)
	    return $str;

	  $list = substr($str,$open,$close - $open + 8);

	  //[list]Text[/list]
	  $list = preg_replace(
	    "/\[list\]\s*((\s|.)+?)\s*\[\/list\]\s*/i",
	    "<ul class=\"list-c\">\\1</ul>", $list);

	  //[list=1]Text[/list]
	  $list = preg_replace(
	    "/\[list=([0-9])\]\s*((\s|.)+?)\s*\[\/list\]\s*/i",
	    "<ol class=\"list-o\" start=\"\\1\">\\2</ol>", $list);

	  $str = substr($str,0,$open) . $list . substr($str,$close + 8);
  }

  return $str;
 }

 public function parse($str)
 {
	$str = stripslashes($str);
	
  	if ($this->strip_html)
     $str = htmlentities($str, ENT_QUOTES);
  
	// [*]
	if ($this->include_li)
	 $str = preg_replace("/\[\*\]((.*)+?)/", "<li><span>\\1</span></li>", $str);
	
	// [b]Bold[/b]
	if ($this->include_bold)
  	 $str = preg_replace("/\[b\]((\s|.)+?)\[\/b\]/", "<b>\\1</b>", $str);

	// [i]Italic[/i]
	if ($this->include_italic)
	 $str = preg_replace("/\[i\]((\s|.)+?)\[\/i\]/", "<i>\\1</i>", $str);

	// [u]Underline[/u]
	if ($this->include_underline)
	 $str = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/", "<u>\\1</u>", $str);

	// [img]http://www/image.gif[/img]
	if ($this->include_imgs)
	 $str = preg_replace("/\[img\](http:\/\/[^\s'\"<>]+(\.(jpg|gif|png)))\[\/img\]/i", "<img border=\"0\" src=\"\\1\" alt='' />", $str);

	// [img=normal-img]http://www/image.gif[/img]
	if ($this->include_imgs)
	 $str = preg_replace("/\[img=normal-img\]((http|https):\/\/[^\s'\"<>]+)\[\/img\]/i", "<img class=\"normal-img\" border=\"0\" src=\"\\1\" alt='' width=\"752\" />", $str);

	// [img=float-left-img]http://www/image.gif[/img]
	if ($this->include_imgs)
	 $str = preg_replace("/\[img=float-left-img\]((http|https):\/\/[^\s'\"<>]+)\[\/img\]/i", "<img class=\"float-left-img\" border=\"0\" src=\"\\1\" alt='' width=\"342\" />", $str);

	// [img=float-right-img]http://www/image.gif[/img]
	if ($this->include_imgs)
	 $str = preg_replace("/\[img=float-right-img\]((http|https):\/\/[^\s'\"<>]+)\[\/img\]/i", "<img class=\"float-right-img\" border=\"0\" src=\"\\1\" alt='' width=\"342\" />", $str);

	// [img=http://www/image.gif]
	if ($this->include_imgs)
	 $str = preg_replace("/\[img=(http:\/\/[^\s'\"<>]+(\.(gif|jpg|png)))\]/i", "<img border=\"0\" src=\"\\1\" alt='' />", $str);

	// [color=blue]Text[/color]
	if ($this->include_colors)
	 $str = preg_replace(
		"/\[color=([a-zA-Z]+)\]((\s|.)+?)\[\/color\]/i",
		"<font color='\\1'>\\2</font>", $str);

	// [color=#ffcc99]Text[/color]
	if ($this->include_colors)
	 $str = preg_replace(
		"/\[color=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])\]((\s|.)+?)\[\/color\]/i",
		"<font color='\\1'>\\2</font>", $str);

	// [url=http://www.example.com]Text[/url]
	if ($this->include_urls)
	 $str = preg_replace(
		"/\[url=([^()<>\s]+?)\]((\s|.)+?)\[\/url\]/i",
		"<a href=\"\\1\">\\2</a>", $str);

	// [url]http://www.example.com[/url]
	if ($this->include_urls)
	 $str = preg_replace(
		"/\[url\]([^()<>\s]+?)\[\/url\]/i",
		"<a href=\"\\1\">\\1</a>", $str);

	// [size=4]Text[/size]
	if ($this->include_size)
	 $str = preg_replace(
		"/\[size=([1-7])\]((\s|.)+?)\[\/size\]/",
		"<font size='\\1'>\\2</font>", $str);

	// [font=Arial]Text[/font]
	if ($this->include_fonts)
	 $str = preg_replace(
		"/\[font=([a-zA-Z ,]+)\]((\s|.)+?)\[\/font\]/i",
		"<font face=\"\\1\">\\2</font>", $str);

	// quotes
	if ($this->include_quotes)
	 $str = $this->_quotes($str);
	 
	// code
	if ($this->include_code)
	 $str = $this->_code($str);

	// lists
	if ($this->include_list)
	 $str = $this->_f_list($str);
    
	// URLs
	if ($this->include_urls)
	 $str = $this->_urls($str);

	// Linebreaks
	if ($this->include_nl2br)
	 $str = nl2br($str);

	// [pre]Preformatted[/pre]
	if ($this->include_pre)
	 $str = preg_replace("/\[pre\]((\s|.)+?)\[\/pre\]/i", "<tt><span style=\"white-space: nowrap;\">\\1</span></tt>", $str);

	// Maintain spacing
	if ($this->include_spacing)
	 $str = str_replace("  ", " &nbsp;", $str);
	
	//remove all break lines inside lists and such
	$search = array("/(?<=<\/li>)<br[\s|\/|\>]+\>*?/is", "/(?<=<\/ol>)<br[\s|\/|\>]+\>*?/is", "/(?<=<\/ul>)<br[\s|\/|\>]+\>*?/is");
	$str = preg_replace($search, "", $str);
	
	//remove break lines around images
	$str = preg_replace("/<br[\s|\/|\>]+\>*?(<img[^>]+>)<br[\s|\/|\>]+\>*?/is", "\\1", $str);
	
  return $str;
 }

}
?>