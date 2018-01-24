<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Medma\MarketPlace\Helper;

use Magento\Framework\View\Result\Page as ResultPage;

/**
 * Catalog category helper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ViewLayout extends \Magento\Catalog\Helper\Product\View
{
    /**
     * List of catalog product session message groups
     *
     * @var array
     */
    protected $messageGroups;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_catalogProduct = null;

    /**
     * Catalog design
     *
     * @var \Magento\Catalog\Model\Design
     */
    protected $_catalogDesign;

    /**
     * Catalog session
     *
     * @var \Magento\Catalog\Model\Session
     */
    protected $_catalogSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator
     */
    protected $categoryUrlPathGenerator;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Catalog\Model\Design $catalogDesign
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator
     * @param array $messageGroups
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Model\Design $catalogDesign,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator,
        array $messageGroups = []
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context,$catalogSession,$catalogDesign,$catalogProduct,$coreRegistry,$messageManager,$categoryUrlPathGenerator,$messageGroups);
    }

    /**
     * Init layout for viewing product page
     *
     * @param \Magento\Framework\View\Result\Page $resultPage
     * @param \Magento\Catalog\Model\Product $product
     * @param null|\Magento\Framework\DataObject $params
     * @return \Magento\Catalog\Helper\Product\View
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function initProductLayout(ResultPage $resultPage, $product, $params = null)
    {
       
        $settings = $this->_catalogDesign->getDesignSettings($product);
        $pageConfig = $resultPage->getConfig();

        if ($settings->getCustomDesign()) {
            $this->_catalogDesign->applyCustomDesign($settings->getCustomDesign());
        }

        // Apply custom page layout
        if ($settings->getPageLayout()) {
            $pageConfig->setPageLayout($settings->getPageLayout());
        }
        else
        {
            $pageConfig->setPageLayout($this->getStoreConfig('marketplace/general/product_view_layout'));
        }
          
              
    
        $urlSafeSku = rawurlencode($product->getSku());

        // Load default page handles and page configurations
        if ($params && $params->getBeforeHandles()) {
            foreach ($params->getBeforeHandles() as $handle) {
                $resultPage->addPageLayoutHandles(
                    ['id' => $product->getId(), 'sku' => $urlSafeSku, 'type' => $product->getTypeId()],
                    $handle
                );
            }
        }

        $resultPage->addPageLayoutHandles(
            ['id' => $product->getId(), 'sku' => $urlSafeSku, 'type' => $product->getTypeId()]
        );

        if ($params && $params->getAfterHandles()) {
            foreach ($params->getAfterHandles() as $handle) {
                $resultPage->addPageLayoutHandles(
                    ['id' => $product->getId(), 'sku' => $urlSafeSku, 'type' => $product->getTypeId()],
                    $handle
                );
            }
        }

        // Apply custom layout update once layout is loaded
        $update = $resultPage->getLayout()->getUpdate();
        $layoutUpdates = $settings->getLayoutUpdates();
        if ($layoutUpdates) {
            if (is_array($layoutUpdates)) {
                foreach ($layoutUpdates as $layoutUpdate) {
                    $update->addUpdate($layoutUpdate);
                }
            }
        }

        $currentCategory = $this->_coreRegistry->registry('current_category');
        $controllerClass = $this->_request->getFullActionName();
        if ($controllerClass != 'catalog-product-view') {
            $pageConfig->addBodyClass('catalog-product-view');
        }
        $pageConfig->addBodyClass('product-' . $product->getUrlKey());
        if ($currentCategory instanceof \Magento\Catalog\Model\Category) {
            $pageConfig->addBodyClass('categorypath-' . $this->categoryUrlPathGenerator->getUrlPath($currentCategory))
                ->addBodyClass('category-' . $currentCategory->getUrlKey());
        }

        return $this;
    }

    public function getStoreConfig($storePath){
        
        $StoreConfig =  $this->scopeConfig->getValue($storePath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
        return $StoreConfig;
    }

}
