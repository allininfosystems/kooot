<?php
namespace Medma\MarketPlace\Block\Product;

class Vendors extends \Magento\Framework\View\Element\Template
{
    protected $_registry;
        
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Medma\MarketPlace\Block\VendorList $vendorListBlock,
        array $data = []
    ) {
    
        $this->_registry = $registry;
        $this->vendorListBlock = $vendorListBlock;
        parent::__construct($context, $data);
    }
    
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }
    
    public function getCurrentProduct()
    {
        $data = $this->_registry->registry('current_product');
        $vendor = $this->vendorListBlock->getProfile($data->getVendor());
        return $vendor->getId();
    }
    public function getVendorProfileUrl($id)
    {
        return $this->getUrl('marketplace/vendor/profile', ['id' => $id]);
    }
}
