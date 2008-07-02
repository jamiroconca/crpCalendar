/**
 * crpCalendar
 *
 * @copyright (c) 2007-2008, Daniele Conca
 * @link http://noc.postnuke.com/projects/crpcalendar Support and documentation
 * @author Daniele Conca <jami at cremonapalloza dot org>
 * @license GNU/GPL - v.2.1
 * @package crpCalendar
 */
function crpCalendarFormInit(){
	if ($('crpcalendar_day_event').checked == true) {
		$('end_date_block').addClassName('pn-hide');
	}
	
	Event.observe('crpcalendar_day_event', 'click', function(){
		hideDateFilter('crpcalendar_day_event', 'end_date_block')
	}, false);
}

function hideDateFilter(trigger, block){
	if ($(trigger).checked == true) {
		$(block).addClassName('pn-hide');
	}
	else {
		$(block).removeClassName('pn-hide');
	}
}

function crpCalendarAdminViewInit(){
	var real = $$('span.crp-status-real');
	
	real.each(function(node){
		node.addClassName('pn-hide');
	})
	
	var fake = $$('span.crp-status-fake');
	
	fake.each(function(node){
		node.removeClassName('pn-hide');
	})
}

function togglestatus(eventid, status){
	var pars = "module=crpCalendar&func=toggleStatus&eventid=" + eventid +
	"&status=" +
	status;
	var myAjax = new Ajax.Request("ajax.php", {
		method: 'get',
		parameters: pars,
		onComplete: togglestatus_response
	});
}

function togglestatus_response(req){
	if (req.status != 200) {
		pnshowajaxerror(req.responseText);
		return;
	}
	
	var jsonArray = pndejsonize(req.responseText);
	
	$('eventstatus_fake_A_' + jsonArray.eventid).toggle();
	$('eventstatus_fake_P_' + jsonArray.eventid).toggle();
}

function crpCalendarConfigInit(gd_version, locations_avail){
	if (gd_version < 2) {
		$('crpcalendar_use_gd').parentNode.remove();
		$('crpcalendar_use_browser').removeClassName('pn-hide');
	}
	if (!locations_avail) {
		$('crpcalendar_enable_locations').checked = false;
		$('crpcalendar_enable_locations').disabled = true;
	}
}

function crpCalendarContentLoad(){
	Event.observe('eventid_category', 'change', function(){
		category_event();
	}, false);
}

// 
function category_event(){
	var pars = "module=crpCalendar&func=getCategorizedEvent&" +
	'&category=' +
	$F('eventid_category');
	
	var myAjax = new Ajax.Request("ajax.php", {
		method: 'get',
		parameters: pars,
		onComplete: category_event_response
	});
}

function category_event_response(req){
	if (req.status != 200) {
		pnshowajaxerror(req.responseText);
		showinfo();
		return;
	}
	
	var eventSelect = $('contentEvent');
	
	var i;
	for (i = eventSelect.length - 1; i >= 0; i--) {
		eventSelect.remove(i);
	}
	
	var jsonArray = pndejsonize(req.responseText);
	
	for (i in jsonArray) {
		if (isNumeric(i)) {
			var optNew = document.createElement('option');
			optNew.text = jsonArray[i].name;
			optNew.value = jsonArray[i].id;
			try {
				eventSelect.add(optNew, null);
			} 
			catch (ex) {
				eventSelect.add(optNew);
			}
		}
	}
}

// key verification
function isNumeric(strString)//  check for valid numeric strings
{
	var strValidChars = "0123456789.-";
	var strChar;
	var blnResult = true;
	
	if (strString.length == 0) 
		return false;
	
	//  test strString consists of valid characters listed above
	for (k = 0; k < strString.length && blnResult == true; k++) {
		strChar = strString.charAt(k);
		if (strValidChars.indexOf(strChar) == -1) {
			blnResult = false;
		}
	}
	return blnResult;
}
