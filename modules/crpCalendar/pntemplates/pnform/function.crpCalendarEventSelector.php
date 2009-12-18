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

class crpCalendarEventSelector extends pnFormPlugin
{
  var $inputName;
  var $dataField;
  var $dataBased;
  var $group;
  var $selectedItemId;
  var $mandatory;
  var $isValid = true;
  var $errorMessage;

  function getFilename()
  {
    return __FILE__;
  }

  function create(&$render, $params)
  {
    $this->inputName = $this->id;
    $this->dataBased = (array_key_exists('dataBased', $params) ? $params['dataBased'] : true);
    $this->dataField = (array_key_exists('dataField', $params) ? $params['dataField'] : $this->id);
    $this->mandatory = (array_key_exists('mandatory', $params) ? $params['mandatory'] : false);
  }

  function load(&$render, &$params)
  {
    $this->loadValue($render, $render->get_template_vars());
  }

  function initialize(&$render)
  {
    $render->pnFormAddValidator($this);
  }

  function render(&$render)
  {
    PageUtil::AddVar('stylesheet', ThemeUtil::getModuleStylesheet('crpCalendar'));

    // get all module vars
    $modvars = pnModGetVar('crpCalendar');

    // load the category registry util
    if (!($class = Loader :: loadClass('CategoryRegistryUtil')))
      pn_exit('Unable to load class [CategoryRegistryUtil] ...');
    if (!($class = Loader :: loadClass('CategoryUtil')))
      pn_exit('Unable to load class [CategoryUtil] ...');

    $dom = ZLanguage::getModuleDomain('crpCalendar');

    $category = null;
    $startnum = '1';
    $mainCat = CategoryRegistryUtil :: getRegisteredModuleCategory('crpCalendar', 'crpcalendar', 'Main', '/__SYSTEM__/Modules/crpCalendar');
    $cats = CategoryUtil::getCategoriesByParentID($mainCat);
    $ignoreml = true;
    $sortOrder = 'DESC';

    $data = compact('startnum', 'category', 'clear', 'ignoreml', 'mainCat', 'cats', 'modvars', 'sortOrder');

    $events = pnModAPIFunc('crpCalendar', 'user', 'getall_formlist', $data);

    if ($events === false)
      return LogUtil::registerError (__('Error! Could not do what you wanted. Please check your input.', $dom));

    if ($this->selectedItemId != null)
    {
      $event = pnModAPIFunc('crpCalendar', 'user', 'get', array('eventid' => $this->selectedItemId));
      if ($event === false)
        return LogUtil::registerError (__('Error! Could not do what you wanted. Please check your input.', $dom));

      $selectedEventId = $event['eventid'];
      $selectedCategoryId = $event[__CATEGORIES__]['Main']['id'];
    }
    else
    {
      $event = null;
      $selectedEventId = null;
      $selectedCategoryId = null;
    }

    $crpRender = &pnRender::getInstance('crpCalendar', false);
    $crpRender->assign('mainCategory', $mainCat);
    $crpRender->assign('selectedEventId', $selectedEventId);
    $crpRender->assign('selectedCategoryId', $selectedCategoryId);
    $crpRender->assign($modvars);

    $crpRender->assign('events', $events);

    $output = $crpRender->fetch('crpcalendar_event.htm');

    return $output;
  }

  function decode(&$render)
  {
    $this->clearValidation($render);

    $value = FormUtil::getPassedValue($this->inputName, null, 'POST');
    if (get_magic_quotes_gpc())
      $value = stripslashes($value);
    //
    $category = FormUtil::getPassedValue("{$this->inputName}_category", null, 'POST');
    if (get_magic_quotes_gpc())
      $category = stripslashes($category);

    $this->selectedItemId = $value;
  }

  function validate(&$render)
  {
    if ($this->mandatory  &&  empty($this->selectedItemId))
    {
      $this->setError(__('A selection here is mandatory.', $dom));
    }
  }


  function setError($msg)
  {
    $this->isValid = false;
    $this->errorMessage = $msg;
  }

  function clearValidation(&$render)
  {
    $this->isValid = true;
    $this->errorMessage = null;
  }

  function saveValue(&$render, &$data)
  {
    if ($this->dataBased)
    {
      if ($this->group == null)
      {
        $data[$this->dataField] = $this->selectedItemId;
      }
      else
      {
        if (!array_key_exists($this->group, $data))
          $data[$this->group] = array();
        $data[$this->group][$this->dataField] = $this->selectedItemId;
      }
    }
  }


  function loadValue(&$render, &$values)
  {
    if ($this->dataBased)
    {
      $value = null;

      if ($this->group == null)
      {
        if ($this->dataField != null  &&  isset($values[$this->dataField]))
          $value = $values[$this->dataField];
      }
      else
      {
        if (isset($values[$this->group]))
        {
          $data = $values[$this->group];
          if (isset($data[$this->dataField]))
          {
            $value = $data[$this->dataField];
          }
        }
      }

      $this->selectedItemId = $value;
    }
  }

}



function smarty_function_crpCalendarEventSelector($params, &$render)
{
  return $render->pnFormRegisterPlugin('crpCalendarEventSelector', $params);
}

?>