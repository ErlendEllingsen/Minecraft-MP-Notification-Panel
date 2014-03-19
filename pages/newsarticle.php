<?php
	/*
		All code in this script (including ALL files inside this directory) is property of Erlend Ellingsen.
		Script written for use on 'XioCo' and 'XioCo' ONLY. 
		Do not copy any code without permission from Erlend Ellingsen.
	*/

	include(file_exists("../inc/reqLogin.php") ? "../inc/reqLogin.php" : "inc/reqLogin.php");
	if (!isset($_GET["id"]))
	{
		echo '
		<div class="notification error">
			Mangler ID.
		</div>';
		return;
	}

	if (!is_numeric($_GET["id"]))
	{
		echo '
		<div class="notification error">
			Feil ID.
		</div>';
		return;
	}

	$articleid = floor(escapeString($_GET["id"]));

	$sql = "SELECT * FROM `news` WHERE `id`='" . $articleid . "' AND `active`='1'";
	$result = qq($sql);
	$num_of_articles = mysql_num_rows($result);
	if ($num_of_articles <= 0)
	{
		echo '
		<div class="notification error">
			Fant ingen publiserte artikler med den spesifiserte id\'en.
		</div>';
		return;
	}

	$articledata = mysql_fetch_object($result);
	$aD = new userData();
	$aD->getUserData($articledata->author);
	echo '
	<div class="contentbox newsBox" id="newsBoxOnly">
		<h3>' . $articledata->subject . '</h3>
		<span class="newsAuthorDesc">Skrevet av <a href="index.php?p=viewprofile&id=' . $articledata->author . '"><span style="text-shadow: 1px 1px 1px #747474; color: ' . $accessLevelColors[$aD->data->userrank] . ';">' . $aD->data->username . '</span></a> den ' . date('d-M-Y H:i', $articledata->creationdate) . '.</span>
		' . $articledata->content . '
	</div>
	';
?>