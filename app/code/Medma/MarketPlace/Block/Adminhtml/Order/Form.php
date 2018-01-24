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
 
namespace Medma\MarketPlace\Block\Adminhtml\Order;
 
class Form extends \Magento\Backend\Block\Widget\Grid\Extended
{
 
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;
 
    /**
     * @var \Medma\MarketPlace\Model\ProfileFactory $profileFactory
     */
    protected $profileFactory;
    
    /**
     * @var \Magento\User\Model\UserFactory $UserFactory
     */
    
    protected $UserFactory;
 
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    
    /**
     * @var \Medma\MarketPlace\Helper\Data $vendorHelper
     */
    protected $vendorHelper;
    protected $adminSession;
    
    /**
     * @var \Magento\Authorization\Model\RoleFactory $rolesFactory
     */
    protected $rolesFactory;
    protected $productFactory;
    protected $orderFactory;
    protected $_adminHelper;
    protected $_template = 'marketplace/sales/order/view/form.phtml';
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
        \Medma\MarketPlace\Model\ProfileFactory $profileFactory,
        \Magento\User\Model\UserFactory $UserFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Medma\MarketPlace\Helper\Data $vendorHelper,
        \Magento\Authorization\Model\RoleFactory $rolesFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Helper\Admin $_adminHelper,
        array $data = []
    ) {
    
        $this->profileFactory = $profileFactory;
        $this->UserFactory = $UserFactory;
        $this->rolesFactory = $rolesFactory;
        $this->productFactory = $productFactory;
        $this->orderFactory = $orderFactory;
        $this->vendorHelper = $vendorHelper;
        $this->_adminHelper = $_adminHelper;
        $this->adminSession = $adminSession;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('current_order') && $this->_coreRegistry->registry('current_order')->getId()) {
            return __("Order # | ".$this->getOrder()->getIncrementId());
        }
    }
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }
    public function getPaymentHtml()
    {
        return $this->getChildHtml('order_payment');
    }
    public function getGiftmessageHtml()
    {
        return $this->getChildHtml('order_giftmessage');
    }
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index');
    }

    public function getShipUrl()
    {
        return $this->getUrl('*/*/ship') . 'order_id/' . $this->getOrder()->getId();
    }

    public function getInvoiceUrl()
    {
        return $this->getUrl('*/*/invoice') . 'order_id/' . $this->getOrder()->getId();
    }
    public function isShipButtonDisplay()
    {
        $roleId = $this->vendorHelper->getConfig('general', 'vendor_role');
        
        // $role = Mage::getModel('admin/roles')->load($roleId);

        $current_user = $this->adminSession->getUser();

        if ($current_user->getRole()->getRoleId() == $roleId) {
            $productIds = $this->getProductIdsCollection();

            foreach ($this->getOrder()->getAllItems() as $item) {
                if (in_array($item->getProductId(), $productIds) && $item->canShip()) {
                    return true;
                }
            }
        }
        return false;
    }
    public function getProductIdsCollection()
    {
        $roleId = $this->vendorHelper->getConfig('general', 'vendor_role');
        
        // $role = Mage::getModel('admin/roles')->load($roleId);

        $current_user = $this->adminSession->getUser();

        $collection = $this->productFactory->create()->getCollection()
                ->addFieldToFilter('status', 1);

        if ($current_user->getRole()->getRoleId() == $roleId) {
            $collection->addFieldToFilter('vendor', $current_user->getId());
        }

        return $collection->getAllIds();
    }
    public function isInvoiceButtonDisplay()
    {
        $roleId = $this->vendorHelper->getConfig('general', 'vendor_role');
        
        // $role = Mage::getModel('admin/roles')->load($roleId);

        $current_user = $this->adminSession->getUser();

        if ($current_user->getRole()->getRoleId() == $roleId) {
            $productIds = $this->getProductIdsCollection();

            foreach ($this->getOrder()->getAllItems() as $item) {
                if (in_array($item->getProductId(), $productIds) && $item->canInvoice()) {
                    return true;
                }
            }
        }
        return false;
    }
    public function displayShippingPriceInclTax($order)
    {
        $shipping = $order->getShippingInclTax();
        if ($shipping) {
            $baseShipping = $order->getBaseShippingInclTax();
        } else {
            $shipping = $order->getShippingAmount() + $order->getShippingTaxAmount();
            $baseShipping = $order->getBaseShippingAmount() + $order->getBaseShippingTaxAmount();
        }
        return $this->displayPrices($baseShipping, $shipping, false, ' ');
    }
    public function displayPrices($basePrice, $price, $strong = false, $separator = '<br/>')
    {
        return $this->_adminHelper->displayPrices(
            $this->getPriceDataObject(),
            $basePrice,
            $price,
            $strong,
            $separator
        );
    }
    public function displayPriceAttribute($code, $strong = false, $separator = '<br/>')
    {
        return $this->_adminHelper->displayPriceAttribute($this->getPriceDataObject(), $code, $strong, $separator);
    }
    public function getPriceDataObject()
    {
        $obj = $this->getData('price_data_object');
        if ($obj === null) {
            return $this->getOrder();
        }
        return $obj;
    }
}
