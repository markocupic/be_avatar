<?php

/**
 * BeAvatar for Contao Open Source CMS
 * Copyright (C) 2014 Marko Cupic <m.cupic@gmx.ch>
 * @package Be_avatar
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */

namespace MCupic\BeAvatar;

/**
 * Class InsertTags
 *
 * @copyright  Marko Cupic (C) 2014
 * @author     Marko Cupic <m.cupic@gmx.ch>
 */
class InsertTags extends \KirstenRoschanski\Avatar\InsertTags
{
       /**
        * InsertTag
        * @param $strTag
        * @return bool|string
        */
       public function replaceTags($strTag)
	{
		list($strTag, $strParams) = trimsplit('?', $strTag);
		$arrTag = trimsplit('::', $strTag);

		if ($arrTag[0] != 'be_avatar') {
			return false;
		}

		// get default settings
		$arrDims  = deserialize($GLOBALS['TL_CONFIG']['avatar_maxdims']);
		$strAlt   = $GLOBALS['TL_CONFIG']['avatar_default_alt'];
		$strTitle = $GLOBALS['TL_CONFIG']['avatar_default_title'];
		$strClass = $GLOBALS['TL_CONFIG']['avatar_default_class'];

		// parse query parameters
		$strParams = \String::decodeEntities($strParams);
		$strParams = str_replace('[&]', '&', $strParams);
		$arrParams = explode('&', $strParams);
		foreach ($arrParams as $strParam) {
			list($key, $value) = explode('=', $strParam);

			switch ($key) {
				case 'width':
					$arrDims[0] = $value;
					break;

				case 'height':
					$arrDims[1] = $value;
					break;

				case 'alt':
					$strAlt = specialchars($value);
					break;

				case 'title':
					$strTitle = specialchars($value);
					break;

				case 'class':
					$strClass = $value;
					break;

				case 'mode':
					$arrDims[2] = $value;
					break;
			}
		}


		// search the member record
		$objUser = \UserModel::findByPk($arrTag[1]);

		// return anonymous avatar, if member not found
		if (!$objUser) {
			return $this->generateAnonymousAvatar($arrDims);
		}

		// get the avatar
		$strAvatar = $objUser->avatar;

		// parse the alt and title text
              $strAlt = 'Avatar';
              if ($objUser){
                     $strAlt = $objUser->name;
              }

              $strTitle = '';
              if ($objUser){
                     $strTitle = $objUser->name;
              }

		// avatar available and file exists
		if ($strAvatar &&
			($objFile = \FilesModel::findByUuid($strAvatar)) &&
			file_exists(TL_ROOT . '/' . $objFile->path)
		) {
			$strAvatar = $objFile->path;
		}

		else if ($GLOBALS['TL_CONFIG']['avatar_fallback_image'] &&
			($objFile = \FilesModel::findByUuid($GLOBALS['TL_CONFIG']['avatar_fallback_image'])) &&
			file_exists(TL_ROOT . '/' . $objFile->path)
		) {
			$strAvatar = $objFile->path;
		}

		// fallback to default avatar
		else {
			$strAvatar = 'system/modules/avatar/assets/male.png';
		}

		// resize if size is requested
		$this->resize($strAvatar, $arrDims);

		// generate the img tag
		return sprintf(
			'<img src="%s" width="%s" height="%s" alt="%s" title="%s" class="%s">',
			TL_FILES_URL . $strAvatar,
			$arrDims[0],
			$arrDims[1],
			$strAlt,
			$strTitle,
			$strClass
		);
	}


}
