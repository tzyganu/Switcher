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
 * Configurable product additional config
 *
 * @category    Easylife
 * @package	    Easylife_Switcher
 */
class Easylife_Switcher_Block_Catalog_Product_View_Type_Configurable_Config extends Mage_Catalog_Block_Product_View_Type_Configurable
{
    /**
     * config path to transform settings
     */
    const XML_TRANSFORM_PATH        = 'easylife_switcher/settings/transform';
    /**
     * config path to autoselect first option if none specified
     */
    const XML_AUTOSELECT_FIRST_PATH = 'easylife_switcher/settings/autoselect_first';
    /**
     * config path to transform dropdowns
    */
    const XML_ADDED_PRICES_PATH     = 'easylife_switcher/settings/show_added_prices';
    /**
     * config path to change images
     */
    const XML_CHANGE_IMAGES_PATH    = 'easylife_switcher/settings/change_images';
    /**
     * config path to change image attributes
     */
    const XML_CHANGE_IMAGE_PATH     = 'easylife_switcher/settings/change_image_attributes';
    /**
     * config path to js image selector
     */
    const XML_MAIN_IMAGE_SELECTOR   = 'easylife_switcher/settings/image_selector';
    /**
     * config path to image resize
     */
    const XML_IMAGE_RESIZE          = 'easylife_switcher/settings/image_resize';
    /**
     * config path to labels / options image resize
     */
    const XML_OPTIONS_IMAGE_RESIZE  = 'easylife_switcher/settings/options_image_resize';
    /**
     * config pat to change image callback
     */
    const XML_IMAGE_CALLBACK_PATH   = 'easylife_switcher/settings/image_change_callback';
    /**
     * config path to change media attributes
     */
    const XML_CHANGE_MEDIA_PATH     = 'easylife_switcher/settings/change_media_attributes';
    /**
     * config path to media selector
     */
    const XML_MEDIA_SELECTOR        = 'easylife_switcher/settings/media_selector';
    /**
     * config path to media change callback
     */
    const XML_MEDIA_CALLBACK_PATH   = 'easylife_switcher/settings/media_change_callback';
    /**
     * config path to media block alias
     */
    const XML_MEDIA_BLOCK_TYPE_PATH = 'easylife_switcher/settings/media_block';
    /**
     * config path to media template name
     */
    const XML_MEDIA_TEMPLATE_PATH   = 'easylife_switcher/settings/media_template';
    /**
     * config path to allow out of stock products to be selected
     */
    const XML_NO_STOCK_SELECT_PATH  = 'easylife_switcher/settings/allow_no_stock_select';
    /**
     * default media block type
     */
    const DEFAULT_MEDIA_BLOCK_TYPE  = 'catalog/product_view_media';
    /**
     * default media template
     */
    const DEFAULT_MEDIA_TEMPLATE    = 'catalog/product/view/media.phtml';
    /**
     * show out of stock combinations
     */
    const XML_SHOW_OUT_OF_STOCK_PATH = 'easylife_switcher/settings/out_of_stock';
    /**
     * keep previously selected values
     */
    const XML_KEEP_SELECTED_VALUES  = 'easylife_switcher/settings/keep_values';
    /**
     * use configurable product image if the simple product does not have one.
     */
    const XML_USE_CONF_IMAGE        = 'easylife_switcher/settings/use_conf_image';
    /**
     * cache for switch attributes
     * @var array
     */
    protected $_switchAttributes    = array();
    /**
     * @var string
     */
    protected $_confProductImage;
    /**
     * @var array
     */
    protected $_switcherConfig;

    /**
     * @return array
     */
    public function getSwitcherConfig()
    {
        if (is_null($this->_switcherConfig)) {
            $this->_switcherConfig = array();
            if ($this->getSwitcherHelper()->isEnabled()) {
                $transform = Mage::getStoreConfig(self::XML_TRANSFORM_PATH);
                $this->_switcherConfig = $this->getCoreHelper()->jsonDecode($transform);
            }
        }
        return $this->_switcherConfig;
    }

    /**
     * @return Easylife_Switcher_Helper_Data
     */
    public function getSwitcherHelper()
    {
        return Mage::helper('easylife_switcher');
    }

    /**
     * @return Mage_Core_Helper_Data
     */
    public function getCoreHelper()
    {
        return Mage::helper('core');
    }

    /**
     * @return Mage_Catalog_Helper_Product
     */
    public function getCatalogHelper()
    {
        return Mage::helper('catalog/product');
    }

    /**
     * @return Easylife_Switcher_Helper_Optimage
     */
    public function getImageHelper()
    {
        return Mage::helper('easylife_switcher/optimage');
    }

    /**
     * @return Mage_Catalog_Helper_Image
     */
    public function getCatalogImageHelper()
    {
        return Mage::helper('catalog/image');
    }

