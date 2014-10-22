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
 * extend Product.Config class to support image switchers and option labels
 *
 * @category    Easylife
 * @package	    Easylife_Switcher
 * @author 	    Marius Strajeru <marius.strajeru@gmail.com>
 */
if(typeof Easylife=='undefined') {
    var Easylife = {};
}
Easylife.Switcher = Class.create(Product.Config, {
    currentValues: {},
    /**
     * rewrite initialize to transform dorpdowns and support default configuration
     * @param $super
     * @param config
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    initialize: function($super, config){
        if (typeof config.defaultValues == 'undefined'){
            var separatorIndex = window.location.href.indexOf('#');
            if (separatorIndex != -1) {
                var paramsStr = window.location.href.substr(separatorIndex+1);
                var urlValues = paramsStr.toQueryParams();
                if (!config.defaultValues) {
                    config.defaultValues = {};
                }
                for (var i in urlValues) {
                    if (typeof config.attributes[i] != 'undefined'){
                        config.defaultValues[i] = urlValues[i];
                    }
                }
            }
            if (config.autoselect_first) {
                var count = 0;
                if (typeof config.defaultValues != 'undefined'){
                    for (var k in config.defaultValues) {
                        if (config.defaultValues.hasOwnProperty(k)) {
                            ++count;
                        }
                    }
                }
                if (count == 0){
                    config.defaultValues = {};
                    for (var attribute in config.attributes){
                        if (config.attributes.hasOwnProperty(attribute)){
                            var option = config.attributes[attribute].options[0].id;
                            config.defaultValues[attribute] = option;
                        }
                    }
                }
            }
        }
        $super(config);
        this.rewritten = true;
        if (this.config.transform_dropdowns){
            this.transformDropdowns();
        }
    },
    /**
     * rewrite fillSelect to transform elements to labels
     * @param $super
     * @param element
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    fillSelect: function($super, element){
        $super(element);
        if (this.config.transform_dropdowns){
            this.transformDropdown(element);
        }
        else {
            if (!this.config.allow_no_stock_select) {
                var attributeId = element.id.replace(/[a-z]*/, '');
                var options = this.getAttributeOptions(attributeId);
                for (var i in options) {
                    if (options.hasOwnProperty(i)){
                        var optval = options[i].id;
                        var inStock = this.isInStock(attributeId, optval)
                        $(element).select('option').each (function(elem){
                            if ($(elem).value == optval && !inStock) {
                                $(elem).disabled="disabled";
                            }
                        });
                    }
                }
            }
        }

    },
    /**
     * transform dropdowns to labels
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    transformDropdowns: function(){
        for (var i=0; i<this.settings.length;i++){
            this.transformDropdown(this.settings[i]);
        }
    },
    /**
     * transform one dropdown to labels
     * @param selectid
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    transformDropdown: function(selectid){
        var that = this;
        var attributeId = $(selectid).id.replace(/[a-z]*/, '');
        if (this.config.transform_dropdowns == 0) {
            return false;
        }
        if (this.config.transform_dropdowns == 2) {
            if (this.config.transform_specific.indexOf(attributeId) == -1) {
                return false;
            }
        }

        var selectname = $(selectid).name;
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
        //create a container
        $(selectid).insert({after: '<div class="switcher-field switcher-'+that.config.attributes[attributeId]['code']+'" id="' + newId + '"></div>'});
        //create a label for each element
        $(selectid).childElements().each(function(elem, index){
            //skip first element "Choose..."
            if (index == 0){
                return;
            }
            var optval = $(elem).value;
            if (optval != ''){
                var opttext = $(elem).innerHTML;
                if (typeof that.config.images[attributeId] != 'undefined' || typeof that.config.option_images[attributeId] != 'undefined'){
                    for ( var j=0; j<that.config.attributes[attributeId].options.length;j++){
                        if (that.config.attributes[attributeId].options[j].id != optval){
                            continue;
                        }
                        var product = parseInt(that.config.attributes[attributeId].options[j].allowedProducts[0]);
                        //replace label with image if available
                        var replaced = false;
                        if (typeof that.config.images[attributeId] != 'undefined' && typeof that.config.images[attributeId][product] != 'undefined') {
                            opttext = '<img src="' + that.config.images[attributeId][product] + '" alt="' + opttext + '" title="' + opttext + '" />';
                        } else if (typeof that.config.option_images[attributeId] != 'undefined' && typeof that.config.option_images[attributeId][optval] != 'undefined') {
                            if(typeof that.config.option_images[attributeId][optval]['image_url'] != 'undefined') {
                                opttext = '<img src="' + that.config.option_images[attributeId][optval]['image_url'] + '" alt="' + opttext + '" title="' + opttext + '" />';
                            } else if( typeof that.config.option_images[attributeId][optval]['hexa_code'] != 'undefined') {
                                opttext = '<span class="switcher-hexacode" title="' + opttext + '" style="background-color:' + that.config.option_images[attributeId][optval]['hexa_code']+'"></span>';
                            }
                        }

                    }
                }
                var labelClass = '';
                if ($(elem).selected){
                    labelClass = ' selected';
                }
                var inStock = that.isInStock(attributeId, optval);
                //check if the combination is in stock
                if (!inStock){
                    labelClass += ' no-stock';
                    if (that.config.allow_no_stock_select){
                        labelClass += ' allow-select';
                    }
                }
                $(newId).insert('<label class="switcher-label' + labelClass + '" id="' + $(selectid).id + '_' + optval + '" value="' + optval + '">'+opttext+'</label></div>')
                //change the select value on click
                if (inStock || that.config.allow_no_stock_select){
                    Event.observe($($(selectid).id + '_' + optval), 'click', function() {
                        that.selectValue(this, $(this).readAttribute('value'), selectid);
                    });
                }
                // Make IE 7 & 8 behave like browsers - damn you IE
                if (index == $(selectid).childElements().length - 1){
                    $(newId).insert('<div style="clear:both"></div>');
                }
            }
        })
    },
    /**
     * select a value when clicking a label
     * @param elem
     * @param value
     * @param selectid
     * @author Marius Strajeru <marius.strajeru@gmail.com>
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
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    simulateSelect: function(selectid, value){
        $(selectid).value = value;
        $(selectid).simulate('change');
    },
    /**
     * check if a combination is in stock
     * @param attributeId
     * @param value
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    isInStock: function(attributeId, value){
        var that = this;
        for ( var j=0; j<that.config.attributes[attributeId].options.length;j++){
            if (that.config.attributes[attributeId].options[j].id != value){
                continue;
            }
            for (var i = 0; i<that.config.attributes[attributeId].options[j].allowedProducts.length;i++){
                var product = that.config.attributes[attributeId].options[j].allowedProducts[i];
                if (this.config.stock[product] == 1){
                    return 1;
                }
            }
            return 0;
        }
        return 1;
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
     * rewrite condigureElement to change the main image or media block
     * @param $super
     * @param element
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    configureElement: function($super, element){

        try{
            //parent action
            $super(element);
            var attributeId = $(element).id.replace(/[a-z]*/, '');
            this.currentValues[attributeId] = $(element).value;
            this.keepSelection(element);
            var value = $(element).value;
            var options = this.config.attributes[attributeId].options;
            //if we should not switch anything stop it here
            if (this.config.switch_image_type == 0){
                return false;
            }
            for (var id in options){
                if (options.hasOwnProperty(id) && options[id].id == value){
                    var product = options[id].allowedProducts[0];
                    //if we should switch only the image
                    if (parseInt(this.config.switch_image_type) == 1 &&
                        //check id an image is available for the current product
                        typeof this.config.switch_images[attributeId] != 'undefined' &&
                        typeof this.config.switch_images[attributeId][product] != 'undefined'
                    ) {
                        //get the image selector set in system->configuration
                        var image = eval(this.config.main_image_selector);
                        //if the image exists
                        if (image){
                            //change the src
                            $(image).src = this.config.switch_images[attributeId][product];
                            //hack for default theme zoomer
                            //don't call the callback on the first page load
                            if (this.fullLoad && this.config.switch_image_callback){
                                //callback - give it 0.1 seconds for the image src to be changed
                                //a small flicker might occur
                                //if you don't like it you can remove it at your own risk
                                eval.delay('0.1', this.config.switch_image_callback)
                            }
                        }
                    }
                    //if the media block should be changed
                    if (parseInt(this.config.switch_image_type) == 2 &&
                        //check if there is a media to change for the current product
                        typeof this.config.switch_media[attributeId] != 'undefined' &&
                        typeof this.config.switch_media[attributeId][product] != 'undefined'
                    ){

                        //get the media block selector from system->configuration
                        var media = eval(this.config.switch_media_selector);
                        if (media){
                            //hack for default theme zoom-er that doesn't work if called twice.
                            //if it's not the page load
                            if (this.fullLoad){
                                $(media).update(this.config.switch_media[attributeId][product]);
                                if (this.config.switch_media_callback){
                                    //give it 0.1 seconds to settle in
                                    //a small flicker might occur
                                    //if you don't like it you can remove it at your own risk
                                    eval.delay('0.1', this.config.switch_media_callback)
                                }
                            }
                            else{
                                //if on page load, just add the html
                                $(media).innerHTML = this.config.switch_media[attributeId][product];
                            }
                        }
                    }
                }
            }
        }
        catch (e){
            console.log(e);
        }
    },
    /**
     * rewrite configureForValues to avoid calling the switch callback on first load
     * this may not be necessary if the default theme zoomer is not used
     * @param $super
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    configureForValues: function($super){
        this.fullLoad = false;
        $super();
        this.fullLoad = true;
    },
    getOptionLabel : function($super, option, price) {
        if (this.config.show_added_prices) {
            return $super(option, price);
        }
        return option.label;
    }
});
