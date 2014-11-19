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
 * @copyright   2013 - 2014 Marius Strajeru-2014
 * @license     http://opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Attribute Options image helper
 *
 * @category    Easylife
 * @package     Easylife_Switcher
 */

class Easylife_Switcher_Helper_Optimage extends Easylife_Switcher_Helper_Data
{
     /**
     * config path to transform dropdowns
     */
    const XML_TRANSFORM_PATH   = 'easylife_switcher/settings/transform_dropdowns';

    /**
     * config path to color attributes
     */
    const XML_USE_OPTION_IMAGES_PATH= 'easylife_switcher/settings/use_option_images';

    /*  IMAGE RESIZE HELPER */

    //image placeholder @var string, located in skin directory
    protected $_placeholder = '/images/easylife_switcher/no-optimage.gif';
    protected $_subdir      = 'optimages';



    protected $_imageProcessor = null; //image processor, @var null|Varien_Image_Adapter_Gd2
    protected $_image = null; //image to process, @var null|string
    protected $_absImage = null; //absolut path to the image to process, @var string


    protected $_openError    = "";

    //default values (used on init)
    protected $_keepFrame       = false;
    protected $_keepAspectRatio = true;
    protected $_keepTransparency= true;
    protected $_constrainOnly   = true;
    protected $_adaptiveResize  = 'center'; // false|center|top|bottom

    protected $_width           = null;
    protected $_height          = null;

    protected $_scheduledResize = null; //must to resize image on _toString, @var null|array
    protected $_resized         = false; //is image already resized

    protected $_adaptiveResizePositions = array(
        'center'=>array(0.5,0.5),
        'top'   =>array(1,0),
        'bottom'=>array(0,1)
    );


    /**
     * resized image folder name
     * @var string
     */
    protected $_resizeFolderName = 'cache';


    public function getImageBaseDir()
    {
        return Mage::getBaseDir('media').DS.$this->_subdir;
    }
    /**
     * get the image url for article
     * @access public
     * @return string
     *
     */
    public function getImageBaseUrl()
    {
        return Mage::getBaseUrl('media').$this->_subdir;
    }

    /**
     * @param $image
     * @return $this
     */
    public function init($image)
    {

        $this->_imageProcessor  = null;
        $this->_image           = ltrim($image, '/\ ');
        $this->_absImage        = null;
        $this->_widht           = null;
        $this->_height          = null;
        $this->_scheduledResize = false;
        $this->_resized         = false;
        $this->_adaptiveResize  = 'center';

        $this->_openError = '';

        $checkImages = array();

        if ($this->_image) {
            $this->_image = DS.$this->_image;
            $checkImages[$this->_image] = $this->getImageBaseDir().$this->_image;
        };

        $_placeholder = DS.ltrim($this->_placeholder, ' \/');
        $checkImages[$_placeholder] = array_unique(
            array(
                Mage::getDesign()->getSkinBaseDir().$_placeholder,
                Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default')).$_placeholder,
                Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default', '_package' => 'base')).$_placeholder
            )
        );

        $_mediaPlaceholder = DS.basename($_placeholder);
        $checkImages[$_mediaPlaceholder] = $this->getImageBaseDir().$_mediaPlaceholder;


        foreach ($checkImages as $_image=>$_absImages) {
            if (!is_array($_absImages)) {
                $_absImages = array($_absImages);
            }
            foreach ($_absImages as $_absImage) {
                if (file_exists($_absImage)) {
                    $this->_image       = $_image;
                    $this->_absImage    = $_absImage;
                    break 2; //break both foreach
                }
            }
        }

        if ($this->_absImage) {
            try{
                $this->_getImageProcessor()->open($this->_absImage);
            } catch (Exception $e){
                $this->_openError .= $e->getMessage();
                $this->_image = null;
                $this->_absImage = '';
            }
        }
        return $this;
    }


