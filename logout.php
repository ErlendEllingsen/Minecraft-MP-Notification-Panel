<?php
	/*
		All code in this script (including ALL files inside this directory) is property of Erlend Ellingsen.
		Script written for use on 'XioCo' and 'XioCo' ONLY. 
		Script written June 2012.

		Do not copy any code without permission from Erlend Ellingsen.
	*/
	
	session_start();

	if (isset($_SESSION["username"]))
	{
		unset($_SESSION["username"]);
		session_destroy();
	}

	header('Location: login.php');
?>