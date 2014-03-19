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
			Du har ikke tilgang til passordbytte for brukere.
		</div>
		';		
	} else 
	{
		if (empty($_GET["targetid"]))
		{
			echo '
			<div class="notification error">
				Ingen bruker valgt!
			</div>
			';
		} else
		{
			if (!is_numeric($_GET["targetid"]))
			{
				echo '
				<div class="notification error">
					Bruker-id\'en er ikke lesbar / i korrekt format. Kun heltall kan brukes.
				</div>
				';
			} else
			{
				if (!userExists(0, escapeString($_GET["targetid"])))
				{
					echo '
					<div class="notification error">
						Målbrukeren eksisterer ikke.
					</div>
					';
				} else 
				{
					$pd = new userData();
					$pd->getUserData(escapeString($_GET["targetid"]));

					if (isset($_POST["generateAvLink"]))
					{
						$sql = "DELETE FROM `refs` WHERE `targetuser`='" . escapeString($_GET["targetid"]) . "'";
						$result = qq($sql);
						if (!$result)
						{
							echo '
							<div class="notification error">
								Noe gikk galt ved sletting av tidligere aktiveringslenke. Vennligst kontakt administrator!
							</div>
							';
						} else 
						{
							$actlink = createSalt(30);
							$sql = "INSERT INTO `refs` (targetuser, userstring) VALUES ('" . escapeString($_GET["targetid"]) . "', '" . $actlink . "')";
							$result = qq($sql);
							if (!$result)
							{
								echo '
								<div class="notification error">
									Noe gikk feil med innsetting av ny aktiveringslenke. Vennligst kontakt administrator!
								</div>
								';
							} else 
							{
								$sql = "UPDATE `users` SET `passwordstatus`='1' WHERE `id`='" . $pd->data->id . "'";
								$result = qq($sql);
								if (!$result)
								{
									echo '
									<div class="notification error">
										Noe gikk feil ved redigering av passordstatus. Vennligst kontakt administrator!
									</div>
									';
								} else 
								{

									echo '
									<input type="hidden" id="actlink" value="' . constant('PATH') . 'activation.php?doactivate&actcode=' . $actlink . '">
									<div class="notification success">
										Aktiveringslenke generert! Klikk <a href="activation.php?doactivate&actcode=' . $actlink . '">denne linken</a> eller klikk <a href="#" id="copyactlink">her for å kopiere linken.</a>
									</div>
									';
								}
							}
						}
					}

					if (isset($_POST["closePassStatus"]))
					{
						$sql = "UPDATE `users` SET `passwordstatus`='0' WHERE `id`='" . $pd->data->id . "'";
						$result = qq($sql);
						if (!$result)
						{
							echo '
							<div class="notification error">
								Noe gikk feil ved bytte av passordstatus, vennligst kontakt administrator.
							</div>
							';
						} else 
						{
							$sql = "DELETE FROM `refs` WHERE `targetuser`='" . $pd->data->id . "'";
							$result = qq($sql);
							if (!$result)
							{
								echo '
								<div class="notification error">
									Noe gikk feil ved sletting av refs. Vennligst kontakt administrator.
								</div>
								';
							} else 
							{

								echo '
								<div class="notification success">
									Muligheten for å bytte passord har blitt <span style="font-weight: bold;">stengt</span>.
								</div>
								';
							}
						}
					}

					if (isset($_POST["generateAvLink"]) || isset($_POST["closePassStatus"]))
					{
						//possible changes to the data (caused by link or closepassStaus)
						$pd->getUserData($pd->data->id); 
					}

					echo '
					<div class="contentbox" id="openpassbox">
						<h3 style="text-transform: none;">Muligheten for å bytte passord</h3>
						Målbruker: <span style="font-weight: bold;">' . $pd->data->username . '</span>
						<p>Passordstatus: <span style="font-weight: bold; ' . ($pd->data->passwordstatus == 0 ? 'color: #970A0A;"> Stengt' : ($pd->data->passwordstatus == 1 ? 'color: #22970A;"> Åpen' : 'Ukjent')) . '</span></p>
						<form action="" method="post">
							<input type="submit" name="generateAvLink" value="Generer aktiveringslenke"> <input type="submit" name="closePassStatus" value="Steng muligheten for å bytte passord">
						</form>
					</div>
					';
				}
		 	}
		}
		
	}
?>