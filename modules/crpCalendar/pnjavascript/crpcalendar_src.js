/**
 * crpCalendar
 *
 * @copyright (c) 2007,2009 Daniele Conca
 * @link http://code.zikula.org/crpcalendar Support and documentation
 * @author Daniele Conca <conca dot daniele at gmail dot com>
 * @license GNU/GPL - v.2.1
 * @package crpCalendar
 */
function crpCalendarFormInit() {
  if ($('crpcalendar_day_event').checked == true) {
    $('end_date_block').addClassName('z-hide');
  }

  Event.observe('crpcalendar_day_event', 'click', function() {
    hideDateFilter('crpcalendar_day_event', 'end_date_block')
  }, false);

  datePickerController.createDatePicker( {
    formElements : {
      // "inp3" represents the year part
      "start_datefield" : "y",
      // "inp1" represents the day part
      "start_datefield-dd" : "d",
      // "inp2" represents the month part
      "start_datefield-mm" : "m"
    },
    highlightDays : [ 0, 0, 0, 0, 0, 1, 1 ],
    noFadeEffect : true,
    finalOpacity : 100,
    showWeeks : true,
    noDrag : true
  });

  datePickerController.createDatePicker( {
    formElements : {
      // "inp3" represents the year part
      "end_datefield" : "y",
      // "inp1" represents the day part
      "end_datefield-dd" : "d",
      // "inp2" represents the month part
      "end_datefield-mm" : "m"
    },
    highlightDays : [ 0, 0, 0, 0, 0, 1, 1 ],
    noFadeEffect : true,
    finalOpacity : 100,
    showWeeks : true,
    noDrag : true
  });
}

function crpCalendarPurgeInit() {
  datePickerController.createDatePicker( {
    formElements : {
      // "inp3" represents the year part
      "end_datefield" : "y",
      // "inp1" represents the day part
      "end_datefield-dd" : "d",
      // "inp2" represents the month part
      "end_datefield-mm" : "m"
    },
    highlightDays : [ 0, 0, 0, 0, 0, 1, 1 ],
    noFadeEffect : true,
    finalOpacity : 100,
    showWeeks : true,
    noDrag : true
  });
}

function crpCalendarFilterInit() {
  datePickerController.createDatePicker( {
    formElements : {
      // "inp3" represents the year part
      "start_datefield" : "y",
      // "inp1" represents the day part
      "start_datefield-dd" : "d",
      // "inp2" represents the month part
      "start_datefield-mm" : "m"
    },
    highlightDays : [ 0, 0, 0, 0, 0, 1, 1 ],
    noFadeEffect : true,
    finalOpacity : 100,
    showWeeks : true,
    noDrag : true
  });
}

function hideDateFilter(trigger, block) {
  if ($(trigger).checked == true) {
    $(block).addClassName('z-hide');
  } else {
    $(block).removeClassName('z-hide');
  }
}

function crpCalendarAdminViewInit() {
  var real = $$('span.crp-status-real');

  real.each( function(node) {
    node.addClassName('z-hide');
  })

  var fake = $$('span.crp-status-fake');

  fake.each( function(node) {
    node.removeClassName('z-hide');
  })
}

function togglestatus(eventid, status) {
  var pars = "module=crpCalendar&func=toggleStatus&eventid=" + eventid
  + "&status=" + status;
  var myAjax = new Ajax.Request("ajax.php", {
    method : 'get',
    parameters : pars,
    onComplete : togglestatus_response
  });
}

function togglestatus_response(req) {
  if (req.status != 200) {
    pnshowajaxerror(req.responseText);
    return;
  }

  var jsonArray = pndejsonize(req.responseText);

  $('eventstatus_fake_A_' + jsonArray.eventid).toggle();
  $('eventstatus_fake_P_' + jsonArray.eventid).toggle();
}

function crpCalendarConfigInit(gd_version, locations_avail, formicula_avail) {
  if (gd_version < 2) {
    $('crpcalendar_use_gd').parentNode.remove();
    $('crpcalendar_use_browser').removeClassName('z-hide');
  }
  if (!locations_avail) {
    $('crpcalendar_enable_locations').checked = false;
    $('crpcalendar_enable_locations').disabled = true;
  }
  if (!formicula_avail) {
    $('crpcalendar_enable_formicula').checked = false;
    $('crpcalendar_enable_formicula').disabled = true;
  }
}

function crpCalendarContentLoad() {
  Event.observe('eventid_category', 'change', function() {
    category_event();
  }, false);
}

//
function category_event() {
  var pars = "module=crpCalendar&func=getCategorizedEvent&" + '&category='
  + $F('eventid_category');

  var myAjax = new Ajax.Request("ajax.php", {
    method : 'get',
    parameters : pars,
    onComplete : category_event_response
  });
}

function category_event_response(req) {
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
      optNew.text = jsonArray[i].start_date + ' - ' + jsonArray[i].name;
      optNew.value = jsonArray[i].id;
      try {
        eventSelect.add(optNew, null);
      } catch (ex) {
        eventSelect.add(optNew);
      }
    }
  }
}

// key verification
function isNumeric(strString)// check for valid numeric strings
{
  var strValidChars = "0123456789.-";
  var strChar;
  var blnResult = true;

  if (strString.length == 0)
    return false;

  // test strString consists of valid characters listed above
  for (k = 0; k < strString.length && blnResult == true; k++) {
    strChar = strString.charAt(k);
    if (strValidChars.indexOf(strChar) == -1) {
      blnResult = false;
    }
  }
  return blnResult;
}

function addDynamicTextRow(propId) {
  var numChild = $(propId).immediateDescendants().size();
  if ($(propId).hasClassName('disabledfield')) {
    $(propId).removeClassName('disabledfield');
    $('dynamic_datefield-dd').disabled = false;
    $('dynamic_datefield-mm').disabled = false;
    $('dynamic_datefield').disabled = false;
    $('dynamic_timefield-hh').disabled = false;
    $('dynamic_timefield-mm').disabled = false;
    $('dynamic_datefield-dd').name = 'serial[startDay][]';
    $('dynamic_datefield-mm').name = 'serial[startMonth][]';
    $('dynamic_datefield').name = 'serial[startYear][]';
    $('dynamic_timefield-hh').name = 'serial[startHour][]';
    $('dynamic_timefield-mm').name = 'serial[startMinute][]';
  } else {
    var newSlot = $('slot_' + propId).cloneNode(true);
    $(propId).appendChild(newSlot);
  }
}

function removeDynamicTextRow(propId) {
  var numChild = $(propId).immediateDescendants().size();
  if (numChild > 1)
    $(propId).removeChild($(propId).lastChild);
  else {
    $('dynamic_datefield-dd').disabled = true;
    $('dynamic_datefield-mm').disabled = true;
    $('dynamic_datefield').disabled = true;
    $('dynamic_timefield-hh').disabled = true;
    $('dynamic_timefield-mm').disabled = true;
    $(propId).addClassName('disabledfield');
  }
}