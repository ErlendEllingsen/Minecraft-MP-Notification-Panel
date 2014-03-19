<?php
	if (isset($_SESSION["username"]))
	{
		$sql = "UPDATE `users` SET `lastactive`='" . time() . "', `lastip`='". $_SERVER['REMOTE_ADDR'] . "' WHERE `id`='" . $ud->data->id . "'";
		$result = qq($sql);
		if (!$result)
		{
			echo '
			<span style="font-weight: bold;">Her gikk noe alvorlig galt! Vennligst kontakt admin og si han følgende: (updateLA)</span>
			';
			die();
		}
	}
?>