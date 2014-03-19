<?php
	include "../config.php";
	include "../functions_base.php";
	include "../inc/notfuncs.php";
	include "apifunctions.php";

	securityLayer();

	include "../inc/updateActiveConstructions.php";

	$sql = "SELECT * FROM `violating_constructions` WHERE `active`='1' ORDER BY `id` DESC";
	$result = qq($sql);
	$num_of_constructions = mysql_num_rows($result);
	if ($num_of_constructions <= 0)
	{
		echo '
		no_constructions
		';
		apiDIE(); return;
	}

	while ($data = mysql_fetch_object($result))
	{
		$xyz = unserialize($data->xyz);
		echo '
		<announce>
			<id>' . $data->id . '</id>
			<target>' . $data->targetid . '</target>
			<author>' . $data->authorid . '</author>
			<x>' . $xyz['x'] . '</x>
			<z>' . $xyz['z'] . '</z>
			<y>' . $xyz['y'] . '</y>
			<frequency>' . $data->frequency . '</frequency>
			<message>' . $data->message. '</message>
			<active_from>' . $data->activefrom . '</active_from>
			<active_to>' . $data->activeto . '</active_to>
		</announce>
		';
	}


?>