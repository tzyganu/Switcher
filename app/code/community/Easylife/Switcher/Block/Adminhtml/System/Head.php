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
class Easylife_Switcher_Block_Adminhtml_System_Head extends Mage_Adminhtml_Block_Template
{
    /**
     * @return $this
     */
    public function addResourcesToParent()
    {
        $section = Mage::app()->getRequest()->getParam('section');
        if ($section == 'easylife_switcher') {
            /** @var Mage_Adminhtml_Block_Page_Head $head */
            $head = $this->getParentBlock();
            if ($head && $head instanceof Mage_Adminhtml_Block_Page_Head) {
                $head->addJs('easylife_switcher/adminhtml/system.js');
            }
        }
        return $this;
    }
}
