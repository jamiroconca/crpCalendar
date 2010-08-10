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

Loader :: includeOnce('modules/crpLicense/pnclass/crpLicenseUI.php');
Loader :: includeOnce('modules/crpLicense/pnclass/crpLicenseDAO.php');

/**
 * crpLicense Object
 */
class crpLicense
{

	function crpLicense()
	{
		$this->ui= new crpLicenseUI();
		$this->dao= new crpLicenseDAO();

		(function_exists('gd_info')) ? $this->gd= gd_info() : $this->gd= array ();
	}

	/**
	 * License insertion, check for existence before
	 */
	function insertLicense($objectid= null, $extrainfo= array (), $licenselist= null)
	{
		$this->dao->createArchive(array (
			'id_license' => $licenselist,
			'id_module' => $objectid,
			'modname' => $extrainfo['module']
		));

		return $extrainfo;
	}

	/**
	 * license update, check for existence before
	 */
	function editLicense($objectid= null, $extrainfo= array (), $licenselist= null)
	{
		// clean from old values
		$this->dao->cleanArchive(null, $objectid, $extrainfo['module']);

		$this->dao->createArchive(array (
			'id_license' => $licenselist,
			'id_module' => $objectid,
			'modname' => $extrainfo['module']
		));

		return $extrainfo;
	}

	/**
	 * license deletion for an item
	 */
	function deleteLicense($objectid= null, $extrainfo= array ())
	{
		// clean from old values
		$this->dao->cleanArchive(null, $objectid, $extrainfo['module']);

		return $extrainfo;
	}

	/**
	 * Licenses deletion for a module
	 */
	function removeLicense($extrainfo = array ())
	{
		// clean from old values
		$this->dao->cleanArchive(null, null, $extrainfo['module']);

		return $extrainfo;
	}

	function adminView()
	{
		// Get parameters from whatever input we need.
		$startnum= (int) FormUtil :: getPassedValue('startnum', isset ($args['startnum']) ? $args['startnum'] : null, 'GET');
		$active= FormUtil :: getPassedValue('license_status', null);
		$clear= FormUtil :: getPassedValue('clear');
		if ($clear)
		{
			$active= null;
		}

		// get all module vars
		$modvars= pnModGetVar('crpLicense');

		// Get all matching pages
		$items= pnModAPIFunc('crpLicense', 'admin', 'getall', array (
			'startnum' => $startnum,
			'numitems' => $modvars['itemsperpage'],
			'active' => $active,
			'sortOrder' => 'ASC'
		));

		if (!$items)
			$items= array ();

		$rows= array ();
		foreach ($items as $key => $item)
		{
			$options= array ();
			if (SecurityUtil :: checkPermission('crpLicense::License', "::", ACCESS_EDIT))
			{
				$options[]= array (
					'url' => pnModURL('crpLicense', 'admin', 'modify', array (
						'id' => $item['id']
					)),
					'image' => 'xedit.gif',
					'title' => _EDIT
				);
				if (SecurityUtil :: checkPermission('crpLicense::License', "::", ACCESS_DELETE))
				{
					$options[]= array (
						'url' => pnModURL('crpLicense', 'admin', 'delete', array (
							'id' => $item['id']
						)),
						'image' => '14_layer_deletelayer.gif',
						'title' => _DELETE
					);
				}
			}

			// Add the calculated menu options to the item array
			$item['options']= $options;
			$rows[]= $item;
		}

		$pager= array (
			'numitems' => pnModAPIFunc('crpLicense', 'user', 'countitems'),
			'itemsperpage' => $modvars['itemsperpage']
		);

		return $this->ui->drawAdminView($rows, $active, $modvars, $pager);
	}

	/**
	 * Insert a license
	 *
	 * @return string html
	 */
	function newLicense()
	{
		$inputValues= $this->collectDataFromInput();
		$temp_values= array ();
		$temp_values= SessionUtil :: getVar('crpLicense_temp_values');

		return $this->ui->newLicense($temp_values, $inputValues['modvars']);
	}

