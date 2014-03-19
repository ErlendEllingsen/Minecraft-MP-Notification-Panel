<?php
	/*
		All code in this script (including ALL files inside this directory) is property of Erlend Ellingsen.
		Script written for use on 'XioCo' and 'XioCo' ONLY. 
		Script written in 2012.

		Do not copy any code without permission from Erlend Ellingsen.
	*/
	include(file_exists("../inc/reqLogin.php") ? "../inc/reqLogin.php" : "inc/reqLogin.php");
	if ($ud->data->userrank <= $accessLevelValues['vakt'])
	{
		echo '
		<div class="notification error">
			Du har ikke tilgang til nyhetsredigering. Du trenger minst rangeringen <span stlye="font-weight: bold;">Admin</span>.
		</div>';
		return;
	} 

	if (isset($_POST["createnewbutton"]))
	{
		echo '
			<form action="" method="post">
				<h2 id="newsArticleTitle">Opprett nyhetsartikkel</h2>
				<p class="newArticleAddin">Emne: <input type="text" name="subject"></p>
				<p class="newArticleAddin">Forfatter: <span style="text-shadow: 1px 1px 1px #747474; color: ' . $accessLevelColors[$ud->data->userrank] . ';">' . $ud->data->username . '</span></p>
				<p class="newArticleAddin">Publiser: <input type="radio" name="publishwhen" value="1" checked="checked"> Med en gang <input type="radio" name="publishwhen" value="0"> Senere</p>
				<h3 id="contentTitle">Innhold</h3>
				<textarea class="ckeditor" name="newscontent"></textarea>
				<input type="submit" name="createarticle" id="createarticle" value="Opprett artikkel">
			</form>
		';
		return;
	}

	if (isset($_POST["createarticle"]))
	{
		if (empty($_POST["subject"]) || empty($_POST["newscontent"]) || (!isset($_POST["publishwhen"])))
		{
			echo '
			<div class="notification error">
			Alle feltene må fylles ut!
			</div>
			';
			return;
		} else
		{
			$publishwhen = escapeString($_POST["publishwhen"]);
			if (!is_numeric($publishwhen))
			{
				echo '
				<div class="notification error">
				Feil publiseringsmåte valgt.
				</div>
				';
				return;
			}
			$publishwhen = round($publishwhen);
			if ($publishwhen > 1 || $publishwhen < 0)
			{
				echo '
				<div class="notification error">
				Feil publiseringsmåte valgt.
				</div>
				';
				return;
			}

			$subject = escapeString($_POST["subject"]); 
			$newscontent = escapeString($_POST["newscontent"]);

			$sql = "INSERT INTO `news` (subject, author, creationdate, content, active) VALUES ('" . strip_tags($subject) . "', '" . $ud->data->id . "', '" . time() . "', '" . $newscontent . "', '" . $publishwhen . "')";
			$result = qq($sql);
			if (!$result)
			{
				echo '
				<div class="notification error">
					Noe gikk galt ved opprettingen av nyhetsartikkelen. Vennligst kontakt administrator.
				</div>
				';
				return;
			}

			//create notification
			$notificationdata = array(
				"id" => mysql_insert_id(),
				"author" => $ud->data->id
			);

			addNotificationNews($notificationdata);

			echo '
			<div class="notification success">
				Artikkelen har blitt opprettet! Klikk <a href="index.php?p=news">her</a> for å se artikkelen på forsiden, eller klikk <a href="index.php?p=editnews">her</a> for å se oversikten over artikler.
			</div>
			';

		}
	}

	echo '
	<div id="editNewsList">
		<h2>Nyhetsoversikt</h2>
		<div id="tableKeeper">
			<table>
				<tr id="header">
				 	<td>Id</td>
				 	<td>Emne</td>
				 	<td>Dato</td>
				 	<td>Forfatter</td>
				 	<td>Valg</td>
				 	<td>Publisert</td>
				</tr> 
	';

	$sql = "SELECT `id`, `subject`, `author`, `creationdate`, `active` FROM `news` ORDER BY `id` DESC";
	$result = qq($sql);
	while ($data = mysql_fetch_object($result))
	{
		$authorSQL = "SELECT `username`, `userrank` FROM `users` WHERE `id`='" . $data->author . "'";
		$aRes = qq($authorSQL);
		$ad = mysql_fetch_object($aRes);
		if (!$aRes)
		{
			echo '
			Error: forfatteren eksisterer ikke.
			';
			return;
		}

		echo '
				<tr>
					<td><a href="index.php?p=editnewsarticle&id=' . $data->id . '">#' . $data->id . '</a></td>
					<td><a href="index.php?p=editnewsarticle&id=' . $data->id . '">' . $data->subject . '</a></td>
					<td>' . date('d-M-Y H:i:s', $data->creationdate) .  '</td>
					<td><a href="index.php?p=viewprofile&id=' . $data->author . '" style="text-decoration: none; border-bottom: 0px; "><span style="text-shadow: 1px 1px 1px #747474; color: ' . $accessLevelColors[$ad->userrank] . ';">' . $ad->username . '</span></a></td>
					<td><a href="index.php?p=editnewsarticle&id=' . $data->id . '"><img src="style/img/edit.png" border="0"></a> <a href="index.php?p=editnewsarticle&id=' . $data->id . '&delete"><img src="style/img/delete.png" border="0"></a></td>
					<td>' . ($data->active == 1 ? '<a href="index.php?p=editnewsarticle&id=' . $data->id . '&editshow&editshowto=0"><img src="style/img/true.png" border="0"></a></td> ' : '<a href="index.php?p=editnewsarticle&id=' . $data->id . '&editshow&editshowto=1"><img src="style/img/false.png" border="0"></a></td>') . '
				</tr>

		';
	}

	echo '
			</table>
		</div>
		<form action="" method="post">
			<input type="submit" name="createnewbutton" value="Opprett nyhetsartikkel" id="createnewbutton">
		</form>
	</div>
	';

?>