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
abstract class Easylife_Switcher_Block_Adminhtml_System_Attribute_After_Abstract extends Mage_Adminhtml_Block_Template
{
    /**
     * @var Easylife_Switcher_Block_Adminhtml_System_Attribute_Renderer
     */
    protected $_rendererInstance;
    /**
     * @var Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected $_attributeInstance;

    /**
     * @param Easylife_Switcher_Block_Adminhtml_System_Attribute_Renderer $rendererInstance
     * @return $this
     */
    public function setRendererInstance(Easylife_Switcher_Block_Adminhtml_System_Attribute_Renderer $rendererInstance)
    {
        $this->_rendererInstance = $rendererInstance;
        return $this;
    }

    /**
     * @return Easylife_Switcher_Block_Adminhtml_System_Attribute_Renderer
     */
    public function getRendererInstance()
    {
        return $this->_rendererInstance;
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attributeInstance
     * @return $this
     */
    public function setAttributeInstance(Mage_Catalog_Model_Resource_Eav_Attribute $attributeInstance)
    {
        $this->_attributeInstance = $attributeInstance;
        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getAttributeInstance()
    {
        return $this->_attributeInstance;
    }

    /**
     * @return mixed
     */
    public function getDisabled()
    {
        return $this->getRendererInstance()->getDisabled();
    }

    /**
     * @return string
     */
    public function getHtmlPrefix()
    {
        return $this->getRendererInstance()->getHtmlId();
    }

    /**
     * @return mixed
     */
    public function getAttributeId()
    {
        return $this->getAttributeInstance()->getId();
    }
}
