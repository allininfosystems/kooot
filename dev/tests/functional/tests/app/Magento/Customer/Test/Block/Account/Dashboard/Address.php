<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Test\Block\Account\Dashboard;

use Magento\Mtf\Block\Block;

/**
 * Customer Dashboard Address Book block.
 */
class Address extends Block
{
    /**
     * Default Billing Address Edit link.
     *
     * @var string
     */
    protected $defaultBillingAddressEdit = '[data-ui-id=default-billing-edit-link]';

    /**
     * Default Shipping Address Edit link.
     *
     * @var string
     */
    protected $defaultShippingAddressEdit = '[data-ui-id=default-shipping-edit-link]';

    /**
     * Shipping address block selector.
     *
     * @var string
     */
    protected $shippingAddressBlock = '.box-shipping-address';

    /**
     * Billing address block selector.
     *
     * @var string
     */
    protected $billingAddressBlock = '.box-billing-address';

    /**
     * Edit Default Billing Address.
     *
     * @return void
     */
    public function editBillingAddress()
    {
        $this->_rootElement->find($this->defaultBillingAddressEdit)->click();
    }

    /**
     * Edit Default Shipping Address.
     *
     * @return void
     */
    public function editShippingAddress()
    {
        $this->_rootElement->find($this->defaultShippingAddressEdit)->click();
    }

    /**
     * Returns Default Billing Address Text.
     *
     * @return array|string
     */
    public function getDefaultBillingAddressText()
    {
        return $this->_rootElement->find($this->billingAddressBlock)->getText();
    }

    /**
     * Returns Default Shipping Address Text.
     *
     * @return array|string
     */
    public function getDefaultShippingAddressText()
    {
        return $this->_rootElement->find($this->shippingAddressBlock)->getText();
    }
}