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
abstract class Easylife_Switcher_Block_Adminhtml_System_Attribute_After_Options extends Easylife_Switcher_Block_Adminhtml_System_Attribute_After_Abstract
{
    /**
     * @var string
     */
    protected $_template = 'easylife_switcher/system/config/attribute/after.phtml';

    /**
     * @return string
     */
    abstract public function getButtonLabel();

    /**
     * @return string
     */
    abstract public function getType();
    /**
     * @return mixed
     */
    public function getSubmitUrl()
    {
        return 0;
    }

    /**
     * @return string
     */
    public function getButtonUrl()
    {
        return $this->getUrl('adminhtml/switcher/configure');
    }

    /**
     * @return string
     */
    public function getButton()
    {
        /** @var Mage_Adminhtml_Block_Widget_Button $button */
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'id'        => $this->getHtmlPrefix().'_'.$this->getAttributeId().'_'.$this->getType().'_button',
                    'label'     => $this->getButtonLabel(),
                    'onclick'   => $this->getHtmlPrefix()."_switcher_config.fillValues('".
                                        $this->getButtonUrl()."', ".$this->getAttributeId().", '".
                                        $this->getType()."', '".$this->getSubmitUrl()."')",
                    'disabled'  => $this->getDisabled()
                )
            );
        return $button->toHtml();
    }

    /**
     * @return string
     */
    public function getInputName()
    {
        return $this->getRendererInstance()->getName().'[options]['.$this->getAttributeId().']['.$this->getType().']';
    }

    /**
     * @return string
     */
    public function getInputId()
    {
        return $this->getHtmlPrefix().'_'.$this->getAttributeId().'_'.$this->getType();
    }

    /**
     * @return mixed|string
     */
    public function getInputValue()
    {
        $values = $this->getRendererInstance()->getValue();
        if (isset($values['options'][$this->getAttributeId()][$this->getType()])) {
            return Mage::helper('core')->escapeHtml($values['options'][$this->getAttributeId()][$this->getType()]);
        }
        return '';
    }
}
