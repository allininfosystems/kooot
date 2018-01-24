<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Medma\MarketPlace\Controller\Product;

class Addtocart extends \Magento\Framework\App\Action\Action
{
   /**
    * @var \Magento\Checkout\Model\Cart
    */
    protected $cart;
         /**
          * @var \Magento\Catalog\Model\Product
          */
    protected $product;
         
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Model\Product $product,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->cart = $cart;
        $this->product = $product;
        parent::__construct($context);
    }
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $qty = $this->getRequest()->getPost('qty');

        try {
            $params = [];
            $params['qty'] =$qty;
            $_product = $this->product->load($id);
            if ($_product) {
                $this->cart->addProduct($_product, $params);
                $this->cart->save();
            }

            $this->messageManager->addSuccess(__('You added %1 to your shopping cart.', $_product->getName()));
            //$checkoutSession = $this->checkoutSession->destroy();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addException(
                $e,
                __('%1', $e->getMessage())
            );
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('error.'));
        }
    }
}
