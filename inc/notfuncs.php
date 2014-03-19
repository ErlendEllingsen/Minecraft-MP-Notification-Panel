<?php
	/*
		All code in this script (including ALL files inside this directory) is property of Erlend Ellingsen.
		Script written for use on 'XioCo' and 'XioCo' ONLY. 

		Do not copy any code without permission from Erlend Ellingsen.
	*/
	$notificationtypes = array(
	
		'note' => 0,
		'note_edit' => 1,
		'news' => 2,
		'comment' => 3

	);

	$notificationerrors = array(
		'[err:notnumeric]' => 'Feil publiseringsmåte valgt. Om du tror dette skjedde ved en feil, vennligst kontakt administrator.',
		'[err:wrongpublished]' => 'Feil publiseringsmåte valgt. Om du tror dette skjedde ved en feil, vennligst kontakt administrator.',
		'[err:dbinserterror]' => 'Noe gikk galt ved lagringen av notifikasjonen. Vennligst kontakt administrator.',
		'[err:connectednote_not_found]' => 'Notatet som ble oppgitt som tilkoblingsnotat, ble ikke funnet.'

	);

	function addNotificationNote($notedata)
	{
		if (!addNotification('note', $notedata, $notedata['id']))
		{
			echo '
			<div class="notification error">
				Fikk ikke opprettet notifikasjon. Vennligst kontakt <span style="font-weight: bold;">Administratoren</span>
			</div>
			';
			return;
		}

		if (!increaseUnreadNotifications($notedata['target']))
		{
			echo '
			<div class="notification error">
				Fikk ikke opprettet notifikasjonvarsel på brukeren. Vennligst kontakt <span style="font-weight: bold;">Administratoren</span>
			</div>
			';
			return;
		}
	}

	function addNotificationEditNews($notedata)
	{
		if (!addNotification('note_edit', $notedata))
		{
			echo '
			<div class="notification error">
				Fikk ikke opprettet notifikasjon. Vennligst kontakt <span style="font-weight: bold;">Administratoren</span>
			</div>
			';
			return;
		}

		if (!increaseUnreadNotifications($notedata['target']))
		{
			echo '
			<div class="notification error">
				Fikk ikke opprettet notifikasjonvarsel på brukeren. Vennligst kontakt <span style="font-weight: bold;">Administratoren</span>
			</div>
			';
			return;
		}

		if (!increaseUnreadNotifications($notedata['author']))
		{
			echo '
			<div class="notification error">
				Fikk ikke opprettet notifikasjonvarsel på brukeren. Vennligst kontakt <span style="font-weight: bold;">Administratoren</span>
			</div>
			';
			return;
		}
	}

	function addNotificationNews($notedata)
	{
		if (!addNotification('news', $notedata))
		{
			echo '
			<div class="notification error">
				Fikk ikke opprettet notifikasjon. Vennligst kontakt <span style="font-weight: bold;">Administratoren</span>
			</div>
			';
			return;
		}

		$sql = "UPDATE `users` SET `unreadnotifications`=unreadnotifications+1"; 
		$result = qq($sql);
		if (!$result)
		{
			echo '
			<div class="notification error">
				Fikk ikke opprettet notifikasjonvarsel på brukere. Vennligst kontakt <span style="font-weight: bold;">Administratoren</span>
			</div>
			';
			return;
		}
	}

	function addNotificationComment($notedata)
	{
		if (!addNotification('comment', $notedata))
		{
			echo '
			<div class="notification error">
				Fikk ikke opprettet notifikasjon. Vennligst kontakt <span style="font-weight: bold;">Administratoren</span>
			</div>
			';
			return;
		}

		$sql = "UPDATE `users` SET `unreadnotifications`=unreadnotifications+1 WHERE `id`='" . $notedata['author'] . "' OR `id`='" . $notedata['target'] . "'"; 
		$result = qq($sql);
		if (!$result)
		{
			echo '
			<div class="notification error">
				Fikk ikke opprettet notifikasjonvarsel på brukere. Vennligst kontakt <span style="font-weight: bold;">Administratoren</span>
			</div>
			';
			return;
		}
	}



	function addNotification($notificationtype, $notificationdata, $connectednote = -1, $published = 1)
	{
		global $notificationtypes;
		$notificationtype = escapeString($notificationtype);
		$published = escapeString($published);
		$connectednote = escapeString($connectednote);
		if (!is_numeric($published))
		{
			return "[err:notnumeric]";
		}

		$published = floor($published);

		if ($published > 1 || $published < 0)
		{
			return "[err:wrongpublished]";
		}

		if (!is_numeric($connectednote))
		{
			return "[err:notnumeric]";
		}

		$connectednote = floor($connectednote);
		if (!$connectednote == -1)
		{
			$sql = "SELECT `id` FROM `notes` WHERE `id`='" . $connectednote . "'";
			$result = qq($sql);
			$num_of_notes = mysql_num_rows($result);
			if ($num_of_notes <= 0)
			{
				return "[err:connectednote_not_found]";
			}
		}

		$notificationdata = serialize($notificationdata);
		$sql = "INSERT INTO `notifications` (notificationtype, notificationdata, creationdate, published, connectednote) VALUES ('" . $notificationtypes[$notificationtype]  . "', '" . $notificationdata . "', '" . time() . "', '" . $published . "', '" . $connectednote . "')";
		$result = qq($sql);

		if (!$result)
		{
			return "[err:dbinserterror]";
		} 
		return true;
	}

	function removeAllNotesConnectedToNote($noteid, $notificationtype)
	{
		$connectednote = escapeString($noteid);
		if (!is_numeric($connectednote))
		{
			return false;
		}

		$connectednote = floor($connectednote);
		if ($notificationtype == $notificationtypes['note'])
		{
			$sql = "SELECT `id` FROM `notes` WHERE `id`='" . $connectednote . "'";
			$result = qq($sql);
			$num_of_notes = mysql_num_rows($result);
			if ($num_of_notes <= 0)
			{
				return false;
			}
		}

		$sql = "DELETE FROM `notifications` WHERE `connectednote`='" . $connectednote . "' AND `notificationtype`='" . $notificationtype . "'";
		$result = qq($sql);
		if (!$result)
		{
			return false;
		}
		return true;
	}
?>