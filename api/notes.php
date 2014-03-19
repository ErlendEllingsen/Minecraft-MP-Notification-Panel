<?php
	include "../config.php";
	include "../functions_base.php";
	include "../inc/notfuncs.php";
	include "apifunctions.php";


	/*
		INSE
	*/
	securityLayer();

	if (isset($_GET["setnote"]))
	{
		if (empty($_GET["username"]))
		{
			echo '
			[err:empty_field]
			';
			apiDIE(); return;
		}

		$username = escapeString($_GET["username"]);
		$founduser = userExists($username);
		if (!$founduser)
		{
			echo '
			[err:no_user_found]
			';
			apiDIE(); return;
		}

		if ((!isset($_GET["type"]) || (!isset($_GET["author"])) || (empty($_GET["subject"]))))
		{
			echo '
			[err:empty_fields]
			';
			apiDIE(); return;
		}

		$type = escapeString($_GET["type"]);
		$author = escapeString($_GET["author"]);
		$subject = escapeString($_GET["subject"]);
		if (!is_numeric($type))
		{
			echo '
			[err:wrong_type]
			';
			apiDIE(); return;
		}

		$type = floor($type);
		if (($type > 5) || ($type < 0))
		{
			echo '
			[err:wrong_type]
			';
			apiDIE(); return;
		}

		$authorExists = userExists($author);
		if (!$authorExists)
		{
			echo '
			[err:author_doesnt_exist]
			';
			apiDIE(); return;
		}

		$uTargetID = userGetID($username);
		$authorid = userGetID($author);
		$sql = "INSERT INTO `notes` (targetid, type, subject, descr, author, creationdate) VALUES ('" . $uTargetID . "', '" . $type . "', '" . strip_tags($subject) . "', '" . strip_tags($subject) . "', '" . $authorid . "', '" . time() . "')";
		$result = qq($sql);
		if (!$result)
		{
			echo '
			[err:insertion_error]
			';
			apiDIE(); return;
		}

		//Create notification
		$notificationData = array(
			'id' => mysql_insert_id(),
			'target' => $uTargetID,
			'type' => $type,
			'author' => $authorid
		);

		addNotificationNote($notificationData);

		echo '
		[succ:insertion_correct]
		';

		apiDIE(); return;
	}

	/*
		RETR
	*/

	if (isset($_GET["getnotes"]))
	{
		if (empty($_GET["username"]))
		{
			echo '
			[err:empty_field]
			';
			apiDIE(); return;
		}

		$limitTypes = -1;
		if (isset($_GET["limittotype"]))
		{
			if (!is_numeric($_GET["limittotype"]))
			{
				echo '
				[err:limittotype must be integer]
				';
				apiDIE(); return;
			}

			$limittotype = floor(escapeString($_GET["limittotype"]));
			if (($limittotype < 0) || ($limittotype > 3))
			{
				echo '
				[err:wrong type]
				';
				apiDIE(); return;
			}

			$limitTypes = $limittotype;
		}

		$username = escapeString($_GET["username"]);
		$sql = "SELECT `username` FROM `users` WHERE `username`='" . $username . "'";
		$result = qq($sql);
		$num_of_users = mysql_num_rows($result);
		if ($num_of_users < 1)
		{
			echo '
			[err:no_user_found]
			';
			apiDIE(); return;
		}

		if ($num_of_users >= 1)
		{
			$db_username = mysql_fetch_object($result)->username;
			if (!($db_username === $username))
			{
				echo '
				[err:wrong_user]
				';
				apiDIE(); return;
			}
		}

		mysql_data_seek($result, 0);
		while ($selUsername = (mysql_fetch_object($result)))
		{
			if ($selUsername->username === $username)
			{
				$foundUser = true;
			}
		}
		if (!$foundUser)
		{
			echo '
			[err:wrong_user]
			';
			apiDIE(); return;
		} 

		$user_id = userGetID($username);
		if ($user_id == false)
		{
			echo '
			[err:no_user_found]
			';
			apiDIE(); return;
		}

		$maxlimit = isset($_GET["expandmaxlimit"]) ? escapeString($_GET["expandmaxlimit"]) : 5;
		$sql = ($limitTypes == -1 ? ("SELECT `subject`, `type`, `author`, `creationdate` FROM `notes` WHERE `targetid`='" . $user_id . "' ORDER BY `id` DESC LIMIT 0," . $maxlimit) : ("SELECT `subject`, `type`, `author`, `creationdate` FROM `notes` WHERE `targetid`='" . $user_id . "' AND `type`='" . $limitTypes . "' ORDER BY `id` DESC LIMIT 0," . $maxlimit));
		$result = qq($sql);
		$num_of_notes = mysql_num_rows($result);
		if ($num_of_notes <= 0)
		{
			echo '
			[err:no_notes_exist]
			';
			apiDIE(); return;
		}

		echo '
		[succ:notes_found]
		';

		while ($data = mysql_fetch_object($result))
		{
			echo '
			<note>
				[subject=' . $data->subject . ']
				[type=' . $data->type . ']
				[author=' . $data->author . ']
				[creationdate=' . $data->creationdate . ']
			</note>
			';
		}


		apiDIE(); return;
	}

	echo '
	[err:no_input]
	';
	apiDIE(); return;

	mysql_close();
?>