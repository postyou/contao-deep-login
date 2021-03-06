<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   deepLogin
 * @author    Gerald Meier
 * @license   LGPL
 * @copyright Postyou 2016
 */

$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .=
    ';{login_legend:hide},globalLogin';

// Add fields
$GLOBALS['TL_DCA']['tl_settings']['fields']['globalLogin'] = array
(
    'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['globalLogin'],
    'exclude'			=> true,
    'inputType'			=> 'checkbox',
    'eval'				=> array('submitOnChange'=>true, 'tl_class' => 'clr w50'),
    'sql'				=> "char(1) NOT NULL default ''"
);