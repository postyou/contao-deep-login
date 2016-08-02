<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   deepLogin
 * @author    Gerald Meier
 * @license   LGPL
 * @copyright Postyou 2016
 */


if (!defined('TL_ROOT')) die('You cannot access this file directly!');

$GLOBALS['TL_HOOKS']['generatePage'][] = array('postyou\LoginPage', 'checkLoginStatus');
$GLOBALS['TL_HOOKS']['getPageStatusIcon'][] = array('postyou\LoginPage', 'getLoginPageIcon');

$GLOBALS['TL_PTY']['login']='postyou\LoginPage';

