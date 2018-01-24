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
 
namespace Medma\MarketPlace\Block\Adminhtml\Message;
 
class Form extends \Magento\Backend\Block\Widget\Grid\Extended
{
 
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;
 
    /**
     * @var \Medma\MarketPlace\Model\ProfileFactory $profileFactory
     */
    protected $messageFactory;
    protected $detailFactory;
    protected $adminSession;
    protected $profilefactory;

    protected $_template = 'form.phtml';
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
        \Medma\MarketPlace\Model\MessageFactory $messageFactory,
        \Medma\MarketPlace\Model\DetailFactory $detailFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Medma\MarketPlace\Model\ProfileFactory $profilefactory,
        array $data = []
    ) {
    
        $this->messageFactory = $messageFactory;
        $this->adminSession = $adminSession;
        $this->detailFactory = $detailFactory;
        $this->profilefactory = $profilefactory;

        parent::__construct($context, $backendHelper, $data);
    }
    public function getMessages()
    {
        $userid = $this->adminSession->getUser()->getId();
        $profileid = $this->profilefactory->create()->getCollection()->addFieldToFilter('user_id', $userid)->getFirstItem()->getId();
        $id = $this->detailFactory->create()->getCollection()->addFieldToFilter('vendor_id', $profileid)->getFirstItem()->getId();
        $id = $this->getRequest()->getParam('id');
        return $this->messageFactory->create()->getCollection()->addFieldToFilter('msg_id', $id);
    }
    public function getVendorName()
    {
        return $this->adminSession->getUser()->getName();
    }
}
