<?php
/**
 * Easylife_Switcher extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE_EASYLIFE_SWITCHER.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category   	Easylife
 * @package	    Easylife_Switcher
 * @copyright   2013 - 2014 Marius Strajeru
 * @license	    http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Module helper
 *
 * @category    Easylife
 * @package	    Easylife_Switcher
 */
class Easylife_Switcher_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * config path to enabled flag
     */
    const XML_ENABLED_PATH                      = 'easylife_switcher/settings/enabled';
    /**
     * default configuration attribute code
     */
    const DEFAULT_CONFIGURATION_ATTRIBUTE_CODE  = 'default_configuration_id';

    /**
     * check if the module is enabled
     * @access public
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_ENABLED_PATH);
    }

    /**
     * check if module is enabled in admin
     * @return bool
     */
    public function isEnabledAdmin()
    {
        $store = Mage::app()->getRequest()->getParam('store', 0);
        return Mage::getStoreConfigFlag(self::XML_ENABLED_PATH, $store);
    }

}
