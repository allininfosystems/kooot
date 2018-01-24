<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ProductLabel\Controller\Adminhtml\Label;

use Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog\NewConditionHtml as PromoNewConditionHtml;

class NewConditionHtml extends PromoNewConditionHtml
{
    /**
     * Check ACL
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
