<?php
	function escapeString($string)
	{
		if (get_magic_quotes_gpc()) $string = stripslashes($string);
		return mysql_real_escape_string($string);
	}

	function qq($string)
	{
		global $num_queries;
		$result = mysql_query($string);
		$num_queries++;
		return $result;
	}

	function createSalt($length = 8)
	{
		$output = "";
		$valChars = "abcdefghjiklmnopqrstuvwxyz123456789";
		for ($i = 0; $i < $length; $i++)
		{
			$pickChar = $valChars[mt_rand(0, strlen($valChars) - 1)];
			$output .= $pickChar;
		}
		return $output;
	}
	function encryptRawPass($password)
	{
		$salt = createSalt();
		$passRaw = $salt . $password;
		$passEncrypted = md5($passRaw);

		$outputdata = array(
			'salt' => $salt,
			'pass' => $passEncrypted
			);
		return $outputdata;
	}
	function increaseUnreadNotifications($userid)
	{
		$sql = "UPDATE `users` SET `unreadnotifications`=unreadnotifications+1 WHERE `id`='" . $userid . "'";
		$result = qq($sql);
		if (!$result)
		{
			return false;
		}
		return true;
	}
?>