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
class Easylife_Switcher_Model_Resource_Setup extends Mage_Catalog_Model_Resource_Setup
{
    /**
     * @var array
     */
    protected $_allConfigurableAttributes;
    /**
     * @var array
     */
    protected $_defaultValues;
    /**
     * @var array
     */
    protected $_usesColors;
    /**
     * @var array
     */
    protected $_pathsToCheck = array(
        'easylife_switcher/settings/transform_dropdowns',
        'easylife_switcher/settings/transform_specific',
        'easylife_switcher/settings/use_option_images',
        'easylife_switcher/settings/use_images',
        'easylife_switcher/settings/image_attribute'
    );

    /**
     * get all configurable attributes
     *
     * @return array
     */
    public function getAllConfigurableAttributes()
    {
        if (is_null($this->_allConfigurableAttributes)) {
            $collection = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addVisibleFilter()
                ->addFieldToFilter('is_configurable', 1)
                ->addFieldToFilter('frontend_input', 'select')
                ->addFieldToFilter('is_global', 1)
                ->addFieldToFilter('is_user_defined', 1);
            $allAttributes = array();
            foreach ($collection as $item) {
                /** @var Mage_Catalog_Model_Resource_Eav_Attribute $item */
                $allAttributes[$item->getId()] = $item->getAttributeCode();
            }
            $this->_allConfigurableAttributes = $allAttributes;
        }
        return $this->_allConfigurableAttributes;
    }

    /**
     * get default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        if (is_null($this->_defaultValues)) {
            $this->_defaultValues = array();
            foreach ($this->getPathsToCheck() as $path) {
                $this->_defaultValues[$path] = $this->getConfigSettings($path, $this->getDefaultScope());
            }
        }
        return $this->_defaultValues;
    }

    /**
     * get default scope settings
     * @return array
     */
    public function getDefaultScope()
    {
        return array(
            'scope_id' => 0,
            'scope' => 'default'
        );
    }

    /**
     * get paths to check
     * @return array
     */
    public function getPathsToCheck()
    {
        return $this->_pathsToCheck;
    }

    /**
     * get config settings
     * @param $path
     * @param $scope
     * @param null $default
     * @return mixed|null
     */
    public function getConfigSettings($path, $scope, $default = null)
    {
        if ($scope['scope_id'] == 0) {
            return Mage::getStoreConfig($path, 0);
        }
        /** @var Mage_Core_Model_Config_Data $config */
        $config = Mage::getModel('core/config_data');

        $collection = $config->getCollection();
        $collection->addFieldToFilter('scope', $scope['scope']);
        $collection->addFieldToFilter('scope_id', $scope['scope_id']);
        $collection->addFieldToFilter('path', $path);
        /** @var Mage_Core_Model_Config_Data $item */
        $item = $collection->getFirstItem();
        if ($item->getId()) {
            return $collection->getFirstItem()->getValue();
        }
        return $default;
    }

    /**
     * get value for the default scope
     * @param $path
     * @return null
     */
    public function getDefaultValue($path)
    {
        $defaultValues = $this->getDefaultValues();
        return (isset($defaultValues[$path]) ? $defaultValues[$path] : null);
    }

    /**
     * get attributes to transform
     * @param $scope
     * @return array
     */
    public function getToTransform($scope)
    {
        $path = 'easylife_switcher/settings/transform_dropdowns';
        $howToTransform = $this->getConfigSettings($path, $scope, $this->getDefaultValue($path));
        $allAttributes = $this->getAllConfigurableAttributes();
        $toTransform = array();
        if ($howToTransform == 2) {
            $specificPath = 'easylife_switcher/settings/transform_specific';
            $toTransformConfig = explode(
                ',',
                $this->getConfigSettings(
                    $specificPath,
                    $scope,
                    $this->getDefaultValue($specificPath)
                )
            );
            foreach ($toTransformConfig as $value) {
                if (isset($allAttributes[$value])) {
                    $toTransform[$value] = $allAttributes[$value];
                }
            }
        } elseif ($howToTransform == 1) {
            $toTransform = $allAttributes;
        }
        return $toTransform;
    }

