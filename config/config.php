<?php

/**
 * BeAvatar for Contao Open Source CMS
 * Copyright (C) 2014 Marko Cupic <m.cupic@gmx.ch>
 * @package Be_avatar
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Register hooks
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('BeAvatar\InsertTags', 'replaceTags');

