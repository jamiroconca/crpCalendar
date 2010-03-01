<?php

/**
 * crpCalendar
 *
 * @copyright (c) 2007,2009 Daniele Conca
 * @link http://code.zikula.org/crpcalendar Support and documentation
 * @author Daniele Conca <conca.daniele@gmail.com>
 * @license GNU/GPL - v.2.1
 * @package crpCalendar
 */

/**
 * crpCalendarDAO
 */
class crpCalendarDAO
{

/**
 * constructor, set values
 */
  function crpCalendarDAO()
  {
  // images allowed
    $this->ImageTypes[] = 'image/gif';
    $this->ImageTypes[] = 'image/jpeg';
    $this->ImageTypes[] = 'image/pjpeg';
    $this->ImageTypes[] = 'image/png';
  }

  function getAuth($level = 0, $author_object = '', $id_object = '', $name_object = '')
  {
    $result = SecurityUtil :: checkPermission("crpCalendar::", "$author_object:$name_object:$id_object", $level);
    return $result;
  }

  /**
   * return administrative list of events
   *
   * @param int $startnum pager offset
   * @param int $category current category if specified
   * @param bool $ignoreml ignore multilanguage
   * @param array $modvars module's variables
   * @param int $mainCat main module's category
   * @param int $active event status
   * @param int $interval event date interval
   * @param string $sortOrder results sort order
   * @param string $startDate start date filter
   * @param string $endDate end date filter
   * @param string $typeList output type
   * @param string $bylocation location filter
   * @param string $sortColumn which column for list order
   *
   * @return array events list
   */
  function adminList($startnum = 1, $category = null, $clear = false, $ignoreml = true, $modvars = array (), $mainCat = null, $active = null, $interval = null, $sortOrder = 'DESC', $startDate = null, $endDate = null, $typeList = null, $bylocation = null, $sortColumn = 'start_date')
  {
    (empty ($startnum)) ? $startnum = 1 : '';
    (empty ($modvars['itemsperpage'])) ? $modvars['itemsperpage'] = '-1' : '';

    if (!is_numeric($startnum) || !is_numeric($modvars['itemsperpage']))
    {
      return LogUtil :: registerError(__('Error! Could not do what you wanted. Please check your input.', $this->dom));
    }

    $catFilter = array ();
    if (is_array($category))
      $catFilter = $category;
    elseif ($category)
    {
      if ($modvars['subcategory_listing'])
      {
        $catstofilter[] = $category;
        // TODO : Zikula 1.1.1 fail to load CategoryUtil from blocks ?
        Loader::loadClass ('CategoryUtil');
        $childs = CategoryUtil::getSubCategories($category);
        // TODO : attributes param is commented ?
        foreach ($childs as $child)
          $catstofilter[] = $child['id'];

        $catFilter['Main'] = $catstofilter;
      }
      else
        $catFilter['Main'] = $category;

      $catFilter['__META__']['module'] = 'crpCalendar';
    }

    $items = array ();
    $nowDate = DateUtil :: getDatetime();

    // Security check
    if (!$this->getAuth(ACCESS_READ))
    {
      return $items;
    }
    if (!$this->getAuth(ACCESS_ADD) && $active)
    { // userapi should not query pending or rejected events
      $active = 'A';
    }

    $pntable = pnDBGetTables();
    $crpcalendarcolumn = $pntable['crpcalendar_column'];
    $queryargs = array ();

    if (pnConfigGetVar('multilingual') == 1 && !$ignoreml)
    {
      $queryargs[] = "($crpcalendarcolumn[language]='" . DataUtil :: formatForStore(ZLanguage::getLanguageCode()) . "' " .
          "OR $crpcalendarcolumn[language]='')";
    }

    if ($active)
    {
      $queryargs[] = "($crpcalendarcolumn[obj_status]='" . DataUtil :: formatForStore($active) . "')";
    }

    if ($interval)
    {
      $intervaltime = time() + $interval * 86400;
      $intervalDate = DateUtil :: getDatetime($intervaltime);
      $queryargs[] = "(($crpcalendarcolumn[start_date] > '" . DataUtil :: formatForStore($nowDate) . "' " .
          "AND $crpcalendarcolumn[start_date] < '" . DataUtil :: formatForStore($intervalDate) . "') " .
          "OR ($crpcalendarcolumn[end_date] > '" . DataUtil :: formatForStore($nowDate) . "' " .
          "AND $crpcalendarcolumn[end_date] < '" . DataUtil :: formatForStore($intervalDate) . "')" .
          "OR ($crpcalendarcolumn[start_date] < '" . DataUtil :: formatForStore($nowDate) . "' " .
          "AND $crpcalendarcolumn[end_date] > '" . DataUtil :: formatForStore($intervalDate) . "'))";
    }

    if ($startDate && $endDate)
    {
      $queryargs[] = "( (($crpcalendarcolumn[start_date] >= '" . DataUtil :: formatForStore($startDate) . "' AND $crpcalendarcolumn[start_date] < '" . DataUtil :: formatForStore($endDate) . "') " .
          "OR ($crpcalendarcolumn[end_date] > '" . DataUtil :: formatForStore($startDate) . "' AND $crpcalendarcolumn[end_date] < '" . DataUtil :: formatForStore($endDate) . "')) " .
          "OR (('" . DataUtil :: formatForStore($startDate) . "' >= $crpcalendarcolumn[start_date] AND '" . DataUtil :: formatForStore($startDate) . "' < $crpcalendarcolumn[end_date]) " .
          "AND ('" . DataUtil :: formatForStore($endDate) . "' > $crpcalendarcolumn[start_date] AND '" . DataUtil :: formatForStore($endDate) . "' < $crpcalendarcolumn[end_date])) )";
    }

    switch ($typeList)
    {
      case "upcoming" :
        $queryargs[] = "($crpcalendarcolumn[start_date] > '" . DataUtil :: formatForStore($nowDate) . "')";
        break;
      case "archive" :
        $queryargs[] = "($crpcalendarcolumn[start_date] <= '" . DataUtil :: formatForStore($nowDate) . "')";
        break;
      default :
        break;
    }

    if ($bylocation)
    {
      $queryargs[] = "($crpcalendarcolumn[location] LIKE '" . DataUtil :: formatForStore($bylocation) . "')";
    }

    $where = null;
    if (count($queryargs) > 0)
    {
      $where = ' WHERE ' . implode(' AND ', $queryargs);
    }

    // define the permission filter to apply
    $permFilter = array (
        array (
        'realm' => 0,
        'component_left' => 'crpCalendar',
        'component_right' => 'Event',
        'instance_left' => 'cr_uid',
        'instance_center' => 'title',
        'instance_right' => 'eventid',
        'level' => ACCESS_READ
        )
    );

    $orderby = "ORDER BY " . $crpcalendarcolumn[$sortColumn] . " $sortOrder";

    // get the objects from the db
    $objArray = DBUtil :: selectObjectArray('crpcalendar', $where, $orderby, $startnum -1, $modvars['itemsperpage'], '', $permFilter, $catFilter);

    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($objArray === false)
    {
      return LogUtil :: registerError(__('Error! Could not load items.', $this->dom));
    }

    // need to do this here as the category expansion code can't know the
    // root category which we need to build the relative path component
    if ($objArray && isset ($mainCat) && $mainCat)
    {
      if (!Loader :: loadClass('CategoryUtil'))
      {
        pn_exit('Unable to load class [CategoryUtil]');
      }
      ObjectUtil :: postProcessExpandedObjectArrayCategories($objArray, $mainCat);
    }

    if ($modvars['crpcalendar_userlist_image'])
    {
      foreach ($objArray as $kObj => $vObj)
        $objArray[$kObj]['image'] = $this->getFile($vObj['eventid'], 'image');
    }

    // Return the items
    return $objArray;
  }

