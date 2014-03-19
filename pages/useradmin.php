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
		$searchdatamode = (isset($_GET["modem"]) ? $_GET : $_POST);
		if (isset($searchdatamode["search"]))
		{
			if (!isset($searchdatamode["targetuser"]))
			{
				echo '
				<div class="notification error">
					Du må søke etter et brukernavn!
				</div>
				';
			} else 
			{
				
				$showWhat = calculationResults(50);

				$sql = isset($searchdatamode["exactname"]) ? "SELECT * FROM `users` WHERE `username`='" . escapeString($searchdatamode["targetuser"]) . "'" : "SELECT * FROM `users` WHERE `username` LIKE '%" . escapeString($searchdatamode["targetuser"]) . "%'";
				$sql .= isset($searchdatamode["filter_rank"]) ? " AND `userrank`='" . escapeString($searchdatamode["filter_rank_value"]) . "'" : "";

				$gettotalnum_sql = $sql;
				
				$sql .= "ORDER BY `id` ASC LIMIT " . $showWhat['min'] . "," . $showWhat['max'];

				$result_totalnum = qq($gettotalnum_sql);
				$total_num_of_users = mysql_num_rows($result_totalnum);

				$result = qq($sql);
				$num_of_users = mysql_num_rows($result);
				if ($num_of_users <= 0)
				{
					echo '
					<div class="notification">
						Eksisterer ingen profiler ved navnet <span style="font-weight: bold;">' . $searchdatamode["targetuser"] . '</span>' . ', vil du oprette en profil for denne brukeren? <a href="index.php?p=useradmin&createprofile&profilename=' . escapeString($searchdatamode["targetuser"]) . '">Ja</a> <a href="index.php?p=useradmin">Nei</a>
					</div>
					';
				} else 
				{
					echo '
					<div class="contentbox">
						<h3>Søkeresultater (' . $total_num_of_users . ' brukere, viser 50 per side)</h3>
						<table id="userlist">
							<tr class="tabletoprow">
								<td>Id</td>
								<td>Brukernavn</td>
								<td>Bruker-rank</td>
								<td>Antall notater</td>
							</tr>
					';

					$colorcount = 0;
					while ($data = mysql_fetch_object($result))
					{
						$colorcount++;
						$color = $colorcount % 2 === 0 ? '#D6D7D7' : '#FFF';//'#BFBFBF';

						//skaff antall notater
						$sqln = "SELECT `id` FROM `notes` WHERE `targetid`='" . $data->id . "'";
						$resultn = qq($sqln);
						$num_of_notes = mysql_num_rows($resultn);

						echo '
							<tr style="background-color: ' . $color . ';">
								<td>' . $data->id . '</td>
								<td><a href="index.php?p=viewprofile&id=' . $data->id . '">' . $data->username . '</a></td>
								<td><span style="text-shadow: 1px 1px 1px #747474; color: ' . $accessLevelColors[$data->userrank] . ';">' . $accessLevels[$data->userrank] . '</span></td>
								<td>' . $num_of_notes . '</td>
							</tr>
						';
					}

					echo '
						</table>
					';

					$argbuilder = "&modem";
					foreach ($searchdatamode as $k=>$v)
					{
						if ((!strstr($k, "pagination")) || (!strstr($k, "cp")))
						{
							$argbuilder = ($argbuilder === "" ? ($k . '=' . $v) : $argbuilder . '&' . ($k . '=' . $v));
						}
					}

					echo '<br />';
					generatePagination($total_num_of_users, 50, 'useradmin' . $argbuilder);

					echo '
					</div>';
				}
			}
		} else if (isset($_GET["createprofile"]) && (!isset($_POST["createprofile_creation"])))
		{
			echo '
			<div class="contentbox">
				<h3>Opprett profil</h3>
				<form action="" method="post">
					<table id="profilecreationtable">
						<tr>
							<td>Brukernavn:</td>
							<td><input type="text" name="targetusername" value="' . (!empty($_GET["profilename"]) ? $_GET["profilename"] : "") . '"></td>
						</tr>
						<tr>
							<td>Rank:</td>
							<td>
								<select name="targetrank">
									<option value="0">Spiller</option>
									<option value="1">Sponsor</option>
								</select>
								(kan redigeres senere)
							</td>
						</tr>
						<tr>
							<td><input type="submit" name="createprofile_creation" value="Opprett profil" id="createprofilebutton"></td>
						</tr>
					</table>
				</form>
			</div>
			';
		} else if(isset($_POST["createprofile_creation"]))
		{
			if (empty($_POST["targetusername"]) || (!isset($_POST["targetrank"])))
			{
				echo '
				<div class="notification error">
					Du kan ikke opprette en profil uten navn eller rank.
				</div>
				';
			} else 
			{
				$sql = "SELECT `id` FROM `users` WHERE `username`='" . escapeString($_POST["targetusername"]) . "' LIMIT 0,1";
				$result = qq($sql);
				$num_of_users = mysql_num_rows($result);
				if ($num_of_users > 0)
				{
					echo '
					<div class="notification error">
						Det eksisterer allerede en profil med dette navnet.
					</div>
					';
				} else 
				{
					$sql = "INSERT INTO `users` (username, userrank, creationdate) VALUES ('" . escapeString($_POST["targetusername"]) . "', '" . escapeString($_POST["targetrank"]) . "', '" . time() . "')";
					$result = qq($sql);
					if (!$result)
					{
						echo '
						<div class="notification error">
							Noe gikk feil ved opprettingen av profilen, vennligst kontakt 
						</div>
						';
					}


					echo '
					<div class="notification success">
						Profil opprettet! Klikk <a href="index.php?p=viewprofile&id=' . mysql_insert_id() . '">her</a> for å besøke profilen.
					</div>
					';					
				}

			}
		}
		else 
		{

			echo '
			<div class="contentbox">
				<h3>Brukersøk</h3>
					<form action="" method="post">
						<table id="searchtable">
							<tr>
								<td>
									Brukernavn: <input type="text" name="targetuser"></td>
									<td>Eksakt navn <input type="checkbox" name="exactname"></td>
									<td>Filtrer etter kun brukerrank <input type="checkbox" name="filter_rank"> 
									<select name="filter_rank_value">
										<option value="0">Spiller</option>
										<option value="1">Sponsor</option>
										<option value="2">Hjelper</option>
										<option value="3">Vakt</option>
										<option value="4">Admin</option>
										<option value="5">Eier</option>
										<option value="6">Utvikler</option>
									</select>
								</td>
							</tr>
							<tr>
								<td><input type="submit" name="search" value="Søk!" id="usearchbutton"></td>
							</tr>
						</table>	
					</form>
				</span>
				<h3>Opprett ny profil</h3>
				<form action="index.php?p=useradmin&createprofile" method="post" id="newprofile">
					<input type="submit" value="Opprett en ny profil">
				</form>
			</div>
			';
		}
	}
?>