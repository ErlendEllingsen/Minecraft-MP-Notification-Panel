<?php
	//XioCo does not support http response code

	if (!isset($_GET["userid"]))
	{
		//http_response_code(203); 
		header("HTTP/1.1 203 Non-Authoritative Information");
		return;
	}

	include "../../config.php";
	include "../../functions_base.php";

	$userid = escapeString($_GET["userid"]);
	if (!is_numeric($userid))
	{
		//http_response_code(406);
		header("HTTP/1.0 406 Not Acceptable");
		return;
	}

	$userid = floor($userid);
	$sql = "SELECT `unreadnotifications` FROM `users` WHERE `id`='" . $userid . "' LIMIT 0,1";
	$result = qq($sql);
	$num_of_users = mysql_num_rows($result);
	if ($num_of_users <= 0)
	{
		//http_response_code(401);
		header("HTTP/1.0 401 Unauthorized");
		return;
	}

	$numnotifications = mysql_fetch_object($result)->unreadnotifications;
	//http_response_code(200);
	header("HTTP/1.0 200 OK");
	echo $numnotifications;

	mysql_close();
?>