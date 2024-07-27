<?php

namespace TargetTraining\Ticket\Plugin;

class Product
{
    protected $event;
    protected $date;
    protected $location;

    public function __construct(
        \Magenest\Ticket\Model\Event $event,
        \Magenest\Ticket\Model\EventDateFactory $date,
        \Magenest\Ticket\Model\EventLocationFactory $location
    ) {
        $this->event = $event;
        $this->date = $date;
        $this->location = $location;
    }

    public function beforeSave(\Magento\Catalog\Model\Product $subject)
    {
        $event = $this->event->loadByProductId($subject->getId());
        $locationModel = $this->location->create()->getCollection()
                                        ->addFieldToFilter('product_id', $subject->getId())
                                        ->addFieldToFilter('location_is_enabled', 1);
        $location = $locationModel->getFirstItem();
        $dateModel = $this->date->create()->getCollection()
                   ->addFieldToFilter('event_location_id', $location->getId());
        $date = $dateModel->getFirstItem();

        $subject->setData('sortable_date', $date->getDateStart());
    }
}