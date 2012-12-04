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

namespace ExtendedNavigation\Page;

use Controller;
use Database;
use ExtendedNavigation\Tree\Generator;
use ExtendedNavigation\Tree\Item;
use ExtendedNavigation\Tree\ItemDataSource;
use ExtendedNavigation\Tree\ItemFactory;
use Model;
use PageModel;

/**
 * Class DefaultPageProvider
 *
 *
 * @package ExtendedNavigation
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @link    http://bit3.de
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */
class DefaultPageProvider implements ItemDataSource, ItemFactory
{
    /**
     * Singleton instance.
     *
     * @var DefaultPageProvider|null
     */
    protected static $instance = null;

    /**
     * Get singleton instance.
     *
     * @return DefaultPageProvider
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new DefaultPageProvider();
        }
        return self::$instance;
    }

    /**
     * Generate an Item from the Model.
     *
     * @param \ExtendedNavigation\Tree\Generator $generator
     * @param \PageModel                         $model
     *
     * @return \ExtendedNavigation\Tree\Item
     */
    public function generateItem(
        Generator $generator,
        Model $model
    ) {
        if ($model instanceof PageModel) {
            $item = new Item($model);
            $item->setLabel($model->title);
            $item->setTitle($model->pageTitle ? : $model->title);
            $item->setUrl(Controller::generateFrontendUrl($model->row()));
            $item->setPosition($model->sorting);

            if ($GLOBALS['objPage']) {
                $item->setTrail(in_array($model->id, $GLOBALS['objPage']->trail));
                $item->setActive($model->id == $GLOBALS['objPage']->id);
            }

            if ($model->robots != 'index,follow') {
                $item->setAttribute('rel', $model->robots);
            }
            if ($model->cssClass) {
                $item->setAttribute('class', array($model->cssClass));
            }

            return $item;
        }

        return false;
    }

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
    ) {
        if (
            $generator->isLevelAllowed(
                $currentLevel,
                $parent->getActive() ||
                $parent->getTrail()
            ) &&
            $parent->getType() == 'page'
        ) {
            $columns = array('pid=?');
            $values  = array($parent->getModel()->id);

            $this->generateFindConditions($generator, $columns, $values);

            $allPages = PageModel::findBy($columns, $values);

            if ($allPages !== null) {
                // filter pages that are limited to members
                if ($generator->getIncludeMembersOnly()) {
                    $pages = array();

                    // Get all groups of the current front end user
                    $groups = $generator->getAllowedGroups();

                    foreach ($allPages as $page) {
                        $pageGroups = deserialize($page->groups, true);
                        if (!$page->protected || count(array_intersect($groups, $pageGroups))) {
                            $pages[] = $page;
                        }
                    }
                }

                // use all items
                else {
                    while ($allPages->next()) {
                        $pages[] = $allPages->current();
                    }
                }

                return $pages;
            }
        }

        return array();
    }

    /**
     * Generate search conditions for query builder.
     *
     * @param \ExtendedNavigation\Tree\Generator $generator
     *
     * @return array
     */
    protected function generateFindConditions(Generator $generator, &$columns, &$values)
    {
        // filter published items
        if (!$generator->getIncludePublished()) {
            $columns[] = 'tl_page.published != ?';
            $values[]  = '1';
        }
        // filter unpublished items
        if (!$generator->getIncludeUnpublished()) {
            $columns[] = 'tl_page.published != ?';
            $values[]  = '';
        }
        // filter hidden items
        if (!$generator->getIncludeHidden()) {
            $columns[] = 'tl_page.hide != ?';
            $values[]  = '1';
        }
        // filter members only items
        if (!$generator->getIncludeMembersOnly()) {
            $columns[] = 'tl_page.protected != ?';
            $values[]  = '1';
        }
        // filter guests only items
        if (!$generator->getIncludeGuestsOnly()) {
            $columns[] = 'tl_page.guests != ?';
            $values[]  = '1';
        }
    }
}
