<?php
	include(file_exists("../inc/reqLogin.php") ? "../inc/reqLogin.php" : "inc/reqLogin.php");

	echo '
	<div class="notification">
		Denne funksjonen er under utvikling, og kun begrenset innhold er tilgjengelig.
	</div>
	';

	include "setemail_important.php";

	if ($ud->data->userrank >= $accessLevelValues['eier'])
	{
		if (isset($_POST["resetnotes"]))
		{
			$sql = "TRUNCATE `notes`";
			$result = qq($sql);
			if (!$result)
			{
				echo '
				<div class="notification error">
					Feil ved nullstilling.
				</div>
				';
				return;
			}

			echo '
				<div class="notification success">
					Nullstilling har blitt fullført.
				</div>
			';
		}

		if (isset($_POST["resetnotifications"]))
		{
			$sql = "TRUNCATE `notifications`";
			$result = qq($sql);
			if (!$result)
			{
				echo '
				<div class="notification error">
					Feil ved nullstilling.
				</div>
				';
				return;
			}

			$sql = "UPDATE `users` SET `unreadnotifications`='0'";
			$result = qq($sql);
			if (!$result)
			{
				echo '
				<div class="notification error">
					Feil ved nullstilling.
				</div>
				';
				return;
			}

			echo '
				<div class="notification success">
					Nullstilling har blitt fullført.
				</div>
			';
		}

		if (isset($_POST["resetcomments"]))
		{
			$sql = "TRUNCATE `comments`";
			$result = qq($sql);
			if (!$result)
			{
				echo '
				<div class="notification error">
					Feil ved nullstilling.
				</div>
				';
				return;
			}

			echo '
				<div class="notification success">
					Nullstilling har blitt fullført.
				</div>
			';
		}

		if (isset($_POST["resetarticles"]))
		{
			$sql = "TRUNCATE `news`";
			$result = qq($sql);
			if (!$result)
			{
				echo '
				<div class="notification error">
					Feil ved nullstilling.
				</div>
				';
				return;
			}

			echo '
				<div class="notification success">
					Nullstilling har blitt fullført.
				</div>
			';
		}

		echo '
		<div class="contentbox" style="margin-top: 10px;">
			<h3>Systeminstllinger</h3>
			<form action="" method="post" style="padding: 5px;">
				<input type="submit" name="resetnotes" value="Nullstill notater">
				<input type="submit" name="resetnotifications" value="Nullstill notifikasjoner">
				<input type="submit" name="resetcomments" value="Nullstill notatkommentarer">
				<input type="submit" name="resetarticles" value="Nullstill artikler">
			</form>
		</div>
		';
	}
?>