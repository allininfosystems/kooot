<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Medma\MarketPlace\Model\CatalogImportExport;

class CategoryProcessor extends \Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor
{
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryColFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Medma\MarketPlace\Helper\Data $marketHelper,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
         $this->marketHelper = $marketHelper;
         $this->productMetadata = $productMetadata;
         parent::__construct($categoryColFactory,$categoryFactory);
    }
    protected function upsertCategory($categoryPath)
    {   
      $version = $this->productMetadata->getVersion();
      
if( strpos( $version, "2.1" ) !== false ) {
    if (!isset($this->categories[$categoryPath])) {
			$role_id = $this->marketHelper->getConfig('general', 'vendor_role');
			$current_adminuser = $this->marketHelper->getLoggedInAdminUser();

            if($role_id != $current_adminuser)
            {
				$pathParts = explode(self::DELIMITER_CATEGORY, $categoryPath);
				$parentId = \Magento\Catalog\Model\Category::TREE_ROOT_ID;
				$path = '';
				foreach ($pathParts as $pathPart) {
					$path .= $pathPart;
					if (!isset($this->categories[$path])) {
						
						$this->categories[$path] = $this->createCategory($pathPart, $parentId);
						
					}
					$parentId = $this->categories[$path];
					$path .= self::DELIMITER_CATEGORY;
				}
            }
            else
            {
				return 0;
			}
        }
		
		return $this->categories[$categoryPath];
}
else
{
        $index = $this->standardizeString($categoryPath);

        if (!isset($this->categories[$index])) {
            $role_id = $this->marketHelper->getConfig('general', 'vendor_role');
            $current_adminuser = $this->marketHelper->getLoggedInAdminUser();
            $pathParts = explode(self::DELIMITER_CATEGORY, $categoryPath);
            $parentId = \Magento\Catalog\Model\Category::TREE_ROOT_ID;
            $path = '';
            if($role_id != $current_adminuser)
           {
              foreach ($pathParts as $pathPart) {
                $path .= $this->standardizeString($pathPart);
                if (!isset($this->categories[$path])) {
                    $this->categories[$path] = $this->createCategory($pathPart, $parentId);
                }
                $parentId = $this->categories[$path];
                $path .= self::DELIMITER_CATEGORY;
              }
            }
            else
            {
               return 0;
            }
        }

        return $this->categories[$index];
	}
    }

    /**
     * Returns IDs of categories by string path creating nonexistent ones.
     *
     * @param string $categoriesString
     * @param string $categoriesSeparator
     *
     * @return array
     */
    public function upsertCategories($categoriesString, $categoriesSeparator)
    {
        
        $categoriesIds = [];
        
        $categories = explode($categoriesSeparator, $categoriesString);

        foreach ($categories as $category) {
            try {
                if($this->upsertCategory($category))
                {
					$categoriesIds[] = $this->upsertCategory($category);
				}
            } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
                $this->addFailedCategory($category, $e);
            }
        }
	
        return $categoriesIds;
    }
    private function standardizeString($string)
    {
        return mb_strtolower($string);
    }
}
