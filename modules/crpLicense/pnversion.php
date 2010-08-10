<?php

/**
 * crpLicense
 *
 * @copyright (c) 2009, Daniele Conca
 * @link http://code.zikula.org/crplicense Support and documentation
 * @author Daniele Conca <conca.daniele@gmail.com>
 * @license GNU/GPL - v.2.1
 * @package crpLicense
 */

$modversion['name']= _CRPLICENSE_NAME;
$modversion['displayname']= _CRPLICENSE_DISPLAYNAME;
$modversion['description']= _CRPLICENSE_DESCRIPTION;
$modversion['version']= '0.1.0';
$modversion['credits']= 'pndocs/credits.txt';
$modversion['help']= 'pndocs/install.txt';
$modversion['changelog']= 'pndocs/changelog.txt';
$modversion['license']= 'pndocs/license.txt';
$modversion['official']= 1;
$modversion['author']= 'Daniele Conca - jami';
$modversion['contact']= 'conca.daniele@gmail.com';
$modversion['securityschema']= array (
	'crpLicense::License' => '::',
	'crpLicense::Hook' => '::'
);
?>