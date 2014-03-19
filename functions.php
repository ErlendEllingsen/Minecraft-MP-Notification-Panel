<?php
	/*
		All code in this script (including ALL files inside this directory) is property of Erlend Ellingsen.

		Do not copy any code without permission from Erlend Ellingsen.
	*/

	include "functions_base.php";
	include "inc/notfuncs.php";

	/*
		CONTENT METHODS
	*/

	function generatePagination($numrows, $rowsperpage, $page)
	{
		$currPage = 0;

		if (isset($_GET["pagination"]) && isset($_GET["cp"]))
		{

			if (is_numeric($_GET["cp"]))
			{
				$currPage = round(escapeString($_GET["cp"]), 0);
			}
		}
		if ($currPage >= 0)
		{
			if ($currPage == 0)
			{
				echo '
				<a class="pagin currpage">Første</a>
				';
			} else
			{
				echo '
				<a class="pagin" href="index.php?p=' . $page . '&pagination&cp=0">Første</a>
				';
			}

			$displayNumPages = floor($numrows / $rowsperpage);
			$displayLastPage = $displayNumPages;

			for ($i = ($currPage - 3); $i < ($currPage + 3); $i++)
			{
				if ($i >= 0 && ($i < ($displayLastPage + 1)))
				{
					if ($i == $currPage)
					{
						echo '
						<a class="pagin currpage">' . ($currPage + 1) . '</a>
						';
					} else
					{
						echo '
						<a class="pagin" href="index.php?p=' . $page . '&pagination&cp=' . $i . '">' . ($i + 1) . '</a>
						';
					}
				}
			}

			if ($currPage == $displayNumPages)
			{
				echo '
				<a class="pagin currpage">Siste</a>
				';
			} else
			{
			 	echo '
			 	<a class="pagin" href="index.php?p=' . $page . '&pagination&cp=' . $displayLastPage . '">Siste</a>
			 	';
			}
		}
	}

	function calculationResults($rowsperpage)
	{
		$cp = 0;
		if (isset($_GET["pagination"]) && (isset($_GET["cp"])))
		{
			if (is_numeric($_GET["cp"]))
			{
				$cp = round(escapeString($_GET["cp"]), 0);
			}
		}

		$returnArray = array(
			'min' => 0,
			'max' => 0
		);

		if ($cp < 0)
		{
			$returnArray['min'] = 0;
			$returnArray['max'] = 0;
		} else
		{
			$returnArray['min'] = $cp * $rowsperpage; 
			$returnArray['max'] = $rowsperpage;//(($cp + 1) * $rowsperpage);
		}

		return $returnArray;
	}	

	/*
		User methods
	*/
	function userExists($mode = 0, $input)
	{
		$string_getbyid = "SELECT `id` FROM `users` WHERE `id`='" . $input . "' LIMIT 0,1";
		$string_getbyname = "SELECT `id` FROM `users` WHERE `username`='" . $input . "' LIMIT 0,1";

		$sql = $mode == 0 ? $string_getbyid : $string_getbyname ;
		$result = qq($sql);
		$num_of_users = mysql_num_rows($result);

		if ($num_of_users <= 0)
		{
			return false;
		} else 
		{
			return true;
		}

	}

	function getUsername($id)
	{
		$sql = "SELECT `username` FROM `users` WHERE `id`='" . $id . "' LIMIT 0,1";
		$result = qq($sql);
		$num_of_users = mysql_num_rows($result);
		if ($num_of_users <= 0)
		{
			return false;
		}

		$data = mysql_fetch_object($result);
		return $data->username;
	}

	function getID($username)
	{
		$sql = "SELECT `id` FROM `users` WHERE `username`='" . $username . "' LIMIT 0,1";
		$result = qq($sql);
		$num_of_users = mysql_num_rows($result);
		if ($num_of_users <= 0)
		{
			return false;
		}

		$data = mysql_fetch_object($result);
		return $data->id;
	}
?>