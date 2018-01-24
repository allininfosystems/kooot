<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoAll\Ui\Component\Listing\Column;

class Category extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * URL builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \MageWorx\SeoRedirects\Model\Redirect\Source\Category
     */
    protected $categoryOptions;

    /**
     * @var string
     */
    protected $targetField = 'category_id';

    /**
     * Constructor
     *
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \MageWorx\SeoAll\Model\Source\Category $categoryOptions,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->categoryOptions = $categoryOptions;
        $this->urlBuilder = $urlBuilder;

        if (!empty($data['targetField'])) {
            $this->targetField = $data['targetField'];
        }
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }


    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $categoryOptions = $this->categoryOptions->toArray();

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (!isset($item[$this->targetField])) {
                    continue;
                }

                if (!empty($categoryOptions[$item[$this->targetField]])) {
                    $item[$this->getData('name')] = $item[$this->targetField];
                } else {
                    $item[$this->getData('name')] = __('UNKNOWN CATEGORY') . ' (ID#' . $item[$this->targetField] . ')';
                }
            }
        }
        return $dataSource;
    }
}
