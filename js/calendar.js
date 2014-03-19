window.onload = function(){
	setInterval(sitemanager, 1000); 
	setupSystem();

	new JsDatePick({
		useMode:2,
		target:"tsStart",
		dateFormat:"%d/%m/%Y"
		/*selectedDate:{				This is an example of what the full configuration offers.
			day:5,						For full documentation about these settings please see the full version of the code.
			month:9,
			year:2006
		},
		yearsRange:[1978,2020],
		limitToToday:false,
		cellColorScheme:"beige",
		dateFormat:"%m-%d-%Y",
		imgPath:"img/",
		weekStartDay:1*/
	});
	new JsDatePick({
		useMode:2,
		target:"tsStop",
		dateFormat:"%d/%m/%Y"
		/*selectedDate:{				This is an example of what the full configuration offers.
			day:5,						For full documentation about these settings please see the full version of the code.
			month:9,
			year:2006
		},
		yearsRange:[1978,2020],
		limitToToday:false,
		cellColorScheme:"beige",
		dateFormat:"%m-%d-%Y",
		imgPath:"img/",
		weekStartDay:1*/
	});
};