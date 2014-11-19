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
 * change callbacks for 1.9 version
 */
if (is_callable(array('Mage', 'getEdition'))) {
    $isEE = Mage::getEdition() == Mage::EDITION_ENTERPRISE;
} else {
    $isEE = Mage::helper('core')->isModuleEnabled('Enterprise_Enterprise');
}
$checkVersion = ($isEE)? '1.14': '1.9';

if (version_compare(Mage::getVersion(), $checkVersion, '>=')) {
    $configSettings = array(
        'easylife_switcher/settings/image_change_callback' => 'ProductMediaManager.destroyZoom();
ProductMediaManager.createZoom(jQuery(\'#image-main\'));',
        'easylife_switcher/settings/image_selector' => '$(\'image-main\')',
        'easylife_switcher/settings/media_change_callback' => 'ProductMediaManager.destroyZoom();
ProductMediaManager.createZoom(jQuery(\'#image-main\'));ProductMediaManager.init();',
        'easylife_switcher/settings/media_selector' => '$$(\'.product-view .product-img-box\')[0]',
    );
    foreach ($configSettings as $path=>$value) {
        /** @var Mage_Core_Model_Config_Data $config */
        $config = Mage::getModel('core/config_data')->load($path, 'path');
        $config->setValue($value)->setPath($path);
        $config->save();
    }
}
