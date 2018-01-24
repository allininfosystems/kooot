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
 
namespace Medma\MarketPlace\Block\Adminhtml\Transaction;

use Medma\MarketPlace\Model\Transaction as Transactiontype;
 
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
 
   /**
    * @var \Magento\Backend\Helper\Data
    */
    protected $backendHelper;
 
   /**
    * @var \Medma\MarketPlace\Model\ProfileFactory $profileFactory
    */
    protected $transactionFactory;
    
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
    
    /**
     * @var \Magento\Authorization\Model\RoleFactory $rolesFactory
     */
    protected $rolesFactory;
    
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
        \Medma\MarketPlace\Model\TransactionFactory $transactionFactory,
        \Magento\User\Model\UserFactory $UserFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Medma\MarketPlace\Helper\Data $vendorHelper,
        \Magento\Authorization\Model\RoleFactory $rolesFactory,
        array $data = []
    ) {
        $this->transactionFactory = $transactionFactory;
        $this->UserFactory = $UserFactory;
        $this->rolesFactory = $rolesFactory;
        $this->vendorHelper = $vendorHelper;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }
 
    protected function _construct()
    {
        parent::_construct();
        $this->setId('VendorTransactionGrid');
        $this->setDefaultSort('vendorGrid_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    
    /**
     * @method : this method prepare collection for the grid
     * @return : array
     */
    protected function _prepareCollection()
    {
        $collection = $this->transactionFactory->create()->getCollection();
        foreach ($collection as $p) {
            if ($p->getType()==Transactiontype::CREDIT) {
                $p['amount']=$p->getAmount();
            }
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
     
    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'transaction_date',
            [
                'header' => __('Transaction Date'),
                'index' => 'transaction_date',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'width' => '30px',
                ]
        );
                $this->addColumn(
                    'order_number',
                    [
                    'header' => __('Order Number'),
                    'index' => 'order_number',
                    'class' => 'xxx',
                    'width' => '50px',
                    ]
                );
                $this->addColumn(
                    'information',
                    [
                    'header' => __('Information'),
                    'index' => 'information',
                    'class' => 'xxx',
                    'width' => '50px',
                    ]
                );
                $this->addColumn(
                    'credit',
                    [
                    'header' => __('Credit'),
                    'index' => 'credit',
                    'type' => 'number',
                    'class' => 'xxx',
                    'width' => '50px',
                    ]
                );
                $this->addColumn(
                    'debit',
                    [
                    'header' => __('Debit'),
                    'index' => 'debit',
                    'class' => 'xxx',
                    'width' => '50px',
                    ]
                );
                $this->addColumn(
                    'net',
                    [
                    'header' => __('Net'),
                    'index' => 'amount',
                    'class' => 'xxx',
                    'width' => '50px',
                    ]
                );
                
        return $this;
    }
}
