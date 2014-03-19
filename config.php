<?php
	/*
		All code in this script (including ALL files inside this directory) is property of Erlend Ellingsen.

		Do not copy any code without permission from Erlend Ellingsen.
	*/
	define('HOSTNAME', gethostname());

	error_reporting(E_ALL);

	if (constant('HOSTNAME') == 'remote_site')
	{
		$dbhost=""; // Host name
		$dbusername=""; // Mysql username
		$dbpassword=""; // Mysql password
		$dbdatabase=""; // Database name
	} elseif (constant('HOSTNAME') == 'Erlend-PC')
	{
		$dbhost=""; // Host name
		$dbusername=""; // Mysql username
		$dbpassword=""; // Mysql password
		$dbdatabase=""; // Database name
	}
	$num_queries = 0;

	mysql_connect("$dbhost", "$dbusername", "$dbpassword")or die("Feil ved databaseoppkobling!");
	mysql_select_db("$dbdatabase")or die("Kan ikke velge database");

	include "globalvars.php";

	session_start();
?>