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

namespace ExtendedNavigation\Tree;

use ExtendedNavigation\Tree\Generator;
use ExtendedNavigation\Tree\Item;
use Model;

/**
 * Class ItemDataSource
 *
 * @package ExtendedNavigation
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @link    http://bit3.de
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */
interface ItemDataSource
{
    /**
     * Collect a model list of all children for a parent item.
     *
     * @param \ExtendedNavigation\Tree\Generator $generator
     * @param \Model                             $parent
     *
     * @return array
     */
    public function collectChildModels(
        Generator $generator,
        Item $parent,
        $currentLevel
    );
}
