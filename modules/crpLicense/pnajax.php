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

function crpLicense_ajax_toggleStatus()
{
	if (!SecurityUtil :: checkPermission('crpLicense::License', '::', ACCESS_ADD))
	{
		AjaxUtil :: error(pnVarPrepHTMLDisplay(_MODULENOAUTH));
	}

	$id= FormUtil :: getPassedValue('id', null, 'GET');
	$status= FormUtil :: getPassedValue('status', -null, 'GET');

	pnModAPIFunc('crpLicense', 'admin', 'change_status', array (
		'id' => $id,
		'status' => $status
	));
	//($status=='A')?$status='P':$status='A';

	return array (
		'id' => $id,
		'status' => $status
	);
}
?>
