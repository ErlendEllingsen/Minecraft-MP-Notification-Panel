/*
	JS written by Erlend Ellingsen
*/

var d = new Date(2012, 11, 1, 18, 0, 0, 0); //december = 11
function counter()
{
	var counter = document.getElementById('date');
	var now = new Date();
	if (!(now.getTime() > d.getTime()))
	{
		var timeleft = ((Math.round((d.getTime() / 1000))) - (Math.round((now.getTime() / 1000))));

		var days = (((timeleft / 60) / 60) / 24);
		timeleft -= (Math.floor(days) * 60 * 60 * 24);

		var hours = ((timeleft / 60) / 60);
		timeleft -= (Math.floor(hours) * 60 * 60);

		var minutes = (timeleft / 60);
		timeleft -= (Math.floor(minutes) * 60);

		var seconds = timeleft;
		counter.innerHTML = Math.floor(days) + "<span style=\'font-weight: bold;\'>d</span> " + Math.floor(hours) + "<span style=\'font-weight: bold;\'>t</span> " + Math.floor(minutes) + "<span style=\'font-weight: bold;\'>m</span> " + Math.floor(seconds) + "<span style=\'font-weight: bold;\'>s</span>";
	} else 
	{
		counter.style.color = "green";
		counter.innerHTML = "XioCo har åpnet!!!";
	}

	
}

/*function setProperCSS()
{
	var useragent = navigator.userAgent;
	if (useragent.indexOf('Opera') != -1)
	{
		document.getElementById('extralinks').style.margin = "10px 0px 10px 0px";
	}
}*/