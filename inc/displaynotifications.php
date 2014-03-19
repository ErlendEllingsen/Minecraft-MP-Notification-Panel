<?php
	while ($data = mysql_fetch_object($result))
	{
		$notificationdata = unserialize($data->notificationdata);

		if ($data->notificationtype == $notificationtypes['note'])
		{
			$aD = new userData(); //authorData
			$aD->getUserData($notificationdata['author']);

			$tD = new UserData(); //targetData
			$tD->getUserData($notificationdata['target']);

			echo '
					<li class="' . $noteTypes[$notificationdata['type']] . '" onClick="navigate(\'' . constant('PATH') . 'index.php?p=singlenotereader&noteid=' . $notificationdata['id'] . '\', 500);"><a href="index.php?p=viewprofile&id=' . $aD->data->id . '"><span style="color: ' . $accessLevelColors[$aD->data->userrank] . ';">' . $aD->data->username . '</span></a> opprettet et <a href="index.php?p=singlenotereader&noteid=' . $notificationdata['id'] . '">notat</a> på brukeren <a href="index.php?p=viewprofile&id=' . $tD->data->id . '"><span style="color: ' . $accessLevelColors[$tD->data->userrank] . ';">' . $tD->data->username . '</span></a>.</li>
			';
		}

		if ($data->notificationtype == $notificationtypes['note_edit'])
		{
			$aD = new userData(); //authorData
			$aD->getUserData($notificationdata['author']);

			$tD = new userData(); //targetData
			$tD->getUserData($notificationdata['target']);

			$eD = new userData(); //editorData
			$eD->getUserData($notificationdata['targeteditor']);

			echo '
					<li class="' . $noteTypes[$notificationdata['type']] . '" onClick="navigate(\'' . constant('PATH') . 'index.php?p=singlenotereader&noteid=' . $notificationdata['id'] . '\', 500);"><a href="index.php?p=viewprofile&id=' . $eD->data->id . '"><span style="color: ' . $accessLevelColors[$eD->data->userrank] . ';">' . $eD->data->username . '</span></a> redigerte et <a href="index.php?p=singlenotereader&noteid=' . $notificationdata['id'] . '">notat</a> på brukeren <a href="index.php?p=viewprofile&id=' . $tD->data->id . '"><span style="color: ' . $accessLevelColors[$tD->data->userrank] . ';">' . $tD->data->username . '</span></a>. Orginalt skrevet av <a href="index.php?p=viewprofile&id=' . $aD->data->id . '"><span style="color: ' . $accessLevelColors[$aD->data->userrank] . ';">' . $aD->data->username . '</span></a>.</li>
			';
		}

		if ($data->notificationtype == $notificationtypes['news'])
		{
			$aD = new userData(); //authorData
			$aD->getUserData($notificationdata['author']);
			echo '
					<li class="note" onClick="navigate(\'' . constant('PATH') . 'index.php?p=newsarticle&id=' . $notificationdata['id'] . '\', 500);"><a href="index.php?p=viewprofile&id=' . $aD->data->id . '"><span style="color: ' . $accessLevelColors[$aD->data->userrank] . ';">' . $aD->data->username . '</span></a> opprettet en <span style="font-weight: bold;">nyhetsartikkel</span></a>.</li>
			';
		}

		if ($data->notificationtype == $notificationtypes['comment'])
		{
			$aD = new userData(); //authorData
			$aD->getUserData($notificationdata['author']);

			$tD = new userData(); //targetData
			$tD->getUserData($notificationdata['target']);

			$cD = new UserData(); //commenterData
			$cD->getUserData($notificationdata['commenter']);

			/*echo '
					<li class="note" onClick="navigate(\'' . constant('PATH') . 'index.php?p=singlenotereader&noteid=' . $notificationdata['noteid'] . '\', 500);"><a href="index.php?p=viewprofile&id=' . $cD->data->id . '"><span style="color: ' . $accessLevelColors[$cD->data->userrank] . ';">' . $cD->data->username . '</span></a> kommenterte et <a href="index.php?p=singlenotereader&noteid=' . $notificationdata['noteid'] . '">notat</a> på brukeren <a href="index.php?p=viewprofile&id=' . $tD->data->id . '"><span style="color: ' . $accessLevelColors[$tD->data->userrank] . ';">' . $tD->data->username . '</span></a>. Orginalt skrevet av <a href="index.php?p=viewprofile&id=' . $aD->data->id . '"><span style="color: ' . $accessLevelColors[$aD->data->userrank] . ';">' . $aD->data->username . '</span></a>.</li>
			';*/
			echo '
					<li class="note" onClick="navigate(\'' . constant('PATH') . 'index.php?p=singlenotereader&noteid=' . $notificationdata['noteid'] . '\', 500);"><a href="index.php?p=viewprofile&id=' . $cD->data->id . '"><span style="color: ' . $accessLevelColors[$cD->data->userrank] . ';">' . $cD->data->username . '</span></a> kommenterte et <a href="index.php?p=singlenotereader&noteid=' . $notificationdata['noteid'] . '">notat</a>.</li>
			';
		}
	}
?>	