	/**
	 * create a license
	 *
	 * @param int $id item identifier
	 * @param array $inputValues array of updated values
	 *
	 * @return string html
	 */
	function createLicense()
	{

		$inputValues= array ();

		// Confirm authorisation code
		if (!SecurityUtil :: confirmAuthKey())
			return LogUtil :: registerAuthidError(pnModURL('crpLicense', 'admin', 'view'));

		$inputValues= $this->collectDataFromInput();
		SessionUtil :: setVar('crpLicense_temp_values', $inputValues['license']);

		$id= $this->dao->create($inputValues);

		if (!$id)
		{
			// Error
			LogUtil :: registerError(_CREATEFAILED);
			return pnRedirect(pnModUrl('crpLicense', 'admin', 'new'));
		}

		// all went fine
		LogUtil :: registerStatus(_CREATESUCCEDED);

		SessionUtil :: delVar('crpLicense_temp_values');

		return pnRedirect(pnModURL('crpLicense', 'admin', 'view'));
	}

	/**
	 * Modify a license
	 *
	 * @param int $id item identifier
	 *
	 * @return string html
	 */
	function modifyLicense()
	{
		$inputValues= $this->collectDataFromInput();

		// Get the event
		$item= $this->dao->getAdminData($inputValues['id']);

		if ($item == false)
		{
			return LogUtil :: registerError(_NOSUCHITEM);
		}

		return $this->ui->modifyLicense($item, $inputValues['modvars']);
	}

	/**
	 * update a license
	 *
	 * @param int $id item identifier
	 * @param array $inputValues array of updated values
	 *
	 * @return string html
	 */
	function updateLicense()
	{
		$inputValues= array ();

		// Confirm authorisation code
		if (!SecurityUtil :: confirmAuthKey())
			return LogUtil :: registerAuthidError(pnModURL('crpLicense', 'admin', 'view'));

		$inputValues= $this->collectDataFromInput();

		if (!$this->dao->update($inputValues))
		{
			// Error
			return pnRedirect(pnModUrl('crpCalendar', 'admin', 'modify', array (
				'id' => $inputValues['id']
			)));
		}

		// all went fine
		LogUtil :: registerStatus(_UPDATESUCCEDED);

		return pnRedirect(pnModURL('crpLicense', 'admin', 'view'));
	}

	/**
	 * Change item status
	 *
	 * @param int $eventid item identifier
	 * @param string $obj_status active or pending
	 *
	 * @return string html
	 */
	function changeStatus()
	{
		$id= FormUtil :: getPassedValue('id', null);
		$obj_status= FormUtil :: getPassedValue('obj_status', null);

		if ($obj_status == 'P' || $obj_status == 'A')
		{
			($obj_status == 'A') ? $obj_status= 'P' : $obj_status= 'A';
			if (!$this->dao->updateStatus($id, $obj_status))
				LogUtil :: registerError(_UPDATEFAILED);
			else
				LogUtil :: registerStatus(_UPDATESUCCEDED);
		}
		else
			LogUtil :: registerError(_UPDATEFAILED);

		return pnRedirect(pnModURL('crpLicense', 'admin', 'view'));
	}

	/**
	 * Modify module's configuration
	 */
	function modifyConfig()
	{
		// get all module vars
		$modvars= pnModGetVar('crpLicense');

		return $this->ui->modifyConfig($modvars, $this->gd);
	}

	/**
	 * Update module's configuration
	 */
	function updateConfig()
	{
		// Confirm authorisation code
		if (!SecurityUtil :: confirmAuthKey())
			return LogUtil :: registerAuthidError(pnModURL('crpLicense', 'admin', 'view'));

		$navigationValues= $this->collectNavigationFromInput();

		// Update module variables
		$itemsperpage= (int) FormUtil :: getPassedValue('itemsperpage', 25, 'POST');
		if ($itemsperpage < 1)
		{
			$itemsperpage= 25;
		}
		pnModSetVar('crpLicense', 'itemsperpage', $itemsperpage);

		$file_dimension= (int) FormUtil :: getPassedValue('file_dimension', 35000, 'POST');
		pnModSetVar('crpLicense', 'file_dimension', $file_dimension);
		$image_width= (int) FormUtil :: getPassedValue('image_width', 100, 'POST');
		pnModSetVar('crpLicense', 'image_width', $image_width);
		$crplicense_use_gd= (bool) FormUtil :: getPassedValue('crplicense_use_gd', false, 'POST');
		pnModSetVar('crpLicense', 'crplicense_use_gd', $crplicense_use_gd);
		$crplicense_userlist_image= (bool) FormUtil :: getPassedValue('crplicense_userlist_image', false, 'POST');
		pnModSetVar('crpLicense', 'crplicense_userlist_image', $crplicense_userlist_image);
		$userlist_width= (int) FormUtil :: getPassedValue('userlist_width', 32, 'POST');
		pnModSetVar('crpLicense', 'userlist_width', $userlist_width);

		// Let any other modules know that the modules configuration has been updated
		pnModCallHooks('module', 'updateconfig', 'crpLicense', array (
			'module' => 'crpCalendar'
		));

		// the module configuration has been updated successfuly
		LogUtil :: registerStatus(_CONFIGUPDATED);

		return pnRedirect(pnModURL('crpLicense', 'admin', 'view'));
	}

