<?php

namespace TargetTraining\EventFinder\Model\Layer;

use Magento\Catalog\Model\Layer\Search;
use TargetTraining\EventFinder\Model\Layer\EventFinder\Collection\Filter\Location;
use TargetTraining\EventFinder\Model\Layer\EventFinder\Collection\Filter\Type;

/**
 * Class EventFinder
 */
class EventFinder extends Search
{
    /**
     * Event finder layer key
     */
    const LAYER_TYPE = 'eventFinder';

    /**
     * @var \TargetTraining\EventFinder\Model\Layer\EventFinder\Collection\Filter\Location
     */
    private $locationCollectionFilter;

    /**
     * @var \TargetTraining\EventFinder\Model\Layer\EventFinder\Collection\Filter\Type
     */
    private $typeCollectionFilter;

    /**
     * EventFinder constructor.
     *
     * @param \Magento\Catalog\Model\Layer\ContextInterface                                  $context
     * @param \Magento\Catalog\Model\Layer\StateFactory                                      $layerStateFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory       $attributeCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product                                   $catalogProduct
     * @param \Magento\Store\Model\StoreManagerInterface                                     $storeManager
     * @param \Magento\Framework\Registry                                                    $registry
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface                               $categoryRepository
     * @param \TargetTraining\EventFinder\Model\Layer\EventFinder\Collection\Filter\Location $locationCollectionFilter
     * @param \TargetTraining\EventFinder\Model\Layer\EventFinder\Collection\Filter\Type     $typeCollectionFilter
     * @param array                                                                          $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\ContextInterface $context,
        \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $catalogProduct,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        Location $locationCollectionFilter,
        Type $typeCollectionFilter,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $layerStateFactory,
            $attributeCollectionFactory,
            $catalogProduct,
            $storeManager,
            $registry,
            $categoryRepository,
            $data
        );

        $this->locationCollectionFilter = $locationCollectionFilter;
        $this->typeCollectionFilter = $typeCollectionFilter;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return \Magento\Catalog\Model\Layer
     */
    public function prepareProductCollection($collection)
    {
        $this->locationCollectionFilter->filter($collection, $this->getCurrentCategory());
        $this->typeCollectionFilter->filter($collection, $this->getCurrentCategory());

        return parent::prepareProductCollection($collection);
    }
}
