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

$GLOBALS['TL_DCA']['tl_module']['palettes']['deep_login']=
   '{title_legend},name,headline,type;
   {config_legend},autologin;
   {redirect_legend},use_jumpTo;
   {template_legend:hide},cols;
   {protected_legend:hide},protected;
   {expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['subpalettes']['use_jumpTo']="jumpTo,redirectBack";
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][]= 'use_jumpTo';

$GLOBALS['TL_DCA']['tl_module']['fields']['use_jumpTo']=array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['use_jumpTo'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('submitOnChange'=>true),
    'sql'                     => "char(1) NOT NULL default ''"
);