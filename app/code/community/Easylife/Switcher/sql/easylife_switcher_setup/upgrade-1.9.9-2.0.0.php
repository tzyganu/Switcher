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
/** @var Easylife_Switcher_Model_Resource_Setup $this */

//set the default configuration attribute for store view scope
$this->updateAttribute(
    'catalog_product',
    Easylife_Switcher_Helper_Data::DEFAULT_CONFIGURATION_ATTRIBUTE_CODE,
    'is_global',
    Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
);
//fix typos
$paths = array(
    'easylife_switcher/settings/change_image_attribtues' => 'easylife_switcher/settings/change_image_attributes',
    'easylife_switcher/settings/change_media_attribtues' => 'easylife_switcher/settings/change_media_attributes'
);
$configTable = $this->getTable('core/config_data');
foreach ($paths as $oldPath => $newPath) {
    $q = "UPDATE `{$configTable}` SET `path` = '".$newPath."' WHERE `path` = '".$oldPath."'";
    $this->run($q);
}
