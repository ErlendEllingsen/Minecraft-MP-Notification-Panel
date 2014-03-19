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
			Du har ikke tilgang til å lese notater.
		</div>';
	} else 
	{
		if (empty($_GET["noteid"]))
		{
			echo '
			<div class="notification error">
				Det er ikke valgt noe notat.
			</div>
			';
		} else 
		{
			if(!is_numeric($_GET["noteid"]))
			{
				echo '
				<div class="notification error">
					Notat-id\'en er ikke gyldig.
				</div>
				';
			} else
			{
				$sql = "SELECT * FROM `notes` WHERE `id`='" . escapeString($_GET["noteid"]) . "' LIMIT 0,1";
				$result = qq($sql);
				$num_of_notes = mysql_num_rows($result);
				if ($num_of_notes <= 0)
				{
					echo '
					<div class="notification error">
						Det eksisterer ingen notater med id\'en <span style="font-weight: bold;">' . escapeString($_GET["noteid"]) . '</span>.
					</div>
					';
				} else 
				{
					$data = mysql_fetch_object($result);

					$targetData = new userData();
					$targetData->getUserData($data->targetid);

					$authorData = new userData();
					$authorData->getUserData($data->author);

					if (isset($_POST["sendcomment"]))
					{
						if (!isset($_POST["comment"]))
						{
							echo '
							<div class="notification error">
								Kommentaren må være sendt.
							</div>
							';
							return;
						}

						$comment = strip_tags(escapeString($_POST["comment"]));
						if (strlen($comment) < 5)
						{
							echo '
							<div class="notification error">
								Kommentaren må være på minst 5 bokstaver.
							</div>
							';
							return;
						}

						$sql = "INSERT INTO `comments` (`userid`, `comment`, `noteid`, `created`) VALUES ('" . $ud->data->id . "', '" . $comment . "', '" . escapeString($_GET["noteid"]) . "', '" . time() . "')";
						$result_comment = qq($sql);
						if (!$result_comment)
						{
							echo '
							<div class="notification error">
								Noe gikk galt ved lagringen av kommentaren din.
							</div>
							';
							return;
						}

						$notificationdata = array(
							"commenter" => $ud->data->id,
							"noteid" => escapeString($_GET["noteid"]),
							"author" => $authorData->data->id,
							"target" => $targetData->data->id
						);

						addNotificationComment($notificationdata);

						echo '
						<div class="notification success">
							Kommentaren er publisert!
						</div>
						';
					}

					echo '
					<div class="contentbox note_' . $noteTypes[$data->type] . ' singlenotereader">
						<h3>' . ucfirst($noteTypes[$data->type]) . ': ' . $data->subject . '</h3>
						<p class="datadescription">Notat ført den ' . date('d/m/y - H:i:s', $data->creationdate) . ' av <a href="index.php?p=viewprofile&id=' . $authorData->data->id . '"><span style="text-shadow: 1px 1px 1px #000; color: ' . $accessLevelColors[$authorData->data->userrank] . ';">' . $authorData->data->username . '</span></a>, notatet gjelder <a href="index.php?p=viewprofile&id=' . $targetData->data->id . '"><span style="text-shadow: 1px 1px 1px #000; color: ' . $accessLevelColors[$targetData->data->userrank] . ';">' . $targetData->data->username . '</span></a>.</p>
					';

						if ($data->editauthor != 0)
						{
							$editorData = new userData();
							$editorData->getUserData($data->editauthor);

							echo '
							<p class="datadescription">(Redigert den ' . date('d/m/y - H:i:s', $data->editdate) . ' av ' . '<a href="index.php?p=viewprofile&id=' . $editorData->data->id . '"><span style="text-shadow: 1px 1px 1px #000; color: ' . $accessLevelColors[$editorData->data->userrank] . ';">' . $editorData->data->username . '</span></a>)</p>';
						}

						echo '<a href="index.php?p=editnote&m=edit&noteid='  . $data->id . '"><img src="style/img/edit.png" class="linkIcon"></a><a href="index.php?p=editnote&m=delete&noteid=' . $data->id . '"><img src="style/img/delete.png" class="linkIcon"></a>
					</div>
					<div class="contentbox notereader_contentBlockData">
						<h3>Beskrivelse</h3>
						&ldquo;' . BBCode::parse($data->descr) . '&rdquo;
					</div>
					<div class="contentbox notereader_contentBlockData">
						<h3>Kommentarer (siste 30)</h3>
						<ul id="commentlist">
					';

					$sql = "SELECT * FROM `comments` WHERE `noteid`='" . $data->id . "' ORDER BY `id` DESC LIMIT 0,30";
					$result = qq($sql);
					while ($cdata = mysql_fetch_object($result))
					{
						$authorData = new userData();
						$authorData->getUserData($cdata->userid);
						echo '
							<li><p class="postdata">Av <a href="index.php?p=viewprofile&id=' . $authorData->data->id . '"><span style="text-shadow: 1px 1px 1px #000; color: ' . $accessLevelColors[$authorData->data->userrank] . ';">' . $authorData->data->username . '</span></a> den <span style="font-style: italic; color: #606060;">' . date('d-M-Y', $cdata->created) . '</span></p>
								' . BBCode::parse($cdata->comment) . '
							</li>
						';
					}

					echo '
						</ul>
						<h3>Skriv kommentar</h3>
						<form action="" method="post">
							<textarea name="comment"></textarea>HTML er <span style="font-weight: bold;">ikke</span> tillat, bruk <a href="http://en.wikipedia.org/wiki/BBCode">BBCodes</a>!
							<input type="submit" name="sendcomment" value="Publiser kommentar">
						</form>
					</div>
					';
				}
			}
		}
	}	
	?>