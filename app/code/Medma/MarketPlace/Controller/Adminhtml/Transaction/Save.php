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
 
namespace Medma\MarketPlace\Controller\Adminhtml\Transaction;
 
use Medma\MarketPlace\Model\Transaction as transactiontype;

class Save extends \Magento\Backend\App\Action
{
    protected $_adminSession;
    protected $vendorHelper;
    protected $profileFactory;
    protected $pricehelper;
    protected $userFactory;
    protected $coreRegistry;
    protected $transactionFactory;
 
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     * @param \Medma\MarketPlace\Helper\Data $vendorHelper
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Medma\MarketPlace\Model\ProfileFactory $profileFactory
     * @param \Medma\MarketPlace\Model\TransactionFactory $transactionFactory
     * @param \Magento\Framework\Pricing\Helper\Data $pricehelper
     * @param \Magento\Framework\Registry $coreRegistry
     */
   
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Medma\MarketPlace\Helper\Data $vendorHelper,
        \Magento\User\Model\UserFactory $userFactory,
        \Medma\MarketPlace\Model\ProfileFactory $profileFactory,
        \Medma\MarketPlace\Model\TransactionFactory $transactionFactory,
        \Magento\Framework\Pricing\Helper\Data $pricehelper,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->userFactory = $userFactory;
        $this->transactionFactory = $transactionFactory;
        $this->_adminSession = $adminSession;
        $this->pricehelper = $pricehelper;
        $this->vendorHelper = $vendorHelper;
        $this->profileFactory = $profileFactory;
        parent::__construct($context);
    }
 
    public function execute()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $vendor_id = $data['vendor_id'];
                $postData = $this->getRequest()->getPost();

                $profileModel = $this->profileFactory->create()->getCollection()->addFieldToFilter('user_id', $vendor_id)->getFirstItem();
     
                $remaining_amount = ($profileModel->getTotalVendorAmount() - $profileModel->getTotalVendorPaid());
                if ($remaining_amount < $postData['amount']) {
                    $remaining_amount = $this->pricehelper->currency($remaining_amount, true, false);

                   
                    $this->messageManager->addError('You can not transfer more then ' . $remaining_amount);

                    $this->_adminSession->setAmount($postData['amount']);

                    $this->_redirect('*/*/new', ['vendor_id' =>$data['vendor_id']]);
                    return;
                }
                $amount_paid = $profileModel->getTotalVendorPaid();
                $amount_paid += floatval($postData['amount']);
                $this->profileFactory->create()->load($profileModel->getId())->setTotalVendorPaid($amount_paid)->save();

                $transaction = $this->transactionFactory->create();
                $transaction->setVendorId($vendor_id)
                        ->setTransactionDate(date("Y-m-d H:i:s"))
                        ->setOrderNumber('')
                        ->setInformation($data['information'])
                        ->setAmount($postData['amount'])
                        ->setType(transactiontype::DEBIT)
                        ->save();
                   $this->messageManager->addSuccess('Your Transaction is successfully complete');
            } catch (\Magento\Framework\Validator\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager
                        ->settestData($this->getRequest()
                                ->getPost());
                $this->_redirect('*/*/new', ['vendor_id' => $data['vendor_id']]);
                return;
            }
        }

        $this->_redirect('*/*/', ['id' => $data['vendor_id']]);
    }
}
