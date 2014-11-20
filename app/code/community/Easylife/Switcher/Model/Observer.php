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
 * Module observer
 *
 * @category    Easylife
 * @package	    Easylife_Switcher
 */
class Easylife_Switcher_Model_Observer
{
    /**
     * config path to show out of stock configurations
     */
    const XML_SHOW_OUT_OF_STOCK_PATH = 'easylife_switcher/settings/out_of_stock';

    /**
     * @return Mage_Catalog_Helper_Product
     */
    protected function _getCatalogHelper()
    {
        return Mage::helper('catalog/product');
    }

    /**
     * @return Easylife_Switcher_Helper_Data
     */
    protected function _getSwitcherHelper()
    {
        return Mage::helper('easylife_switcher');
    }
    /**
     * tell Magento to load out of stock products also
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Easylife_Switcher_Model_Observer
     */
    public function checkShowStock(Varien_Event_Observer $observer)
    {
        if ($this->_getSwitcherHelper()->isEnabled()) {
            /** @var Mage_Catalog_Model_Product $product */
            $product = $observer->getEvent()->getProduct();
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                Mage::register('old_skip_aleable_check', $this->_getCatalogHelper()->getSkipSaleableCheck());
                $this->_getCatalogHelper()->setSkipSaleableCheck(
                    Mage::getStoreConfigFlag(self::XML_SHOW_OUT_OF_STOCK_PATH)
                );
            }
        }
        return $this;
    }

    /**
     * add column to simple products grid
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Easylife_Switcher_Model_Observer
     */
    public function addDefaultColumn(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid) {
            /** @var Easylife_Switcher_Helper_Data $helper */
            $helper = Mage::helper('easylife_switcher');
            if ($helper->isEnabledAdmin()) {
                if (!$block->isReadonly()) {
                    $block->addColumnAfter(
                        'default_combination',
                        array(
                            'header'     => Mage::helper('easylife_switcher')->__('Default'),
                            'header_css_class' => 'a-center',
                            'type'      => 'radio',
                            'name'      => 'default_combination',
                            'values'    => $this->_getDefaultConfigurationId(),
                            'align'     => 'center',
                            'index'     => 'entity_id',
                            'html_name' => 'default_combination',
                            'sortable'  => false,
                            'filter'    => 'Easylife_Switcher_Block_Adminhtml_Grid_Filter_Switcher',
                        ),
                        'in_products'
                    );
                }
            }
        }
        return $this;
    }

    /**
     * get the default configuration
     * @access protected
     * @return array|string
     */
    protected function _getDefaultConfigurationId()
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::registry('current_product');
        if ($product) {
            return array($product->getData(Easylife_Switcher_Helper_Data::DEFAULT_CONFIGURATION_ATTRIBUTE_CODE));
        }
        return '';
    }

    /**
     * Clean generated thumbs for attribute option images
     * @access public
     * @param $observer
     * @return null
     */

    public function cleanOptImages($observer)
    {
        /** @var Easylife_Switcher_Helper_Optimage $helper */
        $helper = Mage::helper('easylife_switcher/optimage');
        $helper->cleanCache();
        return $this;
    }
}
