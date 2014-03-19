/*
	All code in this script (including ALL files inside this directory) is property of Erlend Ellingsen.
	Script written for use on 'XioCo' and 'XioCo' ONLY. 

	Do not copy any code without permission from Erlend Ellingsen.
*/
var user_id = -1;
var system_path = null;

var timer_getnotifications = 5;
var timer_getnotifications_textblinking = false;
var timer_getnotifications_textblinking_timer = 0;


var nav_target;


/*
	EXTERNAL FUNCTIONS
*/
function is_numeric(input){
    return typeof(input)=='number';
}

function form_input_is_numeric(input){
	return !isNaN(input);
}



function navTo()
{
	window.location = nav_target;
}

function navigate(page, ms)
{
	nav_target = page;
	setTimeout("navTo()", ms);
}

function sitemanager()
{
	if (timer_getnotifications <= 0)
	{
		getNotifications();
		timer_getnotifications = 60;
	} else 
	{
		timer_getnotifications--;
	}

	if (timer_getnotifications_textblinking)
	{
		if (timer_getnotifications_textblinking_timer <= 0)
		{
			var currNotificaitonCounter = document.getElementById('newnotifications');
			currNotificaitonCounter.style.textDecoration = "none";
			timer_getnotifications_textblinking = false;
		} else 
		{
			timer_getnotifications_textblinking_timer--;
		}
	}
}

function getNotifications()
{
	try 
	{
		console.log('get notifications');
		var currNotificaitonCounter = document.getElementById('newnotifications');
		
		if (user_id == -1)
		{
			console.log('error invalid user');
			notificationCounterError(currNotificaitonCounter, 'invaliduser');
			return;
		}

		var request = new XMLHttpRequest();
		request.open('GET', system_path + 'js/api/notifications.php?userid=' + user_id, false);
		request.send();

		console.log('notifications api code: ' + request.status);
		 
		if (request.status != 200) {
			notificationCounterError(currNotificaitonCounter, request.status);
			console.log('request error, content: ' + request.responseText + " status code: " + request.status)
			return;
		}

		var unreadnotifications = request.responseText;
		
		if (unreadnotifications.indexOf("[status:") != -1)
		{
			var errorcode = unreadnotifications; errorcode = errorcode.replace("[status:", ""); errorcode = errorcode.replace("]", "");
			notificationCounterError(currNotificaitonCounter, 'invalid status (' + errorcode + ')');
			return;
		}

		if (!form_input_is_numeric(unreadnotifications))
		{
			notificationCounterError(currNotificaitonCounter, 'notnumeric');
			return;
		}

		unreadnotifications = Math.floor(unreadnotifications);
		if (unreadnotifications <= 0)
		{
			currNotificaitonCounter.innerHTML = unreadnotifications;
			currNotificaitonCounter.style.color = "#D2D2D2";
			currNotificaitonCounter.style.textDecoration = "none";
		} else 
		{
			currNotificaitonCounter.innerHTML = unreadnotifications;
			currNotificaitonCounter.style.color = "yellow";
			currNotificaitonCounter.style.textDecoration = "blink";

			timer_getnotifications_textblinking = true;
			timer_getnotifications_textblinking_timer = 10;
		}

	} catch (err)
	{
		console.log('error exception: ' + err);
		notificationCounterError(currNotificaitonCounter, 'exception');
		return;
	}
}

function notificationCounterError(currNotificaitonCounter, specmsg)
{
	console.log('error: ' + specmsg);
	specmsg = typeof specmsg !== 'undefined' ? specmsg : '0';
	currNotificaitonCounter.innerHTML = "Error (" + specmsg + ")";
	currNotificaitonCounter.style.color = "red";
}