    /**
     * get the image processor
     * @access protected
     * @return Varien_Image_Adapter_Gd2
     *
     */
    protected function _getImageProcessor()
    {
        if (is_null($this->_imageProcessor)) {
            $this->_imageProcessor = Varien_Image_Adapter::factory('GD2');
            $this->_imageProcessor->keepFrame($this->_keepFrame);
            $this->_imageProcessor->keepAspectRatio($this->_keepAspectRatio);
            $this->_imageProcessor->keepTransparency($this->_keepTransparency);
            $this->_imageProcessor->constrainOnly($this->_constrainOnly);
        }
        return $this->_imageProcessor;
    }


    /**
     * Get/set keepAspectRatio
     *
     * @param bool $value
     * @return bool|Easylife_Switcher_Helper_Optimage
     */
    public function keepAspectRatio($value = null)
    {
        if (null !== $value) {
            $this->_getImageProcessor()->keepAspectRatio($value);
            return $this;
        } else {
            return $this->_getImageProcessor()->keepAspectRatio();
        }
    }

    /**
     * Get/set keepFrame
     *
     * @param bool $value
     * @return bool|Easylife_Switcher_Helper_Optimage
     */
    public function keepFrame($value = null)
    {
        if (null !== $value) {
            $this->_getImageProcessor()->keepFrame($value);
            return $this;
        } else {
            return $this->_getImageProcessor()->keepFrame();
        }
    }


    /**
     * Get/set keepTransparency
     *
     * @param bool $value
     * @return bool|Easylife_Switcher_Helper_Optimage
     */
    public function keepTransparency($value = null)
    {
        if (null !== $value) {
            $this->_getImageProcessor()->keepTransparency($value);
            return $this;
        } else {
            return $this->_getImageProcessor()->keepTransparency();
        }
    }


    /**
     * Get/set adaptiveResize
     *
     * @param bool|string $value
     * @return bool|Easylife_Switcher_Helper_Optimage
     */
    public function adaptiveResize($value = null)
    {
        if (null !== $value) {
            $this->_adaptiveResize = $value;
            if ($value) {
                $this->keepFrame(false);
            }
            return $this;
        } else {
            return $this->_adaptiveResize;
        }
    }

    /**
     * Get/set constrainOnly
     *
     * @param bool $value
     * @return bool|Easylife_Switcher_Helper_Optimage
     */
    public function constrainOnly($value = null)
    {
        if (null !== $value) {
            $this->_getImageProcessor()->constrainOnly($value);
            return $this;
        } else {
            return $this->_getImageProcessor()->constrainOnly();
        }
    }

    /**
     * Get/set quality, values in percentage from 0 to 100
     *
     * @param int $value
     * @return int|Easylife_Switcher_Helper_Optimage
     */
    public function quality($value = null)
    {
        if (null !== $value) {
            $this->_getImageProcessor()->quality($value);
            return $this;
        } else {
            return $this->_getImageProcessor()->quality();
        }
    }

    /**
     * Get/set keepBackgroundColor
     *
     * @param array $value
     * @return array|Easylife_Switcher_Helper_Optimage
     */
    public function backgroundColor($value = null)
    {
        if (null !== $value) {
            $this->_getImageProcessor()-> backgroundColor($value);
            return $this;
        } else {
            return $this->_getImageProcessor()-> backgroundColor();
        }
    }

    /**
     * resize image
     * @access public
     * @param int $width - defaults to null
     * @param int $height - defaults to null
     * @return $this
     *
     */
    public function resize($width = null, $height = null)
    {
        $this->_scheduledResize = true;
        $this->_width  = $width;
        $this->_height = $height;
        return $this;
    }


    protected function getDestinationImagePrefix()
    {
        if (!$this->_image) {
            return $this;
        }

        $imageRealPath = "";

        if ($this->_scheduledResize) {
            $width  = $this->_width;
            $height = $this->_height;

            $adaptive           = $this->adaptiveResize();
            $keepFrame          = $this->keepFrame();
            $keepAspectRatio    = $this->keepAspectRatio();
            $constrainOnly      = $this->constrainOnly();

            $imageRealPath = $width.'x'.$height;

            $options = "";

            if (!$keepAspectRatio) {
                $imageRealPath .= '-exact';
            } else {
                if (!$keepFrame && $width && $height && ($adaptive !== false)) {
                    $adaptive = strtolower(trim($adaptive));
                    if (isset($this->_adaptiveResizePositions[$adaptive])) {
                        $imageRealPath .= '-'.$adaptive;
                    }
                }
            }
            if ($keepFrame) {
                $imageRealPath .= '-frame';
                $_backgroundColor = $this->backgroundColor();
                if ($_backgroundColor) {
                    $imageRealPath .= '-'.implode('-', $_backgroundColor);
                }
            }
            if (!$constrainOnly) {
                $imageRealPath .= '-zoom';
            }
        }
        return $imageRealPath;
    }

