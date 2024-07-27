<?php

namespace TargetTraining\EventFinder\Model\Layer\EventFinder\Collection\Filter;

use Magenest\Ticket\Model\ResourceModel\Event\CollectionFactory;
use Magento\Catalog\Model\Layer\CollectionFilterInterface;

/**
 * Class Type
 */
class Type implements CollectionFilterInterface
{
    /**
     * @var \Magenest\Ticket\Model\ResourceModel\Event\CollectionFactory
     */
    private $eventCollectionFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @param \Magenest\Ticket\Model\ResourceModel\Event\CollectionFactory $eventCollection
     */
    public function __construct(
        CollectionFactory $eventCollection,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->eventCollectionFactory= $eventCollection;
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
        $type = $this->getTypeFromRequest();

        if (!$type) {
            return;
        }

        $typeProductIds = $this->getProductIdsByType($type);

        $collection->addIdFilter($typeProductIds);
    }

    /**
     * @return mixed
     */
    private function getProductIdsByType($type)
    {
        $collection = $this->getEventCollection();
        $collection->addFieldToFilter('event_name', ['eq' => $type]);

        return $collection->getColumnValues('product_id');
    }

    /**
     * @return mixed
     */
    private function getEventCollection()
    {
        return $this->eventCollectionFactory->create();
    }

    /**
     * @return mixed|\Zend\Stdlib\ParametersInterface
     */
    private function getTypeFromRequest()
    {
        return $this->request->getPost('type');
    }
}
