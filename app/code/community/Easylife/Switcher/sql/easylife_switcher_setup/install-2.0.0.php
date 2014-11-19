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
 * Install script - add a new attribute for configurable products
 *
 * @category    Easylife
 * @package	    Easylife_Switcher
 */
$this->startSetup();
$this->addAttribute(
    'catalog_product',
    Easylife_Switcher_Helper_Data::DEFAULT_CONFIGURATION_ATTRIBUTE_CODE,
    array(
        'group'             => 'General',
        'type'              => 'int',
        'input'             => 'hidden',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Default configuration id',
        'class'             => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => true,
        'default'           => '',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'   => false,
        'visible_in_advanced_search'   => false,
        'unique'            => false,
        'apply_to'          => Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
        'is_configurable'   => false,
    )
);
$this->endSetup();
