<?php
	/*
		All code in this script (including ALL files inside this directory) is property of Erlend Ellingsen.
		Script written for use on 'XioCo' and 'XioCo' ONLY. 
		Script written June 2012.

		Do not copy any code without permission from Erlend Ellingsen.
	*/

	include "config.php";
	include "functions.php";
	include "inc/userData.php";
	include "inc/BBCode.class.php";

	if (!isset($_SESSION["username"]))
	{
		header('Location: login.php');
		die;
	}

	$user_id = getID($_SESSION["username"]);
	include "inc/cData.php";
	include "inc/updateLA.php";

	if (!array_key_exists(constant('PATH'), $officalVersions))
	{
		echo '
		<span style="color: red; background-color: #000;">Dette er <span style="font-weight: bold;">developer</span>-versjonen av stabpanelet. Du finner XioCo stabpanelet <a style="color: red;" href="http://xioco.gameadvise.no/panel/index.php">her</a>.</span>
		';
	}
	
	
	echo '
	<html>
		<head>
			<meta name="author" content="Erlend Ellingsen" />
			<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
			<title>XioCo Staff-panel</title>
			<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
			<script type="text/javascript" src="js/jquery.zclip.js"></script>
			<script type="text/javascript" src="js/clipboard.js"></script>
			<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
			<script type="text/javascript" src="js/calendar.js"></script>
			<script type="text/javascript" src="js/ckeditor/ckeditor.js"></script>
			<script type="text/javascript" src="js/staffpanel.js"></script>
			<link href="style/css/index.css" type="text/css" rel="stylesheet">
			<link href="style/css/datecalendar/jsDatePick_ltr.min.css" type="text/css" rel="stylesheet" media="all"  />
			<script type="text/javascript">
				function setupSystem()
				{
					system_path = \'' . constant('PATH') . '\';
					user_id = \'' . $ud->data->id . '\';
					console.log(\'System setup completed.\');
				}
			</script>
		</head>
		<body>
			<div id="header">
			 	<div id="header_right">
			 		<span class="topmenu_object">
			 			<a href="index.php?p=settings">Innstillinger</a>
			 		</span>
			 		<span class="topmenu_object">
			 			<a href="logout.php">Logg ut</a>
			 		</span>
			 	</div>
				<span class="topmenu_object">
					Hallo hallo, <span id="header_username">' . ucfirst($ud->data->username) . '</span> (<span style="color: ' . $accessLevelColors[$ud->data->userrank] . ';">' . $accessLevels[$ud->data->userrank] . '</span>)
				</span>
				<span class="topmenu_object">
					<a href="index.php?p=news">Hjem</a>
				</span>
				<span class="topmenu_object" id="notificationstopobject">
					<a href="index.php?p=notifications">Varsler</a> (' . ($ud->data->unreadnotifications <= 0 ? '<span style="color: #D2D2D2;" id="newnotifications">0</span>' : '<span style="color: yellow;" id="newnotifications">' . number_format($ud->data->unreadnotifications) . '</span>') . ')
				</span>
				<span class="topmenu_object">
					<a href="index.php?p=useroptions">Brukere</a>
				</span>
				<span class="topmenu_object">
					<a href="index.php?p=noteoptions">Notater</a>
				</span>
				
	';

				if ($ud->data->userrank > $accessLevelValues['vakt'])
				{
					echo '
				<span class="topmenu_object">
					<a href="index.php?p=editnews">Nyhetsoversikt</a>
				</span>
					';
				}

				echo '
				<span class="topmenu_object">
					<a href="index.php?p=stats">Statistikk</a>
				</span>
				';

	echo '
			</div>
			<div id="main">
				';

	if (empty($ud->data->email))
	{
		include 'pages/setemail_important.php';
	} else 
	{
		$page = isset($_GET["p"]) ? $_GET["p"] : 'news';
		if (!file_exists('pages/' . $page . '.php'))
		{
			echo '
			404 - Siden eksisterer ikke.
			';
		} else 
		{
			include 'pages/' . $page . '.php';
		}
	}

				echo '
			</div>
		</body>
	</html>
	';

	mysql_close();

?>