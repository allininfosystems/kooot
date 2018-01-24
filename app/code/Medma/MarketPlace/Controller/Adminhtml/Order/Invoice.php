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

use Magento\Sales\Model\Service\InvoiceService;

class Invoice extends \Magento\Backend\App\Action
{
    protected $vendorHelper;
    protected $adminSession;
    protected $productFactory;
    protected $orderFactory;
    protected $invoiceService;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Medma\MarketPlace\Helper\Data $vendorHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        InvoiceService $invoiceService
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->adminSession = $adminSession;
        $this->vendorHelper = $vendorHelper;
        $this->productFactory = $productFactory;
        $this->orderFactory = $orderFactory;
        $this->invoiceService = $invoiceService;
        parent::__construct($context);
    }
    public function execute()
    {
        try {
            $productIds = $this->getProductIdsCollection();
            $current_user = $this->adminSession->getUser()->getName();
            $orderId = $this->getRequest()->getParam('order_id');
            $order = $this->orderFactory->create()->load($orderId);
            $items = $order->getItemsCollection();
            $invoice_quentities = [];
            foreach ($items as $item) {
                $qty_to_invoice = $item->getQtyOrdered();
                if (!in_array($item->getProductId(), $productIds)) {
                    $qty_to_invoice = 0;
                }

                    $invoice_quentities[$item->getId()] = $qty_to_invoice;
            }
                $invoice =$this->invoiceService->prepareInvoice($order, $invoice_quentities);
                
                $amount = $invoice->getGrandTotal();
            
                $invoice->register()->pay();
                $invoice->getOrder()->setIsInProcess(true);
                $history = $invoice->getOrder()->addStatusHistoryComment(
                    'Partial amount of $' . $amount . ' captured automatically.',
                    false
                );
                $history->setIsCustomerNotified(true);
                $order->save();
                
                $transactionSave = $this->_objectManager->create(
                    'Magento\Framework\DB\Transaction'
                )->addObject(
                    $invoice
                )->addObject(
                    $invoice->getOrder()
                );
                $transactionSave->save();


                $order->addStatusToHistory($order->getStatus(), 'Order Invoice Created By Vendor ' . $current_user, false);

                $invoice->save();

                //$this->invoiceSender->send($invoice);
                $this->messageManager->addSuccess(__('The invoice has been created.'));
                 $this->_redirect('*/*/view', ['order_id' => $orderId]);
        } catch (\Exception $e) {
            $this->messageManager->addError(__('The Invoice cannot be created for the order.'));
            $this->_redirect('*/*/view', ['order_id' => $orderId]);
        }
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
}
