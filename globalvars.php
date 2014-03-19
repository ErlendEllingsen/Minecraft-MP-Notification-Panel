<?php
	/*
		All code in this script (including ALL files inside this directory) is property of Erlend Ellingsen.
		Do not copy any code without permission from Erlend Ellingsen.
	*/
	$officalVersions = array(
		'http://213.166.171.78/panel/' => true,
		'http://xioco.gameadvise.no/panel/' => true
	);

	//system
	
	$availablePaths = array(
		'xioco.hostingservice.no' => 'http://213.166.171.78/panel/',
		'Erlend-PC' => 'http://localhost/erlend/xioco_admin/'
	);

	
	define('PATH', (isset($availablePaths[constant('HOSTNAME')]) ? $availablePaths[constant('HOSTNAME')] : $availablePaths['xioco.hostingservice.no']));

	$path = constant('PATH'); //with trailing slash - DEPRECATED
	$noteTypes = array('note', 'kick', 'warning', 'ban', 'tempban', 'unban');
	

	define('USER_PANEL_LOGIN_REQUIREMENT_LEVEL', 2);

	//API
	define('API_PASSWORD', 'spekEcr2');


	//user vars, userrank: 0-5
	$accessLevels = array('Spiller', 'Sponsor', 'Hjelper', 'Vakt', 'Admin', 'Eier', 'Utvikler'); $accessLevelColors = array('#000', '#7D09CF', '#02A905', '#093CBD', '#DF9A22', '#DFDF22', '#DF2222');
	$accessLevelValues = array(
		'spiller' => 0,
		'sponsor' => 1,
		'hjelper' => 2,
		'vakt' => 3,
		'admin' => 4,
		'eier' => 5,
		'utvikler' => 6
		);
?>