<?php
	$sql = "SELECT `id` FROM `notifications`";
	$result = qq($sql);
	$num_of_notifications = mysql_num_rows($result);

	$sql = "SELECT `type` FROM `notes`";
	$result = qq($sql);
	$num_of_notes = mysql_num_rows($result);

	$totNotes = array(
		0 => array('name' => 'nøytrale notater', 'amount' => 0),
		1 => array('name' => 'kicks', 'amount' => 0),
		2 => array('name' => 'advarsler', 'amount' => 0),
		3 => array('name' => 'bans', 'amount' => 0),
		4 => array('name' => 'tempbans', 'amount' => 0),
		3 => array('name' => 'unbans', 'amount' => 0)
	);	
	while ($data = mysql_fetch_object($result))
	{
		$totNotes[$data->type]['amount']++;
	}


	echo '
	<div class="contentbox">
		<h2>Statistikk</h2>
		<ul style="background-color: #FFF; padding: 30px; border-radius: 5px;">
			<li><span style="font-weight: bold;">Varsler totalt:</span> ' . number_format($num_of_notifications) . '</li>
	';

	for($i = 0; $i < count($totNotes); $i++)
	{
		echo '
			<li><span style="font-weight: bold;">Totalt antall ' . $totNotes[$i]['name'] . ':</span> ' . number_format($totNotes[$i]['amount']) . '</li>
		';
	}

	echo '
		</ul>
	';

	$noteTopList = array();
	$sql = "SELECT `author`, `type` FROM `notes`";
	$result = qq($sql);
	while ($data = mysql_fetch_object($result))
	{
		if (!isset($noteTopList[$data->author])) {$noteTopList[$data->author] = 1;} else 
		{
			$noteTopList[$data->author]++;
		}
	}

	arsort($noteTopList);

	echo '
			<div style="background-color: #FFF; border-radius: 5px; padding: 25px;">
				<h3>Aktivitet</h3>
				<table style="width: 500px;">
					<tr style="font-weight: bold;">
						<td>Rangering</td>
						<td>Brukernavn</td>
						<td>Notater</td>
					</tr>
	';

	$currPos = 0;
	foreach ($noteTopList as $index => $value)
	{
		$sql = "SELECT `username`, `userrank` FROM `users` WHERE `id`='" . $index. "'";
		$result = qq($sql);
		$cudata = mysql_fetch_object($result);
		if (!($cudata->userrank <= 1))
		{
			$currPos++;

			$color = $currPos % 2 === 0 ? '#D6D7D7' : '#FFF';//'#BFBFBF';

			echo '
						<tr style="background-color: ' . $color . ';">
							<td><span style="font-weight: bold;">' . $currPos . '</span></td>
							<td><a href="index.php?p=viewprofile&id=' . $index . '"><span style="text-shadow: 1px 1px 1px #747474; color: ' . $accessLevelColors[$cudata->userrank] . ';">' . $cudata->username . '</span></a></td>
							<td><span style="font-weight: bold;">' . $value . '</span> notater</td>
						</tr>
			';
		}
	}

	echo '
				</table>
			</div>
	';	

	/*echo '
	<table style="background-color: #FFF; width: 725px;">
		<tr>
			<td colspan="4" style="text-align: center; font-weight: bold;">Topp 20 </td>
		</tr>
		<tr>
			<td>Bruker</td>
			<td>Totalt antall notater</td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</table>
	';*/

	echo '
	</div>
	';



?>