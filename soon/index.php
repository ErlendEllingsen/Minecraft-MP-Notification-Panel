<?php

	if (!file_exists('config'))
	{
		echo '
		Informasjon kommer snart.
		';
		return;
		die;
	}

	$results = fread(fopen('config', 'r'), filesize('config'));
	if (strstr($results, '[status=open]') == false)
	{
		echo '
		Informasjon kommer snart.
		';
		return;
		die;
	}
	echo '
	<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<link href="xioco.css" rel="stylesheet" type="text/css">
			<title>XioCo gjenåpning</title>
			<script type="text/javascript" src="xioco.js"></script>
		</head>
		<body onLoad="setInterval(counter, 900); counter();">
		<!--[if lt IE 7]>
			<div style="width:100%; text-align:center; background-color:#ffffff; color:#000000">Vi anbefaler deg å oppgradere nettleseren din, det kan du gjøre på <a href="http://www.updatebrowser.net/" target="_blank">Updatebrowser.net</a>. Internet Explorer 6 og eldre nettlesere er ikke lenger støttet på de fleste nettsider.</div>
		<![endif]-->
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/nb_NO/all.js#xfbml=1&appId=114021425368592";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, \'script\', \'facebook-jssdk\'));
			</script>
			<div id="imageObject">
				<div id="titleContainer">
					<img src="images/logo/newlogo_test.png">
					<h2>XioCo gjenåpning 1. desember 2012 18:00</h2>
					<br />
					<span id="date"></span>
					<h2 id="date">Server ip: Xioco.gameadvise.no</h2>
				</div>
			</div>
			<div id="centerfb">
				<div class="fb-like-box" data-href="http://www.facebook.com/xioco.no" data-width="370" data-height="100" data-colorscheme="dark" data-show-faces="false" data-border-color="CCC" data-stream="false" data-header="false" style="float: left;"></div>
				<p id="extralinks" style="float: left;"><a href="http://xioco.no/faq/">Les mer</a> <a href="https://www.facebook.com/xioco.no">Facebook</a> <a href="https://twitter.com/Addexio">Twitter</a> <a href="https://www.youtube.com/addexio">Addexio</a></p>
				<div class="clear: both;"></div>
			</div>
		</body>
	</html>

	';

?>