  /**
   * return form list of events
   *
   * @param int $startnum pager offset
   * @param int $category current category if specified
   * @param bool $ignoreml ignore multilanguage
   * @param array $modvars module's variables
   * @param int $active event status
   * @param int $interval event date interval
   * @param string $sortOrder results sort order
   *
   * @return array element list
   */
  function formList($startnum = 1, $category = null, $clear = false, $ignoreml = true, $modvars = array (), $active = null, $interval = null, $sortOrder = 'DESC')
  {
    if (!is_numeric($startnum) || !is_numeric($modvars['itemsperpage']))
    {
      return LogUtil :: registerError(__('Error! Could not do what you wanted. Please check your input.', $this->dom));
    }

    $catFilter = array ();
    if (is_array($category))
      $catFilter = $category;
    else
      if ($category)
      {
        if ($modvars['subcategory_listing'])
        {
          $catstofilter[] = $category;
          // TODO : Zikula 1.1.1 fail to load CategoryUtil from blocks ?
          Loader::loadClass ('CategoryUtil');
          $childs = CategoryUtil::getSubCategories($category);
          // TODO : attributes param is commented ?
          foreach ($childs as $child)
            $catstofilter[] = $child['id'];

          $catFilter['Main'] = $catstofilter;
        }
        else
          $catFilter['Main'] = $category;

        $catFilter['__META__']['module'] = 'crpCalendar';
      }

    $items = array ();
    $nowDate = DateUtil :: getDatetime();

    // Security check
    if (!$this->getAuth(ACCESS_READ))
    {
      return $items;
    }

    $pntable = pnDBGetTables();
    $crpcalendarcolumn = $pntable['crpcalendar_column'];
    $queryargs = array ();
    if (pnConfigGetVar('multilingual') == 1 && !$ignoreml)
    {
      $queryargs[] = "($crpcalendarcolumn[language]='" . DataUtil :: formatForStore(ZLanguage::getLanguageCode()) . "' " .
          "OR $crpcalendarcolumn[language]='')";
    }

    if ($active)
    {
      $queryargs[] = "($crpcalendarcolumn[obj_status]='" . DataUtil :: formatForStore($active) . "')";
    }

    if ($interval)
    {
      $intervaltime = time() + $interval * 86400;
      $intervalDate = DateUtil :: getDatetime($intervaltime);
      $queryargs[] = "(($crpcalendarcolumn[start_date] > '" . DataUtil :: formatForStore($nowDate) . "' " .
          "AND $crpcalendarcolumn[start_date] < '" . DataUtil :: formatForStore($intervalDate) . "') " .
          "OR ($crpcalendarcolumn[end_date] > '" . DataUtil :: formatForStore($nowDate) . "' " .
          "AND $crpcalendarcolumn[end_date] < '" . DataUtil :: formatForStore($intervalDate) . "'))";
    }

    $where = null;
    if (count($queryargs) > 0)
    {
      $where = ' WHERE ' . implode(' AND ', $queryargs);
    }

    // define the permission filter to apply
    $permFilter = array (
        array (
        'realm' => 0,
        'component_left' => 'crpCalendar',
        'component_right' => 'Event',
        'instance_left' => 'cr_uid',
        'instance_center' => 'title',
        'instance_right' => 'eventid',
        'level' => ACCESS_READ
        )
    );

    $orderby = "ORDER BY $crpcalendarcolumn[start_date] $sortOrder";

    $columnArray = array (
        'eventid',
        'start_date',
        'title'
    );

    // get the objects from the db
    $objArray = DBUtil :: selectObjectArray('crpcalendar', $where, $orderby, $startnum -1, -1, '', $permFilter, $catFilter, $columnArray);

    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($objArray === false)
    {
      return LogUtil :: registerError(__('Error! Could not load items.', $this->dom));
    }

    foreach ($objArray as $kObj => $vObj)
    {
      $formArray[] = array (
          'id' => $vObj['eventid'],
          'start_date' => DateUtil :: formatDatetime($vObj['start_date'],$modvars['only_date_format']),
          'name' => $vObj['title']
      );
    }

    // Return the items
    return $formArray;
  }

  /**
   * get a specific admin item data
   *
   * @param int $eventid item identifier
   * @param bool $extend add image and document if true
   *
   * @return array item value
   */
  function getAdminData($eventid = null, $extend = true, $title = null)
  {
  // define the permission filter to apply
    $permFilter = array (
        array (
        'realm' => 0,
        'component_left' => 'crpCalendar',
        'component_right' => 'Event',
        'instance_left' => 'cr_uid',
        'instance_center' => 'title',
        'instance_right' => 'eventid',
        'level' => ACCESS_READ
        )
    );

    if (isset ($eventid) && is_numeric($eventid))
      $object = DBUtil :: selectObjectByID('crpcalendar', $eventid, 'eventid', '', $permFilter);
    elseif ($title) $object = DBUtil :: selectObjectByID('crpcalendar', $title, 'urltitle', '', $permFilter);

    if ($extend && $object)
    {
      $object['image'] = $this->getFile($eventid, 'image');
      $object['document'] = $this->getFile($eventid, 'document');
    }
    // load the category registry util
    if (!($class = Loader :: loadClass('CategoryRegistryUtil')))
      pn_exit('Unable to load class [CategoryRegistryUtil] ...');

    $mainCat = CategoryRegistryUtil :: getRegisteredModuleCategory('crpCalendar', 'crpcalendar', 'Main');

    if (pnModGetVar('crpCalendar', 'enablecategorization') && !empty ($object['__CATEGORIES__']))
    {
      ObjectUtil :: postProcessExpandedObjectCategories($object['__CATEGORIES__'], $mainCat);
    }

    return $object;
  }

