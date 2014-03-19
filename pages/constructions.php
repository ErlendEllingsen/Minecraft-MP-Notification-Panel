<?php
	include(file_exists("../inc/reqLogin.php") ? "../inc/reqLogin.php" : "inc/reqLogin.php");
	include "inc/updateActiveConstructions.php";

	$frequency = array(
		0 => "15 min",
		1 => "20 min",
		2 => "30 min",
		3 => "45 min",
		4 => "1 time",
		5 => "2 timer",
		6 => "3 timer"
	);

	if (isset($_GET["newconstruction"]))
	{
		if (isset($_POST["createmsg"]))
		{
			if (empty($_POST["targetuser"]) || empty($_POST["tsStartV"]) || empty($_POST["tsStopV"]) || empty($_POST["message"]) || !isset($_POST["frequency"]) || !isset($_POST["corX"]) || !isset($_POST["corZ"]) || !isset($_POST["corY"]))
			{
				echo '
				<div class="notification error">
					Alle felt må fylles ut!
				</div>
				';
				return;
			}

			if (!is_numeric($_POST["corX"]) || !is_numeric($_POST["corZ"]) || !is_numeric(($_POST["corY"])))
			{
				echo '
				<div class="notification error">
					Alle kordinatene må være numeriske.
				</div>
				';
				return;
			}

			$targetuser = escapeString($_POST["targetuser"]);
			$tsStart = escapeString($_POST["tsStartV"]);
			$tsStop = escapeString($_POST["tsStopV"]);
			$message = strip_tags(escapeString($_POST["message"]));
			$frequency = escapeString($_POST["frequency"]);

			if (!is_numeric($frequency))
			{
				echo '
				<div class="notification error">
					Krekvensen må være numerisk.
				</div>
				';
				return;
			}

			$corX = floor(escapeString($_POST["corX"])); $corZ = floor(escapeString($_POST["corZ"])); $corY = floor(escapeString($_POST["corY"]));
			$frequency = floor($frequency);
			if ($frequency < 0 || $frequency > 6)
			{
				echo '
				Feil frekvens valgt.
				';
				return;
			}


			$timespan_dates = array('start' => $tsStart, 'stop' => $tsStop);
							
			foreach ($timespan_dates as $k => $v)
			{
				$day = substr($v, 0, 2); $month = substr($v, 3, 2); $year = substr($v, 6, 4);
				if (!(is_numeric($day) && is_numeric($month) && is_numeric($year)))
				{
					echo '
					<div class="notification error">
						Enten startdatoen eller stoppdatoen i søkeperioden er i feil format. Gyldig format: <span style="font-weight: bold;">dd/mm/yy</span>, HUSK: \'/\'
					</div>';
					return;
				}
				else 
				{

					$timespan_dates[$k] = mktime(0, 0, 0, $month, $day, $year);
				}
			}

			$sql = "SELECT `id` FROM `users` WHERE `username`='" . $targetuser . "'";
			$result = qq($sql);
			$num_of_users = mysql_num_rows($result);
			if ($num_of_users <= 0)
			{
				echo '
				<div class="notification error">
					Den bestemte brukeren eksisterer ikke. (Eller har aldri logget inn på serveren).
				</div>
				';
				return;
			}
			$targetuserid = mysql_fetch_object($result)->id;

			$xyz = array(
				'x' => $corX,
				'z' => $corZ,
				'y' => $corY
			);



			$sql = "INSERT INTO `violating_constructions` (`targetid`, `activefrom`, `activeto`, `message`, `xyz`, `active`, `authorid`, `frequency`) VALUES ('" . $targetuserid . "', '" . $timespan_dates["start"] . "', '" . $timespan_dates["stop"] . "', '" . $message . "', '" . serialize($xyz) . "', '1', '" . $ud->data->id . "', '" . $frequency . "')";
			$result = qq($sql);
			if (!$result)
			{
				echo '
				<div class="notification error">
					Noe gikk galt ved opprettingen av meldingen. Vennligst kontakt administrator.
				</div>
				';
				return;
			}

			echo '
			<div class="notification success">
				Meldingen om konstruksjonen har blitt opprettet!
			</div>
			';


			return;
		}


		if (isset($_GET["corX"]) && isset($_GET["corZ"]) && isset($_GET["corY"]))
		{
			$positions = array(
				"x" => $_GET["corX"],
				"z" => $_GET["corZ"],
				"y" => $_GET["corY"]
			);

			foreach ($positions as $key => $value)
			{
				if (!is_numeric($value))
				{
					$positions[$key] = 0;
				} else 
				{
					$positions[$key] = floor($value);
				}
			}


		} else 
		{
			$positions = array(
				"x" => 0,
				"z" => 0,
				"y" => 0
			);
		}

		

		echo '
		<div class="contentbox">
			<h2>Ny upassende konstruksjon</h2>
			<form action="" method="post">
				<table>
					<tr>
						<td>Målbruker: </td>
						<td><input type"text" name="targetuser"></td>
					</tr>
					<tr>
						<td>Forfatter: </td>
						<td><span style="color: ' . $accessLevelColors[$ud->data->userrank] . '">' . $ud->data->username . '</span></td>
					</tr>
					<tr>
						<td>Aktiv fra:</td>
						<td><input type="text" id="tsStart" name="tsStartV"></td>
					</tr>
					<tr>
						<td>Aktiv til:</td>
						<td><input type="text" id="tsStop" name="tsStopV"></td>
					</tr>
					<tr>
						<td>Frekvens</td>
						<td>
							<select name="frequency">
								<option value="0">15 min</option>
								<option value="1">20 min</option>
								<option value="2">30 min</option>
								<option value="3">45 min</option>
								<option value="4">1 time</option>
								<option value="5">2 timer</option>
								<option value="6">3 timer</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><span style="font-weight: bold;">Koordinater</span></td>
					</tr>
					<tr>
						<td colspan="3">
							X:
							<input type="text" name="corX" class="coordInput" value="' . $positions['x'] . '">
							Z:
							<input type="text" name="corZ" class="coordInput" value="' . $positions['z'] . '">
							Y:
							<input type="text" name="corY" class="coordInput" value="' . $positions['y'] . '">
						</td>
					</tr>
					<tr>
						<td><span style="font-weight: bold;">Melding</span></td>
					</tr>
					<tr>
						<td colspan="3"><textarea name="message" id="constructionMessage"></textarea></td>
					</tr>
					<tr>
						<td><span style="font-weight: bold;">Melding</span></td>
					</tr>
					<tr>
						<td colspan="3"><input type="submit" name="createmsg" value="Opprett melding"></td>
					</tr>
				</table>
			</form>
		</div>
		';
		return;
	}

	if (isset($_GET["activelist"]))
	{
		$showWhat = calculationResults(10);

		$sql = "SELECT `id` FROM `violating_constructions` WHERE `active`='1'";
		$result = qq($sql);
		$total_num_constructions = mysql_num_rows($result);

		$sql = "SELECT * FROM `violating_constructions` WHERE `active`='1' ORDER BY `id` DESC LIMIT " . $showWhat['min'] . "," . $showWhat['max'];
		$result = qq($sql);
		$num_of_constructions = mysql_num_rows($result);

		echo '
		<div class="contentbox">
			<h2>Aktive upassende byggverk</h2>
			<table id="activeConstructionList">
				<tr id="headerRow">
					<td># id</td>
					<td>Målbruker</td>
					<td>Forfatter</td>
					<td>Aktiv fra</td>
					<td>Aktiv til</td>
					<td>XYZ</td>
					<td>Frekvens</td>
					<td>Melding</td>
				</tr>
			
		';

		while ($data = mysql_fetch_object($result))
		{
			echo '
			<tr>
				<td>' . $data->id . '</td>
				<td><a href="index.php?p=viewprofile&id=' . $data->targetid . '">' . getUsername($data->targetid) . '</a></td>
				<td><a href="index.php?p=viewprofile&id=' . $data->authorid . '">' . getUsername($data->authorid) . '</a></td>
				<td>' . date('d-m-y', $data->activefrom) . '</td>
				<td>' . date('d-m-y', $data->activeto) . '</td>
			';

			$xyz = unserialize($data->xyz);
			echo '
				<td>(' . $xyz['x'] . ', ' . $xyz['z'] . ', ' . $xyz['y'] . ')</td>
				<td>' . $frequency[$data->frequency] . '</td>
			';

			echo '
				<td>
			';

			$string = strip_tags($data->message);

			if (strlen($string) > 50) {
				$stringCut = substr($string, 0, 50);
				$string = substr($stringCut, 0, strrpos($stringCut, ' ')).'...'; 
			}
			echo $string;

			echo '</td>
			</tr>
			';
		}

		echo '
		</table>
		';

		generatePagination($total_num_constructions, 10, 'constructions&activelist');
		
	}


?>