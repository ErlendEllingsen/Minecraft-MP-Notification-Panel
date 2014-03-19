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
	} else 
	{
		if (!isset($_GET["id"]))
		{
			echo '
			<div class="notification error">
				Ingen profil-id spesifisert!
			</div>
			';
		} else
		{
			if (!is_numeric($_GET["id"]))
			{
				echo '
				<div class="notification error">
					Bruker id-en må være et heltall!
				</div>
				';
			} else 
			{
				if (!userExists(0, escapeString($_GET["id"])))
				{
					echo '
				 	<div class="notification error">
				 		Profilen eksisterer ikke!
				 	</div>
					';
				} 
				else 
				{
					$pdata = new userData();
					$profileResult = $pdata->getUserData(escapeString($_GET["id"]));
				
					echo '
					<div class="contentbox" style="background-image: url(\'http://s3.amazonaws.com/MinecraftSkins/' . $pdata->data->username . '.png\'); background-repeat: no-repeat; background-position: right bottom;">
						<h3 class="notransform">' . $pdata->data->username . ' (' . $accessLevels[$pdata->data->userrank] . ')</h3>
						<span id="profileUserDesc">Profilen ble oprettet den <span style="font-weight: bold;">' . date('d/m/y - H:i:s', $pdata->data->creationdate) . '</span></span>
					</div>
					';

					if ($pdata->data->userrank > $accessLevelValues['sponsor'])
					{
						$totNotes = array(0, 0, 0, 0, 0, 0);
						$sql = "SELECT `id`, `type` FROM `notes` WHERE `author`='" . $pdata->data->id . "'";
						$result = qq($sql);
						$num_of_notes = mysql_num_rows($result);
						while ($data = mysql_fetch_object($result))
						{
							$totNotes[$data->type]++;
						}

						echo '
						<div class="contentbox" style="margin-top: 10px;">
							<h3>Stabmedlem info</h3>
							<ul>
								<li><span style="font-weight: bold;">Antall notater skrevet: </span>' . number_format($num_of_notes) . '</li>

								<li><span style="font-weight: bold;">Antall nøytrale notater: </span>' . number_format($totNotes[0]) . '</li>
								<li><span style="font-weight: bold;">Antall kicks: </span>' . number_format($totNotes[1]) . '</li>
								<li><span style="font-weight: bold;">Antall advarsler: </span>' . number_format($totNotes[2]) . '</li>
								<li><span style="font-weight: bold;">Antall bans: </span> ' . number_format($totNotes[3]) . '</li>
								<li><span style="font-weight: bold;">Antall tempbans: </span> ' . number_format($totNotes[4]) . '</li>
								<li><span style="font-weight: bold;">Antall unbans: </span> ' . number_format($totNotes[5]) . '</li>
							</ul>
						</div>
						';

					}

					echo '
					<div id="profileNoteListContainer">
						<h3 id="profileNoteListTitle">Notater (siste 15)</h3>
						<p id="profileNoteListDesc">
						Fargekoder: <div class="colorBox" style="background-color: #9DD0FF;"></div> Notat <div class="colorBox" style="background-color: #505050;"></div> Kick <div class="colorBox" style="background-color: #FFE59D;"></div> Advarsel <div class="colorBox" style="background-color: #FA5858;"></div> Ban <div class="colorBox" style="background-color: #FF9D9D;"></div> Tempban <div class="colorBox" style="background-color: #60EE73;"></div> Unban</p> 
						<ul id="profileNoteList">
					';

					$sql = "SELECT * FROM `notes` WHERE `targetid`='" . $pdata->data->id . "' ORDER BY `id` DESC LIMIT 0,15";
					$result = qq($sql);
					while ($data = mysql_fetch_object($result))
					{
						$notetype = (isset($noteTypes[$data->type]) ? $noteTypes[$data->type] : 'invalid notetype');
						if ($notetype == 'invalid notetype')
						{
							echo '
							<li class="note">
								Error: feil type notat.
							</li>
							';
						} else 
						{
							$authordata = new userData();
							$authordata->getUserData(escapeString($data->author));

							echo '
								<li class="' . $notetype . '" onClick="navigate(\'' . constant('PATH') . 'index.php?p=singlenotereader&noteid=' . $data->id . '\', 500);">
									<span class="noteTitle"><a href="index.php?p=singlenotereader&noteid=' . $data->id . '">' . $data->subject . '</a></span>
									<span class="noteDate">' . date('d/m/y - H:i:s', $data->creationdate) . ' ført av <a href="index.php?p=viewprofile&id=' . $authordata->data->id . '"><span style="font-weight: bold; color: ' . $accessLevelColors[$authordata->data->userrank] . '; text-shadow: 1px 1px 1px #000;">' . $authordata->data->username . '</span></span></a>
								</li>
							';
						}
					}

					echo '
						</ul>
						<p id="profileMoreNotesLink">
							<a href="index.php?p=notebrowser&mode=listall&target=' . $pdata->data->id . '">Se alle notater</a>
						</p>
					</div>
					<div id="profileAdministrativeOptions">
						<h3>Administrative handlinger</h3>
						<ul id="adminoptions">
							<li><a href="index.php?p=newnote&preid=' . $pdata->data->id . '">Skriv et nytt notat</a></li>
					';

					if ($ud->data->userrank >= 5)
					{
						echo '
							<li><a href="index.php?p=editaccess&targetid=' . $pdata->data->id . '"><span style="color: #BB0808;">Rediger brukertilgang</span></a></li>
							<li><a href="index.php?p=openpassword&targetid=' . $pdata->data->id . '"><span style="color: #BB0808;">Åpne for passordbytte</span></a></li>
						';

					}

					echo '
						</ul>
					</div>
					<div style="clear: both;"></div>
					';

					if ($ud->data->userrank >= $accessLevelValues['eier'])
					{
						echo '
						<div class="contentbox" style="margin-top: 15px;">
							<h3>Kontaktinfo</h3>
							<span style="font-weight: bold;">E-post:</span> ' . $pdata->data->email . '
						</div>
						';
					}
				}
			}
		}
	}
?>