    /**
     * get additional config for configurable products
     * @access public
     * @return string
     */
    public function getJsonAdditionalConfig()
    {
        $config = array();
        $config['switch'] = array();
        $switcherConfig = $this->getSwitcherConfig();
        $attributes = $this->getAllowAttributes();
        $collectImages = array();
        foreach ($attributes as $attribute) {
            /** @var Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute */
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $productAttribute */
            $productAttribute = $attribute->getProductAttribute();
            if (isset($switcherConfig[$productAttribute->getId()])) {
                $switchType = $switcherConfig[$productAttribute->getId()];
                $attributeSwitchConfig = array();
                switch ($switchType) {
                    case 'none' :
                        break;
                    case 'labels':
                        $attributeSwitchConfig['type'] = 'labels';
                        break;
                    case 'product_images':
                        $attributeSwitchConfig['type'] = 'product_images';
                        $imageAttribute = isset($switcherConfig['options'][$productAttribute->getId()]['product_image'])
                            ? $switcherConfig['options'][$productAttribute->getId()]['product_image']
                            : false;
                        if ($imageAttribute) {
                            $collectImages[$imageAttribute] = 1;
                            $attributeSwitchConfig['product_images'] = $imageAttribute;
                        }
                        break;
                    case 'colors':
                        $attributeSwitchConfig['type'] = 'colors';
                        //get the used colors
                        $colors = isset($switcherConfig['options'][$productAttribute->getId()]['color'])
                            ? $this->getCoreHelper()->jsonDecode(
                                $switcherConfig['options'][$productAttribute->getId()]['color']
                            )
                            : array();
                        $attributeSwitchConfig['color'] = $colors;
                        break;
                    case 'custom_images':
                        $attributeSwitchConfig['type'] = 'custom_images';
                        $images = isset($switcherConfig['options'][$productAttribute->getId()]['image'])
                            ? $this->getCoreHelper()->jsonDecode(
                                $switcherConfig['options'][$productAttribute->getId()]['image']
                            )
                            : array();
                        $validImages = array();
                        $dimensions = $this->_getImageDimensions(self::XML_OPTIONS_IMAGE_RESIZE);
                        foreach ($images as $optionId => $image) {
                            if ($image && file_exists($this->getImageHelper()->getImageBaseDir().$image)) {
                                $realImage = $this->getImageHelper()->init($image);
                                if (!empty($dimensions)) {
                                    $realImage->resize($dimensions[0], $dimensions[1]);
                                }
                                $validImages[$optionId] = (string)$realImage;
                            }
                        }
                        $attributeSwitchConfig['images'] = $validImages;
                        break;
                    default:
                        $params = new Varien_Object(
                            array(
                                'attribute' => $productAttribute,
                                'switch_type' => $switchType,
                                'result' => array()
                            )
                        );
                        Mage::dispatchEvent('easylife_switcher_config', array('params' => $params));
                        $attributeSwitchConfig = $params->getData('result');
                        break;
                }
                if ($switchType != 'none' && $attributeSwitchConfig) {
                    $config['switch'][$productAttribute->getId()] = $attributeSwitchConfig;
                }
            }
        }

        $config['show_added_prices']    = Mage::getStoreConfigFlag(self::XML_ADDED_PRICES_PATH);
        $config['stock']                = $this->getStockOptions();
        $config['images']               = $this->getImages(array_keys($collectImages));

        $config['main_image_selector']      = Mage::getStoreConfig(self::XML_MAIN_IMAGE_SELECTOR);
        $config['switch_image_callback']    = Mage::getStoreConfig(self::XML_IMAGE_CALLBACK_PATH);

        $config['switch_image_type']        = $this->getSwitchImageType();

        $config['switch_images']            = $this->getSwitchImages();
        $config['switch_media']             = $this->getSwitchMedia();
        $config['switch_media_selector']    = Mage::getStoreConfig(self::XML_MEDIA_SELECTOR);
        $config['switch_media_callback']    = Mage::getStoreConfig(self::XML_MEDIA_CALLBACK_PATH);
        $config['allow_no_stock_select']    = $this->getAllowNoStockSelect();
        $config['keep_values']              = Mage::getStoreConfigFlag(self::XML_KEEP_SELECTED_VALUES);
        $config['image_size']               = $this->_getImageDimensions(self::XML_OPTIONS_IMAGE_RESIZE);

        if (!$this->getProduct()->hasPreconfiguredValues()) {
            if ($this->getDefaultValues()) {
                $config['defaultValues']    = $this->getDefaultValues();
            }
        }

        $oldCheck = Mage::registry('old_skip_aleable_check');
        if (!is_null($oldCheck)) {
            $this->getCatalogHelper()->setSkipSaleableCheck($oldCheck);
        }
        return $this->getCoreHelper()->jsonEncode($config);
    }

