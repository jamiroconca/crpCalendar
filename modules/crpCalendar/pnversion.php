<?php
/**
 * crpCalendar
 *
 * @copyright (c) 2007,2008 Daniele Conca
 * @link http://code.zikula.org/crpcalendar Support and documentation
 * @author Daniele Conca <conca dot daniele at gmail dot com>
 * @license GNU/GPL - v.2.1
 * @package crpCalendar
 */

$modversion['name'] = _CRPCALENDAR_NAME;
$modversion['displayname'] = _CRPCALENDAR_DISPLAYNAME;
$modversion['description'] = _CRPCALENDAR_DESCRIPTION;
$modversion['version'] = '0.5.1';
$modversion['credits'] = 'pndocs/credits.txt';
$modversion['help'] = 'pndocs/install.txt';
$modversion['changelog'] = 'pndocs/changelog.txt';
$modversion['license'] = 'pndocs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'Daniele Conca - jami';
$modversion['contact'] = 'conca.daniele@gmail.com';
$modversion['securityschema'] = array ('crpCalendar::' => 'AuthorID:EventTitle:EventID',
                                      'crpCalendar::Category' => 'CategoryName::CategoryID');

?>