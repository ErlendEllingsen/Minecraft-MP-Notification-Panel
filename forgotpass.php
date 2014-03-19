<?php
	include "config.php";
	include "functions.php";

	$reqchars = array(
		"@", "."
	);
	$loginerrors = array(

		"fields" => "Alle feltene må fylles ut!",
		"wrongformat" => "Feil format",
		"invalidinfo" => "Fant ingen brukere med denne e-posten",
		"couldnotcreatereset" => "Fikk ikke opprettet resettingsøkt"

	);

	if (isset($_SESSION["username"]))
	{
		header('Location: index.php');
		return;
		die();
	}

	if (isset($_POST["resetpass"]))
	{
		if (empty($_POST["email"]))
		{
			header('Location: forgotpass.php?error&errormsg=fields');	
			return;
		}

		for ($i = 0; $i < count($reqchars); $i++)
		{
			if (strstr($_POST["email"], $reqchars[$i]) == false)
			{
				header('Location: forgotpass.php?error&errormsg=wrongformat');	
				return;
			}
		}

		$setemail = strip_tags(escapeString($_POST["email"]));
		$sql = "SELECT `id`, `username`, `userrank` FROM `users` WHERE `email`='" . $setemail . "' LIMIT 0,1";
		$result = qq($sql);
		$num_of_users = mysql_num_rows($result);
		if ($num_of_users <= 0)
		{
			header('Location: forgotpass.php?error&errormsg=invalidinfo');	
			return;
		}

		$userdata = mysql_fetch_object($result);
		$selectedhash = createSalt(20);
		$sql = "INSERT INTO `passresets` (`userid`, `hash`) VALUES ('" . $userdata->id . "', '" . $selectedhash . "')";
		$result = qq($sql);
		if (!$result)
		{
			header('Location: forgotpass.php?error&errormsg=couldnotcreatereset');	
			return;
		}

		header('Location: forgotpass.php?success');
		return;
	}

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

	if (isset($_GET["success"]))
	{
		echo '
		<div class="presentMessage presentMessageSuccess">
				Vi har sendt en e-post til deg anngående informasjon om hvordan du tilbakestiller passordet ditt.
		</div>
		';
	}

	echo '
	<html>
		<head>
			<meta name="author" content="Erlend Ellingsen" />
			<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
			<link href="style/css/forgotpass.css" type="text/css" rel="stylesheet">
			<title>XioCo Staff Login</title>
		</head>
		<body>
			<div id="forgotbox">
				<h3>Glemt passord</h3>
				Skriv inn e-posten din, så sender vi en informasjonsmail om hvordan du tilbakestiller passordet på brukeren din til eposten din.
				<form action="" method="post">
					<input type="text" name="email" placeholder="Emailen din her">	
					<input type="submit" name="resetpass" value="Send e-post">
				</form>
				<a href="login.php">Tilbake</a>
				<span><span style="font-weight: bold;">Merk:</span> IP-addressen din sendes med e-posten. Vandalisme vil føre til utestengelse.</span>
			</div>
		</body>
	</html>
	';

	mysql_close();
?>