    /**
     * @return bool
     */
    public function getAllowNoStockSelect()
    {
        return Mage::getStoreConfigFlag(self::XML_SHOW_OUT_OF_STOCK_PATH) &&
            Mage::getStoreConfigFlag(self::XML_NO_STOCK_SELECT_PATH);
    }
    /**
     * @return array
     */
    public function getCachedAllowedProducts()
    {
        /** @var Mage_Catalog_Block_Product_View_Type_Configurable $block */
        $block = $this->getConfigurableBlock();
        if ($block) {
            $simpleProducts = $block->getAllowProducts();
        } else {
            $simpleProducts = $this->getAllowProducts();
        }
        return $simpleProducts;
    }
    /**
     * get stock options
     * @access public
     * @return array
     */
    public function getStockOptions()
    {
        $simpleProducts = $this->getCachedAllowedProducts();
        $stock = array();
        foreach ($simpleProducts as $product) {
            /** @var Mage_Catalog_Model_Product $product */
            $productId  = $product->getId();
            $stock[$productId] = $product->getIsSalable();
        }
        return $stock;
    }

    /**
     * get current product
     * @access public
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * get block used for configurable products in layout
     * @access public
     * @return bool|Mage_Core_Block_Abstract
     */
    public function getConfigurableBlock()
    {
        $block = Mage::app()->getLayout()->getBlock('product.info.configurable');
        if ($block) {
            return $block;
        }
        return false;
    }

    /**
     * get attributes for switching images
     * @access public
     * @param string $path
     * @return mixed
     */
    public function getSwitchAttributes($path)
    {
        if (!isset($this->_switchAttributes[$path])) {
            $allowedString = trim(Mage::getStoreConfig($path), ' ,');
            if (!$allowedString) {
                $this->_switchAttributes[$path] = array();
            } else {
                $allowed = explode(',', $allowedString);
                $allowedAttributeIds = array();
                $allowedAttributes = $this->getAllowAttributes();
                foreach ($allowedAttributes as $attribute) {
                    /** @var Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute */
                    /** @var Mage_Catalog_Model_Resource_Eav_Attribute $productAttribute */
                    $productAttribute = $attribute->getProductAttribute();
                    if (in_array($productAttribute->getAttributeCode(), $allowed)) {
                        $allowedAttributeIds[(int)$productAttribute->getId()] = $productAttribute->getAttributeCode();
                    }
                }
                $this->_switchAttributes[$path] = $allowedAttributeIds;
            }
        }
        return $this->_switchAttributes[$path];
    }

    /**
     * @param array $imageAttributes
     * @return array
     */
    public function getImages(array $imageAttributes)
    {
        $simpleProducts = $this->getCachedAllowedProducts();
        $images = array();
        foreach ($imageAttributes as $imageAttribute) {
            foreach ($simpleProducts as $product) {
                /** @var Mage_Catalog_Model_Product $product */
                if ($product->getData($imageAttribute) != '' && $product->getData($imageAttribute) != 'no_selection') {
                    $image = $this->getCatalogImageHelper()->init($product, $imageAttribute);
                    $dimensions = $this->_getImageDimensions(self::XML_OPTIONS_IMAGE_RESIZE);
                    if (!empty($dimensions)) {
                        $image->resize($dimensions[0], $dimensions[1]);
                    }
                    $images[$imageAttribute][$product->getId()] = (string)$image;
                }
            }
        }
        return $images;
    }

    /**
     * get the default configuration
     * @access public
     * @return array|null
     */
    public function getDefaultValues()
    {
        $defaultId = $this->getProduct()->getData(Easylife_Switcher_Helper_Data::DEFAULT_CONFIGURATION_ATTRIBUTE_CODE);
        if (!$defaultId) {
            //set first as default
            if (Mage::getStoreConfigFlag(self::XML_AUTOSELECT_FIRST_PATH)) {
                $simpleProducts = $this->getCachedAllowedProducts();
                $default = false;
                foreach ($simpleProducts as $product) {
                    /** @var Mage_Catalog_Model_Product $product */
                    if ($product->getIsSalable() || $this->getAllowNoStockSelect()) {
                        $defaultId = $product->getId();
                        break;
                    }
                }
            }
        }
        if (!$defaultId) {
            return null;
        }
        $defaultValues = array();
        foreach ($this->getAllowAttributes() as $attribute) {
            /** @var Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute */
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $productAttribute */
            $productAttribute = $attribute->getProductAttribute();
            $defaultValues[$productAttribute->getId()] = Mage::getResourceModel('catalog/product')
                ->getAttributeRawValue(
                    $defaultId,
                    $productAttribute->getAttributeCode(),
                    Mage::app()->getStore()->getId()
                );
        }
        return $defaultValues;
    }

