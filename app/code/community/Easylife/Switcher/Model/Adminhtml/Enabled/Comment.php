<?php
class Easylife_Switcher_Model_Adminhtml_Enabled_Comment
{
    public function getCommentText()
    {
        /** @var Mage_Core_Helper_Data $helper */
        $helper = Mage::helper('core');
        $hasCoreSwatches = $helper->isModuleEnabled('Mage_ConfigurableSwatches');
        if ($hasCoreSwatches) {
            return Mage::helper('easylife_switcher')->__('Your magento instance contains the extension "Mage_ConfigurableSwatches". Enabling this might conflict in special cases with that core extension. Test before going live.');
        }
        return '';
    }
}