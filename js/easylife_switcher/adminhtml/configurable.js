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
 * extend Product.Configurable class to support default configuration
 *
 * @category    Easylife
 * @package	    Easylife_Switcher
 */
if(typeof Easylife=='undefined') {
    var Easylife = {};
}
Easylife.Switcher = Class.create(Product.Configurable, {
    /**
     * initialize the object
     * add a restriction "canCreateAttributes" so the configurable attributes won't be added twice
     *
     * @param $super
     * @param attributes
     * @param links
     * @param idPrefix
     * @param grid
     * @param readonly
     * @param defaultConfigurationElement
     */
    initialize: function($super, attributes, links, idPrefix, grid, readonly, defaultConfigurationElement){
        var that = this;
        this.canCreateAttributes = false;
        this.defaultConfigurationElement = defaultConfigurationElement;
        $super(attributes, links, idPrefix, grid, readonly);
        this.canCreateAttributes = true;
        if ($(defaultConfigurationElement + '_default')) {
            this.defaultConfigurationElementInherit = $(defaultConfigurationElement + '_default');
            //move the element outside the grid
            //kind of hacky but I don't want to rewrite 2 more blocks and a new js class
            $(this.defaultConfigurationElementInherit).up().show();
            $('super_product_links').insert({
                after: $(this.defaultConfigurationElementInherit).up()
            })
        }
        else {
            this.defaultConfigurationElementInherit = false;
        }
        this.bindInherit();
    },
    /**
     * bind inherit click for store views
     */
    bindInherit: function() {
        var that = this;
        if (this.defaultConfigurationElementInherit) {
            $(this.defaultConfigurationElementInherit).observe('change', function() {
                that.checkInherit();
            });
            this.checkInherit();
        }
    },
    /**
     * check inherit
     */
    checkInherit: function() {
        toggleValueElements(
            $(this.defaultConfigurationElementInherit),
            $(this.defaultConfigurationElement).parentNode.parentNode
        );
        var checked = $(this.defaultConfigurationElementInherit).checked;
        $$('#super_product_links_table input[name="default_combination"]').each(function(elem) {
            if (checked) {
                $(elem).disabled = checked;
            }
            else {
                //check if the checkbox near the radio is checked
                var tr = $(elem).up(1);
                var checkbox = $(tr).down('input[type=checkbox]');
                if (checkbox.checked) {
                    $(elem).disabled = checked;
                }
            }
        })
    },
    /**
     * rewrite rowInit to support click on the 'default config' radio
     * @param $super
     * @param grid
     * @param row
     */
    rowInit : function($super, grid, row) {
        $super(grid, row);
        var checkbox = $(row).down('.checkbox');
        var input = $(row).down('.value-json');
        var radio = $(row).down('.radio');
        if (checkbox && input) {
            if (checkbox.disabled == true || checkbox.checked == false){
                radio.disable();
                radio.checked = false;
                this.clearDefaultConfiguration(radio.value);
            } else{
                if (!this.defaultConfigurationElementInherit || !$(this.defaultConfigurationElementInherit).checked) {
                    if (radio.value == $(this.defaultConfigurationElement).value) {
                        radio.checked = 'checked';
                    }
                    radio.enable();
                } else {
                    radio.disable();
                }
            }
        }
    },
    /**
     * rewrite createAttributes so it won't run twice
     * @param $super
     */
    createAttributes: function($super){
        if (this.canCreateAttributes){
            $super();
        }
    },
    /**
     * rewrite rowClick to support default config radio
     * @param $super
     * @param grid
     * @param event
     */
    rowClick : function($super, grid, event) {
        $super(grid, event);
        var trElement = Event.findElement(event, 'tr');
        var isInput = Event.element(event).tagName.toUpperCase() == 'INPUT';

        if ($(Event.findElement(event, 'td')).down('a')) {
            return;
        }
        if (isInput && Event.element(event).type == "radio"){
            $(this.defaultConfigurationElement).value = Event.element(event).value;
            return;
        }
        if (trElement) {
            var checkbox = $(trElement).down('input[type=checkbox]');
            var radio = $(trElement).down('input[type=radio]');
            if (checkbox){
                if (checkbox.disabled == true || checkbox.checked == false){
                    radio.disable();
                    radio.checked = false;
                    this.clearDefaultConfiguration(radio.value);
                }
                else{
                    if (!this.defaultConfigurationElementInherit || !$(this.defaultConfigurationElementInherit).checked) {
                        radio.enable();
                    }
                    else {
                        radio.disable();
                    }
                }
            }
        }
    },
    /**
     * clear default configuration
     * @param value
     */
    clearDefaultConfiguration: function(value){
        if (value == $(this.defaultConfigurationElement).value){
            $(this.defaultConfigurationElement).value = '';
        }
    }
});