  /**
   * get a specific admin lite data
   *
   * @param int $eventid item identifier
   *
   * @return string item value
   */
  function isAuthor($eventid = null)
  {
    $pntable = pnDBGetTables();
    $crpcalendarcolumn = $pntable['crpcalendar_column'];

    $queryargs[] = "($crpcalendarcolumn[eventid] = '" . DataUtil :: formatForStore($eventid) . "' " .
        "AND $crpcalendarcolumn[cr_uid] = '" . DataUtil :: formatForStore(pnUserGetVar('uid')) . "')";

    $columnArray = array (
        'eventid',
        'cr_uid'
    );

    $where = null;
    if (count($queryargs) > 0)
    {
      $where = ' WHERE ' . implode(' AND ', $queryargs);
    }

    $item = DBUtil :: selectObject('crpcalendar', $where, $columnArray);

    $author = false;
    ($item['cr_uid']) ? $author = true : $author = false;

    return $author;
  }

  /**
   * get a specific event date
   *
   * @param int $eventid item identifier
   * @param int $dateType date type
   *
   * @return string item value
   */
  function getEventDate($eventid = null, $dateType = null)
  {
    $pntable = pnDBGetTables();
    $crpcalendarcolumn = $pntable['crpcalendar_column'];

    $queryargs[] = "($crpcalendarcolumn[eventid] = '" . DataUtil :: formatForStore($eventid) . "')";

    $columnArray = array (
        'eventid',
        '' . $dateType . ''
    );

    $where = null;
    if (count($queryargs) > 0)
    {
      $where = ' WHERE ' . implode(' AND ', $queryargs);
    }

    $item = DBUtil :: selectObject('crpcalendar', $where, $columnArray);

    $dateValue = false;
    ($item[$dateType]) ? $dateValue = $item[$dateType] : $author = false;

    return $dateValue;
  }

  /**
   * create item details
   *
   * @param array $inputValues array of new values
   *
   * @return bool true if success
   */
  function create($inputValues = array ())
  {
  // Argument check
    if (!$this->validateData($inputValues))
      return false;

    // define the permalink title if not present
    if (!isset ($inputValues['event']['urltitle']) || empty ($inputValues['event']['urltitle']))
      $inputValues['event']['urltitle'] = DataUtil :: formatPermalink($inputValues['event']['title']);

    // set some defaults
    if (!isset ($inputValues['event']['language']))
    {
      $inputValues['event']['language'] = '';
    }
    if (!$this->getAuth(ACCESS_EDIT))
      $inputValues['event']['obj_status'] = $inputValues['modvars']['submitted_status'];

    if (pnModAvailable('locations') && pnModGetVar('crpCalendar', 'enable_locations') && $inputValues['event']['locations'])
      $inputValues['event']['location'] = $inputValues['event']['locations'];
    // no more needed
    unset ($inputValues['event']['locations']);

    $object = DBUtil :: insertObject($inputValues['event'], 'crpcalendar', 'eventid');
    if (!$object)
    {
      LogUtil :: registerError(__('Error! Creation attempt failed.', $this->dom));
      return false;
    }

    if (isset ($inputValues['event_image']) && ($inputValues['event_image']['error'] == UPLOAD_ERR_OK))
    {
      $inputValues['event_image']['eventid'] = $object['eventid'];
      $inputValues['event_image']['document_type'] = 'image';
      $id_image = $this->setFile($inputValues['event_image']);
      if ($id_image == '-1')
        return false;
    }

    if (isset ($inputValues['event_document']) && ($inputValues['event_document']['error'] == UPLOAD_ERR_OK))
    {
      $inputValues['event_document']['eventid'] = $object['eventid'];
      $inputValues['event_document']['document_type'] = 'document';
      $id_document = $this->setFile($inputValues['event_document']);
      if ($id_document == '-1')
        return false;
    }

    // Let any other modules know we have created an item
    pnModCallHooks('item', 'create', $object['eventid'], array (
        'module' => 'crpCalendar'
    ));

    return $object['eventid'];
  }

  /**
   * update item details
   *
   * @param array $inputValues array of new values
   *
   * @return bool true if success
   */
  function update($inputValues = array ())
  {
  // Argument check
    if (!$this->validateData($inputValues))
      return false;

    // define the permalink title if not present
    if (!isset ($inputValues['event']['urltitle']) || empty ($inputValues['event']['urltitle']))
      $inputValues['event']['urltitle'] = DataUtil :: formatPermalink($inputValues['event']['title']);

    if (pnModAvailable('locations') && pnModGetVar('crpCalendar', 'enable_locations') && $inputValues['event']['locations'])
      $inputValues['event']['location'] = $inputValues['event']['locations'];
    // no more needed
    unset ($inputValues['event']['locations']);

    // Check page to update exists, and get information for
    // security check
    $item = $this->getAdminData($inputValues['eventid']);

    if ($item == false)
    {
      LogUtil :: registerError(__('No such item found.', $this->dom));
      return false;
    }

    // set some defaults
    if (!isset ($inputValues['event']['language']))
    {
      $inputValues['event']['language'] = '';
    }

    if (!DBUtil :: updateObject($inputValues['event'], 'crpcalendar', '', 'eventid'))
    {
      LogUtil :: registerError(__('Error! Update attempt failed.', $this->dom));
      return false;
    }

    if (isset ($inputValues['event_image']) && ($inputValues['event_image']['error'] == UPLOAD_ERR_OK))
    {
      $inputValues['event_image']['eventid'] = $inputValues['event']['eventid'];
      $inputValues['event_image']['document_type'] = 'image';
      $id_image = $this->setFile($inputValues['event_image']);
      if ($id_image == '-1')
        return false;
    }

    if (isset ($inputValues['event_document']) && ($inputValues['event_document']['error'] == UPLOAD_ERR_OK))
    {
      $inputValues['event_document']['eventid'] = $inputValues['eventid'];
      $inputValues['event_document']['document_type'] = 'document';
      $id_document = $this->setFile($inputValues['event_document']);
      if ($id_document == '-1')
        return false;
    }

    // Let any other modules know we have updated an item
    pnModCallHooks('item', 'update', $inputValues['eventid'], array (
        'module' => 'crpCalendar'
    ));

    // The item has been modified, so we clear all cached pages of this item.
    $render = new pnRender('crpCalendar');
    $render->clear_cache(null, $inputValues['eventid']);

    return true;
  }

