<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'postyou',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'postyou\LoginPage'       => 'system/modules/deepLogin/classes/LoginPage.php',
	'postyou\ModuleDeepLogin' => 'system/modules/deepLogin/classes/ModuleDeepLogin.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'fe_login'            => 'system/modules/deepLogin/templates',
	'mod_login_deep_1cl'  => 'system/modules/deepLogin/templates',
	'mod_login_deep_2cl'  => 'system/modules/deepLogin/templates',
	'mod_logout_deep_1cl' => 'system/modules/deepLogin/templates',
	'mod_logout_deep_2cl' => 'system/modules/deepLogin/templates',
));
