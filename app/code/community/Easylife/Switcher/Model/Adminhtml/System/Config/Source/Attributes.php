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
 * Source model for available attributes
 *
 * @category    Easylife
 * @package	    Easylife_Switcher
 * @author 	    Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_Switcher_Model_Adminhtml_System_Config_Source_Attributes{
    /**
     * available options
     * @var null|mixed
     */
    protected $_options = null;
    /**
     * get the list of attributes
     * @access public
     * @param bool $withEmpty
     * @return mixed|null
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function toOptionArray($withEmpty = true){
        if (is_null($this->_options)){
            $collection = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addVisibleFilter()
                ->addFieldToFilter('is_configurable', 1)
                ->addFieldToFilter('frontend_input', 'select')
            ;
            $this->_options = array();
            if($withEmpty) {
                $this->_options[] = array(
                    'label'=>Mage::helper('easylife_switcher')->__('--- None ---'),
                    'value'=>''
                );
            }
            foreach ($collection as $attribute){
                if (Mage::getSingleton('catalog/product_type_configurable')->canUseAttribute($attribute)){
                    $this->_options[] = array(
                        'label'=>$attribute->getFrontendLabel(),
                        'value'=>$attribute->getAttributeCode()
                    );
                }
            }
        }
        return $this->_options;
    }
}