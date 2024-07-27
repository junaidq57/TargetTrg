<?php

namespace TargetTraining\EventFinder\Model\Layer\EventFinder\Collection\Filter;

use Magenest\Ticket\Model\ResourceModel\EventLocation\CollectionFactory;
use Magento\Catalog\Model\Layer\CollectionFilterInterface;

/**
 * Class Location
 */
class Location implements CollectionFilterInterface
{
    /**
     * @var \Magenest\Ticket\Model\ResourceModel\EventLocation\CollectionFactory
     */
    private $locationCollectionFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @param \Magenest\Ticket\Model\ResourceModel\EventLocation\CollectionFactory $locationCollectionFactory
     */
    public function __construct(
        CollectionFactory $locationCollectionFactory,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->request = $request;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param \Magento\Catalog\Model\Category                         $category
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|void
     */
    public function filter(
        $collection,
        \Magento\Catalog\Model\Category $category
    ) {
        $location = $this->getLocationFromRequest();

        if (!$location) {
            return;
        }

        $productIds = $this->getProductIdsByLocation($location);
        $collection->addIdFilter($productIds);
    }

    /**
     * @return mixed
     */
    private function getLocationCollection()
    {
        return $this->locationCollectionFactory->create();
    }

    /**
     * @param string $location
     *
     * @return mixed
     */
    private function getProductIdsByLocation($location)
    {
        $collection = $this->getLocationCollection();
        $collection->addFieldToFilter('location_id', ['eq' => $location]);

        return $collection->getColumnValues('product_id');
    }

    /**
     * @return mixed|\Zend\Stdlib\ParametersInterface
     */
    private function getLocationFromRequest()
    {
        return $this->request->getPost('location');
    }
}