  /**
   * update item details
   *
   * @param array $inputValues array of new values
   *
   * @return bool true if success
   */
  function cloneEvent($inputValues = array ())
  {
  // define the permalink title if not present
    if (!isset ($inputValues['event']['urltitle']) || empty ($inputValues['event']['urltitle']))
      $inputValues['event']['urltitle'] = DataUtil :: formatPermalink($inputValues['event']['title']);

    // set some defaults
    $inputValues['event']['obj_status'] = 'P';

    $object = DBUtil :: insertObject($inputValues['event'], 'crpcalendar', 'eventid');
    if (!$object)
    {
      LogUtil :: registerError(__('Error! Creation attempt failed.', $this->dom));
      return false;
    }

    // get original files, and clone them
    if (isset ($inputValues['image']) && $inputValues['image']['id'])
    {
      $object['image'] = $this->getFile($inputValues['eventid'], 'image', true);
      $inputValues['event_image'] = array (
          'eventid' => $object['eventid'],
          'document_type' => $object['image']['document_type'],
          'name' => $object['image']['name'],
          'type' => $object['image']['content_type'],
          'size' => $object['image']['size'],
          'binary_data' => $object['image']['binary_data']
      );
      $id_image = $this->setFile($inputValues['event_image'], true);
      if ($id_image == '-1')
        return false;
    }

    if (isset ($inputValues['document']) && $inputValues['document']['id'])
    {
      $object['document'] = $this->getFile($inputValues['eventid'], 'document', true);
      $inputValues['event_document'] = array (
          'eventid' => $object['eventid'],
          'document_type' => $object['document']['document_type'],
          'name' => $object['document']['name'],
          'type' => $object['document']['content_type'],
          'size' => $object['document']['size'],
          'binary_data' => $object['document']['binary_data']
      );
      $id_document = $this->setFile($inputValues['event_document'], true);
      if ($id_document == '-1')
        return false;
    }

    // Let any other modules know we have created an item
    pnModCallHooks('item', 'create', $inputValues['eventid'], array (
        'module' => 'crpCalendar'
    ));

    // The item has been modified, so we clear all cached pages of this item.
    $render = new pnRender('crpCalendar');
    $render->clear_cache(null, $inputValues['eventid']);

    return true;
  }

  /**
   * update event status
   *
   * @param int $eventid item identifier
   * @param string $obj_status active or pending
   *
   * @return bool true on succes
   */
  function updateStatus($eventid, $obj_status)
  {
    $obj = array (
        'eventid' => $eventid,
        'obj_status' => $obj_status
    );

    if (!DBUtil :: updateObject($obj, 'crpcalendar', '', 'eventid'))
    {
      return false;
    }

    return true;
  }

  /**
   * update event counter
   *
   * @param int $eventid item identifier
   *
   * @return bool true on success
   */
  function updateCounter($eventid)
  {
    if (isset ($eventid))
    {
      return DBUtil :: incrementObjectFieldByID('crpcalendar', 'counter', $eventid, 'eventid');
    }

    return true;
  }

  /**
   * retrieve partecipations list
   *
   * @param int eventid item identifier
   * @param int uid item identifier
   * @param int startnum list start
   * @param bool ignoreml multilanguage or not
   * @param array modvars module's variables
   * @param int mainCat cat identifier
   * @param string active status
   * @param string sortOrder list order
   * @param string $orderby field for order
   * @param string $groupby field for group
   *
   * @return array on success
   */
  function getPartecipations($uid = null, $startnum = 1, $modvars = array (), $mainCat = null, $active = null, $sortOrder = 'DESC', $orderby = null, $groupby = null)
  {
    (empty ($startnum)) ? $startnum = 1 : '';
    (empty ($modvars['itemsperpage'])) ? $modvars['itemsperpage'] = pnModGetVar('crpCalendar', 'itemsperpage') : '';

    if (!is_numeric($startnum) || !is_numeric($modvars['itemsperpage']))
    {
      return LogUtil :: registerError(__('Error! Could not do what you wanted. Please check your input.', $this->dom));
    }

    $pntable = pnDBGetTables();
    $crpcalendarcolumn = $pntable['crpcalendar_column'];
    $crpcalendarAttendeeColumn = $pntable['crpcalendar_attendee_column'];

    if (!$orderby)
      $orderby = "$pntable[crpcalendar].$crpcalendarcolumn[start_date]";
    else
      $orderby = "$pntable[crpcalendar].$crpcalendarcolumn[$orderby]";

    if ($groupby)
      $groupby = " GROUP BY $pntable[crpcalendar].$crpcalendarcolumn[$groupby] ";

    $queryargs = array ();

    if ($active)
    {
      $queryargs[] = "($pntable[crpcalendar].$crpcalendarcolumn[obj_status]='" . DataUtil :: formatForStore($active) . "')";
    }

    if (pnModGetVar('crpCalendar', 'enable_partecipation') && $uid)
    {
      $queryargs[] = "($pntable[crpcalendar_attendee].$crpcalendarAttendeeColumn[uid]='" . DataUtil :: formatForStore($uid) . "')";
    }

    $where = null;
    if (count($queryargs) > 0)
    {
      $where = ' WHERE ' . implode(' AND ', $queryargs);
    }

    $orderby = "$groupby ORDER BY $orderby $sortOrder";

    // get the objects from the db
    $sqlStatement = "SELECT $pntable[crpcalendar].$crpcalendarcolumn[eventid] as eventid, " .
        "$pntable[crpcalendar].$crpcalendarcolumn[title] as title, " .
        "$pntable[crpcalendar].$crpcalendarcolumn[urltitle] as urltitle, " .
        "$pntable[crpcalendar].$crpcalendarcolumn[location] as location, " .
        "$pntable[crpcalendar].$crpcalendarcolumn[url] as url, " .
        "$pntable[crpcalendar].$crpcalendarcolumn[contact] as contact, " .
        "$pntable[crpcalendar].$crpcalendarcolumn[organiser] as organiser, " .
        "$pntable[crpcalendar].$crpcalendarcolumn[image_caption] as image_caption, " .
        "$pntable[crpcalendar].$crpcalendarcolumn[event_text] as event_text, " .
        "$pntable[crpcalendar].$crpcalendarcolumn[start_date] as start_date, " .
        "$pntable[crpcalendar].$crpcalendarcolumn[end_date] as end_date, " .
        "$pntable[crpcalendar].$crpcalendarcolumn[day_event] as day_event, " .
        "$pntable[crpcalendar].$crpcalendarcolumn[language] as language, " .
        "$pntable[crpcalendar].$crpcalendarcolumn[counter] as counter " .
        "FROM $pntable[crpcalendar] " .
        "LEFT JOIN $pntable[crpcalendar_attendee] ON ($pntable[crpcalendar].$crpcalendarcolumn[eventid]=$pntable[crpcalendar_attendee].$crpcalendarAttendeeColumn[eventid]) " .
        "$where $orderby";

    // get the objects from the db
    $res = DBUtil :: executeSQL($sqlStatement, $startnum -1, $modvars['itemsperpage'], true, true);

    $objArray = DBUtil :: marshallObjects($res, array (
        'eventid',
        'title',
        'urltitle',
        'location',
        'url',
        'contact',
        'organiser',
        'image_caption',
        'event_text',
        'start_date',
        'end_date',
        'day_event',
        'language',
        'counter'
        ), true);

    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($objArray === false)
    {
      return LogUtil :: registerError(__('Error! Could not load items.', $this->dom));
    }

    // need to do this here as the category expansion code can't know the
    // root category which we need to build the relative path component
    if ($objArray && isset ($mainCat) && $mainCat)
    {
      if (!Loader :: loadClass('CategoryUtil'))
      {
        pn_exit('Unable to load class [CategoryUtil]');
      }
      ObjectUtil :: postProcessExpandedObjectArrayCategories($objArray, $mainCat);
    }

    if ($modvars['crpcalendar_userlist_image'])
    {
      foreach ($objArray as $kObj => $vObj)
        $objArray[$kObj]['image'] = $this->getFile($vObj['eventid'], 'image');
    }

    // Return the items
    return $objArray;
  }

