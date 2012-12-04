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

namespace ExtendedNavigation\Module;

use ExtendedNavigation\Tree\Generator;
use Module;
use PageModel;
use System;

/**
 * Class XNavigation
 *
 *
 * @package ExtendedNavigation
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @link    http://bit3.de
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */
class XNavigation extends Module
{
    /**
     * Template
     *
     * @var string
     */
    protected $strTemplate = 'mod_xnavigation';

    /**
     * Do not display the module if there are no menu items
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### NAVIGATION MENU ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?do=modules&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }


    /**
     * Generate module
     */
    protected function compile()
    {
        global $objPage;

        $trail    = $objPage->trail;
        $intLevel = ($this->levelOffset > 0) ? $this->levelOffset : 0;

        // Overwrite with custom reference page
        if ($this->defineRoot && $this->rootPage > 0) {
            $trail    = array($this->rootPage);
            $intLevel = 0;
        }

        $request = ampersand($this->Environment->request, true);

        if ($request == 'index.php') {
            $request = '';
        }

        $this->Template->request        = $request;
        $this->Template->skipId         = 'skipNavigation' . $this->id;
        $this->Template->skipNavigation = specialchars($GLOBALS['TL_LANG']['MSC']['skipNavigation']);
        $this->Template->items          = false;

        if (isset($trail[$intLevel])) {
            $root = PageModel::findByPk($trail[$intLevel]);

            if ($root) {
                $generator = new Generator();
                $generator->setRoot($root);

                foreach ($GLOBALS['XNAVIGATION_ITEM_DATA_SOURCES'] as $class) {
                    $dataSource = System::importStatic($class);
                    $generator->addItemDataSource($dataSource);
                }

                foreach ($GLOBALS['XNAVIGATION_ITEM_FACTORIES'] as $class) {
                    $factory =  System::importStatic($class);
                    $generator->addItemFactory($factory);
                }

                if (FE_USER_LOGGED_IN) {
                    $user = System::importStatic('FrontendUser');

                    $generator->setIncludeMembersOnly(true);
                    $generator->setAllowedGroups(deserialize($user->groups, true));
                    $generator->setIncludeGuestsOnly(false);
                }
                else {
                    $generator->setIncludeMembersOnly(false);
                    $generator->setIncludeGuestsOnly(true);
                }

                if (BE_USER_LOGGED_IN) {
                    $generator->setIncludeUnpublished(true);
                }
                else {
                    $generator->setIncludeUnpublished(false);
                }

                $tree = $generator->generate();

                $iterator = new \ExtendedNavigation\Iterator\NavigationIterator($tree->toArray());
                // $iterator = new \RecursiveTreeIterator(new \ExtendedNavigation\Iterator\RecursiveNavigationIterator($tree->toArray()));

                header('Content-Type: text/plain; charset=utf-8');
                foreach ($iterator as $item) {
                    echo $item . "\n";
                }
                exit;
            }
        }
    }

}
