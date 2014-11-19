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
class Easylife_Switcher_Adminhtml_SwitcherController extends Mage_Adminhtml_Controller_Action
{
    /**
     * configure attribute values
     */
    public function configureAction()
    {
        $type = $this->getRequest()->getParam('type');
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $values = urldecode($this->getRequest()->getParam('val'));
        $values = Mage::helper('core')->jsonDecode($values);
        switch($type) {
            case 'color':
                $return = $this->_configureColor($attributeId, $values);
                break;
            case 'image':
                $return = $this->_configureImage($attributeId, $values);
                break;
            default:
                $result = array();
                $resultObject = new Varien_Object(
                    array(
                        'type' => $type,
                        'result' => $result,
                        'attribute_id' => $attributeId
                    )
                );
                Mage::dispatchEvent('easylife_switcher_configure_transform', array('result' => $resultObject));
                $return = $resultObject->getResult();
                break;
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($return));
    }

    /**
     * @param $attributeId
     * @param $values
     * @return array
     */
    protected function _configureColor($attributeId, $values)
    {
        return $this->_configureElements(
            $attributeId,
            'color',
            Mage::helper('easylife_switcher')->__('Colors'),
            'text',
            $values
        );
    }

    /**
     * @param $attributeId
     * @param $values
     * @return array
     */
    protected function _configureImage($attributeId, $values)
    {
        return $this->_configureElements(
            $attributeId,
            'image',
            Mage::helper('easylife_switcher')->__('Images'),
            'image',
            $values
        );
    }

    /**
     * @param $attributeId
     * @param $suffix
     * @param $label
     * @param $fieldType
     * @param $values
     * @return array
     */
    protected function _configureElements($attributeId, $suffix, $label, $fieldType, $values)
    {
        /** @var Mage_Eav_Model_Config $eavConfig */
        $eavConfig = Mage::getModel('eav/config');
        $attribute = $eavConfig->getAttribute('catalog_product', $attributeId);
        if (!$attribute || !$attribute->getId()) {
            $result = array(
                'success' => false,
                'html' => Mage::helper('easylife_switcher')->__(
                    'The attribute with id %s does not exist.',
                    $attributeId
                )
            );
            return $result;
        }
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset(
            'attribute_'.$attribute->getId().'_'.$suffix,
            array(
                'legend' => $attribute->getFrontendLabel().': '.$label
            )
        );
        $fieldset->addType('image', Mage::getConfig()->getBlockClassName('easylife_switcher/adminhtml_helper_image'));
        $options = $attribute->getSource()->getAllOptions(false);
        $this->_addOptions($fieldset, $attribute, $options, $fieldType);
        $form->setHtmlIdPrefix($suffix.'_'.$attributeId.'_');
        $form->setValues($values);
        $result = array(
            'success' => true,
            'html' => $form->toHtml()
        );
        return $result;
    }

    /**
     * @param $fieldset
     * @param $attribute
     * @param $options
     * @param string $fieldType
     * @return $this
     */
    protected function _addOptions(
        Varien_Data_Form_Element_Fieldset $fieldset,
        Mage_Catalog_Model_Resource_Eav_Attribute $attribute,
        array $options,
        $fieldType = 'text'
    ) {
        foreach ($options as $option) {
            if (!is_array($option['value'])) {
                $fieldset->addField(
                    $option['value'],
                    $fieldType,
                    array(
                        'name' => $option['value'],
                        'label' => $option['label'],
                    )
                );
            } else {
                $this->_addOptions($fieldset, $attribute, $option['value'], $fieldType);
            }
        }
        return $this;
    }

    public function saveImageAction()
    {
        $result = array();
        $jsVarName = $this->getRequest()->getPost('js_var_name');
        $post = $this->getRequest()->getPost();
        $errors = array();
        $destinationFolder = Mage::helper('easylife_switcher/optimage')->getImageBaseDir();
        foreach ($_FILES as $key => $data) {
            if (!empty($_FILES[$key]['tmp_name'])) {
                try {
                    $uploader = new Varien_File_Uploader($key);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $uploader->setAllowCreateFolders(true);
                    $upload = $uploader->save($destinationFolder, $_FILES[$key]['name']);
                    $result[$key] = $upload['file'];
                    //remove old file
                    $value = (isset($post[$key]['value'])) ? $post[$key]['value'] : '';
                    if ($value) {
                        $io = new Varien_Io_File();
                        $io->rm($destinationFolder.$value);
                    }
                } catch (Mage_Core_Exception $e) {
                    $errors[$e->getMessage()] = 1;
                } catch(Exception $e) {
                    $errors[Mage::helper('easylife_switcher')->__('There was a problem uploading images')] = 1;
                }
            } else {
                if (isset($post[$key]['delete'])) {
                    //remove file
                    $value = (isset($post[$key]['value'])) ? $post[$key]['value'] : '';
                    if ($value) {
                        $io = new Varien_Io_File();
                        $io->rm($destinationFolder.$value);
                    }
                    $result[$key] = '';
                } else {
                    $result[$key] = (isset($post[$key]['value'])) ? $post[$key]['value'] : '';
                }
            }
        }
        /** @var Mage_Core_Helper_Data $coreHelper */
        $coreHelper = Mage::helper('core');
        if (count($errors) > 0) {
            $messageString = implode("\n", array_keys($errors));
            $this->getResponse()->setBody(
                '<script type="text/javascript">alert(\'' .
                $coreHelper->escapeHtml($messageString).'\');parent.' .
                $jsVarName . '.hideLoadingMask();</script>'
            );
        } else {
            $this->getResponse()->setBody(
                '<script type="text/javascript">parent.' .
                $jsVarName .
                '.updateHidden(\'' .$coreHelper->jsonEncode($result).'\');</script>'
            );
        }
    }
}
