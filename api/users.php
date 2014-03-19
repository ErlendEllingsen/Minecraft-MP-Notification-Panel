<?php
	include "../config.php";
	include "../functions_base.php";
	include "apifunctions.php";


	securityLayer();

	if (isset($_GET["finduser"]))
	{
		if (empty($_GET["username"]))
		{
			echo '
			[err:empty_field]
			';
			apiDIE(); return;
		}

		$username = escapeString($_GET["username"]);
		$foundUser = userExists($username);



		if ($foundUser)
		{
			echo '
			[succ:user_found]
			';
			apiDIE(); return;
		} else
		{
			if (isset($_GET["docreate"]))
			{
				$regresult = false;
				if (isset($_GET["ip"]))
				{
					$regresult = createUser($username, $_GET["ip"]);
				} else
				{
					$regresult = createUser($username);
				}	
				if ($regresult)
				{
					echo '
					[succ:user_registered]
					';
					apiDIE(); return;
				} else
				{
					echo '
					[err:creation_failure]
					';
					apiDIE(); return;
				} 
			} else 
			{
				echo '[err:no_user_found]';
				apiDIE(); return;
			}
		}

		apiDIE(); return;
	}

	if (isset($_GET["getuserid"]))
	{
		if (empty($_GET["username"]))
		{
			echo '
			[err:empty_field]
			';
			apiDIE(); return;
		}


		$getid = userGetID(escapeString($_GET["username"]));
		if ($getid == false)
		{
			echo '
			[err:user_no_exist]
			';
			apiDIE(); return;
		} 

		echo $getid;
		apiDIE(); return;
		
	}

	if (isset($_GET["getusername"]))
	{
		if (empty($_GET["userid"]))
		{
			echo '
			[err:empty_field]
			';
			apiDIE(); return;
		}

		if(!is_numeric($_GET["userid"]))
		{
			echo '
			[err:userid_is_not_integer]
			';
			apiDIE(); return;
		}

		$userid = floor(escapeString($_GET["userid"]));

		$sql = "SELECT `username` FROM `users` WHERE `id`='" . $userid . "' LIMIT 0,1";
		$result = qq($sql);
		$num_of_users = mysql_num_rows($result);
		if ($num_of_users <= 0)
		{
			echo '
			[err:user_no_exist]
			';
			apiDIE(); return;
		}

		$data = mysql_fetch_object($result);
		echo $data->username;
		apiDIE(); return;

	}

	echo '
	[err:no_input]
	';
	apiDIE(); return;

	mysql_close();
?>