    /**
     * @return $this|string
     */
    protected function getDestinationPath()
    {
        if (!$this->_image) {
            return $this;
        }
        if ($this->_scheduledResize) {
            return $this->getImageBaseDir().DS.
                $this->_resizeFolderName.DS.
                $this->getDestinationImagePrefix().
                $this->_image;
        } else {
            return $this->getImageBaseDir().$this->_image;
        }
    }

    protected function getImageUrl()
    {
        if (!$this->_image) {
            return $this;
        }

        if ($this->_scheduledResize) {
            $imageUrl = $this->getImageBaseUrl().'/'.
                $this->_resizeFolderName.'/'.
                $this->getDestinationImagePrefix().
                $this->_image;
        } else {
           $imageUrl = $this->getImageBaseUrl().$this->_image;
        }
        return str_replace(DS, '/', $imageUrl);
    }

    protected function _doResize()
    {

        if (!$this->_image || !$this->_scheduledResize || $this->_resized) {
            return $this;
        }
        $this->_resized = true; //mark as resized
        $width = $this->_width;
        $height = $this->_height;

        $adaptive = $width &&
            $height &&
            $this->keepAspectRatio() &&
            !$this->keepFrame() &&
            ($this->adaptiveResize() !== false);
        $adaptivePosition = false;
        if ($adaptive) {
            $adaptive = strtolower(trim($this->adaptiveResize()));
            if (isset($this->_adaptiveResizePositions[$adaptive])) {
                $adaptivePosition = $this->_adaptiveResizePositions[$adaptive];
            }
        }

        $processor = $this->_getImageProcessor();

        if (!$adaptivePosition) {
            $processor->resize($width, $height);
            return $this;
        }

        //make adaptive resize
        $currentRatio = $processor->getOriginalWidth() / $processor->getOriginalHeight();
        $targetRatio  = $width / $height;

        if ($targetRatio > $currentRatio) {
            $processor->resize($width, null);
        } else {
            $processor->resize(null, $height);
        }

        $diffWidth  = $processor->getOriginalWidth() - $width;
        $diffHeight = $processor->getOriginalHeight() - $height;

        if ($diffWidth || $diffHeight) {
            $processor->crop(
                floor($diffHeight * $adaptivePosition[0]), //top rate
                floor($diffWidth / 2),
                ceil($diffWidth / 2),
                ceil($diffHeight *  $adaptivePosition[1]) //bottom rate
            );
        }

        return $this;

    }

    public function cleanCache()
    {
        $directory =  $this->getImageBaseDir().DS.$this->_resizeFolderName;
        if (is_dir($directory)) {
            $io = new Varien_Io_File();
            $io->rmdir($directory, true);
        }
    }

    /**
     * to string - no need for cache expire because the image names will be different
     * @access public
     * @return string
     *
     */
    public function __toString()
    {
        try {
            if (!$this->_image) {
                throw new Exception($this->_openError);
            }

            $imageRealPath = $this->getDestinationPath();
            //don't need this, on upload, cache is cleared
//             if(file_exists($imageRealPath) && filemtime($imageRealPath) < filemtime($this->_absImage)) {
//                 @unlink($imageRealPath);
//             }
            if (!file_exists($imageRealPath)) {
                $this->_doResize();
                $this->_getImageProcessor()->save($imageRealPath);
            }
            return $this->getImageUrl();
        }
        catch (Exception $e){
            Mage::logException($e);
            return 'data:image/png;base64,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';
        }

    }
}
