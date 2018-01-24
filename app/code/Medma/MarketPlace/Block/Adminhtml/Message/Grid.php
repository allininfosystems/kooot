<?php
namespace Medma\MarketPlace\Block\Adminhtml\Message;
 
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
 
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;
    protected $messageFactory;
    protected $adminSession;
    protected $profilefactory;
 

    
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
        \Medma\MarketPlace\Model\DetailFactory $messageFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Medma\MarketPlace\Model\ProfileFactory $profilefactory,
        array $data = []
    ) {
        $this->messageFactory = $messageFactory;
        $this->adminSession = $adminSession;
        $this->profilefactory = $profilefactory;

        parent::__construct($context, $backendHelper, $data);
    }
 
    protected function _construct()
    {
        parent::_construct();
        $this->setId('msgGrid');
        $this->setDefaultSort('id');
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
        $userid = $this->adminSession->getUser()->getId();
        $profileid = $this->profilefactory->create()->getCollection()->addFieldToFilter('user_id', $userid)->getFirstItem()->getId();
        
        $collection = $this->messageFactory->create()->getCollection()->addFieldToFilter('vendor_id', $profileid);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
     
    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'Entity_id',
            [
            'header' => __('Entity id'),
            'align' => 'right',
            'width' => '80px',
            'index' => 'entity_id',
            ]
        );

        $this->addColumn(
            'customer_id',
            [    'header' => __('Customer Name'),
            'index' => 'customer_id',
            'width' => '100px',
            'renderer' => 'Medma\MarketPlace\Block\Adminhtml\Message\Renderer\Customer'
            ]
        );


        $this->addColumn(
            'action',
            [
            'header' => __('Action'),
            'width' => '50px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => [
                [
                    'caption' => __('View'),
                    'url' => ['base' => '*/*/view'],
                    'field' => 'id'
                ]
            ],
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
            ]
        );
          return parent::_prepareColumns();
    }
     
    /**
     * @return $this
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', ['id' => $row->getId()]);
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/id', ['_current'=>true]);
    }
}