    /**
     * get switch type
     * @access public
     * @return mixed
     */
    public function getSwitchImageType()
    {
        return Mage::getStoreConfig(self::XML_CHANGE_IMAGES_PATH);
    }

    /**
     * @access public
     * @return mixed
     */
    public function getConfProductImage()
    {
        if (is_null($this->_confProductImage)) {
            $this->_confProductImage = $this->getCatalogImageHelper()->init($this->getProduct(), 'image');
            $dimensions = $this->_getImageDimensions();
            if (!empty($dimensions)) {
                $this->_confProductImage->resize($dimensions[0], $dimensions[1]);
            }
        }
        return $this->_confProductImage;
    }
    /**
     * get main images for simple products
     * @access public
     * @return array
     */
    public function getSwitchImages()
    {
        if ($this->getSwitchImageType() != Easylife_Switcher_Model_Adminhtml_System_Config_Source_Switch::SWITCH_MAIN) {
            return array();
        }
        $changeAttributes = $this->getSwitchAttributes(self::XML_CHANGE_IMAGE_PATH);
        $simpleProducts = $this->getCachedAllowedProducts();
        $images = array();
        foreach ($simpleProducts as $product) {
            if ($product->getData('image') != '' && $product->getData('image') != 'no_selection') {
                $image = Mage::helper('catalog/image')->init($product, 'image');
                $dimensions = $this->_getImageDimensions();
                if (!empty($dimensions)) {
                    $image->resize($dimensions[0], $dimensions[1]);
                }
                $images[$product->getId()]['src'] = (string)$image;
                $label = $product->getData('image_label');
                if (empty($label)) {
                    $label = $product->getName();
                }
                $images[$product->getId()]['alt'] = $this->escapeHtml($label);
            } elseif (Mage::getStoreConfigFlag(self::XML_USE_CONF_IMAGE)) {
                $images[$product->getId()] ['src']= (string)$this->getConfProductImage();
                $label = $this->getProduct()->getData('image_label');
                if (empty($label)) {
                    $label = $this->getProduct()->getName();
                }
                $images[$product->getId()]['alt'] = $this->escapeHtml($label);
            }
        }
        return $images;
    }
    /**
     * get media images for changing full media
     * @access public
     * @return array
     */
    public function getSwitchMedia()
    {
        if ($this->getSwitchImageType() !=
            Easylife_Switcher_Model_Adminhtml_System_Config_Source_Switch::SWITCH_MEDIA) {
            return array();
        }
        $simpleProducts = $this->getCachedAllowedProducts();
        $images = array();
        $configurableMedia = null;
        $block = Mage::app()->getLayout()
            ->createBlock($this->_getMediaBlockType())
            ->setTemplate($this->_getMediaBlockTemplate());
        foreach ($simpleProducts as $product) {
            /** @var Mage_Catalog_Model_Product $product */
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($product->getId());
            if ($product->getData('image') != '' && $product->getData('image') != 'no_selection') {
                $block->setProduct($product);
                $images[$product->getId()] = $block->toHtml();
            } else {
                if (is_null($configurableMedia)) {
                    $block->setProduct($this->getProduct());
                    $configurableMedia = $block->toHtml();
                }
                $images[$product->getId()] = $configurableMedia;
            }

        }
        return $images;
    }

    /**
     * @param string $path
     * @return array|bool
     */
    protected function _getImageDimensions($path = self::XML_IMAGE_RESIZE)
    {
        $value = Mage::getStoreConfig($path);
        if (!$value) {
            return false;
        }
        $dimensions = explode('x', $value, 2);
        if (!isset($dimensions[1])) {
            $dimensions[1] = $dimensions[0];
        }
        $dimensions[0] = (int)$dimensions[0];
        $dimensions[1] = (int)$dimensions[1];
        if ($dimensions[0]<=0 || $dimensions[1]<=0) {
            return false;
        }
        return $dimensions;
    }

    /**
     * get the media block alias
     * @access protected
     * @return string
     */
    protected function _getMediaBlockType()
    {
        $block = Mage::getStoreConfig(self::XML_MEDIA_BLOCK_TYPE_PATH);
        if (!$block) {
            $block = self::DEFAULT_MEDIA_BLOCK_TYPE;
        }
        return $block;
    }

    /**
     * get the media block template
     * @access protected
     * @return string
     */
    protected function _getMediaBlockTemplate()
    {
        $template = Mage::getStoreConfig(self::XML_MEDIA_TEMPLATE_PATH);
        if (!$template) {
            $template = self::DEFAULT_MEDIA_TEMPLATE;
        }
        return $template;
    }
}
