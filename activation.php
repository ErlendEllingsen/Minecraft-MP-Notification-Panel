<?php
	include "config.php";
	include "functions.php";
	include "inc/userData.php";

	echo '
	<html>
		<head>
			<meta name="author" content="Erlend Ellingsen" />
			<meta http-equiv="content-type" content="text/html;charset=Windows-1252" />
			<link href="style/css/activation.css" rel="stylesheet" type="text/css">
			<title>XioCo staffpanel</title>
		</head>
		<body>
			<div id="contentcontainer">
	';
	if (!isset($_GET["doactivate"]))
	{
		echo '
		<div class="notification error">
			Ingen modus valgt. Kom du hit med en feil? <a href="index.php">Til innlogging</a>
		</div>
		';
	} else 
	{
		if (!isset($_GET["actcode"]))
		{
			echo '
			<div class="notification error">
				Oops! Her mangler det en aktiveringskode. Vennligst be om en ny aktiveringslink fra administrator.
			</div>
			';
		} else 
		{
			$sql = "SELECT * FROM `refs` WHERE `userstring`='" . escapeString($_GET["actcode"]) . "' LIMIT 0,1";
			$result = qq($sql);
			if (!$result)
			{
				echo '
				<div class="notification error">
					Noe gikk feil ved uthenting av nøkkeldata. Vennligst kontakt administrator.
				</div>
				';
			} else 
			{
				$num_of_refs = mysql_num_rows($result);
				if ($num_of_refs <= 0)
				{
					echo '
					<div class="notification error">
						Aktiveringskoden matcher ingen brukere. Er koden oppbrukt? Er det skjedd en feil? kontakt administrator om du tror dette er feil.
					</div>
					';
				} else 
				{
					$data = mysql_fetch_object($result);
					$userID = $data->targetuser;

					if (!userExists(0, $userID))
					{
						echo '
						<div class="notification error">
							Aktiveringskoden er korrekt. Men brukeren koblet til koden eksisterer ikke. Vennligst kontakt administrator.
						</div>
						';
					} else 
					{
						$userUD = new userData();
						$userUD->getUserData($userID);



						if ($userUD->data->passwordstatus == 0)
						{
							echo '
							<div class="notification error">
								Brukeren som ble referert til, står oppført som aktivert og ikke åpen for registrering. Vennligst kontakt administrator.
							</div>
							';
						} else 
						{
							if (isset($_POST["activateUser"]))
							{
								if (empty($_POST["password"]) || empty($_POST["confirmpassword"]))
								{
									echo '
									<div class="notification error">
										Ingen av feltene kan være tomme.
									</div>
									';
								} else 
								{
									if ($_POST["password"] != $_POST["confirmpassword"])
									{
										echo '
										<div class="notification error">
										 	Passordene er ikke like!
										</div>
										';
									} else 
									{
										$encryptedData = encryptRawPass(escapeString($_POST["password"]));

										$sql = "UPDATE `users` SET `salt`='" . $encryptedData['salt'] . "', `password`='" . $encryptedData['pass']  . "', `passwordstatus`='0' WHERE `id`='" . $userUD->data->id . "'";
										$result = qq($sql);
										if (!$result)
										{
											echo '
											<div class="notification error">
												Noe gikk feil ved aktiveringen av brukeren. Vennligst kontakt administrator.
											</div>
											';
										} else 
										{
											$sql = "DELETE FROM `refs` WHERE `targetuser`='" . $userUD->data->id . "'";
											$result = qq($sql);
											if (!$result)
											{
												echo '
												<div class="notification error">
													En prosess i slettingen av aktiveringskoden slo feil. Brukeren din er aktivert, men aktiveringskoden er ikke fjernet. Vennligst rapport til administrator.
												</div>
												';
											} else 
											{
												echo '
												<div class="notification success">
													Brukeren <span style="font-weight: bold;">' . $userUD->data->username . '</span> er nå aktivert! Klikk <a href="login.php">her</a> for å logge inn.
												</div>
												';
											}
										}
									}
								}
							} else 
							{

								echo '
								<h3>Aktivering av din nye brukerkonto</h3>
								<form action="" method="post">
									<table id="regTable">
										<tr>
											<td>Brukernavn:</td>
											<td><span style="font-weight: bold;">' . $userUD->data->username . '</span></td>
										</tr>
										<tr>
											<td>Tildelt rangering:</td>
											<td><span style="text-shadow: 1px 1px 1px #000; color: ' . $accessLevelColors[$userUD->data->userrank] . '">' . $accessLevels[$userUD->data->userrank] . '</td>
										</tr>
										<tr></tr>
										<tr>
											<td>Passord:</td>
											<td><input type="password" name="password"></td>
										</tr>
										<tr>
											<td>Repeter passord:</td>
											<td><input type="password" name="confirmpassword"></td>
										</tr>
										<tr>
											<td colspan="2"><input type="submit" name="activateUser" value="Aktiver konto!"></td>
										</tr>
									</table>
								</form>
								';
							}
						}
					}
				}
			}

			
		}	

	}
				

	echo '
			</div>
		</body>
	</html>
	';

	mysql_close();
?>