  /**
   * retrieve partecipations list
   *
   * @param int eventid item identifier
   * @param int startnum list start
   * @param array modvars module's variables
   * @param int mainCat cat identifier
   * @param string active status
   * @param string sortOrder list order
   * @param string $orderby field for order
   * @param string $groupby field for group
   *
   * @return array on success
   */
  function getEventPartecipations($eventid = null, $startnum = 1, $modvars = array (), $mainCat = null, $active = null, $sortOrder = 'DESC', $orderby = null, $groupby = null)
  {

    $pntable = pnDBGetTables();
    $crpcalendarcolumn = $pntable['crpcalendar_column'];
    $crpcalendarAttendeeColumn = $pntable['crpcalendar_attendee_column'];

    if (!$orderby)
      $orderby = "counter";
    else
      $orderby = "$pntable[crpcalendar].$crpcalendarcolumn[$orderby]";

    if ($groupby)
      $groupby = " GROUP BY $pntable[crpcalendar_attendee].$crpcalendarAttendeeColumn[$groupby] ";

    if ($active)
    {
      $queryargs[] = "($pntable[crpcalendar].$crpcalendarcolumn[obj_status]='" . DataUtil :: formatForStore($active) . "')";
    }

    if (pnModGetVar('crpCalendar', 'enable_partecipation') && $eventid)
    {
      $queryargs[] = "($pntable[crpcalendar].$crpcalendarcolumn[eventid]='" . DataUtil :: formatForStore($eventid) . "')";
    }

    $where = null;
    if (count($queryargs) > 0)
    {
      $where = ' WHERE ' . implode(' AND ', $queryargs);
    }

    $orderby = "$groupby ORDER BY $orderby $sortOrder";

    $sqlStatement = "SELECT $pntable[crpcalendar_attendee].$crpcalendarAttendeeColumn[uid] as uid, " .
        "COUNT($pntable[crpcalendar_attendee].$crpcalendarAttendeeColumn[uid]) as counter " .
        "FROM $pntable[crpcalendar_attendee] " .
        "LEFT JOIN $pntable[crpcalendar] ON ($pntable[crpcalendar].$crpcalendarcolumn[eventid]=$pntable[crpcalendar_attendee].$crpcalendarAttendeeColumn[eventid]) " .
        "$where $orderby";

    // get the objects from the db
    $res = DBUtil :: executeSQL($sqlStatement, $startnum -1, $modvars['itemsperpage'], true, true);

    $objArray = DBUtil :: marshallObjects($res, array (
        'uid'
        ), true);

    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($objArray === false)
    {
      return LogUtil :: registerError(__('Error! Could not load items.', $this->dom));
    }

    // need to do this here as the category expansion code can't know the
    // root category which we need to build the relative path component
    if ($objArray && isset ($mainCat) && $mainCat)
    {
      if (!Loader :: loadClass('CategoryUtil'))
      {
        pn_exit('Unable to load class [CategoryUtil]');
      }
      ObjectUtil :: postProcessExpandedObjectArrayCategories($objArray, $mainCat);
    }

    // Return the items
    return $objArray;
  }

  /**
   * add event partecipation
   *
   * @param int $uid user identifier
   * @param int $eventid item identifier
   *
   * @return bool true on success
   */
  function addPartecipation($uid = null, $eventid = null)
  {
    if (isset ($eventid) && isset ($uid))
    {
      $partecipation = array (
          'uid' => $uid,
          'eventid' => $eventid
      );
      return DBUtil :: insertObject($partecipation, 'crpcalendar_attendee');
    }

    return true;
  }

  /**
   * delete event partecipations
   *
   * @param int $uid user identifier
   * @param int $eventid item identifier
   * @param int $clean clear all partecipations
   *
   * @return bool true on success
   */
  function deletePartecipation($uid = null, $eventid = null, $clean = null)
  {
    $pntable = pnDBGetTables();
    $crpcalendarcolumn = $pntable['crpcalendar_attendee_column'];

    if (isset ($eventid) && isset ($uid))
    {
      $where = " WHERE $crpcalendarcolumn[uid]='" . DataUtil :: formatForStore($uid) . "' " .
          "AND $crpcalendarcolumn[eventid]='" . DataUtil :: formatForStore($eventid) . "' ";

      return DBUtil :: deleteObject(null, 'crpcalendar_attendee', $where);
    }
    elseif (isset ($clean) && isset ($eventid))
    {
      $where = " WHERE $crpcalendarcolumn[eventid]='" . DataUtil :: formatForStore($eventid) . "' ";

      return DBUtil :: deleteObject(null, 'crpcalendar_attendee', $where);
    }
    elseif (isset ($clean) && isset ($uid))
    {
      $where = " WHERE $crpcalendarcolumn[uid]='" . DataUtil :: formatForStore($uid) . "' ";

      return DBUtil :: deleteObject(null, 'crpcalendar_attendee', $where);
    }

    return true;
  }

