<?php
	/*
		All code in this script (including ALL files inside this directory) is property of Erlend Ellingsen.
		Script written for use on 'XioCo' and 'XioCo' ONLY. 
		Script written June 2012.

		Do not copy any code without permission from Erlend Ellingsen.
	*/
	include(file_exists("../inc/reqLogin.php") ? "../inc/reqLogin.php" : "inc/reqLogin.php");
	
	/*
	echo '
	<div class="notification">
		Velkommen <span style="font-weight: bold;">' . $ud->data->username . '</span>, til betatesting av det nye staff-panelet!
	</div>
	*/
	echo '
	<div id="newsContainer">
	';
	

	$showWhat = calculationResults(8);

	$sql = "SELECT `id` FROM `news`";
	$result = qq($sql);
	$num_of_articles_total = mysql_num_rows($result);

	$sql = "SELECT `id`, `subject`, `author`, `creationdate`, `content` FROM `news` WHERE `active`='1' ORDER BY `id` DESC LIMIT " . $showWhat['min'] . "," . $showWhat['max'];
	$result = qq($sql);
	$num_of_articles = mysql_num_rows($result);
	if ($num_of_articles <= 0)
	{
		echo '
		<div class="contentbox newsBox">
			<h3>Ingen artikler</h3>
			Det er for tiden ingen aktive artikler.
		</div>
		';
	}

	while ($data = mysql_fetch_object($result))
	{
		$sql = "SELECT `userrank`, `username` FROM `users` WHERE `id`='" . $data->author . "' LIMIT 0,1";
		$author_result = qq($sql);
		$author_data = mysql_fetch_object($author_result);

		echo '
		<div class="contentbox newsBox">
			<h3 onClick="navigate(\'' . constant('PATH') . 'index.php?p=newsarticle&id=' . $data->id . '\', 0);">' . $data->subject . '</h3>
			<span class="newsAuthorDesc">Skrevet av <a href="index.php?p=viewprofile&id=' . $data->author . '"><span style="text-shadow: 1px 1px 1px #747474; color: ' . $accessLevelColors[$author_data->userrank] . ';">' . $author_data->username . '</span></a> den ' . date('d-M-Y H:i', $data->creationdate) . '.</span>
			' . $data->content . '
		</div>
		';
	}

	generatePagination($num_of_articles_total, 8, 'news');

	echo '
	</div>
	<div class="rightContainer">
		<div class="contentbox rightwidget">
			<div class="lastnoteswidgetTitle" id="notificationnewstitle">
				<h3>Siste notifikasjoner</h3>
			</div>
			<div id="lastnoficationcenterer">
				<ul id="lastnotesWidgetList">
	';


	$sql = "SELECT `notificationdata`, `notificationtype` FROM `notifications` ORDER BY `id` DESC LIMIT 0,10";
	$result = qq($sql);
	if (!$result)
	{
		echo '
					<li>Noe gikk galt. Vennligst kontakt administrator.</li>
		';
	} else 
	{
		include "inc/displaynotifications.php";	
	}

	echo '
				</ul>
			</div>
			<div id="noteWidgetColorCodes"><div class="colorBox" style="background-color: #9DD0FF;"></div> Notat <div class="colorBox" style="background-color: #505050;"></div> Kick <div class="colorBox" style="background-color: #FFE59D;"></div> Advarsel <div class="colorBox" style="background-color: #FA5858;"></div> Ban<br /> <div class="colorBox" style="background-color: #FF9D9D;"></div> Tempban <div class="colorBox" style="background-color: #60EE73;"></div> Unban<br /></div>
		</div>
		<div class="contentbox rightwidget">
			<div class="lastnoteswidgetTitle">
				<h3>Påloggede brukere</h3>
	';

	$lastActiveMax = time() - 600;
	$sql = "SELECT `userrank`, `username`, `id` FROM `users` WHERE `lastactive`>'" . $lastActiveMax . "'";
	$result = qq($sql);
	$num_users = mysql_num_rows($result);
	if ($num_users <= 0) 
	{
		echo '
		Ingen pålogget :(
		';
	} else 
	{
		$i = 0;
		while ($data = mysql_fetch_object($result))
		{
			$i++;

			$profilelink =  '<a href="index.php?p=viewprofile&id=' . $data->id . '" style="text-decoration: none;"><span style="text-shadow: 1px 1px 1px #000; color: ' . $accessLevelColors[$data->userrank] . ';">' . $data->username . '</span></a>';

			if (!($num_users <= $i))
			{
				$profilelink .= ", ";
			}

			echo $profilelink;
			
		}
	}

	echo '
			</div>
		</div>
	</div>
	';

	if (isset($_GET["chat"]))
	{
		echo '
	<div class="contentbox">
		<h3>Stabchatt</h3>
		<ul>
			<li><span style="font-weight: bold;">Kevin: </span> jeg er kul</li>
			<li><span style="font-weight: bold;">Erlend: </span> jeg og</li>
			<li><span style="font-weight: bold;">Thomas: </span> HOLD KJÆFTEN PÅ DEG TORSTEIN</li>
			<li><span style="font-weight: bold;">Torstein: </span> Tihiihiihi</li>
		</ul>
	</div>
		';

	}

	echo '
	<div class="cl"></div>
	';
?>