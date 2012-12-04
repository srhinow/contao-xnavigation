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

use Countable;
use ExtendedNavigation\Iterator\NavigationIterator;
use IteratorAggregate;

/**
 * ExtendedNavigation
 * extension for Contao Open Source CMS
 *
 * @package ExtendedNavigation
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @link    http://bit3.de
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */
class ItemCollection
    implements Countable, IteratorAggregate
{
    /**
     * List of items.
     *
     * @var array
     */
    protected $items = array();

    /**
     * Add an item.
     *
     * @param Item $item
     */
    public function add(Item $item)
    {
        if ($item->getPosition() === null) {
            $position = count($this->items) ? $this->items[count($this->items)-1]->getPosition() + 1 : 1;
            $item->setPosition($position);
        }

        $this->items[] = $item;

        $this->resort();
    }

    /**
     * Add multiple items.
     *
     * @param array $items
     */
    public function addAll(array $items)
    {
        foreach ($items as $item) {
            if ($item->getPosition() === null) {
                $position = count($this->items) ? $this->items[count($this->items)]->getPosition() + 1 : 1;
                $item->setPosition($position);
            }

            $this->items[] = $item;
        }

        $this->resort();
    }

    protected function resort()
    {
        usort($this->items, 'ExtendedNavigation\Tree\ItemCollection::sortByPosition');
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     *       The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Sort items by position callback.
     *
     * @param Item $a
     * @param Item $b
     *
     * @return float
     */
    public static function sortByPosition(Item $a, Item $b)
    {
        return $a->getPosition() - $b->getPosition();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return new NavigationIterator($this->items);
    }

    public function toArray()
    {
        return $this->items;
    }
}
