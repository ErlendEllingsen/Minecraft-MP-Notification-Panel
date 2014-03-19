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
	if (!isset($_GET["id"]))
	{
		echo '
		<div class="notification error">
		For å redigere en nyhetsartikkel så må du spesifisere id\'en.
		</div>
		';
		return;
	}

	$ArtId = escapeString($_GET["id"]);

	$sql = "SELECT `id` FROM `news` WHERE `id`='" . $ArtId . "' LIMIT 0,1";
	$result = qq($sql);
	$num_of_articles = mysql_num_rows($result);
	if ($num_of_articles <= 0)
	{
		echo '
		<div class="notification error">
		Den valgte artikkelen eksisterer ikke.
		</div>
		';
		return;
	}

	if (isset($_GET["delete"]))
	{


		if ($ud->data->userrank < $accessLevelValues['medeier'])
		{
			echo '
			<div class="notification error">
			For å slette artikler må du minst være <span style="font-weight: bold;">medeier</span> eller <span style="font-weight: bold;">eier</span>.
			</div>
			';
			return;
		}

		$sql = "DELETE FROM `news` WHERE `id`='" . $ArtId . "'";
		$result = qq($sql);
		if (!$result)
		{
			echo '
			<div class="notification error">
			Noe gikk galt ved slettingen av artikkelen. Vennligst kontakt administrator.
			</div>
			';
			return;
		}

		echo '
		<div class="notification success">
			Notatet har blitt slettet! <a href="index.php?p=editnews">Tilbake til nyhetsoversikten</a>
		</div>
		';

		return;
	}

	if (isset($_GET["editshow"]))
	{
		if (!isset($_GET["editshowto"]))
		{
			echo '
			<div class="notification error">
			Du har ikke valgt hva nyheten skal endres til.
			</div>
			';
			return;
		} 

		$editshowto = escapeString($_GET["editshowto"]);

		if (!is_numeric($editshowto))
		{
			echo '
			<div class="notification error">
			Feil endringsmodus valgt.
			</div>
			';
			return;
		}

		if ($editshowto > 1 || $editshowto < 0)
		{
			echo '
			<div class="notification error">
			Feil endringsmodus valgt.
			</div>
			';
			return;
		}

		$sql = "UPDATE `news` SET `active`='" . $editshowto . "' WHERE `id`='" . $ArtId . "'";
		$result = qq($sql);
		if (!$result)
		{
			echo '
			<div class="notification error">
			Noe gikk galt ved endringen av artikkelens synlighet.
			</div>
			';
			return;
		}

		$msg = $editshowto == 0 ? 'Artikkelen er ikke lenger synlig.' : 'Artikkelen er nå synliggjort!';
		echo '
		<div class="notification success">
		' . $msg . ' <a href="index.php?p=editnews">Tilbake til nyhetsoversikten.</a>
		</div>
		';
		return;
	}

	if (isset($_POST["updatearticle"]))
	{
		if (empty($_POST["subject"]) || empty($_POST["newscontent"]) || (!isset($_POST["publishwhen"])))
		{
			echo '
			<div class="notification error">
			Alle feltene må være fylt ut.
			</div>
			';
			return;
		}

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

		$sql = "UPDATE `news` SET `subject`='" . escapeString($_POST["subject"]) . "', `content`='" . escapeString($_POST["newscontent"]) . "', `active`='" . $publishwhen . "' WHERE `id`='" . $ArtId . "'";
		$result = qq($sql);
		if (!$result)
		{
			echo '
			<div class="notification error">
			Noe gikk galt ved oppdateringen av artikkelen. Vennligst kontakt administrator.
			</div>
			';
			return;
		}

		echo '
		<div class="notification success">
		Artikkelen har blitt oppdatert. Klikk <a href="index.php?p=news">her</a> for å se artikkelen på forsiden eller klikk <a href="index.php?p=editnews">her</a> for å komme til nyhetsoversikten.
		</div>
		';

		return;
	}

	$sql = "SELECT `subject`, `author`, `creationdate`, `content`, `active` FROM `news` WHERE `id`='" . $ArtId . "' LIMIT 0,1";
	$result = qq($sql);
	if (mysql_num_rows($result) <= 0)
	{
		echo '
		<div class="notification error">
		Noe gikk galt ved lastingen av artikkelinformasjon. Vennligst kontakt administrator.
		</div>
		';
		return;
	}
	$data = mysql_fetch_object($result);

	$publishedInput = $data->active == 1 ? '<input type="radio" name="publishwhen" value="1" checked="checked"> Ja <input type="radio" name="publishwhen" value="0"> Nei.' : '<input type="radio" name="publishwhen" value="1"> Ja <input type="radio" name="publishwhen" value="0" checked="checked"> Nei.';
	echo '
	<form action="" method="post">
		<h2 id="newsArticleTitle">Rediger artikkel</h2>
		<p class="newArticleAddin">Emne: <input type="text" name="subject" value="' . $data->subject . '"></p>
		<p class="newArticleAddin">Forfatter: <span style="text-shadow: 1px 1px 1px #747474; color: ' . $accessLevelColors[$ud->data->userrank] . ';">' . $ud->data->username . '</span></p>
		<p class="newArticleAddin">Publisert: ' . $publishedInput . '</p>
		<p class="newArticleAddin">Opprettet: ' . date('d-M-Y H:i', $data->creationdate) . '</p>
		<h3 id="contentTitle">Innhold</h3>
		<textarea class="ckeditor" name="newscontent">' . $data->content . '</textarea>
		<input type="submit" name="updatearticle" id="updatearticle" value="Lagre artikkelen">
	</form>
	';

?>