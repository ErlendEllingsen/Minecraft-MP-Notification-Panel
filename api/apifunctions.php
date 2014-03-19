<?php
	
	function apiDIE()
	{
		mysql_close();
		return;
		die();
	}

	function securityLayer()
	{
		if (isset($_GET["ignoreip"]))
		{
			if ($_GET["ignoreip"] === constant('API_PASSWORD'))
			{
				return;
			}
		}
		
		$ip = $_SERVER['REMOTE_ADDR'];
		if ($ip != "127.0.0.1")
		{
			echo '
			<html>
				<head>
					<title>No access, be a korean.</title>
					<style type="text/css">
						body, html
						{
							background-color: #000;
						}

						h1
						{
							color: #FFF;
						}
					</style>
				</head>
				<body>
					<h1>this is xioco api, hello hello</h1>
					<object width="800" height="800"><param name="movie" value="http://www.youtube.com/v/9bZkp7q19f0&autoplay=1"></param><embed src="http://www.youtube.com/v/9bZkp7q19f0&autoplay=1" type="application/x-shockwave-flash" width="800" height="800"></embed></object>
				</body>
			</html>';
			
			apiDIE();
			die();
			exit;
		}
	}

	/*
		USER FUNCTIONS
	*/
	function createUser($username, $lastip = "default")
	{
		$username = escapeString($username);
		$user_newpass = createSalt(8);
		$user_actualpass = encryptRawPass($user_newpass);

		$lastip_insert = $lastip == "default" ? "" : $lastip;
		$lastip_insert = escapeString($lastip_insert);
		$sql = "INSERT INTO `users` (`username`, `password`, `passwordstatus`, `userrank`, `salt`, `lastip`, `creationdate`) VALUES ('" . $username . "', '" . $user_actualpass['pass']  . "', '0', '0', '" . $user_actualpass['salt'] . "', '" . $lastip_insert . "', '" . time() . "')";
		$result = qq($sql);
		if ($result)
		{
			return true;
		} else
		{
			return false;
		}
	}

	function userExists($username, $casesensitive = 1, $mode = 1)
	{
		
		$username = escapeString($username);
		$sql = "SELECT `username` FROM `users` WHERE `username`='" . $username . "'";
		$result = qq($sql);
		$num_of_users = mysql_num_rows($result);

		if ($num_of_users <= 0 )
		{
			return false;
		}

		if ($casesensitive == 1)
		{
	
			$foundUser = false;
			if ($num_of_users == 1)
			{
				$db_username = mysql_fetch_object($result)->username;
				if ($db_username === $username)
				{
					$foundUser = true;
				}
			} else
			{
				while ($selUsername = (mysql_fetch_object($result)))
				{
					if ($selUsername->username === $username)
					{
						$foundUser = true;
					}
				}
			}

			return $foundUser;
		} else
		{
			return true;
		}
		return;
	}

	function userGetID($username)
	{
		$finduser = userExists($username);
		$sql = "SELECT `id`, `username` FROM `users` WHERE `username`='" . $username . "'";
		$result = qq($sql);

		$foundUser = false;	
		$foundUser_id = 0;
		while ($data = mysql_fetch_object($result))
		{
			if ($data->username === $username)
			{
				$foundUser = true;
				$foundUser_id = $data->id;
			}
		}

		if (!$foundUser)
		{
			return false;
		}

		return $foundUser_id;
	}
?>