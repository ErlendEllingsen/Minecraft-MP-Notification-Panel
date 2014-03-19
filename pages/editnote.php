<?php
/*
		All code in this script (including ALL files inside this directory) is property of Erlend Ellingsen.
		Script written for use on 'XioCo' and 'XioCo' ONLY. 
		Script written June 2012.

		Do not copy any code without permission from Erlend Ellingsen.
	*/
	include(file_exists("../inc/reqLogin.php") ? "../inc/reqLogin.php" : "inc/reqLogin.php");

	if ($ud->data->userrank <= $accessLevelValues['hjelper'])
	{
		echo '
		<div class="notification error">
			Du har ikke tilgang til å <span style="font-weight: bold;">redigere</span> eller <span style="font-weight: bold;">slette</span> notater!
		</div>
		';
	} else
	{
		if (empty($_GET["m"]) || empty($_GET["noteid"])) {
			echo '
			<div class="notification error">
				Manglende data, både modus og id må være valgt!
			</div>
			';

		}
		else
		{
			if ($_GET["m"] != "edit" && $_GET["m"] != "delete")
			{
				echo '
				<div class="notification error">
					Feil modus valgt! Moduset må enten være \'delete\' eller \'edit\'. Om du kom hit ved en feil, kontakt <span style="font-weight: bold;">Administrator.</span>
				</div>
				';
			}
			else 
			{
				$noteid = escapeString($_GET["noteid"]);
				if (!is_numeric($noteid))
				{
					echo '
					<div class="notification error">
						Notat-id\'en er ikke et heltall. Om du kom hit ved en feil, kontakt <span style="font-weight: bold;">Administrator.</span>
					</div>
					';
				} else
				{
					$sql = "SELECT * FROM `notes` WHERE `id`='" . $noteid . "' LIMIT 0,1"; 
					$result = qq($sql);
					$amount_of_notes = mysql_num_rows($result);
					if ($amount_of_notes <= 0)
					{
						echo '
						<div class="notification error">
							Fant ingen notater ved den spesifiserte id\'en!
						</div>
						';
					} else 
					{
						if ($_GET["m"] == "delete") 
						{
							if ($ud->data->userrank <= $accessLevelValues['vakt']) 
							{
								echo'
								<div class="notification error">
									Kun administratorer og oppover har tilgang til å kunne slette notater!
								</div>
								';
							} else 
							{
								$sql = "DELETE FROM `notes` WHERE `id`='" . $noteid . "'";
								$result = qq($sql);
								if (!$result) 
								{
									echo '
									<div class="notification error">
										Noe gikk galt under slettingen av notatet!
									</div>
									';
									return;
								} 

								echo '
								<div class="notification success">
									Notatet har blitt slettet!
								</div>
								';

								$sql = "DELETE FROM `notifications` WHERE `connectednote`='" . $noteid . "'";
								$result = qq($sql);

								
							}
						} elseif ($_GET["m"] == "edit")
						{
							$data = mysql_fetch_object($result);
							$origAuthor = new userData();
							$origAuthor->getUserData($data->author);
							$targetUser = new userData();
							$targetUser->getUserData($data->targetid);

							if (isset($_POST["keepCurrent"]))
							{
								echo '
								<div class="notification">
									Redigering avbrutt. Klikk <a href="index.php?p=news">her</a> for å fortsette.
								</div>
								';
								
							} elseif (isset($_POST["updateNote"]))
							{
								if (empty($_POST["subject"]) || empty($_POST["descr"]))
								{
									echo '
									<div class="notification error">
										Begge feltene må være fylt ut! (Emne & Beskrivelse)
									</div>
									';
								} else 
								{
									$sql = "UPDATE `notes` SET `editdate`='" . time() . "', `editauthor`='" . $ud->data->id . "', `subject`='" . escapeString($_POST["subject"]) . "', `descr`='" . escapeString($_POST["descr"]) . "' WHERE `id`='" . $noteid . "'";
									$result = qq($sql);
									if (!$result)
									{
										echo '
										<div class="notification error">
											Noe gikk feil ved redigeringen av notatet. Vennligst kontakt <span style="font-weight: bold;">administrator</span>.
										</div>
										';
									} else 
									{
										//Create notification
										$notificationData = array(
											'id' => $noteid,
											'target' => $data->targetid,
											'targeteditor' => $ud->data->id,
											'type' => $data->type,
											'author' => $data->author
										);

										addNotificationEditNews($notificationData);

										echo '
										<div class="notification success">
											Notatet har blitt redigert!
										</div>
										';
									}
								}
							} else 
							{


								echo '
								<div class="contentbox" id="noteedit">
									<form action="" method="post">
										<h3>Rediger notat</h3>
										<table id="noteinfoTable">
											<tr>
												<td><span style="font-weight: bold;">Tittel: </td>
												<td><input type="text" name="subject" value="' . $data->subject. '"></td>
											</tr>
											<tr>
												<td><span style="font-weight: bold;">Målbruker:</span></td>
												<td><a href="index.php?p=viewprofile&id=' . $targetUser->data->id . '"><span style="text-shadow: 1px 1px 1px #000; color: ' . $accessLevelColors[$targetUser->data->userrank] . ';">' . $targetUser->data->username . '</span></a></td>
											</rt>
											<tr>
												<td><span style="font-weight: bold;">Orginalforfatter:</span></td>
												<td><a href="index.php?p=viewprofile&id=' . $origAuthor->data->id . '"><span style="text-shadow: 1px 1px 1px #000; color: ' . $accessLevelColors[$origAuthor->data->userrank] . ';">' . $origAuthor->data->username . '</span></a></td>
											</tr>
											<tr>
												<td><span style="font-weight: bold;">Opprettelsesdato: </span></td>
												<td>' . date('d/m/y - H:i:s', $data->creationdate) . '</td>
											</tr>
											<tr>
												<td><span style="font-weight: bold;">Beskrivelse: </span></td>
												<td><textarea name="descr" id="noteEditDscr">' . $data->descr. '</textarea></td>
											</tr>
											<tr>
												<td colspan="2"><input type="submit" name="updateNote" value="Oppdater notat"> <input type="submit" name="keepCurrent" value="Bevar"></td>
											</tr> 
										</table>
									</form>
								</div>
								';
							}
						}
					}
				}
			}
		}
	}

?>