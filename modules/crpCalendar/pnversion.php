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

$dom = ZLanguage::getModuleDomain('crpCalendar');

$modversion['name'] = 'crpCalendar';
$modversion['displayname'] = __('crpCalendar', $dom);
$modversion['description'] = __('Simple event calendar', $dom);
$modversion['url'] = __('crpCalendar', $dom);
$modversion['version'] = '0.5.5';
$modversion['credits'] = 'pndocs/credits.txt';
$modversion['help'] = 'pndocs/install.txt';
$modversion['changelog'] = 'pndocs/changelog.txt';
$modversion['license'] = 'pndocs/license.txt';
$modversion['official'] = 1;
$modversion['author'] = 'Daniele Conca - jami';
$modversion['contact'] = 'conca.daniele@gmail.com';
$modversion['securityschema'] = array (
	'crpCalendar::' => 'AuthorID:EventTitle:EventID',
	'crpCalendar::Category' => 'CategoryName::CategoryID'
);