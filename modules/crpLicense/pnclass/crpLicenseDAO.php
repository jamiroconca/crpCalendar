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

/**
 * crpLicenseDAO Object
 */
class crpLicenseDAO
{

	function crpLicenseDAO()
	{
		// images allowed
		$this->ImageTypes[]= 'image/gif';
		$this->ImageTypes[]= 'image/jpeg';
		$this->ImageTypes[]= 'image/pjpeg';
		$this->ImageTypes[]= 'image/png';
	}

	/**
	 * Return administrative list of license
	 *
	 * @param int $startnum pager offset
	 * @param array $modvars module's variables
	 *
	 * @return array element list
	 */
	function adminList($startnum= 1, $clear= false, $modvars= array (), $active= null, $sortOrder= 'DESC', $orderBy= 'name')
	{
		(empty ($startnum)) ? $startnum= 1 : '';
		(empty ($modvars['itemsperpage'])) ? $modvars['itemsperpage']= pnModGetVar('crpLicense', 'itemsperpage') : '';

		if (!is_numeric($startnum) || !is_numeric($modvars['itemsperpage']))
		{
			return LogUtil :: registerError(_MODARGSERROR);
		}

		$items= array ();

		// Security check
		if (!SecurityUtil :: checkPermission('crpLicense::License', '::', ACCESS_EDIT))
		{
			return $items;
		}

		$pntable= pnDBGetTables();
		$crplicensecolumn= $pntable['crplicense_column'];
		$queryargs= array ();

		if ($active)
		{
			$queryargs[]= "($crplicensecolumn[obj_status]='" . DataUtil :: formatForStore($active) . "')";
		}

		$where= null;
		if (count($queryargs) > 0)
		{
			$where= ' WHERE ' . implode(' AND ', $queryargs);
		}

		$orderby= "ORDER BY $crplicensecolumn[$orderBy] $sortOrder";

		// get the objects from the db
		$objArray= DBUtil :: selectObjectArray('crplicense', $where, $orderby, $startnum -1, $modvars['itemsperpage'], '');

		// Check for an error with the database code, and if so set an appropriate
		// error message and return
		if ($objArray === false)
		{
			return LogUtil :: registerError(_GETFAILED);
		}

		// Return the items
		return $objArray;
	}

	/**
	 * Return list by parameters
	 */
	function getLicenses($id_license= null, $id_module= null, $modname= null, $extended= null)
	{

		$pntable= pnDBGetTables();
		$licensecolumn= $pntable['crplicense_column'];
		$archivecolumn= $pntable['crplicense_archive_column'];
		$queryargs= array ();

		if ($id_license)
			$queryargs[]= "($archivecolumn[id_license]='" . DataUtil :: formatForStore($id_license) . "')";

		if ($id_module)
			$queryargs[]= "($archivecolumn[id_module]='" . DataUtil :: formatForStore($id_module) . "')";

		if ($modname)
			$queryargs[]= "($archivecolumn[modname]='" . DataUtil :: formatForStore($modname) . "')";

		$queryargs[]= "($archivecolumn[id_module] IS NOT NULL)";

		$groupby= "$pntable[crplicense_archive].$archivecolumn[id_license], $pntable[crplicense_archive].$archivecolumn[id_module], $pntable[crplicense_archive].$archivecolumn[modname]";

		$where= null;
		if (count($queryargs) > 0)
		{
			$where= ' WHERE ' . implode(' AND ', $queryargs);
		}

		$sqlStatement= "SELECT $pntable[crplicense_archive].$archivecolumn[id_license] as id, " .
		"$pntable[crplicense].$licensecolumn[name] as name, " .
		"$pntable[crplicense].$licensecolumn[url] as url, " .
		"$pntable[crplicense_archive].$archivecolumn[id_module] as id_module, " .
		"$pntable[crplicense_archive].$archivecolumn[modname] as modname " .
		"FROM $pntable[crplicense] " .
		"LEFT JOIN $pntable[crplicense_archive] ON ($pntable[crplicense].$licensecolumn[id]=$pntable[crplicense_archive].$archivecolumn[id_license]) " .
		"$where " .
		"GROUP BY $groupby ORDER BY $pntable[crplicense].$licensecolumn[name] ASC";

		// get the objects from the db
		$res= DBUtil :: executeSQL($sqlStatement, -1, -1, true, true);

		$objArray= DBUtil :: marshallObjects($res, array (
			'id',
			'name',
			'url',
			'id_module',
			'modname'
		), true);

		// Check for an error with the database code, and if so set an appropriate
		// error message and return
		if ($objArray === false)
		{
			return LogUtil :: registerError(_GETFAILED);
		}

		foreach ($objArray as $kobj => $vobj)
		{
			if ($extended)
			{
				$objArray[$kobj]['image']= $this->getFile($vobj['id'], 'image');
			}
		}

		// Return the items
		return $objArray;
	}

