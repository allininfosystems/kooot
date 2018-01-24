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
 
class Items extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $metadata;

 /**
  * @var Address\Renderer
  */
    protected $addressRenderer;
    /**
     * Group service
     *
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * Metadata element factory
     *
     * @var \Magento\Customer\Model\Metadata\ElementFactory
     */
    protected $_metadataElementFactory;
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
    protected $pricehelper;
    
    /**
     * @var \Magento\Authorization\Model\RoleFactory $rolesFactory
     */
    protected $rolesFactory;
    protected $productFactory;
    protected $orderFactory;
    protected $_template = 'marketplace/sales/order/view/form/items.phtml';
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
        \Medma\MarketPlace\Model\AdminCommissionFactory $adminCommissionFactory,
        array $data = []
    ) {
        $this->groupRepository = $groupRepository;
        $this->pricehelper = $pricehelper;
        $this->addressRenderer = $addressRenderer;
        $this->metadata = $metadata;
        $this->_metadataElementFactory = $elementFactory;
        $this->profileFactory = $profileFactory;
        $this->UserFactory = $UserFactory;
        $this->rolesFactory = $rolesFactory;
        $this->productFactory = $productFactory;
        $this->orderFactory = $orderFactory;
        $this->vendorHelper = $vendorHelper;
        $this->adminSession = $adminSession;
        $this->_coreRegistry = $coreRegistry;
        $this->adminCommissionFactory = $adminCommissionFactory;
        parent::__construct($context, $backendHelper, $data);
    }
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }
    public function getItemsCollection()
    {
        return $this->getOrder()->getItemsCollection();
    }
    public function getProductModel()
    {
        return $this->productFactory->create();
    }
    public function getProductIdsCollection()
    {
        $roleId = $this->vendorHelper->getConfig('general', 'vendor_role');
        
        

        $current_user = $this->adminSession->getUser();

        $collection = $this->productFactory->create()->getCollection()
                ->addFieldToFilter('status', 1);

        if ($current_user->getRole()->getRoleId() == $roleId) {
            $collection->addFieldToFilter('vendor', $current_user->getId());
        }

        return $collection->getAllIds();
    }
    public function getAdminCommission($item)
    {
        $product_id = $item->getProductId();
        $product = $this->productFactory->create()->load($product_id);
        if (!is_null($product)) {
            $vendor_id = $product->getVendor();
            $profile = $this->profileFactory->create()->getCollection()->addFieldToFilter('user_id', $vendor_id)->getFirstItem();
            if (!is_null($profile)) {
                $commission_type = $profile->getAdminCommissionType();
                $commission_flat = $profile->getAdminCommissionFlat();
                $commission_percentage = $profile->getAdminCommissionPercentage();
                switch ($commission_type) {
                    case '1':
                        $commission_amount = $commission_flat;
                        break;
                    case '2':
                        $commission_amount = (($item->getPriceInclTax() * $item->getQtyOrdered()) * $commission_percentage) / 100;
                        break;
                    
                    default:
                        return;
                }

                
                
                return $commission_amount;
            }
        }
        return 0;
    }

    public function getVendorAmount($item)
    {
        $product_id = $item->getProductId();
        $product = $this->productFactory->create()->load($product_id);
        if (!is_null($product)) {
            $vendor_id = $product->getVendor();
            $profile = $this->profileFactory->create()->getCollection()->addFieldToFilter('user_id', $vendor_id)->getFirstItem();
            if (!is_null($profile)) {
                $commission_type = $profile->getAdminCommissionType();
                $commission_flat = $profile->getAdminCommissionFlat();
                $commission_percentage = $profile->getAdminCommissionPercentage();
                switch ($commission_type) {
                    case '1':
                        $commission_amount = $commission_flat;
                        break;
                    case '2':
                        $commission_amount = (($item->getPriceInclTax() * $item->getQtyOrdered()) * $commission_percentage) / 100;
                        break;
                    
                    default:
                        return;
                }
                $total_price = ($item->getPriceInclTax() * $item->getQtyOrdered());
                $vendor_amount = $total_price - $commission_amount;
                return $vendor_amount;
            }
        }
        return 0;
    }
    public function formatPrice($price)
    {
        return $this->pricehelper->currency($price, true, false);
    }
    public function coreRegistry()
    {
        return $this->_coreRegistry;
    }

    public function getAdminCommissionForOrder($item)
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $productId = $item->getProductId();
        $product = $this->productFactory->create()->load($productId);
        if(!is_null($product))
        {
            $vendorId = $product->getVendor();
            if(isset($vendorId) && $vendorId !='')
            {   
                $commission_amount = 0;
                $commissionInfo = $this->adminCommissionFactory->create()->getCollection()->addFieldToFilter('order_id',$orderId)->addFieldToFilter('vendor_id',$vendorId)->getFirstItem();
                if(!is_null($commissionInfo))
                {
                    $commissionType = $commissionInfo->getCommissionType();
                    $commissionFlat = $commissionInfo->getCommissionFlat();
                    switch ($commissionType) {
                        case 'Currency Amount':
                            $commission_amount = $commissionFlat;
                            break;
                        case 'Percentage':
                            $commission_amount = (($item->getPriceInclTax() * $item->getQtyOrdered()) * $commissionFlat) / 100;
                            break;
                        default:
                            return;
                    }

                   return $commission_amount;
                }
            }
            return 0;
        }
        return 0;
    }
    public function getVendorAmountForOrder($item)
    {   
        $orderId = $this->getRequest()->getParam('order_id');
         $productId = $item->getProductId();
         $product = $this->productFactory->create()->load($productId);

        if(!is_null($product))
        {
            $vendorId = $product->getVendor();

            if(isset($vendorId) && $vendorId !='')
            {   
                $commissionInfo = $this->adminCommissionFactory->create()->getCollection()->addFieldToFilter('order_id',$orderId)->addFieldToFilter('vendor_id',$vendorId)->getFirstItem();
                if(!is_null($commissionInfo))
                {
                    $commissionType = $commissionInfo->getCommissionType();
                    $commissionFlat = $commissionInfo->getCommissionFlat();
                    switch ($commissionType) {
                        case 'Currency Amount':
                            $commission_amount = $commissionFlat;
                            break;
                        case 'Percentage':
                            $commission_amount = (($item->getPriceInclTax() * $item->getQtyOrdered()) * $commissionFlat) / 100;
                            break;
                        
                        default:
                            return;
                    }
                    $total_price = ($item->getPriceInclTax() * $item->getQtyOrdered());
                    $vendor_amount = $total_price - $commission_amount;
                    return $vendor_amount;
                }
            }
            return 0; 
        }
        return 0;
    }
    public function getOrderId()
    {
        
        return $this->getRequest()->getParam('order_id');
    }
}
