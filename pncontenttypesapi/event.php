<?php
/**
 * crpCalendar
 *
 * @copyright (c) 2007-2008, Daniele Conca
 * @link http://code.zikula.org/projects/crpcalendar Support and documentation
 * @author Daniele Conca <conca.daniele@gmail.com>
 * @license GNU/GPL - v.2.1
 * @package crpCalendar
 */

/**
 * class for interaction with Content module
 */
class crpCalendar_contenttypesapi_eventPlugin extends contentTypeBase
{
  var $eventid;

  function getModule() { return 'crpCalendar'; }
  function getName() { return 'event'; }
  function getTitle() { return _CRPCALENDAR_CONTENTENTTYPE_EVENTTITLE; }
  function getDescription() { return _CRPCALENDAR_CONTENTENTTYPE_EVENTDESCR; }


  function loadData($data)
  {
    $this->eventid = $data['eventid'];
  }


  function display()
  {
    if (!empty($this->eventid))
      return pnModFunc('crpCalendar', 'user', 'simple_display', array('eventid' => $this->eventid));
    return '';
  }


  function displayEditing()
  {
    if (!empty($this->eventid))
    {
      $event = pnModAPIFunc('crpCalendar', 'user', 'get', array('eventid' => $this->eventid));
      return '<strong>'.$event['title'].'</strong><br />'.substr($event['event_text'],0,255).((strlen($event['event_text'])>255)?'...':'');
    }
    return _CRPCALENDAR_CONTENTENTTYPE_NOEVENT;
  }


  function getDefaultData()
  {
    return array('eventid' => null);
  }


  function startEditing(&$render)
  {
    array_push($render->plugins_dir, 'modules/crpCalendar/pntemplates/pnform');
  }
}


function crpCalendar_contenttypesapi_event($args)
{
  return new crpCalendar_contenttypesapi_eventPlugin();
}

?>
