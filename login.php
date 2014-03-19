<?php
	/*
		All code in this script (including ALL files inside this directory) is property of Erlend Ellingsen.
		Script written for use on 'XioCo' and 'XioCo' ONLY. 
		Script written June 2012.

		Do not copy any code without permission from Erlend Ellingsen.
	*/
	
	include "config.php";
	include "functions.php";

	$loginerrors = array(

		"fields" => "Begge feltene må fylles ut!",
		"invalidinfo" => "Feil brukernavn eller passord",
		"noaccess" => "Du har ikke tilgang til staffpanelet"

	);


	if (isset($_SESSION["username"]))
	{
		header('Location: index.php');
		die;
	}

	if (isset($_POST["login"]))
	{
		if (empty($_POST["username"]) || empty($_POST["password"]))
		{
			header('Location: login.php?error&errormsg=fields');
			return;
		} 

		$sql = "SELECT `salt` FROM `users` WHERE `username`='" . escapeString($_POST["username"]) . "' LIMIT 0,1";
		$result = qq($sql);
		$num_of_users = mysql_num_rows($result);

		if ($num_of_users <= 0)
		{
			header('Location: login.php?error&errormsg=invalidinfo');
			return;
		}

		$data = mysql_fetch_object($result);
		$password_encrypted = md5($data->salt . $_POST["password"]);
		$sql = "SELECT `userrank` FROM `users` WHERE `username`='" . escapeString($_POST["username"]) . "' AND `password`='" . $password_encrypted . "' LIMIT 0,1";
		$result = qq($sql);
		$num_of_users = mysql_num_rows($result);
		if ($num_of_users <= 0)
		{
			header('Location: login.php?error&errormsg=invalidinfo');
			return;
		} 

		$userrankdata = mysql_fetch_object($result);
		if ($userrankdata->userrank < constant('USER_PANEL_LOGIN_REQUIREMENT_LEVEL'))
		{
			header('Location: login.php?error&errormsg=noaccess');
			return;
		}

		$_SESSION["username"] = escapeString($_POST["username"]);
		header('Location: index.php');
}

	//silly stuff
	$bgpositions = array('center', 'left', 'left top', 'left center', 'right top', 'right center', 'center top', 'center center');

	echo '
	<html>
		<head>
			<meta name="author" content="Erlend Ellingsen" />
			<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
			<link href="style/css/login.css" type="text/css" rel="stylesheet">
			<title>XioCo Staff Login</title>
		</head>
		<body>
	';

	if (isset($_GET["error"]))
	{
		if (!isset($_GET["errormsg"]))
		{
			return;
		}

		if (!array_key_exists($_GET["errormsg"], $loginerrors))
		{
			return;
		}

		echo '
		<div class="presentMessage presentMessageError">
			' . $loginerrors[$_GET["errormsg"]] . '
		</div>
		';

	}

	echo '
			<div id="loginBox">
				<h3>innlogging staffpanel</h3>
				<form action="" method="post">
					<div class="loginField">Brukernavn</div>
					<div class="loginField"><input type="text" name="username"></div>
					<div class="loginField">Passord</div>
					<div class="loginField"><input type="password" name="password"></div>
					<div class="loginField subm"><input type="submit" name="login" value="Logg inn"></div>
				</form>
				<a href="forgotpass.php">Glemt passord</a>
			</div>
		</body>
	</html>
	';

	mysql_close();
?>