	/**
	 * Collect data from insert/modification form
	 *
	 * @param int $eventid item identifier
	 * @param int $objectid object identifier
	 * @param array page item values
	 *
	 * @return array collection of values
	 */
	function collectDataFromInput()
	{
		$id= FormUtil :: getPassedValue('id', null);
		$objectid= FormUtil :: getPassedValue('objectid', null);

		if (!empty ($objectid))
		{
			$id= $objectid;
		}

		$license= FormUtil :: getPassedValue('license', null, 'POST');
		$license_image= FormUtil :: getPassedValue('license_image', null, 'FILES');

		(!empty ($license['objectid'])) ? $license['id']= $license['objectid'] : '';

		// get all module vars
		$modvars= pnModGetVar('crpLicense');

		$data= compact('id', 'objectid', 'license', 'license_image', 'modvars');

		return $data;
	}

	/**
	 * Collect navigation input value
	 *
	 * @param int $startnum pager offset
	 * @param bool clear clean category
	 *
	 * @return array input values
	 */
	function collectNavigationFromInput()
	{
		// Get parameters from whatever input we need.
		$startnum= (int) FormUtil :: getPassedValue('startnum', null, 'GET');
		$active= FormUtil :: getPassedValue('license_status', null);
		$clear= FormUtil :: getPassedValue('clear');

		if ($clear)
		{
			$active= null;
		}

		$ignoreml= FormUtil :: getPassedValue('ignoreml', true);

		// get all module vars
		$modvars= pnModGetVar('crpLicense');

		$data= compact('startnum', 'active', 'clear', 'modvars');

		return $data;
	}

	/**
	 * Generate thumbnail for image
	 *
	 * @param int id doc
	 * @param string width doc
	 * @return nothing
	 */
	function getThumbnail()
	{
		$id= FormUtil :: getPassedValue('id', isset ($args['id']) ? $args['id'] : null, 'GET');
		$width= FormUtil :: getPassedValue('width', isset ($args['width']) ? $args['width'] : null, 'GET');
		if (!SecurityUtil :: checkPermission('crpLicense::License', '::', ACCESS_READ))
			pnShutDown();

		$file= $this->dao->getFile($id, 'image', true);
		$modifiedDate= $this->dao->getLicenseDate($id, 'lu_date');

		if (!(is_numeric($width) && $width > 0))
			$width= pnModGetVar('crpLicense', 'image_width');
		$params['width']= $width; //	$params['append_ghosted']=1;
		$params['modifiedDate']= $modifiedDate;

		crpLicense :: imageGetThumbnail($file['binary_data'], $file['name'], $file['content_type'], $params);
	}

	function imageGetThumbnail(& $pSrcImage, $filename, $content_type, $params= array ())
	{
		// we need a timestamp
		$server_etag= DateUtil :: makeTimestamp($params['modifiedDate']);
		$server_date= gmdate('D, d M Y H:i:s', $server_etag) . " GMT";

		// Check cached versus modified date
		$client_etag= $_SERVER['HTTP_IF_NONE_MATCH'];
		$client_date= $_SERVER['HTTP_IF_MODIFIED_SINCE'];

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
			header("Content-Type: $content_type");
			header("Content-Disposition: inline; filename=thumb_{$filename}");
		}

		/***************************************************************************/

		$alphaThreshold= isset ($params['alpha_threshold']) ? $params['alpha_threshold'] : 64;
		$appendGhosted= $params['append_ghosted'];
		//
		$srcImage= imagecreatefromstring($pSrcImage);

