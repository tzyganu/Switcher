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
class Easylife_Switcher_Block_Adminhtml_Helper_Image extends Varien_Data_Form_Element_Image
{
    /**
     * @return bool|string
     */
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            /** @var Easylife_Switcher_Helper_Optimage $helper */
            $helper = Mage::helper('easylife_switcher/optimage');
            $url = $helper->getImageBaseUrl().$this->getValue();
        }
        return $url;
    }
}
