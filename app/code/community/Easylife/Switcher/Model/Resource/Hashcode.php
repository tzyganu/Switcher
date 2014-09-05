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
class Easylife_Switcher_Model_Resource_Hashcode extends Mage_Core_Model_Resource_Db_Abstract {
    /**
     * constructor
     */
    protected function _construct() {
        $this->_init('easylife_switcher/hashcode', 'entity_id');
    }

    /**
     * @param $values
     * @return int
     */
    public function insertValues($values) {
        return $this->_getWriteAdapter()->insertOnDuplicate($this->getMainTable(), $values);
    }

    /**
     * @param $values
     * @return int
     */
    public function deleteValues($values) {
        if (count($values)) {
            return $this->_getWriteAdapter()->delete($this->getMainTable(), array(' option_id IN (?)'=>implode(',', $values)));
        }
    }
}