  /**
   * return items count
   *
   * @param int $category category identifier
   * @param string $active status required
   * @param int $uid user identifier
   * @param int $eventid item identifier
   * @param array $modvars module's variables
   *
   * @return int on success
   */
  function countAttendeeItems($category = null, $active = null, $uid = null, $eventid = null, $modvars=array())
  {
    $pntable = pnDBGetTables();
    $crpcalendarcolumn = $pntable['crpcalendar_column'];
    $crpcalendarAttendeeColumn = $pntable['crpcalendar_attendee_column'];

    $where = '';

    $catFilter = array ();
    if (is_array($category))
      $catFilter = $category;
    else
      if ($category)
      {
        if ($modvars['subcategory_listing'])
        {
          $catstofilter[] = $category;
          // TODO : Zikula 1.1.1 fail to load CategoryUtil from blocks ?
          Loader::loadClass ('CategoryUtil');
          $childs = CategoryUtil::getSubCategories($category);
          // TODO : attributes param is commented ?
          foreach ($childs as $child)
            $catstofilter[] = $child['id'];

          $catFilter['Main'] = $catstofilter;
        }
        else
          $catFilter['Main'] = $category;

        $catFilter['__META__']['module'] = 'crpCalendar';
      }

    if ($active)
      $where = " WHERE $crpcalendarcolumn[obj_status]='" . DataUtil :: formatForStore($active) . "'";

    // define the permission filter to apply
    $permFilter = array (
        array (
        'realm' => 0,
        'component_left' => 'crpCalendar',
        'component_right' => 'Event',
        'instance_left' => 'cr_uid',
        'instance_center' => 'title',
        'instance_right' => 'eventid',
        'level' => ACCESS_READ
        )
    );

    $joinInfo[] = array (
        'join_table' => 'crpcalendar',
        'join_field' => 'obj_status',
        'object_field_name' => 'obj_status',
        'compare_field_table' => "tbl.$crpcalendarAttendeeColumn[eventid]",
        'compare_field_join' => 'eventid'
    );

    $columnArray = array (
        'uid'
    );

    if ($uid)
      return DBUtil :: selectExpandedObjectByID('crpcalendar_attendee', $joinInfo, $uid, 'uid', $columnArray, $permFilter, $catFilter);
    elseif ($eventid) return DBUtil :: selectExpandedObjectCount('crpcalendar_attendee', $joinInfo, $where, true);
    else
      return DBUtil :: selectObjectCount('crpcalendar', $where, 'eventid', false, $catFilter);
  }

  /**
   * Return items count
   *
   * @param int $category category identifier
   * @param string $active status required
   * @param int $uid user identifier
   * @param int $eventid item identifier
   * @param string $typeList output type
   * @param array $modvars module's variables
   *
   * @return int on success
   */
  function countItems($category = null, $active = null, $uid = null, $eventid = false, $typeList = null, $modvars=array(), $startDate = null, $endDate = null)
  {
    $pntable = pnDBGetTables();
    $crpcalendarcolumn = $pntable['crpcalendar_column'];

    $where = '';
    $nowDate = DateUtil :: getDatetime();

    $catFilter = array ();
    if (is_array($category))
      $catFilter = $category;
    else
      if ($category)
      {
        if ($modvars['subcategory_listing'])
        {
          $catstofilter[] = $category;
          // TODO : Zikula 1.1.1 fail to load CategoryUtil from blocks ?
          Loader::loadClass ('CategoryUtil');
          $childs = CategoryUtil::getSubCategories($category);
          // TODO : attributes param is commented ?
          foreach ($childs as $child)
            $catstofilter[] = $child['id'];

          $catFilter['Main'] = $catstofilter;
        }
        else
          $catFilter['Main'] = $category;

        $catFilter['__META__']['module'] = 'crpCalendar';
      }

    if ($startDate && $endDate)
    {
      $queryargs[] = "( (($crpcalendarcolumn[start_date] >= '" . DataUtil :: formatForStore($startDate) . "' AND $crpcalendarcolumn[start_date] < '" . DataUtil :: formatForStore($endDate) . "') " .
          "OR ($crpcalendarcolumn[end_date] > '" . DataUtil :: formatForStore($startDate) . "' AND $crpcalendarcolumn[end_date] < '" . DataUtil :: formatForStore($endDate) . "')) " .
          "OR (('" . DataUtil :: formatForStore($startDate) . "' >= $crpcalendarcolumn[start_date] AND '" . DataUtil :: formatForStore($startDate) . "' < $crpcalendarcolumn[end_date]) " .
          "AND ('" . DataUtil :: formatForStore($endDate) . "' > $crpcalendarcolumn[start_date] AND '" . DataUtil :: formatForStore($endDate) . "' < $crpcalendarcolumn[end_date])) )";
    }

    switch ($typeList)
    {
      case "upcoming" :
        $queryargs[] = "($crpcalendarcolumn[start_date] > '" . DataUtil :: formatForStore($nowDate) . "')";
        break;
      case "archive" :
        $queryargs[] = "($crpcalendarcolumn[start_date] <= '" . DataUtil :: formatForStore($nowDate) . "')";
        break;
      default :
        break;
    }

    if ($active)
    {
      $queryargs[] = "($crpcalendarcolumn[obj_status]='" . DataUtil :: formatForStore($active) . "')";
    }

    $where = null;
    if (count($queryargs) > 0)
    {
      $where = ' WHERE ' . implode(' AND ', $queryargs);
    }

    if ($uid)
      return DBUtil :: selectObjectCountByID('crpcalendar_attendee', $uid, 'uid');
    elseif ($eventid) return DBUtil :: selectObjectCount('crpcalendar_attendee', $where, 'eventid', true);
    else
      return DBUtil :: selectObjectCount('crpcalendar', $where, 'eventid', false, $catFilter);
  }

  /**
   * remove an event
   *
   * @param int $eventid item identifier
   *
   * @return bool true on succes
   */
  function removeEvent($eventid = null)
  {
  // Argument check
    if (!$eventid)
      return LogUtil :: registerError(__('Error! Could not do what you wanted. Please check your input.', $this->dom));

    // Check item exists before attempting deletion
    $item = $this->getAdminData($eventid);

    if ($item == false)
      return LogUtil :: registerError(__('No such item found.', $this->dom));

    if (!DBUtil :: deleteObjectByID('crpcalendar', $eventid, 'eventid'))
      return LogUtil :: registerError(__('Error! Sorry! Deletion attempt failed.', $this->dom));

    $this->deleteFile('image', $eventid);
    $this->deleteFile('document', $eventid);
    $this->deletePartecipation(null, $eventid, true);

    // Let any hooks know that we have deleted an item.
    pnModCallHooks('item', 'delete', $eventid, array (
        'module' => 'crpCalendar'
    ));

    return true;
  }

