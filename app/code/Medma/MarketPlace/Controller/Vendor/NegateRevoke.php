<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Medma\MarketPlace\Controller\Vendor;

use Magento\Integration\Model\AdminTokenService;

/**
 * Plugin to delete admin tokens when admin becomes inactive
 */
class NegateRevoke
{
    /**
     * @var AdminTokenService
     */
    private $adminTokenService;

    /**
     * @param AdminTokenService $adminTokenService
     */
    public function __construct(
        AdminTokenService $adminTokenService
    ) {
        $this->adminTokenService = $adminTokenService;
    }

    /**
     * Check if admin is inactive - if so, invalidate their tokens
     *
     * @param \Magento\User\Model\User $subject
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    public function afterSave(
        \Magento\User\Model\User $subject,
        \Magento\Framework\DataObject $object
    ) {
        $isActive = $object->getIsActive();
        if (isset($isActive) && $isActive == 0) {
            //do nothing
            //$this->adminTokenService->revokeAdminAccessToken($object->getId());
        }
        return $subject;
    }
}
