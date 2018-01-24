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
 * @author   <ThaoPV>-thaopw@gmail.com
 */
namespace Magenest\ProductLabel\Controller\Adminhtml\Label;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magenest\ProductLabel\Model\ResourceModel\Label\CollectionFactory as LabelCollectionFactory;
use Magenest\ProductLabel\Controller\Adminhtml\Label as LabelController;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MassDelete
 *
 * @package Magenest\ProductLabel\Controller\Adminhtml\Label
 */
class MassDelete extends LabelController
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
        $totals = 0;
        try {
            foreach ($collections as $item) {
                /**
 * @var \Magenest\ProductLabel\Model\Label $item
*/
                $item->delete();
                $totals++;
            }

            $this->messageManager->addSuccess(__('A total of %1 record(s) have been deteled.', $totals));
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while delete the post(s).'));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
