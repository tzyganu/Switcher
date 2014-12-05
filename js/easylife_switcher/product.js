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
 * extend Product.Config class to support image switchers and option labels
 *
 * @category    Easylife
 * @package	    Easylife_Switcher
 */

if(typeof Easylife=='undefined') {
    var Easylife = {};
}
Easylife.Switcher = Class.create(Product.Config, {
    currentValues: {},
    rewritten: false,
    /**
     * rewrite initialize to transform dorpdowns and support default configuration
     * @param $super
     * @param config
     */
    initialize: function($super, config){
        $super(config);
        this.rewritten = true;
        this.transformDropdowns();
    },
    /**
     * rewrite fillSelect to transform elements to labels
     * @param $super
     * @param element
     */
    fillSelect: function($super, element){
        $super(element);
        //if (this.config.transform_dropdowns){
        var transformed = this.transformDropdown(element);
        if (!transformed && !this.getConfigValue(this.config, 'allow_no_stock_select', false)) {
            var attributeId = element.id.replace(/[a-z]*/, '');
            var options = this.getAttributeOptions(attributeId);
            for (var i in options) {
                if (options.hasOwnProperty(i)){
                    var optVal = options[i].id;
                    var inStock = this.isInStock(attributeId, optVal);
                    $(element).select('option').each (function(elem){
                        if ($(elem).value == optVal && !inStock) {
                            $(elem).disabled="disabled";
                        }
                    });
                }
            }
        }
    },
    /**
     * transform dropdowns to labels
     */
    transformDropdowns: function(){
        for (var i=0; i<this.settings.length;i++){
            this.transformDropdown(this.settings[i]);
        }
    },
    /**
     * transform one dropdown to labels
     * @param selectid
     */
    transformDropdown: function(selectid){
        var that = this;
        var attributeId = $(selectid).id.replace(/[a-z]*/, '');
        var transformed = false;
        var switchConfig = this.getConfigValue(this.config, 'switch/' + attributeId, false);
        if (switchConfig && typeof(switchConfig) == "object") {
            transformed = true;
            //transform dropdown
            var newId = $(selectid).id +'_switchers';
            //remove previous labels
            if ($(newId)){
                $(newId).remove();
            }
            //hide the select
            //actually move it outside the visible area so the validation will still work
            $(selectid).setStyle({
                left:"-10000px",
                position: "absolute"
            });
            $(selectid).insert({after: '<div class="switcher-field switcher-'+that.config.attributes[attributeId]['code']+'" id="' + newId + '"></div>'});
            $(selectid).childElements().each(function(elem, index){
                //skip first element "Choose..."
                if (index == 0){
                    return;
                }
                var optVal = $(elem).value;
                var title =  $(elem).innerHTML;
                var optText = that.getOptionText(elem, optVal, switchConfig);
                var inStock = that.isInStock(attributeId, optVal);
                var labelClass = that.getLabelClass(elem, attributeId, optVal, inStock);

                $(newId).insert('<label class="switcher-label' + labelClass + '" id="' + $(selectid).id + '_' + optVal + '" value="' + optVal + '" title="' + title + '">'+optText+'</label>');
                //change the select value on click
                that.bindClickEvent(selectid, optVal, inStock);

                // Make IE 7 & 8 behave like real browsers - damn you IE
                if (index == $(selectid).childElements().length - 1){
                    $(newId).insert('<div style="clear:both"></div>');
                }
            })
        }
        return transformed;
    },
    /**
     * bind click event on labels
     */
    bindClickEvent: function(selectid, optVal, inStock) {
        var that = this;
        if (inStock || this.getConfigValue(this.config, 'allow_no_stock_select', false)){
            Event.observe($($(selectid).id + '_' + optVal), 'click', function() {
                that.selectValue(this, $(this).readAttribute('value'), selectid);
            });
        }
    },
    /**
     * get the class name of the labels
     */
    getLabelClass: function (elem, attributeId, optVal, inStock) {
        var labelClass = '';
        if ($(elem).selected){
            labelClass = ' selected';
        }
        //check if the combination is in stock
        if (!inStock){
            labelClass += ' no-stock';
            labelClass += this.getConfigValue(this.config, 'allow_no_stock_select', false) ? ' allow-select' : '';
        }
        return labelClass;
    },
    /**
     * ge the option text of the label
     */
    getOptionText: function(elem, value, config){
        var text = $(elem).innerHTML;
        var configType = this.getConfigValue(config, 'type', false);
        if (!configType) {
            return text;
        }
        switch (config.type) {
            case 'custom_images':
                var image = this.getConfigValue(config, 'images/' + value, false);
                if (image) {
                    text = '<img src="' + image + '" alt="' + text +'" />';
                }
                break;
            case 'product_images':
                //get the images
                var imageAttribute = this.getConfigValue(config, 'product_images', '');
                var images = this.getConfigValue(this.config.images, imageAttribute, []);
                //get first allowed product
                var attrId = $(elem).parentNode.id.replace(/[a-z]*/, '');
                var options = this.getConfigValue(this.config.attributes, attrId + '/options', false);
                var productId = this.getFirstProductId(options, value);
                if (productId && (image = this.getConfigValue(images, productId, false))) {
                    text = '<img src="' + image + '" alt="' + text +'" />';
                }
                break;
            case 'colors':
                var color = this.getConfigValue(config, 'color/' + value, false);
                if (color) {
                    text = '<span class="switcher-hexacode" title="' + text + '" style="background-color:' + color +'"></span>'
                }
                break;
            default:
                text = this.handleCustomOptionText(text, elem, value, config);
                break;
        }
        return text;
    },
    /**
     * this can be overwritten in a custom class if more transformation types are added.
     * by default it returns the current text
     * @param text
     * @param elem
     * @param value
     * @param config
     * @returns {*}
     */
    handleCustomOptionText: function(text, elem, value, config) {
        return text;
    },
    /**
     * get the first allowed product
     * @param options
     * @param value
     * @returns {boolean}
     */
    getFirstProductId: function(options, value) {
        var productId = false;
        //get first product in the list
        for (var i in options) {
            if (options.hasOwnProperty(i) && options[i].id == value) {
                productId = this.getConfigValue(options[i], 'products/0', false);
            }
        }
        return productId;
    },
    /**
     * get values from an array or object. Similar to magento's getData for Varien_Object
     * @param config
     * @param path
     * @param def
     * @returns {*}
     */
    getConfigValue: function(config, path, def) {
        var parts = path.split('/');
        var cloneConfig = config;
        var i = 0;
        while (i < parts.length && cloneConfig != -1){
            var part = parts[i];
            if (typeof cloneConfig[part] != "undefined") {
                cloneConfig = cloneConfig[part];
            }
            else {
                cloneConfig = -1;
            }
            i++;
        }
        if (cloneConfig == -1) {
            return def;
        }
        return cloneConfig;
    },
    /**
     * select a value when clicking a label
     * @param elem
     * @param value
     * @param selectid
     */
    selectValue: function(elem, value, selectid){
        if ($(elem)){
            $(elem).up(1).select('label').each (function(el){
                $(el).removeClassName('selected');
            });
            $(elem).addClassName('selected');
            this.simulateSelect(selectid, $(elem).readAttribute('value'));
        }
    },
    /**
     * simulate onchange event on select
     * @param selectid
     * @param value
     */
    simulateSelect: function(selectid, value){
        $(selectid).value = value;
        $(selectid).simulate('change');
    },
    /**
     * check if a combination is in stock
     * @param attributeId
     * @param value
     */
    isInStock: function(attributeId, value){
        var options = this.getConfigValue(this.config, 'attributes/' + attributeId + '/options', []);
        var allowedProducts = [];
        for ( var j=0; j<options.length;j++){
            if (options[j].id == value){
                allowedProducts = this.getConfigValue(options[j], 'allowedProducts', []);
                break;
            }
        }
        for (var i = 0; i<allowedProducts.length;i++){
            var product = allowedProducts[i];
            if (this.getConfigValue(this.config, 'stock/' + product, 0) == 1){
                return 1;
            }
        }
        return 0;
    },
    /**
     * keep previously selected values
     * @param element
     */
    keepSelection: function(element) {
        if (this.config.keep_values && element.nextSetting) {
            var nextSettingId = $(element.nextSetting).id.replace(/[a-z]*/, '');
            if (this.currentValues[nextSettingId]) {
                $(element.nextSetting).value = this.currentValues[nextSettingId];
                var label = $('attribute' + nextSettingId + '_' + this.currentValues[nextSettingId]);
                if (label) {
                    $(label).simulate('click');
                }
                else {
                    $(element.nextSetting).simulate('change');
                }
            }
        }
    },
    /**
     * change the main image of the product
     * @param attributeId
     * @param product
     */
    changeMainImage: function(attributeId, product) {
        var productImage = this.getConfigValue(this.config, 'switch_images/' + attributeId + '/' + product, false);
        var image = eval(this.getConfigValue(this.config, 'main_image_selector', false));
        if (productImage && image) {
            //change the src
            $(image).src = productImage;
            //hack for default theme zoomer
            //don't call the callback on the first page load
            var callback = this.getConfigValue(this.config, 'switch_image_callback', false);
            if (this.fullLoad && callback){
                //callback - give it 0.1 seconds for the image src to be changed
                //a small flicker might occur
                //if you don't like it you can remove it at your own risk
                eval.delay('0.1', callback)
            }
        }
    },
    /**
     * change the media block for the product
     * @param attributeId
     * @param product
     */
    changeMediaBlock: function(attributeId, product) {
        var mediaHtml = this.getConfigValue(this.config, 'switch_media/' + attributeId + '/' + product, false);
        var mediaEval = this.getConfigValue(this.config, 'switch_media_selector', false);
        var media = mediaEval ? eval(mediaEval) : false;
        if (media && mediaHtml){
            //hack for default theme zoom-er that doesn't work if called twice.
            //if it's not the page load
            $(media).innerHTML = mediaHtml;
            if (this.fullLoad){
                var callback = this.getConfigValue(this.config, 'switch_media_callback', false);
                if (callback){
                    //give it 0.1 seconds to settle in
                    //a small flicker might occur
                    //if you don't like it you can remove it at your own risk
                    eval.delay('0.1', callback)
                }
            }
        }
    },
    /**
     * rewrite configureElement to change the main image or media block
     * @param $super
     * @param element
     */
    configureElement: function($super, element){
        $super(element);
        var attributeId = $(element).id.replace(/[a-z]*/, '');
        this.currentValues[attributeId] = $(element).value;
        this.keepSelection(element);
        var value = $(element).value;
        //var options = this.config.attributes[attributeId].options;
        //if we should not switch anything stop it here
        var switchType = this.getConfigValue(this.config, 'switch_image_type', 0);
        if (switchType == 0) {
            return ;
        }
        var options = this.getConfigValue(this.config, 'attributes/' + attributeId + '/options', []);
        for (var id in options){
            if (options.hasOwnProperty(id) && options[id].id == value){
                var product = options[id].allowedProducts[0];
                if (switchType == 1) {
                    this.changeMainImage(attributeId, product);
                }
                else if(switchType == 2) {
                    //var product = this.getConfigValue(this.config, 'switch_media/' + attributeId + '/' + product)
                    this.changeMediaBlock(attributeId, product);
                }
            }
        }
    },
    /**
     * rewrite configureForValues to avoid calling the switch callback on first load
     * this may not be necessary if the default theme zoomer is not used
     * @param $super
     */
    configureForValues: function($super){
        this.fullLoad = false;
        $super();
        this.fullLoad = true;
    },
    getOptionLabel : function($super, option, price) {
        if (this.getConfigValue(this.config, 'show_added_prices', false)) {
            return $super(option, price);
        }
        return option.label;
    }
});
