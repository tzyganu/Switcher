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
 *
 * @category    Easylife
 * @package	    Easylife_Switcher
 */
if(typeof Easylife=='undefined') {
    var Easylife = {};
}
Easylife.SwitcherSystem = Class.create({
    /**
     * initialize class
     * @param container
     * @param config
     */
    initialize: function(container, config) {
        var that = this;
        this.config = config;
        this.container = $(container);
        this.container.select('.transform').each(function(elem){
            $(elem).observe('change', function(){
                that.changeTransform(elem);
            });
            that.changeTransform(elem);
        });
        //move the options outside the form so it will not be submitted.
        $$('body')[0].insert($(that.config.prefix + '_options'));


    },
    /**
     * change event on transform fields
     * @param element
     */
    changeTransform: function(element) {
        var id = $(element).id;
        var value = $(element).value;
        $(element).up().select('.after > div').each(function(elem){
            $(elem).hide();
            if ($(id + '_' + value)) {
                $(id + '_' + value).show();
            }
        });
    },
    /**
     * fill the values of the popup form
     * @param url
     * @param attributeId
     * @param type
     * @param submitUrl
     */
    fillValues: function(url, attributeId, type, submitUrl) {
        var that = this;
        that.currentAttributeId = attributeId;
        that.currentType = type;
        that.submitUrl = submitUrl;
        new Ajax.Request(url, {
            method: 'get',
            parameters: {
                attribute_id: attributeId,
                type: type,
                val: $(that.config.prefix + '_' + that.currentAttributeId + '_' + that.currentType).value
            },
            onSuccess: function(response){
                var result = eval('(' + response.responseText + ')');
                if (!result.success){
                    alert(result.html);
                } else{
                    var prefix = '';
                    var suffix = '';
                    if (that.submitUrl) {
                        prefix = '<form target="iframeSwitcherSave" id="' + that.config.prefix + '_form' + '" method="post"  enctype="multipart/form-data" action="' +  that.submitUrl +'">';
                        prefix += '<input type="hidden" name="form_key" value="' + FORM_KEY + '" />';
                        prefix += '<input type="hidden" name="js_var_name" value="' + that.config.prefix + '_switcher_config" />';
                        prefix += '<div id=' + that.config.prefix + '_serializable' + '>';
                        suffix = '</div></form>';
                    }
                    $(that.config.prefix + '_options_content').innerHTML = prefix + result.html + suffix;
                    $(that.config.prefix + '_options').show();
                }
            },
            onComplete: function(response) {
                that.hideLoadingMask();
            }
        });
    },
    /**
     * save the popup form
     */
    save: function() {
        if (this.submitUrl != 0) {
            this.displayLoadingMask();
            $(this.config.prefix + '_form').submit();
        }
        else {
            this.updateHidden(JSON.stringify(Form.serialize($(this.config.prefix + '_serializable'), true)));
        }

    },
    /**
     * display loading overlay
     */
    displayLoadingMask: function() {
        var loaderArea = $$('#html-body .wrapper')[0];
        var mask = $('loading-mask');
        Position.clone($(loaderArea), mask , {offsetLeft:-2});
        toggleSelectsUnderBlock(mask, false);
        Element.show('loading-mask');
    },
    /**
     * hide loading overlay
     */
    hideLoadingMask: function() {
        Element.hide('loading-mask');
    },
    /**
     * update hidden field
     * @param value
     */
    updateHidden: function(value) {
        $(this.config.prefix + '_' + this.currentAttributeId + '_' + this.currentType).value = value;
        this.hideLoadingMask();
    }
});
