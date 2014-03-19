<?php
	if (!isset($_SESSION["username"]))
	{
		echo '
		[Request not allowed.]
		';
		die();
		exit;
	}
?>