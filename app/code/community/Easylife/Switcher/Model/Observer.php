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
 * @copyright   Copyright (c) 2013
 * @license	    http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Module observer
 *
 * @category    Easylife
 * @package	    Easylife_Switcher
 * @author 	    Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_Switcher_Model_Observer{
    /**
     * config path to show out of stock configurations
     */
    const XML_SHOW_OUT_OF_STOCK_PATH = 'easylife_switcher/settings/out_of_stock';

    /**
     * tell Magento to load out of stock products also
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Easylife_Switcher_Model_Observer
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function checkShowStock(Varien_Event_Observer $observer){
        if (Mage::helper('easylife_switcher')->isEnabled()){
            /** @var Mage_Catalog_Model_Product $product */
            $product = $observer->getEvent()->getProduct();
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                Mage::register('old_skip_aleable_check', Mage::helper('catalog/product')->getSkipSaleableCheck());
                Mage::helper('catalog/product')->setSkipSaleableCheck(Mage::getStoreConfigFlag(self::XML_SHOW_OUT_OF_STOCK_PATH));
            }
        }
        return $this;
    }

    /**
     * tell Magento to load out of stock products also on cart configure page
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Easylife_Switcher_Model_Observer
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function checkShowStockOnConfigure(Varien_Event_Observer $observer) {
        if (Mage::helper('easylife_switcher')->isEnabled()){
            Mage::register('old_skip_aleable_check', Mage::helper('catalog/product')->getSkipSaleableCheck());
            Mage::helper('catalog/product')->setSkipSaleableCheck(Mage::getStoreConfigFlag(self::XML_SHOW_OUT_OF_STOCK_PATH));
        }
        return $this;
    }

    /**
     * add column to simple products grid
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Easylife_Switcher_Model_Observer
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function addDefaultColumn(Varien_Event_Observer $observer){
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid){
            if (Mage::helper('easylife_switcher')->isEnabled()){
                if (!$block->isReadonly()) {
                    $block->addColumnAfter('default_combination', array(
                        'header'     => Mage::helper('easylife_switcher')->__('Default'),
                        'header_css_class' => 'a-center',
                        'type'      => 'radio',
                        'name'      => 'default_combination',
                        'values'    => $this->_getDefaultConfigurationId(),
                        'align'     => 'center',
                        'index'     => 'entity_id',
                        'html_name' => 'default_combination',
                        'sortable'  => false,
                        'filter'    => false
                    ), 'in_products');
                }
            }
        }
        return $this;
    }

    /**
     * get the default configuration
     * @access protected
     * @return array|string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getDefaultConfigurationId(){
        $product = Mage::registry('current_product');
        if ($product){
            return array($product->getData(Easylife_Switcher_Helper_Data::DEFAULT_CONFIGURATION_ATTRIBUTE_CODE));
        }
        return '';
    }
}