  /**
   * save file into DB
   *
   * @param array $data file values
   * @param bool $fromDB uploaded or cloned
   *
   * @return int file identifier
   */
  function setFile($data = array (), $fromDB = false)
  {
    $result = -1;

    if (!$data['error'])
    {
      if (!$fromDB)
      {
        $fd = fopen($data['tmp_name'], "r");
        $file_content = fread($fd, filesize($data['tmp_name']));
        fclose($fd);
      }
      else
        $file_content = $data['binary_data'];

      $item = $this->getFile($data['eventid'], $data['document_type']);

      // no empty spaces in filename
      $document['name'] = str_replace(" ", "_", $data['name']);
      $document['content_type'] = $data['type'];
      $document['size'] = $data['size'];
      $document['document_type'] = $data['document_type'];
      $document['eventid'] = $data['eventid'];
      // load binary
      $document['binary_data'] = $file_content;

      if ($item)
      {
        $document['id'] = $item['id'];
        if (!DBUtil :: updateObject($document, 'crpcalendar_files', '', 'id'))
        {
          LogUtil :: registerError(__('Error! Update attempt failed.', $this->dom));
          return false;
        }
        $result = 0;
      }
      elseif (empty ($item))
      {
        if (!DBUtil :: insertObject($document, 'crpcalendar_files', 'id'))
        {
          LogUtil :: registerError(__('Error! Creation attempt failed.', $this->dom));
          return false;
        }
        $result = DBUtil :: getInsertID('crpcalendar_files', 'id');
      }
      else
        return $result;
    }

    return $result;
  }

  /**
   * retrieve binary files
   *
   * @param int $eventid item identifier
   * @param string $file_type document type
   * @param bool $load_binary include blob or not
   *
   * @return array file values
   */
  function getFile($eventid, $file_type, $load_binary = false)
  {
    $pntable = pnDBGetTables();
    $crpcalendarcolumn = $pntable['crpcalendar_files_column'];

    $queryargs[] = "($crpcalendarcolumn[eventid] = '" . DataUtil :: formatForStore($eventid) . "' " .
        "AND $crpcalendarcolumn[document_type] = '" . DataUtil :: formatForStore($file_type) . "')";

    $where = null;
    if (count($queryargs) > 0)
    {
      $where = ' WHERE ' . implode(' AND ', $queryargs);
    }

    $columnArray = array (
        'id',
        'eventid',
        'document_type',
        'name',
        'content_type',
        'size'
    );
    if ($load_binary)
      array_push($columnArray, "binary_data");

    $file = DBUtil :: selectObject('crpcalendar_files', $where, $columnArray);

    return $file;
  }

  /**
   * get image for an event
   *
   * @param int $eventid event identifier
   *
   * @return blob image string
   */
  function getImage()
  {
    $eventid = pnVarCleanFromInput('eventid');

    $pntable = pnDBGetTables();
    $crpcalendarcolumn = $pntable['crpcalendar_files_column'];

    $queryargs[] = "($crpcalendarcolumn[eventid] = '" . DataUtil :: formatForStore($eventid) . "' " .
        "AND $crpcalendarcolumn[document_type] = 'image')";

    $where = null;
    if (count($queryargs) > 0)
    {
      $where = ' WHERE ' . implode(' AND ', $queryargs);
    }

    $columnArray = array (
        'id',
        'eventid',
        'document_type',
        'name',
        'content_type',
        'size',
        'binary_data'
    );

    $file = DBUtil :: selectObject('crpcalendar_files', $where, $columnArray);
    $modifiedDate = $this->getEventDate($eventid, 'lu_date');

    // we need a timestamp
    $server_etag = DateUtil :: makeTimestamp($modifiedDate);
    $server_date = gmdate('D, d M Y H:i:s', $server_etag) . " GMT";

    // Check cached versus modified date
    $client_etag = $_SERVER['HTTP_IF_NONE_MATCH'];
    $client_date = $_SERVER['HTTP_IF_MODIFIED_SINCE'];

    if (($client_etag == $server_etag) && (!$client_date || ($client_date == $server_date)))
    {
      header("HTTP/1.1 304 Not Modified");
      header("ETag: $server_etag");
      pnShutDown();
    }
    else
    {
      header("Expires: " . gmdate('D, d M Y H:i:s', time() + 24 * 3600) . " GMT");
      header('Pragma: cache');
      header('Cache-Control: public, must-revalidate');
      header("ETag: $server_etag");
      header("Last-Modified: " . gmdate('D, d M Y H:i:s', $server_etag) . " GMT");
      header("Content-Type: {$file['content_type']}");
      header("Content-Disposition: inline; filename={$file['name']}");
    }

    echo $file['binary_data'];

    pnShutDown();
  }

  /**
   * delete file
   *
   * @param int $file_type file identifier
   * @param int $eventid event identifier
   *
   * @return bool true on success
   */
  function deleteFile($file_type = null, $eventid = null)
  {
  // Argument check
    if (!$eventid)
      return LogUtil :: registerError(__('Error! Could not do what you wanted. Please check your input.', $this->dom));

    $item = $this->getFile($eventid, $file_type);

    if ($item && !DBUtil :: deleteObjectByID('crpcalendar_files', $item['id'], 'id'))
      return LogUtil :: registerError(__('Error! Sorry! Deletion attempt failed.', $this->dom));

    return true;
  }

  /**
   * verify binary existence
   *
   * @param int $eventid event identifier
   *
   * @return int count
   */
  function existFile($eventid = null)
  {
    $count = DBUtil :: selectObjectCountByID('crpcalendar_files', $eventid, 'id', false);

    return $count > 0;
  }

  /**
   * verify partecipation existence
   *
   * @param int $uid user identifier
   * @param int $eventid event identifier
   *
   * @return int count
   */
  function existPartecipation($uid = null, $eventid = null)
  {
    $pntable = pnDBGetTables();
    $crpcalendarcolumn = $pntable['crpcalendar_attendee_column'];

    $where = "($crpcalendarcolumn[eventid] = '" . DataUtil :: formatForStore($eventid) . "' " .
        "AND $crpcalendarcolumn[uid] = '" . DataUtil :: formatForStore($uid) . "')";

    $count = DBUtil :: selectObjectCount('crpcalendar_attendee', $where);

    return $count > 0;
  }

  /**
   * verify event existence
   *
   * @param string $title event title
   * @param string $location event location
   * @param string $start_date event start
   *
   * @return int count
   */
  function existEvent($title = null, $location = null, $start_date = null)
  {
    $pntable = pnDBGetTables();
    $crpcalendarcolumn = $pntable['crpcalendar_column'];

    $where = "($crpcalendarcolumn[title] = '" . DataUtil :: formatForStore($title) . "' " .
        "AND $crpcalendarcolumn[location] = '" . DataUtil :: formatForStore($location) . "' " .
        "AND $crpcalendarcolumn[start_date] = '" . DataUtil :: formatForStore($start_date) . "')";

    $count = DBUtil :: selectObjectCount('crpcalendar', $where);

    return $count > 0;
  }

