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

namespace Medma\MarketPlace\Block;

use Medma\MarketPlace\Model\ProfileFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Catalog\Model\ProductFactory;
 
class Profile extends \Magento\Framework\View\Element\Template
{
   /**
    * @var Medma\MarketPlace\Model\ProfileFactory
    */
    protected $profile;
   
   /**
    * @var Magento\Framework\Message\ManagerInterface
    */
    protected $messageManager;
   
   /**
    * @var \Magento\User\Model\UserFactory
    */
    protected $ModelUser;

   /**
    * @var \Medma\MarketPlace\Helper\Data
    */
    protected $marketHelper;
      
   /**
    * @var \Magento\Checkout\Model\Cart
    */
    protected $cart;

   /**
    * @var \Magento\Catalog\Model\ProductFactory
    */
    protected $ProductFactory;
      
   /**
    * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
    */
    protected $orderCollectionFactory;

   /**
    * @var \Medma\MarketPlace\Model\ResourceModel\Feedback\CollectionFactory
    */
    protected $feedfactory;
    
    /**
     * @var \Medma\MarketPlace\Model\Transaction
     */
    protected $transactionFactory;
    
   /**
    * @param \Magento\Catalog\Block\Product\Context $context
    * @param \Magento\User\Model\UserFactory $adminuser
    * @param \Medma\MarketPlace\Helper\Data $marketHelper
    * @param ProfileFactory $profile
    * @param ManagerInterface $messageManager
    * @param \Magento\Checkout\Model\Cart $cart
    * @param ProductFactory $ProductFactory
    * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    * @param \Medma\MarketPlace\Model\ResourceModel\Feedback\CollectionFactory $feedfactory
    */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\User\Model\UserFactory $adminuser,
        \Medma\MarketPlace\Helper\Data $marketHelper,
        ProfileFactory $profile,
        ManagerInterface $messageManager,
        \Magento\Checkout\Model\Cart $cart,
        ProductFactory $ProductFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Medma\MarketPlace\Model\ResourceModel\Feedback\CollectionFactory $feedfactory,
        \Medma\MarketPlace\Model\TransactionFactory $transactionfactory
    ) {
        $this->profile = $profile;
        $this->messageManager = $messageManager;
        $this->marketHelper = $marketHelper;
        $this->ModelUser = $adminuser;
        $this->cart = $cart;
        $this->ProductFactory = $ProductFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->feedfactory = $feedfactory;
        $this->transactionFactory = $transactionfactory;
        parent::__construct($context);
    }

    public function getVendorInfo()
    {
        $profileId = $this->getRequest()->getParam('id');
        return $this->profile->create()->load($profileId);
    }
        
    public function getUserObject($userId)
    {
        return $this->ModelUser->create()->load($userId);
    }
        
    public function getVendorImageUrl()
    {
        return $this->marketHelper->getImagesUrl('varifications');
    }
        
    public function getVendorProfileUrl($vendorId)
    {
        return $this->getUrl('marketplace/vendor/profile', ['id' => $vendorId]);
    }
        
    public function getVendorItemsUrl($profileId)
    {
        return $this->getUrl('marketplace/vendor/items', ['id' => $profileId]);
    }
    
    public function getMessage($vendorInfo, $userObject)
    {
        $message = $this->getVendorInfo()->getMessage();
        if (trim($message) != '') {
            return $message;
        } else {
            return (__('Based in ') .
            $this->getCountryName($vendorInfo->getCountry()) . ' ' .
            $vendorInfo->getShopName() . ' has been member since ' .
            date("M j, Y", strtotime($userObject->getCreated())));
        }
    }
    
    public function getCountryName($code)
    {
        return $this->marketHelper->getCountryName($code);
    }
        
    public function getCartSellerVacation()
    {
        $data = [];
        $ItemsCollection = $this->cart->getQuote()->getItemsCollection();
        $i=0;
        foreach ($ItemsCollection as $items) {
            $productCollection = $this->ProductFactory->create();
            $productCollection = $productCollection->load($items->getProductId());
            $UserData = $this->getUserObject($productCollection->getVendor());
            $data[$i]["id"] = $items->getId();
            $data[$i]["product_id"] = $items->getProductId();
            $data[$i]["vacation_mode"] = $UserData->getSellerVacationMode();
            $i++;
        }
        return $data;
    }
        
    /**
     * To get transaction for particular customer
     */
    public function getTransactions($customer_Id)
    {
        $orders = $this->orderCollectionFactory->create()->addFieldToSelect('*')->addFieldToFilter('status', 'complete')->addFieldToFilter('customer_id', $customer_Id)->setOrder('created_at', 'desc');
        return count($orders);
    }
    
    /**
     * To get no. of transactions for particular customer for a particular vendor
     */
    public function getCustomerToVendorTransactions($customer_Id, $vendor_id)
    {
        $orders = $this->orderCollectionFactory->create()->addFieldToSelect('increment_id')->addFieldToFilter('status', ['processing', 'complete'])->addFieldToFilter('customer_id', $customer_Id)->setOrder('created_at', 'desc');
        $order_increment_ids = $orders->getData();
        $order_increment_id_arr = [];
        foreach ($order_increment_ids as $order_increment_id) {
            $order_increment_id_arr[] = $order_increment_id['increment_id'];
        }
        
        if (count($order_increment_id_arr)) {return count($this->transactionFactory->create()->getCollection()->addFieldToSelect('*')->addFieldToFilter('vendor_id', $vendor_id)->addFieldToFilter('order_number', ['in', $order_increment_id_arr])->getData());
        } else {
            return '0';
        }
    }
    
    /**
     * To get no of reviews of particular customer
     */
    public function getReviews($customer_Id, $vendor_id)
    {
        $feedbacks = $this->feedfactory->create()->addFieldToSelect('*')->addFieldToFilter('customer_id', $customer_Id)->addFieldToFilter('vendor_id', $vendor_id);
        return count($feedbacks);
    }
    public function getReviewCollection($vendor_id)
    {
        $feedbacks = $this->feedfactory->create()->addFieldToFilter('vendor_id', $vendor_id)->load();
        return $feedbacks;
    }
}
