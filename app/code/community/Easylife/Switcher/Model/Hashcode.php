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
class Easylife_Switcher_Model_Hashcode extends Mage_Core_Model_Abstract {
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'easylife_switcher_hascode';
    const CACHE_TAG = 'easylife_switcher_hascode';
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'easylife_switcher_hascode';

    /**
     * Parameter name in event
     * @var string
     */
    protected $_eventObject = 'hashcode';

    /**
     * constructor
     * @access public
     * @return void
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function _construct() {
        parent::_construct();
        $this->_init('easylife_switcher/hashcode');
    }
}