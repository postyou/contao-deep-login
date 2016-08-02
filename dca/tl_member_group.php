<?php
/**
 * htdocs
 * Extension for Contao Open Source CMS (contao.org)
 *
 * Copyright (c) 2016 POSTYOU
 *
 * @package htdocs
 * @author  Gerald Meier
 * @link    http://www.postyou.de
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

if(preg_match("/{redirect_legend:hide},redirect;/",$GLOBALS['TL_DCA']['tl_member_group']['palettes']['default'])!==false) {
    $GLOBALS['TL_DCA']['tl_member_group']['palettes']['default'] =
        preg_replace(
            "/{redirect_legend:hide},redirect;/",
            "",
            $GLOBALS['TL_DCA']['tl_member_group']['palettes']['default']
        );
}