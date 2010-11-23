<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    xNavigation
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Class xNavigation
 *
 * Hilfsklasse die einige HOOK-Funktionen enthällt.
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    xNavigation
 */
class xNavigation extends Controller
{
	
	/**
	 * DNS page related cache.
	 * @var array
	 */
	protected static $arrDNSCache = array();

	/**
	 * Initialize the object
	 */
	public function __construct() {
		parent::__construct();
		$this->import('Database');
		$this->import('xNavigationEnvironment', 'Env');
	}

	/**
	 * Search recursive the page dns.
	 * @param array
	 * @return string
	 */
	protected function findPageDNS($arrRow) {
		if ($arrRow != null && count($arrRow)) {
			if (isset(xNavigation::$arrDNSCache[$arrRow['id']])) {
				return xNavigation::$arrDNSCache[$arrRow['id']];
			} else if ($arrRow['type'] == 'root') {
				if (!empty($arrRow['dns']))
				{
					return xNavigation::$arrDNSCache[$arrRow['id']] = $arrRow['dns'];
				}
			} else {
				$objPage = $this->Database->prepare("SELECT id,pid,type,dns FROM tl_page WHERE id=" . (empty($arrRow['pid']) ? "(SELECT pid FROM tl_page WHERE id=?)" : "?"))
										->execute(empty($arrRow['pid']) ? $arrRow['id'] : $arrRow['pid']);
				if ($objPage->next())
				{
					return xNavigation::$arrDNSCache[$arrRow['id']] = $this->findPageDNS($objPage->row());
				}
			}
		}
		if (!empty($GLOBALS['TL_CONFIG']['baseDNS']))
		{
			return $GLOBALS['TL_CONFIG']['baseDNS'];
		}
		else
		{
			$xhost = $this->Env->getHttpXForwardedHost();
			return (!empty($xhost) ? $xhost . '/' : '') . $this->Env->getHttpHost();
		}
	}
	
	/**
	 * Replace insert tags with their values
	 * @param string
	 * @param bool
	 * @return string
	 */
	protected function replaceDomainLinkInsertTags($strBuffer, $blnCache=false)
	{
		global $objPage;

		switch ($strBuffer)
		{
		case 'dns::domain':
			return $this->findPageDNS($objPage != null ? $objPage->row() : null);
		}

		return false;
	}

	/**
	 * Generate an absolute url if the domain of the target page is different from the domain of the current page.
	 * @param array
	 * @param string
	 * @param string
	 * @return string
	 */
	protected function generateDomainLink($arrRow, $strParams, $strUrl)
	{
		global $objPage;
		if (!preg_match('#^(\w+://)#', $strUrl)) {
			$strCurrent = $objPage != null ? $this->findPageDNS($objPage->row()) : $this->Env->getHttpHost();
			$strTarget = $this->findPageDNS($arrRow);
			$blnForce = $strCurrent != $strTarget;
			switch ($GLOBALS['TL_CONFIG']['secureDNS']) {
			case 'insecure':
				$strProtocol = 'http';
				if ($this->Env->isSSL())
				{
					$blnForce = true;
				}
				break;

			case 'secure':
				$strProtocol = 'https';
				if (!$this->Env->isSSL())
				{
					$blnForce = true;
				}
				break;
				
			default:
			case 'auto':
				if ($this->Env->isSSL())
				{
					$strProtocol = 'https';
				}
				else
				{
					$strProtocol = 'http';
				}
				break;
			}
			if (strlen($strTarget) && $blnForce) {
				$strUrl = $strProtocol . '://' . $strTarget . $GLOBALS['TL_CONFIG']['websitePath'] . '/' . $strUrl;
			}
		}
		return $strUrl;
	}
	
}

/**
 * Class xNavigationEnvironment
 *
 * Helper class to make some methods ov Environment public.
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    xNavigation
 */
class xNavigationEnvironment extends Environment
{
	
	/**
	 * Current object instance (Singleton)
	 * @var object
	 */
	protected static $objInstance;
	
	/**
	 * Return the current object instance (Singleton)
	 * @return object
	 */
	public static function getInstance()
	{
		if (!is_object(self::$objInstance))
		{
			self::$objInstance = new xNavigationEnvironment();
		}

		return self::$objInstance;
	}
	
	/**
	 * @see Environment.ssl()
	 */
	public function isSSL() {
		return $this->ssl();
	}
	
	/**
	 * @see Environment.httpXForwardedHost()
	 */
	public function getHttpXForwardedHost() {
		return $this->httpXForwardedHost();
	}
	
	/**
	 * @see Environment.httpHost()
	 */
	public function getHttpHost() {
		return $this->httpHost();
	}
}
?>