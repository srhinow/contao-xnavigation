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
 * Class ItemFactory
 *
 * @package ExtendedNavigation
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @link    http://bit3.de
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */
interface ItemFactory
{
    /**
     * Generate an Item from the Model.
     *
     * @param \ExtendedNavigation\Tree\Generator $generator
     * @param \Model                             $model
     *
     * @return \ExtendedNavigation\Tree\Item
     */
    public function generateItem(
        Generator $generator,
        Model $model
    );
}
