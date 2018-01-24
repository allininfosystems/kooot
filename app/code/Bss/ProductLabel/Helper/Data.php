<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ProductLabel
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductLabel\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem ;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $imageFactory;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory
    ) {
        $this->storeManager=$storeManager;
        $this->date=$date;
        $this->filesystem = $filesystem;
        $this->imageFactory = $imageFactory;
        parent::__construct($context);
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return bool
     */
    public function isEnable()
    {
        return $this->scopeConfig->isSetFlag(
            'productlabel/general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isEnableOutOfStockLabel()
    {
        return $this->scopeConfig->isSetFlag(
            'productlabel/out_of_stock/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isEnableNewProductLabel()
    {
        return $this->scopeConfig->isSetFlag(
            'productlabel/new/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isEnableSaleProductLabel()
    {
        return $this->scopeConfig->isSetFlag(
            'productlabel/sale/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool|string
     */
    public function getOutOfStockImageName()
    {
        $img= $this->scopeConfig->getValue(
            'productlabel/out_of_stock/image',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $imgName=substr($img, 8, strlen($img)-8);
        return $imgName;
    }

    /**
     * @return bool|string
     */
    public function getNewProductImageName()
    {
        $img= $this->scopeConfig->getValue('productlabel/new/image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $imgName=substr($img, 8, strlen($img)-8);
        return $imgName;
    }

    /**
     * @return bool|string
     */
    public function getSaleProductImageName()
    {
        $img= $this->scopeConfig->getValue('productlabel/sale/image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $imgName=substr($img, 8, strlen($img)-8);
        return $imgName;
    }

    /**
     * @return string
     */
    public function getOutOfStockPosition()
    {
        $position= $this->scopeConfig->getValue(
            'productlabel/out_of_stock/label_position',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $position;
    }

    /**
     * @return string
     */
    public function getNewProductPosition()
    {
        $position= $this->scopeConfig->getValue(
            'productlabel/new/label_position',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $position;
    }

    /**
     * @return string
     */
    public function getSaleProductPosition()
    {
        $position= $this->scopeConfig->getValue(
            'productlabel/sale/label_position',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $position;
    }

    /**
     * @return string
     */
    public function getNumberInput()
    {
        $day= $this->scopeConfig->getValue(
            'productlabel/new/number_input',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $day;
    }

    /**
     * @return string
     */
    public function getStoreDate()
    {
        return $this->date->gmtDate();
    }

    /**
     * @param $image
     * @param null $height
     * @return string
     */
    public function resize($image, $height = null)
    {
        $width=100;
        $absolutePath = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                ->getAbsolutePath('image/default/').$image;
        $imageResized = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                ->getAbsolutePath('resized/'.$width.'/').$image;

        /**
         * create image factory...
         */
        $imageResize = $this->imageFactory->create();
        $imageResize->open($absolutePath);
        $imageResize->constrainOnly(TRUE);
        $imageResize->keepTransparency(TRUE);
        $imageResize->keepFrame(FALSE);
        $imageResize->keepAspectRatio(TRUE);
        $imageResize->resize($width, $height);

        /**
         * destination folder
         */
        $destination = $imageResized ;

        /**
         * save image
         */
        $imageResize->save($destination);

        $resizedURL = $this->storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'resized/'.$width.'/'.$image;

        return $resizedURL;
    }

    /**
     * @return string
     */
    public function getLabelPriority()
    {
        $label=$this->scopeConfig->getValue(
            'productlabel/general/select_label',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $label;
    }

    /**
     * @return string
     */
    public function isDisplayOn()
    {
        $display=$this->scopeConfig->getValue(
            'productlabel/general/display_label_on',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $display;
    }

    /**
     * @return bool
     */
    public function isEnableSpecialPrice()
    {
        return $this->scopeConfig->isSetFlag(
            'productlabel/sale/enable_special_price',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}