		if ($srcImage)
		{
			//obtain the original image Height and Width
			$srcWidth= imagesx($srcImage);
			$srcHeight= imagesy($srcImage);

			$newWidth= isset ($params['width']) ? $params['width'] : $srcWidth;

			$destWidth= round($newWidth, '0');
			$destHeight= round(($srcHeight / $srcWidth) * $newWidth, '0');

			// creating the destination image with the new Width and Height
			if (!$appendGhosted)
				$destImage= imagecreatetruecolor($destWidth, $destHeight);
			else
				$destImage= imagecreatetruecolor($destWidth, 2 * $destHeight);

			//png transparency
			switch ($content_type)
			{
				case 'image/png' :
				case 'image/x-png' :
					imageantialias($destImage, true);
					imagealphablending($destImage, false);
					imagesavealpha($destImage, true);
					$transparent= imagecolorallocatealpha($destImage, 255, 255, 255, 80);
					imagefill($destImage, 0, 0, $transparent);
					break;

				case 'image/gif' :
					imageantialias($destImage, true);
					imagealphablending($destImage, false);
					break;
			}

			//copy the srcImage to the destImage
			imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight);

			//
			if ($appendGhosted)
			{
				imagecopyresampled($destImage, $srcImage, 0, $destHeight, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight);

				$ghostImage= imagecreatetruecolor($destWidth, $destHeight);
				imageantialias($ghostImage, true);
				imagealphablending($ghostImage, false);
				imagesavealpha($ghostImage, true);
				$whitetrasp= imagecolorallocatealpha($ghostImage, 255, 255, 255, 0);
				imagefill($ghostImage, 0, 0, $whitetrasp);
				imagecopymerge($destImage, $ghostImage, 0, $destHeight, 0, 0, $destWidth, $destHeight, 50);
				if ($content_type == 'image/png')
				{ //	problems mergins transparent png.. need to restore original pixel transparency
					for ($x= 0; $x < $destWidth; $x++)
						for ($y= 0; $y < $destHeight; $y++)
						{
							$srcPixel= imagecolorsforindex($destImage, imagecolorat($destImage, $x, $y));
							$destPixel= imagecolorsforindex($destImage, imagecolorat($destImage, $x, $y + $destHeight));
							imagesetpixel($destImage, $x, $y + $destHeight, imagecolorallocatealpha($destImage, $destPixel['red'], $destPixel['green'], $destPixel['blue'], $srcPixel['alpha']));
						}

				}
				imagedestroy($ghostImage);

			}

			//save output to a buffer
			ob_start();

			//create the image
			switch ($content_type)
			{
				case 'image/gif' :
					imagetruecolortopalette($destImage, true, 255);
					//
					if (imagecolortransparent($srcImage) >= 0)
					{
						$maskImage= imagecreatetruecolor($destWidth, $destHeight);
						imageantialias($maskImage, true);
						imagealphablending($maskImage, false);
						imagecopyresampled($maskImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight);
						//
						$transp= imagecolorallocatealpha($destImage, 0, 0, 0, 127);
						imagecolortransparent($destImage, $transp);
						//
						for ($x= 0; $x < $destWidth; $x++)
							for ($y= 0; $y < $destHeight; $y++)
							{
								$c= imagecolorsforindex($maskImage, imagecolorat($maskImage, $x, $y));
								if ($c['alpha'] >= $alphaThreshold)
								{
									imagesetpixel($destImage, $x, $y, $transp);
									if ($appendGhosted)
										imagesetpixel($destImage, $x, $y + $destHeight, $transp);
								}
							}
						imagedestroy($maskImage);
					}
					//
					imagegif($destImage);
					break;

				case 'image/jpeg' :
				case 'image/pjpeg' :
					imagejpeg($destImage);
					break;

				case 'image/png' :
				case 'image/x-png' :
					imagepng($destImage);
					break;
			}

			//copy output buffer to string
			$resizedImage= ob_get_contents();

			//clear output buffer that was saved
			ob_end_clean();

			//fre the memory used for the images
			imagedestroy($srcImage);
			imagedestroy($destImage);

			echo $resizedImage;
			pnShutDown();
		}
	}

}