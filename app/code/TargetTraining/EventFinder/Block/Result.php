<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace TargetTraining\EventFinder\Block;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\CatalogSearch\Helper\Data;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Search\Model\QueryFactory;


class Result extends \Magento\CatalogSearch\Block\Result
{
    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @param Context $context
     * @param LayerResolver $layerResolver
     * @param Data $catalogSearchData
     * @param QueryFactory $queryFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        LayerResolver $layerResolver,
        Data $catalogSearchData,
        QueryFactory $queryFactory,
        array $data = []
    )
    {
        parent::__construct($context, $layerResolver, $catalogSearchData, $queryFactory, $data);
    }
    /**
     * Get search query text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getSearchQueryText()
    {
        if($this->getResultCount() <= 0){
            return __("No matching course.");
        }else{
            return __("The following courses matched your search criteria.");
        }
    }
}
