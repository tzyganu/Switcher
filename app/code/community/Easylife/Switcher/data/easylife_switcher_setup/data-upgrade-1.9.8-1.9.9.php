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
 * move values from separate table to config
 */
/** @var Easylife_Switcher_Model_Resource_Setup $this */
$defaultScope = $this->getDefaultScope();
//Build scopes array
$scopes = array(
    $defaultScope
);
$pathsToCheck = $this->getPathsToCheck();
$defaultValues = $this->getDefaultValues();
/** @var Mage_Core_Model_Resource_Website_Collection $websiteCollection */
$websiteCollection = Mage::getModel('core/website')->getCollection()
    ->addFieldToFilter('website_id', array('neq' => 0));
foreach ($websiteCollection as $website) {
    /** @var Mage_Core_Model_Website $website */
    $scope = array(
        'scope_id' => $website->getId(),
        'scope' => 'websites'
    );
    foreach ($pathsToCheck as $path) {
        $websiteValue = $this->getConfigSettings($path, $scope);
        if (!is_null($websiteValue) && $websiteValue != $defaultValues[$path]) {
            $scopes[] = $scope;
            break;
        }
    }
}

/** @var Mage_Core_Model_Resource_Store_Collection $websiteCollection */
$storeCollection = Mage::getModel('core/store')->getCollection()
    ->addFieldToFilter('store_id', array('neq' => 0));
foreach ($storeCollection as $store) {
    /** @var Mage_Core_Model_Store $store */
    $scope = array(
        'scope_id' => $store->getId(),
        'scope' => 'stores'
    );
    foreach ($pathsToCheck as $path) {
        $storeValue = $this->getConfigSettings($path, $scope);
        if (!is_null($storeValue) && $storeValue != $defaultValues[$path]) {
            $scopes[] = $scope;
            break;
        }
    }
}

$usesColors = $this->getUsesColors();
$allAttributes = $this->getAllConfigurableAttributes();

foreach ($scopes as $scope) {
    /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $collection */
    $toTransform = $this->getToTransform($scope);

    $usesImages = $this->getUseOptionImages($scope);
    $useProductImages = $this->getUseProductImages($scope);

    /** @var Mage_Core_Helper_Data $coreHelper */
    $coreHelper = Mage::helper('core');
    //build the config value
    $newConfigArray = array();
    foreach ($toTransform as $id => $code) {
        //if existing images set to use images
        if (isset($usesImages[$id])) {
            $newConfigArray[$id] = 'custom_images';
            $newConfigArray['options'][$id]['image'] = $coreHelper->jsonEncode($usesImages[$id]);
        }
        if (isset($usesColors[$id])) {
            if (!isset($newConfigArray)) {
                $newConfigArray[$id] = 'colors';
            }
            //images will not fall back to colors anymore, but keep the set values
            $newConfigArray['options'][$id]['color'] = $coreHelper->jsonEncode($usesColors[$id]);
        } elseif (isset($useProductImages[$id])) {
            $newConfigArray[$id] = 'product_images';
            $newConfigArray['options'][$id]['product_image'] = $this->getConfigSettings(
                'easylife_switcher/settings/image_attribute',
                $scope,
                $this->getDefaultValue('easylife_switcher/settings/image_attribute')
            );
        } else {
            $newConfigArray[$id] = 'labels';
        }
    }
    $configPath = Easylife_Switcher_Block_Catalog_Product_View_Type_Configurable_Config::XML_TRANSFORM_PATH;

    /** @var Mage_Core_Model_Config_Data $config */
    $configCollection = Mage::getModel('core/config_data')->getCollection();
    $configCollection->addFieldToFilter('path', $configPath);
    $configCollection->addFieldToFilter('scope', $scope['scope']);
    $configCollection->addFieldToFilter('scope_id', $scope['scope_id']);

    $config = $configCollection->getFirstItem();
    $config->setPath($configPath);
    $config->setValue($coreHelper->jsonEncode($newConfigArray));
    $config->setScopeId($scope['scope_id']);
    $config->setScope($scope['scope']);
    $config->save();
}
//cleanup
$this->cleanupConfigTable();

$table = $this->getTable('easylife_switcher/hashcode');
//drop the hashcode table
$this->run("DROP TABLE IF EXISTS `{$table}`");
