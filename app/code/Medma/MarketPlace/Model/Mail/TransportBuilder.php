<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_EmailDemo
 * @author    Webkul
 * @copyright Copyright (c) 2010-2016 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
 
namespace Medma\MarketPlace\Model\Mail;
 
class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * @param Api\AttachmentInterface $attachment
     */
    public function addAttachment($images)
    {
        if ($images) {
            foreach ($images as $image) {
                $om = \Magento\Framework\App\ObjectManager::getInstance();
                $dir = $om->get('Magento\Framework\Filesystem');
                $mediadir=$dir->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('report/');
        
                $this->message->createAttachment(
                    file_get_contents($mediadir.$image),
                    'application/image',
                    \Zend_Mime::DISPOSITION_ATTACHMENT,
                    \Zend_Mime::ENCODING_BASE64,
                    $image
                );
            }
            return $this;
        }
    }
}
