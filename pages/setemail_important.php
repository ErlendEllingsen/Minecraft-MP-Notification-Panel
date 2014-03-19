<?php
	include(file_exists("../inc/reqLogin.php") ? "../inc/reqLogin.php" : "inc/reqLogin.php");

	$reqchars = array(
		"@", "."
	);

	if (isset($_POST["updateemail"]))
	{
		if (empty($_POST["newepost"]))
		{
			echo '
			<div class="notification error">
				Feltet kan ikke være tomt.
			</div>
			';
			return;
		}

		for ($i = 0; $i < count($reqchars); $i++)
		{
			if (strstr($_POST["newepost"], $reqchars[$i]) == false)
			{
				echo '
				<div class="notification error">
					E-posten er ikke i korrekt format.
				</div>
				';
				return;
			}
		}

		$newepost = strip_tags(escapeString($_POST["newepost"]));
		$sql = "SELECT `id` FROM `users` WHERE `email`='" . $newepost . "'";
		$result = qq($sql);
		$num_of_users = mysql_num_rows($result);
		if ($num_of_users > 0)
		{
			echo '
			<div class="notification error">
				E-post addressen er allerede i bruk!
			</div>
			';
			return;
		}

		$sql = "UPDATE `users` SET `email`='" . $newepost . "' WHERE `id`='" . $ud->data->id . "'";
		$result = qq($sql);
		if (!$result)
		{
			echo '
			<div class="notification error">
				Fikk ikke oppdatert e-posten.
			</div>
			';
			return;
		}

		echo '
			<div class="notification success">
				E-post addressen er oppdatert!
			</div>
			';
		return;

	}


 	if (empty($ud->data->email))
 	{
		echo '
		<div class="notification error">
			Det ser ikke ut som du har noen e-post registrert, for å fortsette å bruke stabpanelet må du (grunnet sikkhertsmessige årsaker) <span style="font-weight: bold;">oppdatere e-post addressen</span>.
		</div>
		';
	}

	echo '

	<div class="contentbox">
		<h3>Oppdater e-post addresse</h3>
		<form id="updateemail" action="" method="post">
			E-post addresse: <input type="text" name="newepost" value="' . $ud->data->email . '">
			<input type="submit" name="updateemail" value="Oppdater e-post">
		</form>
	</div>
	';
?>