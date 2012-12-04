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

use Model;

/**
 * Class Item
 *
 *
 * @package ExtendedNavigation
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @link    http://bit3.de
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */
class Item
{
    /**
     * The data model for this item.
     *
     * @var \Model
     */
    protected $model;

    /**
     * Type of the item.
     *
     * @var string
     */
    protected $type;

    /**
     * Label of the item.
     *
     * @var string
     */
    protected $label = null;

    /**
     * Title of the item.
     *
     * @var string
     */
    protected $title = null;

    /**
     * URL of the item.
     *
     * @var string
     */
    protected $url = null;

    /**
     * Position as positive float, starting at 0.
     *
     * @var float
     */
    protected $position = null;

    /**
     * The item is in trail.
     *
     * @var bool
     */
    protected $trail = false;

    /**
     * The item is active.
     *
     * @var bool
     */
    protected $active = false;

    /**
     * The parent item or <em>null</em> if there is no parent item.
     *
     * @var null|Item
     */
    protected $parent = null;

    /**
     * Additional attributes.
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * List of children.
     *
     * @var ItemCollection
     */
    protected $children;

    /**
     * Create new item.
     */
    function __construct(Model $model)
    {
        $this->model    = $model;
        $this->type     = preg_replace('#^tl_#', '', $model->getTable());
        $this->children = new ItemCollection();
    }

    /**
     * @return \Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = is_null($label) ? null : (string) $label;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = is_null($title) ? null : (string) $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = is_null($url) ? null : (string) $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param float $position
     */
    public function setPosition($position)
    {
        $this->position = is_null($position) ? null : (float) $position;
    }

    /**
     * @return float|null
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param boolean $trail
     */
    public function setTrail($trail)
    {
        $this->trail = $trail;
    }

    /**
     * @return boolean
     */
    public function getTrail()
    {
        return $this->trail;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set an attribute.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Get an attribute.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getAttribute($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return null;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param \ExtendedNavigation\Tree\Item|null $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return \ExtendedNavigation\Tree\Item|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param ItemCollection $children
     */
    public function setChildren(ItemCollection $children)
    {
        $this->children = $children;

        foreach ($this->children as $children) {
            $children->setParent($this);
        }
    }

    /**
     * @return ItemCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add a new item.
     *
     * @param Item $child
     */
    public function addChildren(Item $children)
    {
        $this->children->add($children);
        $children->setParent($this);
    }

    /**
     * Add a new item.
     *
     * @param Item $child
     */
    public function addChildrens(array $childrens)
    {
        $this->children->addAll($childrens);

        foreach ($childrens as $children) {
            $children->setParent($this);
        }
    }

    /**
     * Magic getter.
     *
     * @param $name
     */
    function __get($name)
    {
        switch ($name) {
            case 'type':
            case 'label':
            case 'title':
            case 'url':
            case 'position':
            case 'attributes':
            case 'children':
                return $this->$name;

            default:
                return $this->attributes[$name];
        }
    }

    /**
     * Magic setter.
     *
     * @param string $name
     * @param mixed  $value
     */
    function __set($name, $value)
    {
        switch ($name) {
            case 'type':
                $this->setType($value);
                break;

            case 'label':
                $this->setLabel($value);
                break;

            case 'title':
                $this->setTitle($value);
                break;

            case 'url':
                $this->setUrl($value);
                break;

            case 'position':
                $this->setPosition($value);
                break;

            case 'attributes':
                $this->setAttributes($value);
                break;

            case 'children':
                $this->setChildren($value);
                break;

            default:
                unset($this->attributes[$name]);
        }
    }

    /**
     * Magic isset.
     *
     * @param string $name
     *
     * @return bool
     */
    function __isset($name)
    {
        switch ($name) {
            case 'type':
            case 'label':
            case 'title':
            case 'url':
            case 'position':
            case 'attributes':
            case 'children':
                return true;

            default:
                return isset($this->attributes[$name]);
        }
    }

    /**
     * Magic unset.
     *
     * @param string $name
     */
    function __unset($name)
    {
        switch ($name) {
            case 'type':
            case 'label':
            case 'title':
            case 'url':
            case 'position':
            case 'attributes':
            case 'children':
                $this->$name = null;

            default:
                unset($this->attributes[$name]);
        }
    }

    function __toString()
    {
        return sprintf(
            '%s (%s)',
            $this->label,
            $this->url
        );
    }
}
