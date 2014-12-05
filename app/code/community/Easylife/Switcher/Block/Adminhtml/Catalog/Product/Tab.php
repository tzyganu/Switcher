<?php
class Easylife_Switcher_Block_Adminhtml_Catalog_Product_Tab extends Mage_Adminhtml_Block_Widget_Form implements
    Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_template = 'easylife_switcher/catalog/product.phtml';
    public function getTabLabel()
    {
        return Mage::helper('easylife_switcher')->__('Easylife Switcher');
    }
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }
    public function isHidden()
    {
        return false;
    }
    public function canShowTab()
    {
        return $this->getProduct() &&
            $this->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }
    protected function _prepareForm(){
        $form = new Varien_Data_Form();
        $form->setFieldNameSuffix('easylfie_switcher');
        $this->setForm($form);
        $fieldset = $form->addFieldset('easylife_switcher_config', array('legend'=>Mage::helper('easylife_switcher')->__('Switcher config')));
        Mage::getSingleton('easylife_switcher/config')->addFieldToFieldset($fieldset);
//        $fieldset->addField('use_config', 'select', array(
//            'label' => Mage::helper('easylife_switcher')->__('Use Config'),
//            'name'  => 'use_config',
//            'options' => array(
//                1 => Mage::helper('easylife_switcher')->__('Yes'),
//                0 => Mage::helper('easylife_switcher')->__('No')
//            )
//        ));
//        $fieldset->addField('enabled', 'select', array(
//            'label' => Mage::helper('easylife_switcher')->__('Enabled'),
//            'name'  => 'enabled',
//            'options' => array(
//                1 => Mage::helper('easylife_switcher')->__('Use Config'),
//                0 => Mage::helper('easylife_switcher')->__('No')
//            )
//        ));
//        foreach ($this->getConfiguableAttributes() as $attribute) {
//            $productAttribute = $attribute->getProductAttribute();
//            $fieldset->addField('attr_'.$productAttribute->getId(), 'select', array(
//                'label' => $productAttribute->getAttributeLabel(),
//                'name'  => 'attr_'.$productAttribute->getId(),
//                'options' => array(
//                    1 => Mage::helper('easylife_switcher')->__('Use Config'),
//                    0 => Mage::helper('easylife_switcher')->__('No')
//                )
//            ));
//        }

        return parent::_prepareForm();
    }

    public function getConfiguableAttributes()
    {
        return $this->getProduct()->getTypeInstance(true)->getConfigurableAttributes($this->getProduct());
    }
}