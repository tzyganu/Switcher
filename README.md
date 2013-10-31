Easylife Switcher
========

Configurable products switcher v0.0.1- Beta
----------

This Magento extension affects the configurable products display.

Requirements:
---------
 - Magento CE 1.7.0.2 or CE 1.8.0.0.
 - Some minimum js programming skills may be needed to make it work properly on a "very" custom theme. See the *Configuration* section.
 - Your Magento instance must not contain an attribute with the code `default_configuration_id`. If that attribute exists (chances are it doesn't) then edit the file `app/code/community/Easylife/Switcher/Helper/Data.php` and change the value of the constant `DEFAULT_CONFIGURATION_ATTRIBUTE_CODE` to an attribute code that does not exist. Do this before the installation. After...it's too late.

What it does
----------------
Frontend:  

 - it can change the configurable products dropdowns to text or image labels <br />  <img src="http://i.imgur.com/FdHVLAu.png" alt="config" />  
 - it allows you do display the out od stock combinations of configurable products with a "not available" overlay (see image above - medium size). The out of stock products may be selectable or not. Your choice.  
 - it can change the main image in the product page when a combination is selected, if an image is available for the simple product. You can set the attributes that change the image.  
 - it can change the full media block in the product page when a combination is selected, if at least the main image is available for the simple product. You can set the attributes that change the media section.  

Backend: 

 - it allows you to set a default configuration to be selected while accessing the configurable product page.<br /> <img src="http://i.imgur.com/zygt7o2.png" alt="backend" />  

How to use:
---------
 - After installation clear the cache and logout & log in the backend.
 - By default the extension is disabled in the backend. To enable it go to *System->Configuration->Easylife Switcher* and read the *Configuration* section.
 - After enabling it you will see an additional column in the `Associated Products` tab of a configurable product that allows you to set a default configuration. To reset the default configuration just uncheck the checkbox near the selected radio element and check it back again.

Configuration:
------------
 - **Enabled**: This can enable or disable the extension.
 - **Transform dropdowns to labels**: If set to `Yes` then in the frontend the default dropdowns for the configurable products will be replaced by labels.
 - **Show out of stock configurations**: If set to `Yes` then you will see in the configurable product page the out of stock simple product combinations. By default this is disabled in Magento.
 - **Allow out of stock products to be selected**: If this is set to `Yes` then the customer will be able to click on the labels for the out of stock combinations and select them. He will still get an error when trying to add it to the cart. If it is set to `No` the labels for out of stock combinations will be disabled.
 - **Use simple product images instead of labels for attributes**: This allows you to select the attributes that will have images instead of labels. (useful if you have different color combinations). If the simple product does not have an image available then the label with the attribute value will be displayed. (see the first image for the "color" attribute).
 - **Use this image attribute**: From here you can select the attribute image (image, small_image, thumbnail) to be used for image labels.
 - **Switch product images**: This allows you to change the product image or the whole media block when an attribute combination is changed. If you choose to change the image, this will be changed only if there is one available for the simple product. If you have a lot of simple product combinations and choose to change the whole media block it can lead to performance issues.
 - **Change images when these attributes are changed**: It appears only if you choose to change only the main image and allows you to select which attribute change triggers the image change also.
 - **Dom selector for main image**: It appears only if you choose to change only the main image. This should be the prototype selector for the main image element. by default it looks for the element with id `image`.
 - **Main image size**: It appears only if you choose to change only the main image. You can specify here the main image size, because I cannot read it from the media block. If empty then the image won't be resized.
 - **Js Callback after main image change**: It appears only if you choose to change only the main image. This is the javascript code that will be executed after the image changes. Useful if you have image zoom.
 - **Change media content when these attributes are changed**: It appears only if you choose to change the media block and it allows you to select which attribute change triggers the media block change also.
 - **Dom Media block selector**: It appears only if you choose to change the media block. This is the prototype selector of the element that contains the media block.
 - **Js Callback after media change**: It appears only if you choose to change the media block. This is the JS code that is executed after the media block is changed
 - **Media block alias**: It appears only if you choose to change the media block. It allows you to specify which block is used for rendering the media section. It's useful if you have extensions that change the media block. Leave empty to use default (`catalog/product_view_media`).
 - **Media block template**: It appears only if you choose to change the media block. It allows you to specify the template used for rendering the media section. It's useful if you have extensions that change the media template. Leave empty to use default (`catalog/product/view/media.phtml`).

Uninstall:
---------

To unsintall the extension remove the following files and folders  

 - app/code/community/Easylife/Switcher/  
 - app/design/adminhtml/default/default/layout/easylife_switcher.xml  
 - app/design/adminhtml/default/default/template/easylife_switcher/  
 - app/design/frontend/base/default/layout/easylife_switcher.xml  
 - app/design/frontend/base/default/template/easylife_switcher/  
 - etc/modules/Easylife_Switcher.xml  
 - app/local/en_US/Easylife_Switcher.csv  
 - js/easylife_switcher/  
 - skin/frontend/base/default/css/easylife_switcher/  
 - skin/frontend/base/default/images/easylife_switcher/  

Run these queries on your database (add table prefix if you have one):

 - `DELETE FROM core_config_data WHERE path LIKE 'easylife_switcher/%'`
 - `DELETE FROM core_resource WHERE code = 'easylife_switcher_setup'`  

The extension adds a new attribute to the configurable products. To identify it run this query (if you changed the attribute code before install change it in this query also):  
<pre><code>SELECT 
    e.attribute_id, e.attribute_code 
FROM 
    eav_attribute e 
    LEFT JOIN eav_entity_type et 
    ON e.entity_type_id = et.entity_type_id 
WHERE 
    e.attribute_code = 'default_configuration_id' AND 
    et.entity_type_code = 'catalog_product'`</code></pre>    
To delete it just run  

`DELETE FROM eav_attribute where attribute_id = 'ATTRIBUTE ID FROM PREVIOUS SELECT'`.

Known issues:
---------
If you choose to make the out of stock combinations not selectable and you set for default configuration an out of stock simple product, on the initial page load the out of stock combination will still be selected.
Any ideas on how to handle this are welcomed.

Tests:
-------
The extension was tested on Magento CE 1.7.0.2 & 1.8.0.0 with the sample data for 1.6.0.0 and themes: default, modern and blank.

The extension does not rewrite any core classes.  
The extension does not replace any js classes. It just extends 2 of them.  
<a href="http://magento.stackexchange.com/q/7608/146" target="_blank">Thanks to @Fooman for explaining how to manage js "class" extension: </a>

Conflicts:
----------
The extension **might** conflict with other extensions that handle image switching on attribute change.
The extension might not work if the js variable used for configurable products page is not named `spConfig`.
If you changed it's name, change it in this file also: `app/design/frontend/base/default/template/easylife_switcher/catalog/product/view/type/configurable/config.phtml`

Bug report:
-----------
<a href="https://github.com/tzyganu/Switcher/issues">Please submit any bugs or feature requests here</a>.
