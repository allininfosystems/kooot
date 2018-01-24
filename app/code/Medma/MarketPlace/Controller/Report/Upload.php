<?php

namespace Medma\MarketPlace\Controller\Report;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action;
use Magento\Framework\Filesystem;

class Upload extends \Magento\Framework\App\Action\Action
{
    protected $_fileUploaderFactory;
    protected $catalogSession;
 
    public function __construct(
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Catalog\Model\Session $catalogSession,
        Filesystem $fileSystem,
        Action\Context $context
    ) {
        $this->catalogSession = $catalogSession;
        $this->_filesystem = $fileSystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        parent::__construct($context);
    }
 
    public function execute()
    {
        $mergefiles = [];
        if ($this->catalogSession->getUploadedImages()) {
            $mergefiles = $this->catalogSession->getUploadedImages();
        }
        $uploaded_files = [];
        try {
            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'myfile']);
            
            $arr = $uploader->validateFile();
            $correct_name = $uploader->getCorrectFileName($arr['name']);
        
            $uploaded_files[] = $correct_name;
            $completefiles = array_merge($mergefiles, $uploaded_files);
        
            $this->catalogSession->setUploadedImages($completefiles);

            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png','txt','pdf','zip','doc','docx']);
              
            $uploader->setAllowRenameFiles(false);
              
            $uploader->setFilesDispersion(false);
         
            $path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('report/');
            
            $uploader->save($path);
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }
}
