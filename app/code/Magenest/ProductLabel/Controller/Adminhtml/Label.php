<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ProductLabel
 * @author   ThaoPV <thaopw@gmail.com>
 */
namespace Magenest\ProductLabel\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magenest\ProductLabel\Model\ResourceModel\Label\CollectionFactory as LabelCollectionFactory;

/**
 * Class Label
 *
 * @package Magenest\ProductLabel\Controller\Adminhtml
 */
abstract class Label extends Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var LabelCollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context                $context
     * @param Registry               $coreRegistry
     * @param PageFactory            $resultPageFactory
     * @param LabelCollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        LabelCollectionFactory $collectionFactory
    ) {
        $this->_context = $context;
        $this->coreRegistry = $coreRegistry;
        $this->_collectionFactory = $collectionFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /**
 * @var \Magento\Backend\Model\View\Result\Page $resultPage
*/
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_ProductLabel::label')
            ->addBreadcrumb(__('Manage Product Label'), __('Manage Product Label'));
        $resultPage->getConfig()->getTitle()->set(__('Manage Product Label'));

        return $resultPage;
    }
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ProductLabel::label');
    }
}
