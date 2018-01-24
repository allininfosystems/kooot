<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Block\Checkout;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session;
use Magmodules\TradeTracker\Helper\General as GeneralHelper;
use Magento\Catalog\Model\CategoryRepository;

/**
 * Class Pixel
 *
 * @package Magmodules\TradeTracker\Block\Checkout
 */
class Pixel extends Template
{

    /**
     * @var Session
     */
    private $checkoutSession;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var GeneralHelper
     */
    private $generalHelper;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * Pixel constructor.
     *
     * @param Context            $context
     * @param Session            $checkoutSession
     * @param GeneralHelper      $generalHelper
     * @param CategoryRepository $categoryRepository
     * @param array              $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        GeneralHelper $generalHelper,
        CategoryRepository $categoryRepository,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->generalHelper = $generalHelper;
        $this->storeManager = $context->getStoreManager();
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getPixelData()
    {
        $pixelData = [];

        $order = $this->checkoutSession->getLastRealOrder();
        $subtotal = ($order->getGrandTotal() - $order->getTaxAmount() - $order->getShippingAmount());
        $defaultId = $this->generalHelper->getStoreValue('magmodules_tradetracker/pixel/product_id');
        $campaignId = $this->generalHelper->getStoreValue('magmodules_tradetracker/pixel/campaign_id');

        $pixelData['campaign_id'] = $campaignId;
        $pixelData['transaction_id'] = $order->getIncrementId();
        $pixelData['transactions'][$defaultId]['amount'] = number_format($subtotal, 2, '.', '');
        $pixelData['email'] = $order->getCustomerEmail();
        $pixelData['currency'] = $order->getOrderCurrencyCode();

        foreach ($order->getAllVisibleItems() as $item) {
            $categoryIds = $item->getProduct()->getCategoryIds();
            foreach ($categoryIds as $categoryId) {
                $category = $this->categoryRepository->get($categoryId, $this->storeManager->getStore()->getId());
                $ttProductId = $category->getData('tt_product_id');
                if (!empty($ttProductId) && ($ttProductId != $defaultId)) {
                    $pixelData['transactions'][$defaultId]['amount'] -= $item['base_row_total'];
                    if (!empty($pixelData['transactions'][$ttProductId]['amount'])) {
                        $pixelData['transactions'][$ttProductId]['amount'] += $item['base_row_total'];
                    } else {
                        $pixelData['transactions'][$ttProductId]['amount'] = $item['base_row_total'];
                    }
                    break;
                }
            }
        }

        return $pixelData;
    }

}
