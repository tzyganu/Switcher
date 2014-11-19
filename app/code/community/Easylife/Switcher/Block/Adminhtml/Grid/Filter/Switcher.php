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
class Easylife_Switcher_Block_Adminhtml_Grid_Filter_Switcher extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract implements
    Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Interface
{
    /**
     * @return string
     */
    public function getHtml()
    {
        $product = $this->getProduct();
        $store = $product->getStoreId();
        if ($store) {
            $code = Easylife_Switcher_Helper_Data::DEFAULT_CONFIGURATION_ATTRIBUTE_CODE;
            $return = '<div style="display:none">';
            $return .= '<input type="checkbox"
                value="'.$code.'"
                id="'.$code.'_default"'.
                ($this->usedDefault() ? ' checked="checked"' : '').
                ' name="use_default[]" />';
            $return .= '<label for="'.$code.'_default">'.
                Mage::helper('easylife_switcher')->__('Use Configuration for default store view').
                '</label>';
            $return .= '</div>';
            return $return;
        }
        return '';
    }

    /**
     * @return bool
     */
    public function usedDefault()
    {
        $product = $this->getProduct();
        $attributeCode = Easylife_Switcher_Helper_Data::DEFAULT_CONFIGURATION_ATTRIBUTE_CODE;
        $defaultValue = $product->getAttributeDefaultValue($attributeCode);

        if (!$product->getExistsStoreValueFlag($attributeCode)) {
            return true;
        } elseif (
            $product->getData(Easylife_Switcher_Helper_Data::DEFAULT_CONFIGURATION_ATTRIBUTE_CODE) == $defaultValue
            && $product->getStoreId() != Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID
        ) {
            return false;
        }
        return $defaultValue === false;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }
}