    /**
     * get elements that use images
     * @param $scope
     * @return array
     */
    public function getUseOptionImages($scope)
    {
        //get use options images
        $path = 'easylife_switcher/settings/use_option_images';
        $useOptionImages = explode(',', $this->getConfigSettings($path, $scope, $this->getDefaultValue($path)));
        $useOptionImagesIds = array();
        $allAttributes = $this->getAllConfigurableAttributes();
        foreach ($useOptionImages as $attrCode) {
            foreach ($allAttributes as $attrId => $allAttrCode) {
                if ($attrCode == $allAttrCode) {
                    $useOptionImagesIds[$attrId] = $attrCode;
                    break;
                }
            }
        }
        //read the media folder for attribute images
        $usedImages = array();
        /** @var Easylife_Switcher_Helper_Optimage $optImageHelper */
        $optImageHelper = Mage::helper('easylife_switcher/optimage');
        $dir = $optImageHelper->getImageBaseDir();
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if (is_file($dir.DS.$entry)) {
                    $parts = explode('.', $entry);
                    $usedImages[$parts[0]] = $entry;
                }
            }
        }
        $usesImages = array();
        if (count($usedImages)) {
            //get all options that use images
            /** @var Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection $optionCollection */
            $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->addFieldToFilter('option_id', array('in' => array_keys($usedImages)));
            foreach ($optionCollection as $option) {
                /** @var Mage_Eav_Model_Entity_Attribute_Option $option */
                $usesImages[$option->getAttributeId()][$option->getId()] = '/'.$usedImages[$option->getId()];
            }
        }
        return $usesImages;
    }

    /**
     * get attributes that use product images
     * @param $scope
     * @return array
     */
    public function getUseProductImages($scope)
    {
        $path = 'easylife_switcher/settings/use_images';
        $useProductImagesCodes = explode(',', $this->getConfigSettings($path, $scope, $this->getDefaultValue(($path))));
        $allAttributes = $this->getAllConfigurableAttributes();
        $useProductImages = array();
        foreach ($useProductImagesCodes as $code) {
            foreach ($allAttributes as $attrId => $allAttrCode) {
                if ($code == $allAttrCode) {
                    $useProductImages[$attrId] = $code;
                    break;
                }
            }
        }
        return $useProductImages;
    }

    /**
     * get attributes that use colors
     * @return array
     */
    public function getUsesColors()
    {
        if (is_null($this->_usesColors)) {
            //get the used colors - use simple select because model will be removed.
            $table = $this->getTable('easylife_switcher/hashcode');
            $q = "SELECT * FROM {$table}";
            $result = $this->getConnection()->fetchAll($q);
            $usedColors = array();
            foreach ($result as $item) {
                $usedColors[$item['option_id']] = $item['hashcode'];
            }
            $usesColors = array();
            if (count($usedColors)) {
                //get all options that use images
                $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                    ->addFieldToFilter('option_id', array('in' => array_keys($usedColors)));
                foreach ($optionCollection as $option) {
                    /** @var Mage_Eav_Model_Entity_Attribute_Option $option */
                    $usesColors[$option->getAttributeId()][$option->getId()] = $usedColors[$option->getId()];
                }
            }
            $this->_usesColors = $usesColors;
        }
        return $this->_usesColors;
    }

    public function cleanupConfigTable()
    {
        $pathsToDelete = array(
            '\'easylife_switcher/settings/transform_dropdowns\'',
            '\'easylife_switcher/settings/transform_specific\'',
            '\'easylife_switcher/settings/use_images\'',
            '\'easylife_switcher/settings/image_attribute\'',
            '\'easylife_switcher/settings/use_option_images\''
        );
        $pathsToDeleteString = implode(',', $pathsToDelete);
        $q = "DELETE FROM {$this->getTable('core/config_data')} WHERE path IN ($pathsToDeleteString)";
        $this->getConnection()->query($q);
    }
}
