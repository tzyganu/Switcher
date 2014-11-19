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
class Easylife_Switcher_Block_Adminhtml_System_Attribute_After_Color extends Easylife_Switcher_Block_Adminhtml_System_Attribute_After_Options
{
    /**
     * @return string
     */
    public function getButtonLabel()
    {
        return Mage::helper('easylife_switcher')->__('Configure Colors');
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'color';
    }
}
