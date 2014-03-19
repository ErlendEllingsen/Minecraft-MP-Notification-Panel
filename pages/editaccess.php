<?php
	/*
		All code in this script (including ALL files inside this directory) is property of Erlend Ellingsen.
		Script written for use on 'XioCo' and 'XioCo' ONLY. 
		Script written June 2012.

		Do not copy any code without permission from Erlend Ellingsen.
	*/

	include(file_exists("../inc/reqLogin.php") ? "../inc/reqLogin.php" : "inc/reqLogin.php");
	if ($ud->data->userrank < $accessLevelValues['eier'])
	{
		echo '
		<div class="notification error">
			Du har ikke tilgang til denne siden.
		</div>
		';
		return;
	} else 
	{
		if (empty($_GET["targetid"]))
		{
			echo '
		<div class="notification error">
			Ingen bruker valgt.
		</div>
			';
		} else 
		{
			if (!is_numeric($_GET["targetid"]))
			{
				echo '
				<div class="notification error">
					Ingen gyldig brukerid valgt.
				</div>
				';
			} else 
			{
				if (!userExists(0, escapeString($_GET["targetid"])))
				{
					echo '
					<div class="notification error">
						Ingen bruker/profil ved den valgte id\'en.
					</div>
					';
				} else 
				{
					$pdata = new userData();
					$pdata->getUserData(escapeString($_GET["targetid"]));

					if ($pdata->data->userrank > $ud->data->userrank)
					{
						echo '
						<div class="notification error">
							Du kan ikke redigere tilgangen til en som har høyere tilgangsstatus enn deg.
						</div>
						';
					} else 
					{
						if (isset($_POST["editaccesslevel"]))
						{
							if (!isset($_POST["newaccesslevel"]))
							{
								echo '
								<div class="notification error">
									Du må velge et tilgangsnivå.
								</div>
								';
							} else 
							{
								if (!is_numeric($_POST["newaccesslevel"]))
								{
									echo '
									<div class="notification error">
										Brukertilgangen må være et gyldig tall.
									</div>
									';
								} else 
								{
									if ($_POST["newaccesslevel"] > (count($accessLevels) - 1) || $_POST["newaccesslevel"] < 0)
									{
										echo '
									 	<div class="notification error">
											Ugyldig tilgangsnivå
										</div>
										';
									} else
									{
										if ($_POST["newaccesslevel"] >= $ud->data->userrank)
										{
											echo '
											<div class="notification error">
												Du har <span style="font-weight: bold;">ikke</span> muligheten til å gi andre profiler samme eller høyere rank enn deg.
											</div>
											';
										} else 
										{
											$sql = "UPDATE `users` SET `userrank`='" . escapeString($_POST["newaccesslevel"]) . "' WHERE `id`='" . $pdata->data->id . "'";
											$result = qq($sql);
											if (!$result)
											{
												echo '
												<div class="notification error">
													Noe gikk galt ved opdpateringen av tilgangsnivået. Vennligst kontakt administrator.
												</div>
												';
											} else 
											{
												echo '
												<div class="notification success">
													Brukertilgangen har blitt endret! Klikk <a href="index.php?p=viewprofile&id=' . $pdata->data->id . '">her</a> for å se den oppdaterte brukerprofilen.
												</div>
												';
											}
										}
									}
								}
							}
						}

						echo '
						<form action="" method="post">
							<div class="contentbox" id="editaccessbox">
								<h3>Rediger ' . $pdata->data->username . '\'s tilgang</h3>
								<p>Nåværende brukertilgang: <span style="text-shadow: 1px 1px 1px #000; color: ' . $accessLevelColors[$pdata->data->userrank] . ';">' . $accessLevels[$pdata->data->userrank] . '</span></p>
								Ny brukertilgang: 
								<select name="newaccesslevel">
							';
								foreach ($accessLevels as $ck => $cv)
								{
									if ($ck < $ud->data->userrank)
									{
										echo '
										<option value="' . $ck . '">' . $cv . '</option>
										';
									}
								}
							echo '
								</select>
							</div>
							<div class="contentbox savebox">
								<input type="submit" name="editaccesslevel" value="Rediger tilgangsnivå">
							</div>
						</form>
						';
					}
				}
			}
		}
	}	
?>