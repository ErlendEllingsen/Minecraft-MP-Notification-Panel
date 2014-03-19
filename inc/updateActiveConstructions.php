<?php
	$sql = "UPDATE `violating_constructions` SET `active`='0' WHERE `activeto`<'" . time() . "'";
	$result = qq($sql);
	if (!$result)
	{
		echo '
		Error: fikk ikke oppdatert konstruksjoner.
		';
		return;
	}
?>