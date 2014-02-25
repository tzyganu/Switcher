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
 * @category    Easylife
 * @package     Easylife_Switcher
 * @copyright   Copyright (c) 2014
 * @license	http://opensource.org/licenses/mit-license.php MIT License
 */


/**
 * Attribute Options Image helper
 *
 * @category    Easylife
 * @package     Easylife_Switcher
 * @author      Emil [carco] Sirbu <emil.sirbu@gmail.com>
 *
 */
class Easylife_Switcher_Model_Optimage {

    protected $_allowedExt = array('png','gif','jpg','jpeg');

    /**
     * Get default (admin) attribute option collection with image/hexa code attached
     * @access public
     * @param $attrId numeric|array Attribute or options id(s)
     * @param $byOptions boolean If true, filter by options
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection
     * @author Emil [carco] Sirbu <emil.sirbu@gmail.com>
     */
    public function getOptionCollection($attrID,$byOptions = false) {

        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection');

        if($byOptions) {
            //$optionCollection->setIdFilter($attrID) => this sux, "Column 'option_id' in where clause is ambiguous"
            $optionCollection->addFieldToFilter('tsv.option_id',array('in'=>(array)$attrID));
        } else {
            $optionCollection->setAttributeFilter($attrID)->setOrder('main_table.sort_order','ASC');
        }

        //add this last because setStoreFilter also add order by value
        $optionCollection->setStoreFilter(Mage_Core_Model_App::ADMIN_STORE_ID,false);
 

        if($optionCollection->getSize()>0) {

            $imagePath = Mage::helper('easylife_switcher/optimage')->getImageBaseDir();
            foreach($optionCollection as $option) {

                //check for hexacode
                $values = rtrim(trim($option->getValue()),'#');
                if(preg_match('/#[a-f0-9]{6}/i', $values,$color)) {
                    $option->setHexaCode($color[0]);
                }
                foreach($this->_allowedExt as $ext) {
                    $file = $imagePath.DS.$option->getId().".{$ext}";
                    if(file_exists($file)) {
                        $option->setOptimage(basename($file));
                        break;
                    }
                }
            }
        }
        return $optionCollection;
    }



    /**
     * upload and save the option images (after attribute save)
     * @access public
     * @param $observer
     * @return self
     * @author Emil [carco] Sirbu <emil.sirbu@gmail.com>
     */
    public function uploadAndSaveImages($observer){

        $action = $observer->getEvent()->getControllerAction();
        if(!$action instanceof Mage_Adminhtml_Catalog_Product_AttributeController) {
            return $this;
        }

        

        $request =  $action->getRequest();
        $attribute_id = $request->getParam('attribute_id');
        if(!$attribute_id) {
            return $this;
        }

        $session = Mage::getSingleton('adminhtml/session');
        if($session->getAttributeData()) {
            //there was error when saving attribute, do nothing
            $session->addError(Mage::helper('easylife_switcher/optimage')->__('No attribute option image(s) was saved'));
            return $this;
        }

        $options = $request->getParam('option');
        if(empty($options['value'])) {
            //do nothing
            return $this;
        }

        //images marked to delete
        $delete = $action->getRequest()->getPost('delete');


        $success = 0;
        $deleted = 0;

        $destinationFolder = Mage::helper('easylife_switcher/optimage')->getImageBaseDir();

        foreach($options['value'] as $option_id=>$values) {

            $option_id = (int)$option_id;
            if($option_id<=0) continue;



            //remove actual images at user request (or new file is uploaded) or if option was removed ([delete][<option_id>] = 1)
            if(!empty($delete[$option_id]) || !empty($options['delete'][$option_id]) || !empty($_FILES['image_'.$option_id]['name'])) {
                foreach($this->_allowedExt as $ext) {
                    $file = $destinationFolder.DS.$option_id.'.'.$ext;
                    if (file_exists($file)) {
                        $deleted++;
                        @unlink($file);
                    }
                }
            }

            if(!empty($_FILES['image_'.$option_id]['name']) && empty($options['delete'][$option_id])) {
                try {
                        $uploader = new Varien_File_Uploader('image_'.$option_id);
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);
                        $uploader->setAllowCreateFolders(true);
                        $uploader->setAllowedExtensions($this->_allowedExt);
                        $result = $uploader->save($destinationFolder,$option_id.'.'.strtolower($uploader->getFileExtension()));
                        $success++;
                } catch(Exception $e) {
                    $session->addError($label.' [image_'.$option_id.']: '.$e->getMessage());
                }
            } 
        }

        if($deleted) {
            $session->addSuccess( Mage::helper('easylife_switcher/optimage')->__('%s attribute option image(s) deleted',$deleted));
        }

        if($success) {
            $session->addSuccess( Mage::helper('easylife_switcher/optimage')->__('%s attribute option image(s) uploaded',$success));
        }
        if($success || $deleted) {
            //clean cache
           Mage::helper('easylife_switcher/optimage')->cleanCache();
        }
    }


    /**
     * Clean generated thumbs for attribute option images
     * @access public
     * @param $observer
     * @return null
     * @author Emil [carco] Sirbu <emil.sirbu@gmail.com>
     */

    public function cleanOptImages($observer) {
        Mage::helper('easylife_switcher/optimage')->cleanCache();
    }

}