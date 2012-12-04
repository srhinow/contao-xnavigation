Extended Navigation aka xNavigation
===================================

This is an universal navigation module for the Contao Open Source CMS.

How it works
------------

Instead of build a navigation from the page structure,
xNavigation generate the navigation based on Models.

To generate a navigation, there are three components needed:

The `Generator` define the visible items and build the navigation.

The `ItemDataSource` provide an array of Child-Models for a given navigation item.
It is used within the `Generator` to fetch the children.

The `ItemFactory` create a tree node from the model.
It is used within the `Generator` to create items from the models provided by the `ItemDataSource`.

A `Generator` can hold multiple `ItemDataSource` and `ItemFactory` objects.

The navigation tree is build with `Item` objects.
An `Item` contain the underlaying `Model`,
a type depending on the `Model` (table name without "tl_")
and required attributes (label, title, url) to build a navigation item.
Each `Item` also hold is active and trail status and it's position.
Any `Item` have a list of children, stored in an `ItemCollection`.

```
       |
    (create)
       |
       |  /---(define root Model)---\
       V  |                         |
     Generator <--------------------/
    / ^ | ^  \
    | | | |   \---(fetch children for current Item)---> ItemDataSource
    | | | |                                              |
    | | | \----------------------------------------------/
    | | |
    | | \---(create Item from current child Model)---> ItemFactory
    | |                                                 |
    | \-------------------------------------------------/
    |
 (return the generate tree)
    |
    V
```

The generated tree is an `Item` (if `Generator::$includeRoot` is `true`) or an `ItemCollection`.

Now the tree can manipulated before rendering.

// TODO Render navigation

Frontend Module
---------------

The Frontend Modules *XNavigation* and *XSitemap* provide a basic integration of the navigation.
The modules use `ItemDataSource` classes defined in `$GLOBALS['XNAVIGATION_ITEM_DATA_SOURCES']`
and `ItemFactroy` classes defined in `$GLOBALS['XNAVIGATION_ITEM_FACTORIES']` to generate the navigation.

Usage example for custom navigations
------------------------------------

To create a custom navigation, you need to implement the two interfaces `ItemDataSource` and `ItemFactory`.
Then you can create a `Generator` with this two classes:

```php
use ExtendedNavigation\Tree\Generator;

// $myRootModel is any Model object, represents the root of the tree

$generator = new Generator();
$generator->setRoot($myRootModel);

// add custom ItemDataSource and ItemFactory
$generator->addItemDataSource(new MyItemDataSource());
$generator->addItemFactory(new MyItemFactory());

// if you wish (and your MyItemDataSource support), define which items should be visible
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

// now generate the tree
$tree = $generator->generate();

// TODO Render navigation
```

**MyItemDataSource**
```php
use ExtendedNavigation\Tree\Generator;
use ExtendedNavigation\Tree\Item;
use ExtendedNavigation\Tree\ItemDataSource;

class MyItemDataSource implements ItemDataSource
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
    ) {
        if (
            $generator->isLevelAllowed(
                $currentLevel,
                $parent->getActive() ||
                $parent->getTrail()
            ) &&
            $parent->getType() == 'my_model'
        ) {
        	$children = MyModel::findByPid($parent->getModel()->id);

        	// MyModel::findByPid may return null!
        	if ($children) {
        		return $children;
        	}
        }

        return array();
	}
}
```

**MyItemFactory**
```php
use ExtendedNavigation\Tree\Generator;
use ExtendedNavigation\Tree\Item;
use ExtendedNavigation\Tree\ItemFactory;

class MyItemFactory implements ItemFactory
{
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
    	if ($model instanceof MyModel) {
			$item = new Item($model);
			$item->setLabel($model->title);
			$item->setTitle($model->title);
			$item->setUrl(Controller::generateFrontendUrl($model->row(), '/items/' . $model->alias));
			$item->setPosition($model->sorting);

			if (Input::get('items') == $model->id || Input::get('items') == $model->alias) {
				$item->setActive(true);

				$node = $item->getParent();
				while ($node) {
					$node->setTrail(true);
					$node = $node->getParent();
				}
			}

			$item->setAttribute('rel', 'noindex,nofollow');
			$item->setAttribute('class', array('my_css_class'));

			return $item;
		}
    }
}
```
