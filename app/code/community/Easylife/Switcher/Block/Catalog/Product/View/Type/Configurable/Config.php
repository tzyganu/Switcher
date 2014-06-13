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
/**
 * Configurable product additional config
 *
 * @category    Easylife
 * @package	    Easylife_Switcher
 * @author 	    Marius Strajeru <marius.strajeru@gmail.com>
 */
class Easylife_Switcher_Block_Catalog_Product_View_Type_Configurable_Config
    extends Mage_Catalog_Block_Product_View_Type_Configurable{
    /**
     * config path to transform dropdowns
     */
    const XML_TRANSFORM_PATH        = 'easylife_switcher/settings/transform_dropdowns';
    /**
     * config path to transform dropdowns
     */
    const XML_ADDED_PRICES_PATH        = 'easylife_switcher/settings/show_added_prices';
    /**
     * config path to use images
     */
    const XML_USE_IMAGES_PATH       = 'easylife_switcher/settings/use_images';
    /**
     * config path to image attributes
     */
    const XML_IMAGE_ATTRIBUTE_PATH  = 'easylife_switcher/settings/image_attribute';
    /**
     * config path to change images
     */
    const XML_CHANGE_IMAGES_PATH    = 'easylife_switcher/settings/change_images';
    /**
     * config path to change image attributes
     */
    const XML_CHANGE_IMAGE_PATH     = 'easylife_switcher/settings/change_image_attribtues';
    /**
     * config path to js image selector
     */
    const XML_MAIN_IMAGE_SELECTOR   = 'easylife_switcher/settings/image_selector';
    /**
     * config path to image resize
     */
    const XML_IMAGE_RESIZE          = 'easylife_switcher/settings/image_resize';
    /**
     * config pat to change image callback
     */
    const XML_IMAGE_CALLBACK_PATH   = 'easylife_switcher/settings/image_change_callback';
    /**
     * config path to change media attributes
     */
    const XML_CHANGE_MEDIA_PATH     = 'easylife_switcher/settings/change_media_attribtues';
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
     * cache for switch attributes
     * @var array
     */
    protected $_switchAttribtues    = array();

    /**
     * get additional config for configurable products
     * @access public
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getJsonAdditionalConfig(){
        $config = array();
        if (Mage::helper('easylife_switcher')->isEnabled()){
            $config['transform_dropdowns']  = Mage::getStoreConfigFlag(self::XML_TRANSFORM_PATH);
        }
		$config['show_added_prices']        = Mage::getStoreConfigFlag(self::XML_ADDED_PRICES_PATH);
        $config['stock']                    = $this->getStockOptions();
        $config['switch_attributes']        = $this->getSwitchAttributes();
        $config['images']                   = $this->getImages();

        if (!$this->getProduct()->hasPreconfiguredValues()){
            if ($this->getDefaultValues()){
                $config['defaultValues']    = $this->getDefaultValues();
            }
        }
        $config['switch_image_type']        = $this->getSwitchImageType();
        $config['switch_images']            = $this->getSwitchImages();
        $config['main_image_selector']      = Mage::getStoreConfig(self::XML_MAIN_IMAGE_SELECTOR);
        $config['switch_image_callback']    = Mage::getStoreConfig(self::XML_IMAGE_CALLBACK_PATH);
        $config['switch_media']             = $this->getSwitchMedia();
        $config['switch_media_selector']    = Mage::getStoreConfig(self::XML_MEDIA_SELECTOR);
        $config['switch_media_callback']    = Mage::getStoreConfig(self::XML_MEDIA_CALLBACK_PATH);
        $config['allow_no_stock_select']    = Mage::getStoreConfigFlag(self::XML_NO_STOCK_SELECT_PATH);
        $config['currency_symbol']          = Mage::app()->getLocale()->currency( $currency_code )->getSymbol();
        return Mage::helper('core')->jsonEncode($config);
    }

    /**
     * get stock options
     * @access public
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getStockOptions(){
        $simpleProducts = $this->getSimpleProducts();
        $currentProduct = $this->getProduct();
        $stock = array();
        foreach ($simpleProducts as $product) {
            $productId  = $product->getId();
            $stock[$productId] = $product->getIsSalable();
        }
        return $stock;
    }

    /**
     * get current product
     * @access public
     * @return Mage_Catalog_Model_Product|mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getProduct(){
        return Mage::registry('current_product');
    }

    /**
     * get block used for configurable products in layout
     * @access public
     * @return bool|Mage_Core_Block_Abstract
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getConfigurableBlock(){
        $block = Mage::app()->getLayout()->getBlock('product.info.configurable');
        if ($block){
            return $block;
        }
        return false;
    }

    /**
     * get attributes for switchin images
     * @access public
     * @param string $path
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getSwitchAttributes($path = self::XML_USE_IMAGES_PATH){
        if (!isset($this->_switchAttribtues[$path])){
            $allowedString = Mage::getStoreConfig($path);
            if (!$allowedString){
                $this->_switchAttribtues[$path] = array();
            }
            else{
                $allowed = explode(',', $allowedString);
                $allowedAttributeIds = array();
                $allowedAttributes = $this->getAllowAttributes();
                foreach ($allowedAttributes as $attribute){
                    $productAttribute = $attribute->getProductAttribute();
                    if (in_array($productAttribute->getAttributeCode(), $allowed)){
                        $allowedAttributeIds[(int)$productAttribute->getId()] = $productAttribute->getAttributeCode();
                    }
                }
                $this->_switchAttribtues[$path] = $allowedAttributeIds;
            }
        }
        return $this->_switchAttribtues[$path];
    }

    /**
     * get images to use for labels
     * @access public
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getImages(){
        if (count($this->getSwitchAttributes(self::XML_USE_IMAGES_PATH)) == 0){
            return array();
        }
        $block = $this->getConfigurableBlock();
        if ($block){
            $simpleProducts = $block->getAllowProducts();
        }
        else{
            $simpleProducts = $this->getAllowProducts();
        }
        $images = array();
        $imageAttribute = $this->getImageAttribute();
        foreach ($this->getSwitchAttributes() as $id=>$code){
            foreach ($simpleProducts as $product){
                if ($product->getData($imageAttribute) != '' && $product->getData($imageAttribute) != 'no_selection'){
                    $images[$id][$product->getId()] = (string)Mage::helper('catalog/image')->init($product, $this->getImageAttribute())->resize(40);
                }
            }
        }
        return $images;
    }

    /**
     * get the image type attribute to be used for labels
     * @access public
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getImageAttribute(){
        if (!$this->hasData('image_attribute')){
            $this->setData('image_attribute', Mage::getStoreConfig(self::XML_IMAGE_ATTRIBUTE_PATH));
        }
        return $this->getData('image_attribute');
    }

    /**
     * get the default configuration
     * @access public
     * @return array|null
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getDefaultValues(){
        $defaultId = $this->getProduct()->getData(Easylife_Switcher_Helper_Data::DEFAULT_CONFIGURATION_ATTRIBUTE_CODE);
        if (!$defaultId){
            return null;
        }
        $default = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($defaultId);
        if (!$default->getId()){
            return null;
        }
        $defaultValues = array();
        foreach ($this->getAllowAttributes() as $attribute){
            $productAttribute = $attribute->getProductAttribute();
            $defaultValues[$productAttribute->getId()] = $default->getData($productAttribute->getAttributeCode());
        }
        return $defaultValues;
    }

    /**
     * get the simple products
     * @access public
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getSimpleProducts(){
        $block = $this->getConfigurableBlock();
        if ($block){
            $simpleProducts = $block->getAllowProducts();
        }
        else{
            $simpleProducts = $this->getAllowProducts();
        }
        return $simpleProducts;
    }

    /**
     * get switch type
     * @access public
     * @return mixed
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getSwitchImageType(){
        return Mage::getStoreConfig(self::XML_CHANGE_IMAGES_PATH);
    }

    /**
     * get main images for simple products
     * @access public
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getSwitchImages(){
        if ($this->getSwitchImageType() != Easylife_Switcher_Model_Adminhtml_System_Config_Source_Switch::SWITCH_MAIN){
            return array();
        }
        $changeAttributes = $this->getSwitchAttributes(self::XML_CHANGE_IMAGE_PATH);
        $simpleProducts = $this->getSimpleProducts();
        $images = array();
        foreach ($changeAttributes as $id=>$code){
            foreach ($simpleProducts as $product){
                if ($product->getData('image') != '' && $product->getData('image') != 'no_selection'){
                    $image = Mage::helper('catalog/image')->init($product, 'image');
                    $dimensions = $this->_getImageDimensions();
                    if (!empty($dimensions)){
                        $image->resize($dimensions[0], $dimensions[1]);
                    }
                    $images[$id][$product->getId()] = (string)$image;
                }
            }
        }
        return $images;
    }

    /**
     * get media images for changing full media
     * @access public
     * @return array
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    public function getSwitchMedia(){
        if ($this->getSwitchImageType() != Easylife_Switcher_Model_Adminhtml_System_Config_Source_Switch::SWITCH_MEDIA){
            return array();
        }
        $changeAttributes = $this->getSwitchAttributes(self::XML_CHANGE_MEDIA_PATH);
        $simpleProducts = $this->getSimpleProducts();
        $images = array();
        $block = Mage::app()->getLayout()
            ->createBlock($this->_getMediaBlockType())
            ->setTemplate($this->_getMediaBlockTemplate());
        foreach ($changeAttributes as $id=>$code){
            foreach ($simpleProducts as $product){
                $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($product->getId());
                if ($product->getData('image') != '' && $product->getData('image') != 'no_selection'){
                    $block->setProduct($product);
                    $images[$id][$product->getId()] = $block->toHtml();
                }
            }
        }
        return $images;
    }

    /**
     * get main image dimentions
     * @access protected
     * @return array|bool
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getImageDimensions(){
        $value = Mage::getStoreConfig(self::XML_IMAGE_RESIZE);
        if (!$value){
            return false;
        }
        $dimensions = explode('x', $value, 2);
        if (!isset($dimensions[1])){
            $dimensions[1] = $dimensions[0];
        }
        return $dimensions;
    }

    /**
     * get the media block alias
     * @access protected
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getMediaBlockType(){
        $block = Mage::getStoreConfig(self::XML_MEDIA_BLOCK_TYPE_PATH);
        if (!$block){
            $block = self::DEFAULT_MEDIA_BLOCK_TYPE;
        }
        return $block;
    }

    /**
     * get the media block template
     * @access protected
     * @return string
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    protected function _getMediaBlockTemplate(){
        $template = Mage::getStoreConfig(self::XML_MEDIA_TEMPLATE_PATH);
        if (!$template){
            $template = self::DEFAULT_MEDIA_TEMPLATE;
        }
        return $template;
    }
}
