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
 * extend Product.Configurable class to support default configuration
 *
 * @category    Easylife
 * @package	    Easylife_Switcher
 * @author 	    Marius Strajeru <marius.strajeru@gmail.com>
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
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    initialize: function($super, attributes, links, idPrefix, grid, readonly, defaultConfigurationElement){
        this.canCreateAttributes = false;
        this.defaultConfigurationElement = defaultConfigurationElement;
        $super(attributes, links, idPrefix, grid, readonly);
        this.canCreateAttributes = true;
    },
    /**
     * rewrite rowInit to support click on the 'default config' radio
     * @param $super
     * @param grid
     * @param row
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    rowInit : function($super, grid, row) {
        $super(grid, row);
        var checkbox = $(row).down('.checkbox');
        var input = $(row).down('.value-json');
        var radio = $(row).down('.radio')
        if (checkbox && input) {
            if (checkbox.disabled == true || checkbox.checked == false){
                radio.disable();
                radio.checked = false;
                this.clearDefaultConfiguration(radio.value);
            }
            else{
                radio.enable();
            }
        }
    },
    /**
     * rewrite createAttributes so it won't run twice
     * @param $super
     * @author Marius Strajeru <marius.strajeru@gmail.com>
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
     * @author Marius Strajeru <marius.strajeru@gmail.com>
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
            console.log(checkbox);
            var radio = $(trElement).down('input[type=radio]');
            console.log(radio);
            if (checkbox){
                if (checkbox.disabled == true || checkbox.checked == false){
                    radio.disable();
                    radio.checked = false;
                    this.clearDefaultConfiguration(radio.value);
                }
                else{
                    radio.enable();
                }
            }
        }
    },
    /**
     * clear default configuration
     * @param value
     * @author Marius Strajeru <marius.strajeru@gmail.com>
     */
    clearDefaultConfiguration: function(value){
        if (value == $(this.defaultConfigurationElement).value){
            $(this.defaultConfigurationElement).value = '';
        }
    }
});
