<?php
/**
 *
 * Copyright © 2016 Medma. All rights reserved.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 */
 
namespace Medma\MarketPlace\Block\Adminhtml\Order\Form;
 
class Totals extends \Magento\Backend\Block\Widget\Grid\Extended
{
 
    protected $_template = 'marketplace/sales/order/view/form/totals.phtml';
    protected $pricehelper;
    protected $coreRegistry;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Medma\MarketPlace\Model\ProfileFactory $profileFactory
     * @param \Magento\User\Model\UserFactory $UserFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Medma\MarketPlace\Helper\Data $vendorHelper
     * @param \Magento\Authorization\Model\RoleFactory $rolesFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Medma\MarketPlace\Model\ProfileFactory $profileFactory,
        \Magento\User\Model\UserFactory $UserFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Medma\MarketPlace\Helper\Data $vendorHelper,
        \Magento\Authorization\Model\RoleFactory $rolesFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Api\CustomerMetadataInterface $metadata,
        \Magento\Customer\Model\Metadata\ElementFactory $elementFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Framework\Pricing\Helper\Data $pricehelper,
        array $data = []
    ) {
        $this->groupRepository = $groupRepository;
        $this->addressRenderer = $addressRenderer;
        $this->pricehelper = $pricehelper;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }
    public function formatPrice($price)
    {
        return $this->pricehelper->currency($price, true, false);
    }
    public function coreRegistry()
    {
        return $this->coreRegistry;
    }
}
