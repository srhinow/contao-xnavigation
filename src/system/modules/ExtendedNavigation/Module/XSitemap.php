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

/**
 * Class ModuleXSitemap
 *
 * Front end module "xSitemap".
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    xNavigation
 */
class XSitemap extends XNavigation {

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_sitemap';
	

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### SITEMAP ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'typolight/main.php?do=modules&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		if ($this->rootPage)
		{
			$this->rootPageObj = $this->Database->prepare("SELECT * FROM tl_page WHERE id=?")->execute($this->rootPage);
			$this->rootPageObj->next();
		}
		else
		{
			$this->rootPage = 0;
			$this->rootPageObj = null;
		
			if (!$this->includeFullHierarchy)
			{
				// calculate the root page
				$arrTrail = $GLOBALS['objPage']->trail;
				$objPage = $this->Database->execute("
						SELECT
							*
						FROM
							`tl_page`
						WHERE
								`id` IN (" . implode(',', $arrTrail) . ")
							AND `type`='root'
						ORDER BY
							`id`=" . implode(',`id`=', $arrTrail) . "
						LIMIT
							1");
				if ($objPage->next())
				{
					$this->rootPage = $objPage->id;
					$this->rootPageObj = $objPage;
				}
			}
		}
		
		return parent::generate();
	}


	/**
	 * Generate module
	 */
	protected function compile()
	{
		$this->showLevel = 0;
		$this->hardLimit = false;
		$this->levelOffset = 0;

		// create a level_0 root item with its sitemap
		if (!$this->includeFullHierarchy && $this->includeRoot && $this->rootPageObj)
		{
			$this->import('xNavigationPageProvider');
			
			if ($this->rootPageObj->pid > 0)
			{
				$objParentPage = $this->Database->prepare("
						SELECT
							*
						FROM
							`tl_page`
						WHERE
							`id`=?")
					->execute($this->rootPageObj->pid);
			}
			else
			{
				$objParentPage = $this->Database->execute("
						SELECT
							0 as `id`");
			}
			
			$objTemplate = new FrontendTemplate($this->navigationTpl);
	
			$objTemplate->type = get_class($this);
			$objTemplate->level = 'level_0';
			
			$arrItems = array();
			$this->Template->items = $this->xNavigationPageProvider->generateItem($this->rootPageObj,
				$this,
				$objParentPage,
				false,
				true,
				$arrItems,
				0,
				$this->showLevel,
				$this->hardLevel);
			
			// Add classes first and last
			if (count($arrItems))
			{
				$last = count($arrItems) - 1;
				
				$arrItems[0]['class'] = trim($arrItems[0]['class'] . ' first');
				$arrItems[$last]['class'] = trim($arrItems[$last]['class'] . ' last');
			}
	
			$objTemplate->items = $arrItems;
			$this->Template->items = $objTemplate->parse();
		}
		// create the regular sitemap
		else
		{
			$this->Template->items = $this->renderXNavigation($this->rootPage);
		}
	}
}
