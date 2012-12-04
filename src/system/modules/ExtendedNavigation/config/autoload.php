<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package ExtendedNavigation
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Page
	'ExtendedNavigation\Page\DefaultPageProvider' => 'system/modules/ExtendedNavigation/Page/DefaultPageProvider.php',

    // Iterator
	'ExtendedNavigation\Iterator\NavigationIterator' => 'system/modules/ExtendedNavigation/Iterator/NavigationIterator.php',
	'ExtendedNavigation\Iterator\RecursiveNavigationIterator' => 'system/modules/ExtendedNavigation/Iterator/RecursiveNavigationIterator.php',

	// Tree
	'ExtendedNavigation\Tree\ItemCollection'      => 'system/modules/ExtendedNavigation/Tree/ItemCollection.php',
	'ExtendedNavigation\Tree\ItemDataSource'      => 'system/modules/ExtendedNavigation/Tree/ItemDataSource.php',
	'ExtendedNavigation\Tree\ItemFactory'         => 'system/modules/ExtendedNavigation/Tree/ItemFactory.php',
	'ExtendedNavigation\Tree\Item'                => 'system/modules/ExtendedNavigation/Tree/Item.php',
	'ExtendedNavigation\Tree\Generator'           => 'system/modules/ExtendedNavigation/Tree/Generator.php',

	// Module
	'ExtendedNavigation\Module\XSitemap'          => 'system/modules/ExtendedNavigation/Module/XSitemap.php',
	'ExtendedNavigation\Module\XNavigation'       => 'system/modules/ExtendedNavigation/Module/XNavigation.php',
));
