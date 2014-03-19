<?php
	/*
		All code in this script (including ALL files inside this directory) is property of Erlend Ellingsen.
		Script written for use on 'XioCo' and 'XioCo' ONLY. 
		Script written June 2012.

		Do not copy any code without permission from Erlend Ellingsen.
	*/
	include(file_exists("../inc/reqLogin.php") ? "../inc/reqLogin.php" : "inc/reqLogin.php");
	if ($ud->data->userrank <= $accessLevelValues['sponsor'])
	{
		echo '
		<div class="notification error">
			Du har ikke tilgang til brukeradministrasjon.
		</div>';
		return;
	}

	$sql = "UPDATE `users` SET `unreadnotifications`='0' WHERE `id`='" . $ud->data->id . "'";
	$result = qq($sql);
	if (!$result)
	{
		echo '
		<div class="notification error">
			Fikk ikke nullstilt uleste notifikasjoner.
		</div>';
		return;
	}

	echo '
	<div class="contentbox">
		<h3>Varsler (viser 15 per side)</h3>
		<div id="notificationCentring">
			<ul id="notesWidgetListPage">
	';


	$showWhat = calculationResults(15);

	$sql = "SELECT `id` FROM `notifications`";
	$result = qq($sql);
	$num_of_notifications_total = mysql_num_rows($result);

	$sql = "SELECT `notificationdata`, `notificationtype`, `creationdate` FROM `notifications` ORDER BY `id` DESC LIMIT " . $showWhat['min'] . "," . $showWhat['max'];
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
	';

	generatePagination($num_of_notifications_total, 15, 'notifications');

	echo '
		</div>
	</div>
	<div class="contentbox" style="margin-top: 20px;">
		<h3>Fargeforklaring for notater</h3>
		<p id="noteWidgetColorCodes"><img src="style/img/color_note.png"> Notat <img src="style/img/color_kick.png"> Kick <img src="style/img/color_warning.png"> Advarsel <img src="style/img/color_ban.png"> Ban<br /></p>
	</div>
	';
?>