	/**
	 * Archive clean up
	 */
	function cleanArchive($id_tag= null, $id_module= null, $modname= null)
	{
		$pntable= pnDBGetTables();
		$archivecolumn= $pntable['crplicense_archive_column'];

		if ($id_tag)
		{
			$queryargs[]= "($archivecolumn[id_license]='" . DataUtil :: formatForStore($id_tag) . "')";
		}
		if ($id_module)
		{
			$queryargs[]= "($archivecolumn[id_module]='" . DataUtil :: formatForStore($id_module) . "')";
		}
		if ($modname)
		{
			$queryargs[]= "($archivecolumn[modname]='" . DataUtil :: formatForStore($modname) . "')";
		}

		$where= null;
		if (count($queryargs) > 0)
		{
			$where= ' WHERE ' . implode(' AND ', $queryargs);
		}

		return DBUtil :: deleteObject(null, 'crplicense_archive', $where);
	}

	/**
	 * get a specific admin item data
	 *
	 * @param int $eventid item identifier
	 * @param bool $extend add image and document if true
	 *
	 * @return array item value
	 */
	function getAdminData($id= null, $extend= true)
	{
		$object= DBUtil :: selectObjectByID('crplicense', $id, 'id');

		if ($extend && $object)
		{
			$object['image']= $this->getFile($id, 'image');
		}

		return $object;
	}

	/**
	 * create item details
	 *
	 * @param array $inputValues array of new values
	 *
	 * @return bool true if success
	 */
	function create($inputValues= array ())
	{
		// Argument check
		if (!$this->validateData($inputValues))
			return false;

		$object= DBUtil :: insertObject($inputValues['license'], 'crplicense', 'id');
		if (!$object)
		{
			LogUtil :: registerError(_CREATEFAILED);
			return false;
		}

		if (isset ($inputValues['license_image']) && ($inputValues['license_image']['error'] == UPLOAD_ERR_OK))
		{
			$inputValues['license_image']['id_license']= $object['id'];
			$inputValues['license_image']['document_type']= 'image';
			$id_image= $this->setFile($inputValues['license_image']);
			if ($id_image == '-1')
				return false;
		}

		// Let any other modules know we have created an item
		pnModCallHooks('item', 'create', $object['id'], array (
			'module' => 'crpLicense'
		));

		return $object['id'];
	}

	/**
	 * Archive creation
	 */
	function createArchive($inputValues= array ())
	{
		return DBUtil :: insertObject($inputValues, 'crplicense_archive');
	}

	/**
	 * update item details
	 *
	 * @param array $inputValues array of new values
	 *
	 * @return bool true if success
	 */
	function update($inputValues= array ())
	{
		// Argument check
		if (!$this->validateData($inputValues))
			return false;

		// Check page to update exists, and get information for
		// security check
		$item= $this->getAdminData($inputValues['id']);

		if ($item == false)
		{
			LogUtil :: registerError(_NOSUCHITEM);
			return false;
		}

		if (!DBUtil :: updateObject($inputValues['license'], 'crplicense', '', 'id'))
		{
			LogUtil :: registerError(_UPDATEFAILED);
			return false;
		}

		if (isset ($inputValues['license_image']) && ($inputValues['license_image']['error'] == UPLOAD_ERR_OK))
		{
			$inputValues['license_image']['id_license']= $inputValues['license']['id'];
			$inputValues['license_image']['document_type']= 'image';
			$id_image= $this->setFile($inputValues['license_image']);
			if ($id_image == '-1')
				return false;
		}

		// Let any other modules know we have updated an item
		pnModCallHooks('item', 'update', $inputValues['id'], array (
			'module' => 'crpCalendar'
		));

		return true;
	}

