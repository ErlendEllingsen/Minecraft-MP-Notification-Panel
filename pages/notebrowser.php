<?php
	/*
		All code in this script (including ALL files inside this directory) is property of Erlend Ellingsen.
		Script written for use on 'XioCo' and 'XioCo' ONLY. 
		Script written June 2012.

		Do not copy any code without permission from Erlend Ellingsen.
	*/
	include(file_exists("../inc/reqLogin.php") ? "../inc/reqLogin.php" : "inc/reqLogin.php");
	/*
		LOCAL SCRIPT VARS
	*/
	$notebrowser_vars = array(
		'pre_user' => ''
	);

	if ($ud->data->userrank <= $accessLevelValues['sponsor'])
	{
		echo '
		<div class="notification error">
			Du har ikke tilgang til brukernotater.
		</div>';
	} else 
	{
		if (isset($_GET["mode"]))
		{
			if ($_GET["mode"] == "listall")
			{
				if (!is_numeric($_GET["target"]))
				{
					echo '
					<div class="notification error">
						En gyldig profil-id ble ikke spesifisert.
					</div>
					';
				} else 
				{
					$targetUsername = getUsername(escapeString($_GET["target"]));
					if (!$targetUsername)
					{
						echo '
						<div class="notification error">
							Den refererte brukeren eksisterer ikke.
						</div>
						';
					} else { $notebrowser_vars['pre_user'] = $targetUsername; }
				}
			}
		}

		echo '
		<div class="contentbox">
			<h3>Notatleser / Notatsøker</h3>
			<form action="" method="post">
				<table id="searchtable">
					<tr>
						<td>
						Målprofil: <input type="text" name="targetusername" value="' . $notebrowser_vars['pre_user'] . '">
						</td>
						<td>
							<input type="checkbox" name="search_timespan">Søk kun etter notater innenfor spesifisert tidsrom
						</td>
					</tr>
					<tr>
						<td><input type="checkbox" name="search_filternotetypes">Søk kun etter følgende notattyper:</td>
						<td>Dato start: <input type="text" name="timespan_start" id="tsStart"> (format: dd/mm/yy med \'/\')</td>
					</tr>
					<tr>
						<td><input type="checkbox" name="notetype_note" style="background: rgba(157, 208, 255, 1); color: #000;">Notat <input type="checkbox" name="notetype_kick" style="background: rgba(80, 80, 80, 1); color: #FFF;">Kick <input type="checkbox" name="notetype_warning" style="background-color: #FFE59D; color: #000;">Advarsel <input type="checkbox" name="notetype_ban" style="background: #F93737; color: #000;">Ban <input type="checkbox" name="notetype_tempban" style="background: #FB7777; color: #000;">Tempban <input type="checkbox" name="notetype_unban" style="background: #ACF6B6; color: #000;">Unban</td>  
						<td>Dato stopp: <input type="text" name="timespan_stop" id="tsStop"> (format: dd/mm/yy med \'/\')</td>
					</tr>
					<tr>
						<td><input type="submit" name="dosearch" value="Søk!"></td>
						<td><input type="checkbox" name="includeword_check">Søk kun etter notater som inneholder: <input type="text" name="includeword">
					</tr>
				</table>
			</form>
		</div>
		';

		if (isset($_POST["dosearch"]))
		{
			if (empty($_POST["targetusername"]))
			{
				echo '
				<div class="notification error">
					Du må skrive inn et navn i målprofil-feltet!
				</div>
				';
			} else 
			{
				if (!userExists(1, escapeString($_POST["targetusername"])))
				{
					echo '
					<div class="notification error">
						Brukeren eksisterer ikke, husk at her fungerer kun <span style="font-weight: bold;">eksakte</span> brukernavn. (De er IKKE case-sensitive)
					</div>
					';
				} else 
				{
					$targetID = getID(escapeString($_POST["targetusername"]));
					$sql = "SELECT * FROM `notes` WHERE `targetid`='" . $targetID . "'";

					$killSearch = false; $killMessage = "";

					if (isset($_POST["search_timespan"])){
						if (empty($_POST["timespan_start"]) || empty($_POST["timespan_stop"]))
						{
							$killSearch = true;
							$killMessage = "Vennligst sjekk start og stoppdatoen for søket. Sørg for at disse er i riktig format også.";
						} else 
						{
							$timespan_dates = array('start' => escapeString($_POST["timespan_start"]), 'stop' => escapeString($_POST["timespan_stop"]));
							
							foreach ($timespan_dates as $k => $v)
							{
								$day = substr($v, 0, 2); $month = substr($v, 3, 2); $year = substr($v, 6, 4);
								if (!(is_numeric($day) && is_numeric($month) && is_numeric($year)))
								{
									$killSearch = true;
									$killMessage = 'Enten startdatoen eller stoppdatoen i søkeperioden er i feil format. Gyldig format: <span style="font-weight: bold;">dd/mm/yy</span>, HUSK: \'/\'';
								}
								else 
								{

									$timespan_dates[$k] = mktime(0, 0, 0, $month, $day, $year);
								}
							}

							if (!$killSearch)
							{
								if($timespan_dates['start'] > $timespan_dates['stop'])
								{
									echo '
									<div class="notification error">
										Startdatoen kan ikke være <span class="font-weight: bold;">etter</span> stoppdatoen.
									</div>
									';
								} else 
								{
									if ($timespan_dates['start'] == $timespan_dates['stop'])
									{
										$sql .= " AND `creationdate` >= " . ($timespan_dates['start'] - 86400) . " AND `creationdate` <= " . ($timespan_dates['stop'] + 86400) ;
									}
									else 
									{
										$sql .= " AND `creationdate` >= " . $timespan_dates['start'] . " AND `creationdate` <= " . $timespan_dates['stop'];
									}
								}
								
							}
						}
					}

					if (isset($_POST["search_filternotetypes"]))
					{
						if(!(isset($_POST["notetype_note"]) || isset($_POST["notetype_kick"]) || isset($_POST["notetype_warning"]) || isset($_POST["notetype_ban"]) || isset($_POST["notetype_tempban"]) || isset($_POST["notetype_unban"])))
						{
							echo '
							<div class="notification error">
								Ingen av notattypene ble valgt. Kan ikke filtrere ut "ingen" notattyper.
							</div>
							';
						} else 
						{
							$sql .= " AND (";

							if (isset($_POST["notetype_note"]))
							{ 
								$sql .= "`type`='0'";
							}
							if (isset($_POST["notetype_kick"]))
							{
								if (isset($_POST["notetype_note"]))
								{
									$sql .= " OR `type`='1'";
								} else 
								{
									$sql .= "`type`='1'";
								}
							}
							if (isset($_POST["notetype_ban"]))
							{
								if (isset($_POST["notetype_note"]))
								{
									$sql .= " OR `type`='3'";
								} else 
								{
									$sql .= "`type`='3'";
								}
							}
							if (isset($_POST["notetype_warning"]))
							{
								if (isset($_POST["notetype_note"]))
								{
									$sql .= " OR `type`='2'";
								} else 
								{
									$sql .= "`type`='2'";
								}
							}
							if (isset($_POST["notetype_tempban"]))
							{
								if (isset($_POST["notetype_note"]))
								{
									$sql .= " OR `type`='4'";
								} else 
								{
									$sql .= "`type`='4'";
								}
							}
							if (isset($_POST["notetype_unban"]))
							{
								if (isset($_POST["notetype_note"]))
								{
									$sql .= " OR `type`='5'";
								} else 
								{
									$sql .= "`type`='5'";
								}
							}

							$sql .= " )";
						}
					}

					if (isset($_POST["includeword_check"]))
					{
						if (empty($_POST["includeword"]))
						{
							echo '
							<div class="notification error">
								Når du velger å søke etter et ord / en setning, så må du skrive inn noe i <span style="font-weight: bold;">feltet for ordsøk/setningssøk</span>.
							</div>
							';
						} else 
						{
							$sql .= " AND (`subject` LIKE '%" . escapeString($_POST["includeword"]) . "%' OR `descr` LIKE '%" . escapeString($_POST["includeword"]) . "%')";
						}
					}

					$sql .= " ORDER BY `id` DESC";


					if ($killSearch)
					{
						echo '
						<div class="notification error">
							' . $killMessage . '
						</div>
						';
					} else 
					{
						echo '
						<div class="notification">
							Du ser nå på notatene som handler om profilen <span style="font-weight: bold;"><a href="index.php?p=viewprofile&id=' . $targetID . '">' . escapeString($_POST["targetusername"]) . '</a>.</span>
						</div>
						<span id="notebrowser_colortips">
							Fargekoder: <div class="colorBox" style="background-color: #9DD0FF;"></div> Notat <div class="colorBox" style="background-color: #505050;"></div> Kick <div class="colorBox" style="background-color: #FFE59D;"></div> Advarsel <div class="colorBox" style="background-color: #FA5858;"></div> Ban <div class="colorBox" style="background-color: #FF9D9D;"></div> Tempban <div class="colorBox" style="background-color: #60EE73;"></div> Unban<br />
						</span>
						';

						$result = qq($sql);
						$num_of_results = mysql_num_rows($result);
						while ($data = mysql_fetch_object($result))
						{
							$authorData = new userData();
							$authorData->getUserData($data->author);

							echo '
							<div class="contentbox noteblock note_' . $noteTypes[$data->type] . '">
								<p>
									<a class="notetitle" href="index.php?p=singlenotereader&noteid=' . $data->id . '">' . $data->subject . '</a>
									<span class="authorline">' . date('d/m/y - H:i:s', $data->creationdate) . ' ført av <a href="index.php?p=viewprofile&id=' . $authorData->data->id . '"><span style="color: ' . $accessLevelColors[$authorData->data->userrank] . '; text-shadow: 1px 1px 1px #000;">' . ucfirst($authorData->data->username) . '</span></a><br /><a href="index.php?p=editnote&m=edit&noteid=' . $data->id . '" border="0"><img src="style/img/edit.png"></a> <a href="index.php?p=editnote&m=delete&noteid=' . $data->id . '" border="0"><img src="style/img/delete.png"></a></span>
									&ldquo;' . BBCode::parse($data->descr) . '&rdquo;
								</p>
							</div>
							';
						}
					}

				}
				
			}
		}
	}
?>