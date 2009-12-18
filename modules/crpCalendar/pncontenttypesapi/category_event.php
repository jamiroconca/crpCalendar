<?php
/**
 * crpCalendar
 *
 * @copyright (c) 2007-2009, Daniele Conca
 * @link http://code.zikula.org/projects/crpcalendar Support and documentation
 * @author Daniele Conca <conca.daniele@gmail.com>
 * @license GNU/GPL - v.2.1
 * @package crpCalendar
 */

/**
 * class for interaction with Content module
 */
class crpCalendar_contenttypesapi_category_eventPlugin extends contentTypeBase
{
  var $categoryid;

  function getModule() { return 'crpCalendar'; }
  function getName() { return 'category_event'; }
  function getTitle() { $dom = ZLanguage::getModuleDomain('crpCalendar'); return __('Events in a crpCalendar category', $dom); }
  function getDescription() { $dom = ZLanguage::getModuleDomain('crpCalendar'); return __('Show a list of events from a crpCalendar category', $dom); }


  function loadData($data)
  {
    $this->categoryid = $data['categoryid'];
  }


  function display()
  {
    if (!empty($this->categoryid))
      return pnModFunc('crpCalendar', 'user', 'simple_category_display', array('categoryid' => $this->categoryid));
    return '';
  }


  function displayEditing()
  {
  	$dom = ZLanguage::getModuleDomain('crpCalendar');

    if (!empty($this->categoryid))
    {
			// TODO : Zikula 1.1.2 fail to load CategoryUtil ?
      Loader::loadClass ('CategoryUtil');
      $category = CategoryUtil::getCategoryByID($this->categoryid);
			$lang = ZLanguage::getLanguageCode();
      return '<strong>'.$category['display_name'][$lang].'</strong><br />'.$category['display_desc'][$lang];
    }
    return __('No category', $dom);
  }


  function getDefaultData()
  {
    return array('categoryid' => null);
  }


  function startEditing(&$render)
  {
    array_push($render->plugins_dir, 'modules/crpCalendar/pntemplates/pnform');
  }
}


function crpCalendar_contenttypesapi_category_event($args)
{
  return new crpCalendar_contenttypesapi_category_eventPlugin();
}

?>
