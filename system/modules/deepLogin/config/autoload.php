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
	'postyou\LoginPage'           => 'system/modules/deepLogin/classes/LoginPage.php',
	'postyou\ModuleLoginDeepLink' => 'system/modules/deepLogin/classes/ModuleLoginDeepLink.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'fe_login' => 'system/modules/deepLogin/templates',
));
