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
 * Table tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'includeFullHierarchy';

$GLOBALS['TL_DCA']['tl_module']['metapalettes']['xNavigation']          = array(
    'title'     => array('name', 'headline', 'type'),
    'nav'       => array('levelOffset', 'showLevel', 'hardLevel', 'showProtected'),
    'reference' => array(':hide', 'defineRoot'),
    'template'  => array(':hide', 'navigationTpl'),
    'protected' => array(':hide', 'protected'),
    'expert'    => array(
        ':hide',
        'guests',
        'cssID',
        'space'
    )
);
$GLOBALS['TL_DCA']['tl_module']['metapalettes']['xSitemap']             = array(
    'title'     => array('name', 'headline', 'type'),
    'nav'       => array('includeRoot', 'includeFullHierarchy', 'showProtected'),
    'reference' => array(':hide', 'rootPage'),
    'template'  => array(':hide', 'navigationTpl'),
    'protected' => array(':hide', 'protected'),
    'expert'    => array(
        ':hide',
        'guests',
        'cssID',
        'space'
    )
);
$GLOBALS['TL_DCA']['tl_module']['metapalettes']['includeFullHierarchy'] = array(
    'title'     => array('name', 'headline', 'type'),
    'nav'       => array('includeFullHierarchy', 'showProtected'),
    'reference' => array(':hide', 'rootPage'),
    'template'  => array(':hide', 'navigationTpl'),
    'protected' => array(':hide', 'protected'),
    'expert'    => array(
        ':hide',
        'guests',
        'cssID',
        'space'
    )
);

$GLOBALS['TL_DCA']['tl_module']['fields']['hardLevel'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['hardLevel'],
    'default'   => '0',
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => array(
        'maxlength' => 5,
        'rgxp'      => 'digit',
        'tl_class'  => 'w50'
    ),
    'sql'       => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['includeFullHierarchy'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['includeFullHierarchy'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => array(
        'submitOnChange' => true,
        'tl_class'       => 'w50'
    ),
    'sql'       => "char(1) NOT NULL default ''"
);
