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
 
namespace Medma\MarketPlace\Controller\Adminhtml\Order;

use Magento\Sales\Model\Convert\Order;
use Medma\MarketPlace\Model\Transaction as Transactiontype;

class Ship extends \Magento\Backend\App\Action
{
    protected $vendorHelper;
    protected $adminSession;
    protected $productFactory;
    protected $orderFactory;
    protected $convertorder;
    protected $profileFactory;
    protected $transactionFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Medma\MarketPlace\Helper\Data $vendorHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Medma\MarketPlace\Model\ProfileFactory $profileFactory,
        \Medma\MarketPlace\Model\TransactionFactory $transactionFactory,
        Order $convertorder
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->adminSession = $adminSession;
        $this->vendorHelper = $vendorHelper;
        $this->productFactory = $productFactory;
        $this->orderFactory = $orderFactory;
        $this->convertorder = $convertorder;
        $this->transactionFactory = $transactionFactory;
        $this->profileFactory = $profileFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        try {
            $orderId = $this->getRequest()->getParam('order_id');
            $order = $this->orderFactory->create()->load($orderId);
            $convertor = $this->convertorder;
            $shipment = $convertor->toShipment($order);
            $productIds = $this->getProductIdsCollection();
            $current_user = $this->adminSession->getUser()->getName();

            $current_user_id = $this->adminSession->getUser()->getId();
            $profile = $this->profileFactory->create()->getCollection()->addFieldToFilter('user_id', $current_user_id)->getFirstItem();

            $admin_commission_percentage = $profile->getAdminCommissionPercentage();
            $admin_commission_flat = $profile->getAdminCommissionFlat();
            $total_admin_commission = $profile->getData('total_admin_commission');
            $total_vendor_amount = $profile->getData('total_vendor_amount');
            $vendor_amount = 0;
            
            foreach ($order->getAllItems() as $orderItem) {
                if (!$orderItem->getQtyToShip()) {
                    continue;
                }
                if ($orderItem->getIsVirtual()) {
                    continue;
                }

                if (!in_array($orderItem->getProductId(), $productIds)) {
                    continue;
                }

                $item = $convertor->itemToShipmentItem($orderItem);
                $qty = $orderItem->getQtyToShip();
                $item->setQty($qty);
                $shipment->addItem($item);

                $total_price = ($orderItem->getPriceInclTax() * $orderItem->getQtyOrdered());
                
                if ($admin_commission_percentage) {
                    $total_commission = ($total_price * $admin_commission_flat) / 100;
                    $total_admin_commission += $total_commission;
                    $total_vendor_amount += ($total_price - $total_commission);
                    $vendor_amount += ($total_price - $total_commission);
                } else {
                    $total_commission = $admin_commission_flat;
                    $total_admin_commission += $total_commission;
                    $total_vendor_amount += ($total_price - $total_commission);
                    $vendor_amount += ($total_price - $total_commission);
                }
             
                $transactionCollection = $this->transactionFactory->create()->getCollection()
                  ->addFieldToFilter('order_number', $order->getIncrementId())
                  ->addFieldToFilter('vendor_id', $current_user_id);
            
                if (count($transactionCollection) == 0) {
                    $this->profileFactory->create()->load($profile->getId())->setTotalAdminCommission($total_admin_commission)
                        ->setTotalVendorAmount($total_vendor_amount)
                        ->save();
                        
                        $transaction = $this->transactionFactory->create();
                        $transaction->setVendorId($current_user_id)
                            ->setTransactionDate(date("Y-m-d H:i:s"))
                            ->setOrderNumber($order->getIncrementId())
                            ->setInformation('Order')
                            ->setAmount($vendor_amount)
                            ->setType(Transactiontype::CREDIT)
                            ->save();
                }
                  $shipment->register();
                  $email = false;
                  $includeComment = true;
                  $comment = 'Order Shipped By Vendor - ' . $current_user;

                  $shipment->addComment($comment, $email && $includeComment);
                  $shipment->getOrder()->setIsInProcess(true);
                  $transactionSave = $this->_objectManager->create(
                      'Magento\Framework\DB\Transaction'
                  )->addObject(
                      $shipment
                  )->addObject(
                      $shipment->getOrder()
                  );
                $transactionSave->save();
                    
                  $order->addStatusToHistory($order->getStatus(), 'Order Shipped By Vendor ' . $current_user, false);

                  $shipment->save();

                  //$shipment->sendEmail(true);
                  $shipment->setEmailSent(true);
                  $shipment->save();


                $this->messageManager->addSuccess(__('The Shipment has been created.'));
                $this->_redirect('*/*/view', ['order_id' => $orderId]);
            }
        } catch (\Magento\Framework\Validator\Exception $e) {
            $this->messageManager->addError(__('The Shipment cannot be created for the order.'));
            $this->_redirect('*/*/view', ['order_id' => $orderId]);
        }
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
}
