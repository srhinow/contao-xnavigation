<?php

/**
 * ExtendedNavigation
 * extension for Contao Open Source CMS
 *
 * @package ExtendedNavigation
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @link    http://bit3.de
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['navigationMenu']['xNavigation'] = 'ExtendedNavigation\\Module\\XNavigation';
$GLOBALS['FE_MOD']['navigationMenu']['xSitemap']    = 'ExtendedNavigation\\Module\\XSitemap';


/**
 * Default navigation data sources used in XNavigation module.
 */
$GLOBALS['XNAVIGATION_ITEM_DATA_SOURCES']['page'] = 'ExtendedNavigation\Page\DefaultPageProvider';


/**
 * Default navigation item factories used in XNavigation module.
 */
$GLOBALS['XNAVIGATION_ITEM_FACTORIES']['page'] = 'ExtendedNavigation\Page\DefaultPageProvider';
