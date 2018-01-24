<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ProductLabel
 * @author   ThaoPV <thaopw@gmail.com>
 */
namespace Magenest\ProductLabel\Block\Product;

use Magento\Catalog\Block\Product\ImageBuilder as ProductImageBuilder;
use Magento\Catalog\Helper\ImageFactory as HelperFactory;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\CatalogRule\Model\Rule as RuleModel;
use Magenest\ProductLabel\Helper\Label as LabelHelper;

/**
 * Class ImageBuilder
 *
 * @package Magenest\ProductLabel\Block\Product
 */
class ImageBuilder extends ProductImageBuilder
{
    /**
     * @var LabelHelper
     */
    protected $_labelHelper;

    /**
     * @param HelperFactory $helperFactory
     * @param ImageFactory  $imageFactory
     * @param LabelHelper   $labelHelper
     */
    public function __construct(
        HelperFactory $helperFactory,
        ImageFactory $imageFactory,
        LabelHelper $labelHelper
    ) {
        parent::__construct($helperFactory, $imageFactory);
        $this->_labelHelper = $labelHelper;
    }

    /**
     * Create image block
     *
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function create()
    {
        /**
 * @var \Magento\Catalog\Helper\Image $helper
*/
        $helper = $this->helperFactory->create()
            ->init($this->product, $this->imageId);

        $template = $helper->getFrame()
            ? 'Magenest_ProductLabel::product/image.phtml'
            : 'Magenest_ProductLabel::product/image_with_borders.phtml';

        $imagesize = $helper->getResizedImageInfo();

        $labelHelper = $this->_labelHelper->setProduct($this->product);
        $_labelSale = $labelHelper->isSale();
        $_labelNew = $labelHelper->isNew();

        if ($_labelNew) {
            $_labelNew = $labelHelper->getDefaultCategoryNew();
        }

        if ($_labelSale) {
            $rules = $labelHelper->getRulesLabel();
            if ($rules) {
                $_labelSale = $rules;
            } else {
                $_labelSale = $labelHelper->getDefaultCategorySale();
            }
        }

        $data = [
            'data' => [
                'template' => $template,
                'image_url' => $helper->getUrl(),
                'width' => $helper->getWidth(),
                'height' => $helper->getHeight(),
                'label' => $helper->getLabel(),
                'ratio' => $this->getRatio($helper),
                'custom_attributes' => $this->getCustomAttributes(),
                'resized_image_width' => !empty($imagesize[0]) ? $imagesize[0] : $helper->getWidth(),
                'resized_image_height' => !empty($imagesize[1]) ? $imagesize[1] : $helper->getHeight(),
                'label_new' => $_labelNew,
                'label_sale' => $_labelSale
            ],
        ];

        return $this->imageFactory->create($data);
    }
}
