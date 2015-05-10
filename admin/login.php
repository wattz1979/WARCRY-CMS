<?PHP
include_once 'engine/initialize.php';
?>
<!DOCTYPE html>
<html lang="en">
<title>AdminCP</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script><link type="text/css" rel="stylesheet" href="data:text/css,">
<script type="text/javascript" src="login/js/jquery.validate.js"></script>
<script type="text/javascript" src="login/js/css_browser_selector.js"></script>
<script type="text/javascript" src="login/js/notifications.js"></script>
<script type="text/javascript" src="login/js/js.js"></script>
<link rel="stylesheet" href="login/css/reset.css" type="text/css">
<link rel="stylesheet" href="login/css/grid.css" type="text/css">
<link rel="stylesheet" href="login/css/style.css" type="text/css">
</head>
<body>
	<ul id="notifications"></ul>
	<div id="loginbox" style="display: block; ">

		<?php
		if ($error = $ERRORS->DoPrint('login'))
		{
			echo $error, '<br><br>';
			unset($error);
		}			
		?>

		<a href="#" id="logo">AdminCP</a>
		<div id="loginform">
			<form name="login" action="execute.php?take=login" method="post" novalidate>
				<div id="username_field"><input type="text" name="username" placeholder="Username" class="required" value=""></div>
				<div id="password_field"><input type="password" name="password" placeholder="Password" class="required" value=""></div>
				<div id="buttonline">
					<input type="submit" id="loginbutton" class="float_left width_4" value="Login">
				</div>
			</form>
		</div>
	</div>

</body></html>