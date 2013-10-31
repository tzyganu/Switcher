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
 * Source model for available image attributes
 *
 * @category    Easylife
 * @package	    Easylife_Switcher
 * @author 	    Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_Switcher_Model_Adminhtml_System_Config_Source_Image_Attributes{
    /**
     * options
     * @var null|mixed
     */
    protected $_options = null;

    /**
     * get the list of options
     * @access public
     * @param bool $withEmpty
     * @return mixed|null
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function toOptionArray($withEmpty = false){
        if (is_null($this->_options)){
            $collection = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addVisibleFilter()
                ->addFieldToFilter('frontend_input', 'media_image')
            ;
            foreach ($collection as $attribute){
                $this->_options[] = array(
                    'label'=>$attribute->getFrontendLabel(),
                    'value'=>$attribute->getAttributeCode()
                );
            }
        }
        return $this->_options;
    }
}