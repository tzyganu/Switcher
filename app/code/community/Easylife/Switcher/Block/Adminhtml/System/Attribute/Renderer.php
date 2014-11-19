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
class Easylife_Switcher_Block_Adminhtml_System_Attribute_Renderer extends Mage_Core_Block_Template
{
    /**
     * path to transform config
     */
    const XML_TRANSFORM_OPTIONS_PATH = 'easylife_switcher/system/transform';
    /**
     * @var Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    protected $_attributes;
    /**
     * @var array
     */
    protected $_transformOptions;
    /**
     * @var array
     */
    protected $_transformOptionsArray;
    /**
     * @var Varien_Data_Form_Element_Abstract
     */
    protected $_element;
    /**
     * @var Varien_Data_Form
     */
    protected $_form;
    /**
     * @var string
     */
    protected $_template = 'easylife_switcher/system/config/form/field/attribute.phtml';

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return $this
     */
    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $collection = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addVisibleFilter()
                ->addFieldToFilter('is_configurable', 1)
                ->addFieldToFilter('frontend_input', 'select')
                ->addFieldToFilter('is_global', 1)
                ->addFieldToFilter('is_user_defined', 1);
            $this->_attributes = $collection;
        }
        return $this->_attributes;
    }

    /**
     * @return Varien_Data_Form
     */
    public function getForm()
    {
        if (is_null($this->_form)) {
            $this->_form = new Varien_Data_Form();
            $fieldset = $this->_form->addFieldset(
                $this->getHtmlId().'_attributes',
                array(
                    'legend' => Mage::helper('easylife_switcher')->__('Configurable attributes transforms')
                )
            );
            $fieldset->addField(
                $this->getHtmlId().'_table_header',
                'label',
                array(
                    'label' => Mage::helper('easylife_switcher')->__('Attribute'),
                    'value' => Mage::helper('easylife_switcher')->__('Transformation'),
                )
            );
            foreach ($this->getAttributes() as $attribute) {
                /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
                $fieldset->addField(
                    $attribute->getId(),
                    'select',
                    array(
                        'name' => $attribute->getId(),
                        'label' => $attribute->getFrontendLabel(),
                        'options' => $this->getTransformationOptionsArray(),
                        'after_element_html' => $this->getAfterHtml($attribute),
                        'class' => 'transform',
                        'disabled' => $this->getDisabled()
                    )
                );
            }
            $this->_form->setValues($this->getValue());
            $this->_form->setHtmlIdPrefix($this->getHtmlId().'_');
            $this->_form->setFieldNameSuffix($this->getName());

        }
        return $this->_form;
    }

    /**
     * @return array
     */
    public function getTransformationOptions()
    {
        if (is_null($this->_transformOptions)) {
            $config = Mage::getConfig()->getNode(self::XML_TRANSFORM_OPTIONS_PATH);
            if ($config) {
                $this->_transformOptions = (array)$config;
            } else {
                $this->_transformOptions = array();
            }
        }
        return $this->_transformOptions;
    }

    /**
     * @return array
     */
    public function getTransformationOptionsArray()
    {
        if (is_null($this->_transformOptionsArray)) {
            $this->_transformOptionsArray = array();
            foreach ($this->getTransformationOptions() as $key => $settings) {
                $this->_transformOptionsArray[$key] = $settings->label;
            }
        }
        return $this->_transformOptionsArray;
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return string
     * @throws Easylife_Switcher_Exception
     */
    public function getAfterHtml(Mage_Catalog_Model_Resource_Eav_Attribute $attribute)
    {
        $afterHtml = '<div class="after">';
        foreach ($this->getTransformationOptions() as $key => $option) {
            if ((string)$option->after_block) {
                /** @var Easylife_Switcher_Block_Adminhtml_System_Attribute_After_Abstract $block */
                $block = Mage::app()->getLayout()->createBlock((string)$option->after_block);
                if (!($block instanceof Easylife_Switcher_Block_Adminhtml_System_Attribute_After_Abstract)) {
                    throw new Easylife_Switcher_Exception(
                        "After block is not an instance of Easylife_Switcher_Block_Adminhtml_System_Attribute_After_Abstract"
                    );
                }
                $block->setAttributeInstance($attribute);
                $block->setRendererInstance($this);
                $afterHtml .= '<div id="'.$this->getHtmlId().'_'.$attribute->getId().'_'.$key.'">'.
                                $block->toHtml().
                                '</div>';
            }
        }
        $afterHtml .= '</div>';
        return $afterHtml;

    }

    /**
     * @return string
     */
    public function getHtmlId()
    {
        return $this->getElement()->getHtmlId();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getElement()->getName();
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->getElement()->getValue();
    }

    /**
     * @return bool
     */
    public function getDisabled()
    {
        return $this->getElement()->getDisabled();
    }
}