  /**
   * validate submitted data
   *
   * @param array $data submitted data
   *
   * @return boolean true if data are OK
   */
  function validateData(& $data)
  {
    $validateOK = false;

    if (!$data['event']['title'] || (!$data['event']['event_text'] && pnModGetVar('crpCalendar', 'mandatory_description')))
    {
      LogUtil :: registerError(__('Error! Could not do what you wanted. Please check your input.', $this->dom));
    }
    elseif (($data['event_image']['error']) && $data['event_image']['error'] != UPLOAD_ERR_NO_FILE)
    {
      switch ($data['event_image']['error'])
      {
        case UPLOAD_ERR_INI_SIZE :
        case UPLOAD_ERR_FORM_SIZE :
          LogUtil :: registerError(__('Image File size not allowed', $this->dom));
          break;
        case UPLOAD_ERR_PARTIAL :
        case UPLOAD_ERR_NO_TMP_DIR :
          LogUtil :: registerError(__('Image file not uploaded', $this->dom));
          break;
      }
    }
    elseif (($data['event_document']['error']) && $data['event_document']['error'] != UPLOAD_ERR_NO_FILE)
    {
      switch ($data['event_document']['error'])
      {
        case UPLOAD_ERR_INI_SIZE :
        case UPLOAD_ERR_FORM_SIZE :
          LogUtil :: registerError(__('Document size not allowed', $this->dom));
          break;
        case UPLOAD_ERR_PARTIAL :
        case UPLOAD_ERR_NO_TMP_DIR :
          LogUtil :: registerError(__('Document file not uploaded', $this->dom));
          break;
      }
    }
    elseif ($data['event_image']['name'] && !in_array($data['event_image']['type'], $this->ImageTypes))
    {
      LogUtil :: registerError(__('Image invalid type', $this->dom));
    }
    elseif ($data['event']['url'] && !pnVarValidate($data['event']['url'], 'url'))
    {
      LogUtil :: registerError(__('Invalid URL', $this->dom));
    }
    elseif (empty ($data['event']['__CATEGORIES__']['Main']) && pnModGetVar('crpCalendar', 'enablecategorization'))
    {
      LogUtil :: registerError(__('Categorisation is enabled, choose a category', $this->dom));
    }
		/*
		elseif($data['event']['contact'] && !pnVarValidate($data['event']['contact'],'email'))
		{
			LogUtil::registerError (_CRPCALENDAR_INVALID_CONTACT);
		}
		*/
    else
    {
      $validateOK = true;
    }

    return $validateOK;
  }

  /**
   * create event from ics data
   *
   * @param array $data event values
   * @param int $key
   * @param int $id_category value to set
   *
   * @return int id of created object
   * */
  function createFromIcs($data = array (), $key = null, $id_category = null)
  {

    $data['start_date'] = $data['start_date'] . " " . $data['start_time'];
    $data['end_date'] = $data['end_date'] . " " . $data['end_time'];
    ($data['day_event']) ? $data['end_date'] = $data['start_date'] : '';
    ($data['start_date'] != $data['end_date']) ? $data['day_event'] = '0' : $data['day_event'] = '1';
    $data['__CATEGORIES__']['Main'] = $id_category;

    $object = DBUtil :: insertObject($data, 'crpcalendar', 'eventid');
    if (!$object)
    {
      LogUtil :: registerError(__('Error! Creation attempt failed.', $this->dom));
      return false;
    }
    // Let any other modules know we have created an item
    pnModCallHooks('item', 'create', $object['eventid'], array (
        'module' => 'crpCalendar'
    ));

    return $object['eventid'];
  }

  /**
   * remove events from a date
   *
   * @param int $endDay value filter
   * @param int $endMonth value filter
   * @param int $endYear value filter
   *
   * @return bool true on succes
   */
  function removePurge($endDay = null, $endMonth = null, $endYear = null)
  {
  // Argument check
    if (!$endDay || !$endMonth || !$endYear)
      return LogUtil :: registerError(__('Error! Could not do what you wanted. Please check your input.', $this->dom));

    $pntable = pnDBGetTables();
    $crpcalendarcolumn = $pntable['crpcalendar_column'];
    $queryargs = array ();

    $queryargs[] = "($crpcalendarcolumn[end_date] <= '$endYear-$endMonth-$endDay 23:59:59')";

    $where = null;
    if (count($queryargs) > 0)
    {
      $where = ' WHERE ' . implode(' AND ', $queryargs);
    }

    // define the permission filter to apply
    $permFilter = array (
        array (
        'realm' => 0,
        'component_left' => 'crpCalendar',
        'component_right' => 'Event',
        'instance_left' => 'cr_uid',
        'instance_center' => 'title',
        'instance_right' => 'eventid',
        'level' => ACCESS_READ
        )
    );

    $orderby = "ORDER BY $crpcalendarcolumn[start_date] $sortOrder";

    $columnArray = array (
        'eventid',

    );

    // get the objects from the db
    $objArray = DBUtil :: selectObjectArray('crpcalendar', $where, $orderby, -1, -1, '', $permFilter, null, $columnArray);

    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($objArray === false)
    {
      return LogUtil :: registerError(__('Error! Could not load items.', $this->dom));
    }

    foreach ($objArray as $purgetitem)
    {
      if ($purgetitem['eventid'] == false)
        return LogUtil :: registerError(__('No such item found.', $this->dom));

      if (!DBUtil :: deleteObjectByID('crpcalendar', $purgetitem['eventid'], 'eventid'))
        return LogUtil :: registerError(__('Error! Sorry! Deletion attempt failed.', $this->dom));

      $this->deleteFile('image', $purgetitem['eventid']);
      $this->deleteFile('document', $purgetitem['eventid']);
      $this->deletePartecipation(null, $purgetitem['eventid'], true);

      // Let any hooks know that we have deleted an item.
      pnModCallHooks('item', 'delete', $purgetitem['eventid'], array (
          'module' => 'crpCalendar'
      ));
    }

    return true;
  }

  /**
   * Retrieve array of places from locations
   */
  function getLocations()
  {
    $locations = array ();

    // this function is called only when locations module is enabled
    if (pnModAvailable('locations') && pnModGetVar('crpCalendar', 'enable_locations'))
    {
      pnModDBInfoLoad('locations');

      $orderby = "ORDER BY name ASC";

      $objArray = DBUtil :: selectObjectArray('locations_location', null, 'name', -1, -1, '', array (), array (), $columnArray = array (
          'locationid',
          'name'
      ));

      $locations['values'][] = null;
      $locations['output'][] = __('None', $this->dom);
      foreach ($objArray as $klocation => $vlocation)
      {
        $locations['values'][] = $vlocation['locationid'];
        $locations['output'][] = $vlocation['name'];
      }
    }

    return $locations;
  }
}