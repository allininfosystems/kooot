<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Blog extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_Blog
 * @author   <ThaoPV>-thaopw@gmail.com
 */
namespace Magenest\ProductLabel\Controller\Adminhtml\Label;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magenest\ProductLabel\Model\ResourceModel\Label\CollectionFactory as LabelCollectionFactory;
use Magenest\ProductLabel\Controller\Adminhtml\Label as LabelController;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Exception\LocalizedException;

class MassStatus extends LabelController
{
    /**
     * Mass Action Filter
     *
     * @var Filter
     */
    protected $_filter;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param Context                $context
     * @param Registry               $coreRegistry
     * @param PageFactory            $resultPageFactory
     * @param LabelCollectionFactory $collectionFactory
     * @param Filter                 $filter
     * @param ForwardFactory         $resultForwardFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        LabelCollectionFactory $collectionFactory,
        Filter $filter,
        ForwardFactory $resultForwardFactory
    ) {
        $this->_filter = $filter;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $collectionFactory);
    }

    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collections = $this->_filter->getCollection($this->_collectionFactory->create());
        $status = (int) $this->getRequest()->getParam('status');
        $totals = 0;
        try {
            foreach ($collections as $item) {
                /**
 * @var \Magenest\ProductLabel\Model\Label $item
*/
                $item->setStatus($status)->save();
                $totals++;
            }
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been updated.', $totals));
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*');
    }
}
