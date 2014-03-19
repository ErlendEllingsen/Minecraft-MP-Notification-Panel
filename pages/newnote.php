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
			Du har ikke tilgang til å skrive notater!
		</div>
		';
	} else 
	{
		$newnote_d = array(
			'preuser' => ''
			);

		if (isset($_POST["addnote"]))
		{
			if (empty($_POST["targetuser"]) || (!isset($_POST["notetype"])) || empty($_POST["subject"]) || empty($_POST["desc"]))
			{
				echo '
				<div class="notification error">
					Ingen av feltene kan være tomme!
				</div>
				';
			} else 
			{
				$_POST["subject"] = strip_tags($_POST["subject"]);
				$_POST["desc"] = strip_tags($_POST["desc"]);

				if (!userExists(1, escapeString($_POST["targetuser"])))
				{
					echo '
					<div class="notification error">
						Ingen profil eksisterer med dette brukernavnet.
					</div>
					';
				} else 
				{
					if (($_POST["notetype"] < 0) || ($_POST["notetype"] > 5))
					{
						echo '
						<div class="notification error">
							Feil notattype, vennligst velg en av de gjeldende typene.
						</div>
						';
					} else 
					{
						if (strlen($_POST["subject"]) < 3)
						{
							echo '
							<div class="notification error">
								Emnet må være over 3 bokstaver langt.
							</div>
							';
						} else 
						{
							if (strlen($_POST["desc"]) < 5)
							{
								echo '
								<div class="notification error">
									Beskrivelsen må være over 5 bokstaver lang.
								</div>
								';
							} else 
							{
								$uTargetID = getID(escapeString($_POST["targetuser"]));

								$sql = "INSERT INTO `notes` (targetid, type, subject, descr, author, creationdate) VALUES ('" . $uTargetID . "', '" . escapeString($_POST["notetype"]) . "', '" . strip_tags(escapeString($_POST["subject"])) . "', '" . strip_tags(escapeString($_POST["desc"])) . "', '" . $ud->data->id . "', '" . time() . "')";
								$result = qq($sql);
								if (!$result)
								{
									echo '
									<div class="notification error">
										Det skjedde noe feil ved oprettingen av notatet. Vennligst kontakt <span style="font-weight: bold;">Administratoren</span>
									</div>
									';
									return;
								}

								$newnoteid = mysql_insert_id();

								//Create notification
								$notificationData = array(
									'id' => mysql_insert_id(),
									'target' => $uTargetID,
									'type' => escapeString($_POST["notetype"]),
									'author' => $ud->data->id
								);

								addNotificationNote($notificationData);

								echo '
								<div class="notification success">
									Notatet har blitt opprettet! Klikk <a href="index.php?p=singlenotereader&noteid=' . $newnoteid . '">her</a> for å lese notatet. <a href="index.php?p=viewprofile&id=' . $uTargetID . '">Til målprofilen</a>
								</div>
								';
							
							}
						}
					}
				}
			}
		} else 
		{

			if (isset($_GET["preid"]))
			{
				if (!is_numeric($_GET["preid"]))
				{
					echo '
					<div class="notification error">
						Feil forhåndsbruker, om du tror dette gjelder en feil, vennligst kontakt <span style="font-weight: bold;">administrator</span>.
					</div>
					';
				} else 
				{
					if (!userExists(0, escapeString($_GET["preid"])))
					{
						echo '
						<div class="notification error">
							Brukeren som ble linket til eksisterer ikke.
						</div>
						';
					} else 
					{
						$newnote_d['preuser'] = getUsername(escapeString($_GET["preid"]));
					}
				}
			}

			echo '
			<div class="contentbox" id="newnotecontainer">
				<h3>Skriv nytt notat</h3>
				<form action="" method="post">
					<h4>1. Velg målbruker</h4>
					<input type="text" name="targetuser" value="' . $newnote_d['preuser'] . '">
					<h4>2. Velg notattype</h4>
					<input type="radio" name="notetype" value="0" style="background: rgba(157, 208, 255, 1); color: #000;" checked="checked">Notat <input type="radio" name="notetype" value="1" style="background: rgba(80, 80, 80, 1); color: #FFF;">Kick  <input type="radio" name="notetype" value="2" style="background-color: #FFE59D; color: #000;">Advarsel  <input type="radio" name="notetype" value="3" style="background: #F93737; color: #000;">Ban <input type="radio" name="notetype" value="4" style="background: #FB7777; color: #000;">Tempban<input type="radio" name="notetype" value="5" style="background: #ACF6B6; color: #000;">Unban 
					<h4>3. Velg et emne for notatet</h4>
					<input type="text" name="subject">
					<h4>4. Skriv et passende resumé av notatet</h4>
					<textarea id="desc" name="desc"></textarea>HTML er <span style="font-weight: bold;">ikke</span> tillat, bruk <a href="http://en.wikipedia.org/wiki/BBCode">BBCodes</a>!
					<h4>5. Publiser notatet!</h4>
					<input type="submit" name="addnote" value="Publiser notat">
				</form>
			</div>
			';
		}
	}		
?>