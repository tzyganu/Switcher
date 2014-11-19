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
 * Source model for available switch types
 *
 * @category    Easylife
 * @package	    Easylife_Switcher
 */
class Easylife_Switcher_Model_Adminhtml_System_Config_Source_Switch
{
    /**
     * don't swithc images
     */
    const NO_SWITCH     = 0;
    /**
     * switch only main image
     */
    const SWITCH_MAIN   = 1;
    /**
     * switch media block
     */
    const SWITCH_MEDIA  = 2;
    /**
     * available options
     * @var null|mixed
     */
    protected $_options = null;
    /**
     * get the list of options
     * @access public
     * @return mixed|null
     */
    public function toOptionArray()
    {
        if (is_null($this->_options)) {
            $this->_options = array();
            $this->_options[] = array(
                'value' => self::NO_SWITCH,
                'label' => Mage::helper('easylife_switcher')->__('No image switch')
            );
            $this->_options[] = array(
                'value' => self::SWITCH_MAIN,
                'label' => Mage::helper('easylife_switcher')->__('Switch main image')
            );
            $this->_options[] = array(
                'value' => self::SWITCH_MEDIA,
                'label' => Mage::helper('easylife_switcher')->__('Switch all media section')
            );
        }
        return $this->_options;
    }
}
