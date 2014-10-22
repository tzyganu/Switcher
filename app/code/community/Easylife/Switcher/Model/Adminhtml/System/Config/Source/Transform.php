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
class Easylife_Switcher_Model_Adminhtml_System_Config_Source_Transform{
    const ALL = 1;
    const NONE = 0;
    const SPECIFIC = 2;
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
            $this->_options[] = array(
                'value' => self::ALL,
                'label' => Mage::helper('easylife_switcher')->__('All')
            );
            $this->_options[] = array(
                'value' => self::NONE,
                'label' => Mage::helper('easylife_switcher')->__('None')
            );
            $this->_options[] = array(
                'value' => self::SPECIFIC,
                'label' => Mage::helper('easylife_switcher')->__('Specific')
            );
        }
        return $this->_options;
    }
}