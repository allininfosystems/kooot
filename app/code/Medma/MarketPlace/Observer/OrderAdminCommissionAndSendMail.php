<?php
namespace Medma\MarketPlace\Observer;

class OrderAdminCommissionAndSendMail implements \Magento\Framework\Event\ObserverInterface
{
    protected $_transportBuilder;

    public function __construct(
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\User\Model\User $adminUser,
        \Medma\MarketPlace\Helper\Data $marketHelper,
        \Medma\MarketPlace\Block\Adminhtml\Order\Form\Items $_itemsBlock,
        \Medma\MarketPlace\Model\AdminCommission $orderAdminCommision,
        \Medma\MarketPlace\Model\ProfileFactory $profileFactory
    ){
        $this->_transportBuilder = $transportBuilder;
        $this->adminUser = $adminUser;
        $this->marketHelper = $marketHelper;
        $this->_itemsBlock = $_itemsBlock;
    	$this->orderAdminCommision = $orderAdminCommision;
    	$this->profileFactory = $profileFactory;
    }
	public function execute(\Magento\Framework\Event\Observer $observer)
    {
	   
	     /* sending email to vendors code starts */
	     $order = $observer->getEvent()->getOrder();
	     $itemsBlock = $this->_itemsBlock;
	     $orderId = $order->getIncrementId();

	    	$orderEntityId = $order->getId();
	    
	     $itemsOrdered = $order->getItems();
	     $postObject=array();
	     $vendors = array();
	     $vendorIds = array();
	     foreach($itemsOrdered as $item)
	     {
			$product = $item->getProduct(); 
			$vendors [] = $product->getVendor();
		 }
		 $vendorIds = array_unique($vendors);
		 $row = '<table><tr style="padding:15px 0px;"><th style="padding:0px 15px;"><b>'.__('Product Name').'</b></th><th style="padding:0px 15px;"><b>'.__('Product Sku').'</b></th><th style="padding:0px 15px;">'.__('Qty').'</th></tr>';
		foreach($vendorIds as $vendorId)
		{	
		   if($this->marketHelper->getNotifyNewOrderEmail($vendorId))
		   {
			 $str = '';
			 foreach($itemsOrdered as $item)
			 {	
				if($item->getProduct()->getVendor() == $vendorId)
				{
					$vendorProduct = $item->getProduct();
					$str = $str.'<tr style="padding:15px 0px;"><td style="padding:0px 15px;">'.__($vendorProduct->getName()).'</td><td style="padding:0px 15px;">'.__($vendorProduct->getSku()).'</td><td style="padding:0px 15px;">'.__($item->getQtyOrdered()).'</td></tr>';
				}
			 }
			 
			 $vendor = $this->adminUser->load($vendorId);//$product->getVendor());
			 $postObject['vendor'] = $vendor->getFirstname().' '.$vendor->getLastname();
			 $postObject['items'] = $row.$str; 
			 $postObject['order_id'] = $orderId;
			 $sender = $this->marketHelper->getTemplateId('marketplace/email/email_sender');
             $sendername = $this->marketHelper->getTemplateId('trans_email/ident_'.$sender.'/name');
             $senderemail = $this->marketHelper->getTemplateId('trans_email/ident_'.$sender.'/email');
			 $senderInfo = [
                        'name' => $sendername,
                        'email' => $senderemail,
                        ];
			 $transport = $this->_transportBuilder->setTemplateIdentifier('marketplace_email_email_template')->setTemplateOptions([
						 'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file if admin then \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
						 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
						 ])->setTemplateVars($postObject)->setFrom($senderInfo)->addTo($vendor->getEmail(), $vendor->getFirstname().' '.$vendor->getLastName())->getTransport();
			$transport->sendMessage();

		  }
		  $totalCommission=0;
		  foreach($itemsOrdered as $item)
		  {	 $itemCommission = 0;

		  	 if($item->getProduct()->getVendor()==$vendorId)
		  	 {
		  	 	$itemCommision = (int)$itemsBlock->getAdminCommission($item);
		  	 	$totalCommission += $itemCommision; 
		  	 }
		  }
		  $profile = $this->profileFactory->create()->getCollection()->addFieldToFilter('user_id', $vendorId)->getFirstItem();
		  if (!is_null($profile)) {
                $commissionType = $profile->getAdminCommissionType();
                $commissionFlat = $profile->getAdminCommissionFlat();
                $commissionPercentage = $profile->getAdminCommissionPercentage();
                switch ($commissionType) {
                    case '1':
                        $savedCommissionType = 'Currency Amount';
                        $commissionVal = (int)$commissionFlat;
                        break;
                    case '2':
                        $savedCommissionType = 'Percentage';
                        $commissionVal = (int)$commissionPercentage;
                        break;
                    
                    default:
                        return;
           }
        
		  $commissionData = array('order_id'=>$orderEntityId,'vendor_id'=>(int)$vendorId,'commission_type'=>$savedCommissionType,'commission_flat'=>$commissionVal,'commission_amount'=>$totalCommission);
		  
		  $this->orderAdminCommision->setData($commissionData)->save();
		 }
		  
		}
		/* sending email to vendors code ends */

    }
}
