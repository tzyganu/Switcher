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
* @category     Easylife
* @package      Easylife_Switcher
* @copyright    Copyright (c) 2014
* @license      http://opensource.org/licenses/mit-license.php MIT License
*/


/**
 * Attribute option Images tab
 *
 * @category   Easylife
 * @package    Easylife_Switcher
 * @author     Emil [carco] Sirbu <emil.sirbu@gmail.com>
 */

class Easylife_Switcher_Block_Adminhtml_Catalog_Product_Attribute_Edit_Tab_Optimage extends  Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    /**
     * Attribute option collection, with image/hexa code attached
     *
     */

    protected $_optCollection = null;


    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel() {
        return Mage::helper('easylife_switcher/optimage')->__('Manage Option Images');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle() {
        return Mage::helper('easylife_switcher/optimage')->__('Manage Option Images');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab() {

        $attributes = Mage::helper('easylife_switcher/optimage')->getColorImageAttributes();
        if(!$attributes) {
            return false;
        }
        $attribute = Mage::registry('entity_attribute');

        return (
            $attribute && 
            $attribute->getAttributeCode() && 
            $attribute->getIsConfigurable() && 
            $attribute->getFrontendInput() == 'select' && 
            in_array($attribute->getAttributeCode(),$attributes)
        );
    }

    /**
     * Tab is hidden?
     *
     * @return boolean
     */
    public function isHidden() {
        return false;
    }

    /**
     * Get attribute option collection with image/hexa code attached
     *
     * @return Varien_Collection|Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection
     */
    public function getOptionsCollection()
    {
        if(!$this->_optCollection) {
            $attribute = Mage::registry('entity_attribute');
            if(!$attribute || !$attribute->getId()) {
                $this->_optCollection = new Varien_Collection();
            } else {
                $this->_optCollection = Mage::getModel('easylife_switcher/optimage')->getOptionCollection($attribute->getId());
            }
        }
        return $this->_optCollection;
    }


    /**
     * Get upload action
     *
     * @return string
     */
    public function getUploadImageUrl()
    {
        return $this->getUrl('*/attribute_image/upload',array('_current'=>true));
    }
}
