<?php
	/*
		All code in this script (including ALL files inside this directory) is property of Erlend Ellingsen.
		Script written for use on 'XioCo' and 'XioCo' ONLY. 
		Script written June 2012.

		Do not copy any code without permission from Erlend Ellingsen.
	*/
		
	class userData
	{
		public $data;

		public function getUserData($id)
		{
			global $data;

			$sql = "SELECT * FROM `users` WHERE `id`='" . $id . "'";
			$result = qq($sql);

			$num_of_users = mysql_num_rows($result);
			if ($num_of_users <= 0)
			{
				return false;
			} else 
			{
				$this->data = mysql_fetch_object($result);
				return true;
			}
		}
	}
?>