	/**
	 * Return items count
	 *
	 * @param string $active status required
	 *
	 * @return int on success
	 */
	function countItems($active= null)
	{
		$pntable= pnDBGetTables();
		$crplicensecolumn= $pntable['crplicense_column'];

		$where= '';

		if ($active)
		{
			$queryargs[]= "($crplicensecolumn[obj_status]='" . DataUtil :: formatForStore($active) . "')";
		}

		$where= null;
		if (count($queryargs) > 0)
		{
			$where= ' WHERE ' . implode(' AND ', $queryargs);
		}

		return DBUtil :: selectObjectCount('crplicense', $where, 'id', false);
	}

	/**
	 * Update license status
	 *
	 * @param int $id item identifier
	 * @param string $obj_status active or pending
	 *
	 * @return bool true on succes
	 */
	function updateStatus($id= null, $obj_status= null)
	{
		$obj= array (
			'id' => $id,
			'obj_status' => $obj_status
		);

		if (!DBUtil :: updateObject($obj, 'crplicense', '', 'id'))
		{
			return false;
		}

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
	function setFile($data= array (), $fromDB= false)
	{
		$result= -1;

		if (!$data['error'])
		{
			if (!$fromDB)
			{
				$fd= fopen($data['tmp_name'], "r");
				$file_content= fread($fd, filesize($data['tmp_name']));
				fclose($fd);
			}
			else
				$file_content= $data['binary_data'];

			$item= $this->getFile($data['id_license'], $data['document_type']);

			// no empty spaces in filename
			$document['name']= str_replace(" ", "_", $data['name']);
			$document['content_type']= $data['type'];
			$document['size']= $data['size'];
			$document['document_type']= $data['document_type'];
			$document['id_license']= $data['id_license'];
			// load binary
			$document['binary_data']= $file_content;

			if ($item)
			{
				$document['id']= $item['id'];
				if (!DBUtil :: updateObject($document, 'crplicense_images', '', 'id'))
				{
					LogUtil :: registerError(_UPDATEFAILED);
					return false;
				}
				$result= 0;
			}
			elseif (empty ($item))
			{
				if (!DBUtil :: insertObject($document, 'crplicense_images', 'id'))
				{
					LogUtil :: registerError(_CREATEFAILED);
					return false;
				}
				$result= DBUtil :: getInsertID('crplicense_images', 'id');
			}
			else
				return $result;
		}

		return $result;
	}

	/**
	 * delete file
	 *
	 * @param int $file_type file identifier
	 * @param int $id_license license identifier
	 *
	 * @return bool true on success
	 */
	function deleteFile($file_type= null, $id_license= null)
	{
		// Argument check
		if (!$id_license)
			return LogUtil :: registerError(_MODARGSERROR);

		$item= $this->getFile($id_license, $file_type);

		if ($item && !DBUtil :: deleteObjectByID('crplicense_images', $item['id'], 'id'))
			return LogUtil :: registerError(_DELETEFAILED);

		return true;
	}

	/**
	 * retrieve binary files
	 *
	 * @param int $id_license item identifier
	 * @param string $file_type document type
	 * @param bool $load_binary include blob or not
	 *
	 * @return array file values
	 */
	function getFile($id_license= null, $file_type= null, $load_binary= false)
	{
		$pntable= pnDBGetTables();
		$crplicensecolumn= $pntable['crplicense_images_column'];

		$queryargs[]= "($crplicensecolumn[id_license] = '" . DataUtil :: formatForStore($id_license) . "' " .
		"AND $crplicensecolumn[document_type] = '" . DataUtil :: formatForStore($file_type) . "')";

		$where= null;
		if (count($queryargs) > 0)
		{
			$where= ' WHERE ' . implode(' AND ', $queryargs);
		}

		$columnArray= array (
			'id',
			'id_license',
			'document_type',
			'name',
			'content_type',
			'size'
		);
		if ($load_binary)
			array_push($columnArray, "binary_data");

		$file= DBUtil :: selectObject('crplicense_images', $where, $columnArray);

		return $file;
	}

	/**
	 * get image for a license
	 *
	 * @param int $id license identifier
	 *
	 * @return blob image string
	 */
	function getImage()
	{
		$id= pnVarCleanFromInput('id');

		$pntable= pnDBGetTables();
		$crplicensecolumn= $pntable['crplicense_images_column'];

		$queryargs[]= "($crplicensecolumn[id_license] = '" . DataUtil :: formatForStore($id) . "' " .
		"AND $crplicensecolumn[document_type] = 'image')";

		$where= null;
		if (count($queryargs) > 0)
		{
			$where= ' WHERE ' . implode(' AND ', $queryargs);
		}

		$columnArray= array (
			'id',
			'id_license',
			'document_type',
			'name',
			'content_type',
			'size',
			'binary_data'
		);

		$file= DBUtil :: selectObject('crplicense_images', $where, $columnArray);
		$modifiedDate= $this->getLicenseDate($id, 'lu_date');

		while (@ ob_end_clean());
		// credits to Mediashare by Jorn Lind-Nielsen
		if (pnConfigGetVar('UseCompression') == 1)
			header("Content-Encoding: identity");

		// Check cached versus modified date
		$lastModifiedDate= date('D, d M Y H:i:s T', $modifiedDate);
		$currentETag= $modifiedDate;

		global $HTTP_SERVER_VARS;
		$cachedDate= (isset ($HTTP_SERVER_VARS['HTTP_IF_MODIFIED_SINCE']) ? $HTTP_SERVER_VARS['HTTP_IF_MODIFIED_SINCE'] : null);
		$cachedETag= (isset ($HTTP_SERVER_VARS['HTTP_IF_NONE_MATCH']) ? $HTTP_SERVER_VARS['HTTP_IF_NONE_MATCH'] : null);

		// If magic quotes are on then all query/post variables are escaped - so strip slashes to make a compare possible
		// - only cachedETag is expected to contain quotes
		if (get_magic_quotes_gpc())
			$cachedETag= stripslashes($cachedETag);

		if ((empty ($cachedDate) || $lastModifiedDate == $cachedDate) && '"' . $currentETag . '"' == $cachedETag)
		{
			header("HTTP/1.1 304 Not Modified");
			header("Status: 304 Not Modified");
			header("Expires: " . date('D, d M Y H:i:s T', time() + 180 * 24 * 3600)); // My PHP insists on Expires in 1981 as default!
			header('Pragma: cache'); // My PHP insists on putting a pragma "no-cache", so this is an attempt to avoid that
			header('Cache-Control: public');
			header("ETag: \"$modifiedDate\"");
			return true;
		}

		Header("Content-type: {$file['content_type']}");
		Header("Content-Disposition: inline; filename={$file['name']}");
		//Header("Content-Length: " . strlen($file['binary_data']));

		echo $file['binary_data'];

		pnShutDown();
	}

	/**
	 * get a specific license date
	 *
	 * @param int $eventid item identifier
	 * @param int $dateType date type
	 *
	 * @return string item value
	 */
	function getLicenseDate($id= null, $dateType= null)
	{
		$pntable= pnDBGetTables();
		$crplicensecolumn= $pntable['crplicense_column'];

		$queryargs[]= "($crplicensecolumn[id] = '" . DataUtil :: formatForStore($id) . "')";

		$columnArray= array (
			'id',
			'' . $dateType . ''
		);

		$where= null;
		if (count($queryargs) > 0)
		{
			$where= ' WHERE ' . implode(' AND ', $queryargs);
		}

		$item= DBUtil :: selectObject('crplicense', $where, $columnArray);

		$dateValue= false;
		($item[$dateType]) ? $dateValue= $item[$dateType] : $author= false;

		return $dateValue;
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
		$validateOK= false;

		if (!$data['license']['name'])
		{
			LogUtil :: registerError(_MODARGSERROR);
		}
		elseif (($data['license_image']['error']) && $data['license_image']['error'] != UPLOAD_ERR_NO_FILE)
		{
			switch ($data['license_image']['error'])
			{
				case UPLOAD_ERR_INI_SIZE :
				case UPLOAD_ERR_FORM_SIZE :
					LogUtil :: registerError(_CRPLICENSE_ERROR_IMAGE_FILE_SIZE_TOO_BIG);
					break;
				case UPLOAD_ERR_PARTIAL :
				case UPLOAD_ERR_NO_TMP_DIR :
					LogUtil :: registerError(_CRPLICENSE_ERROR_IMAGE_NO_FILE);
					break;
			}
		}
		elseif ($data['license_image']['name'] && !in_array($data['license_image']['type'], $this->ImageTypes))
		{
			LogUtil :: registerError(_CRPLICENSE_IMAGE_INVALID_TYPE);
		}
		elseif ($data['license']['url'] && !pnVarValidate($data['license']['url'], 'url'))
		{
			LogUtil :: registerError(_CRPLICENSE_INVALID_URL);
		}
		else
		{
			$validateOK= true;
		}

		return $validateOK;
	}

}