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

function crpLicense_pntables()
{
	// Initialise table array
	$pntable= array ();

	// Full table definition
	$pntable['crplicense']= DBUtil :: getLimitedTablename('crplicense');

	$pntable['crplicense_column']= array (
		'id' => 'id',
		'name' => 'name',
		'descr' => 'descr',
		'url' => 'url'
	);

	$pntable['crplicense_column_def']= array (
		'id' => 'I AUTOINCREMENT PRIMARY',
		'name' => "C(255) NOTNULL DEFAULT ''",
		'descr' => "X NOTNULL DEFAULT ''",
		'url' => "C(255) NOTNULL DEFAULT ''",

	);

	$pntable['crplicense_images']= DBUtil :: getLimitedTablename('crplicense_images');
	$pntable['crplicense_images_column']= array (
		'id' => 'id',
		'id_license' => 'id_license',
		'document_type' => 'document_type',
		'name' => 'name',
		'content_type' => 'content_type',
		'size' => 'size',
		'binary_data' => 'binary_data'
	);
	$pntable['crplicense_images_column_def']= array (
		'id' => 'I(11) AUTOINCREMENT PRIMARY',
		'id_license' => "I(11) NOTNULL DEFAULT 0",
		'document_type' => "C(255) NOTNULL DEFAULT ''",
		'name' => "C(255) NOTNULL DEFAULT ''",
		'content_type' => "C(255) NOTNULL DEFAULT ''",
		'size' => "I NOTNULL DEFAULT 0",
		'binary_data' => "B NOTNULL DEFAULT ''"
	);

	$pntable['crplicense_archive']= DBUtil :: getLimitedTablename('crplicense_archive');
	$pntable['crplicense_archive_column']= array (
		'id_license' => 'id_license',
		'id_module' => 'id_module',
		'modname' => 'modname'
	);
	$pntable['crplicense_archive_column_def']= array (
		'id_license' => "I(11) NOTNULL DEFAULT 0",
		'id_module' => "I(11) NOTNULL DEFAULT 0",
		'modname' => "C(255) NOTNULL DEFAULT ''"
	);

	// add standard data fields
	ObjectUtil :: addStandardFieldsToTableDefinition($pntable['crplicense_column'], '');
	ObjectUtil :: addStandardFieldsToTableDataDefinition($pntable['crplicense_column_def']);

	return $pntable;
}
?>