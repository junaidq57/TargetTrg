<?php

namespace TargetTraining\EventFinder\Block;

use Magenest\Ticket\Model\EventFactory;
use Magenest\Ticket\Model\EventLocationFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Widget
 */
class Widget extends Template
{
    /**
     * @var string
     */
    const TEMPLATE = 'TargetTraining_EventFinder::widget/event-finder.phtml';

    /**
     * @var \Magenest\Ticket\Model\EventLocationFactory
     */
    private $eventLocationFactory;

    /**
     * @var \Magenest\Ticket\Model\EventFactory
     */
    private $eventFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Repository
     */
    private $productAttributeRepository;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magenest\Ticket\Model\EventLocationFactory      $eventLocationFactory
     * @param \Magenest\Ticket\Model\EventFactory              $eventFactory
     * @param array                                            $data
     */
    public function __construct(
        Context $context,
        EventLocationFactory $eventLocationFactory,
        EventFactory $eventFactory,
        \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->eventLocationFactory = $eventLocationFactory;
        $this->eventFactory = $eventFactory;
        $this->productAttributeRepository= $productAttributeRepository;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        if (null === $this->_template) {
            $this->setTemplate(self::TEMPLATE);
        }

        return parent::getTemplate();
    }

    /**
     * @return array
     */
    public function getAvailableLocations()
    {
        $locations = [];

        /** @var \Magenest\Ticket\Model\ResourceModel\EventLocation\Collection $collection */
        $collection = $this->getAvailableLocationsCollection();

        foreach ($collection as $item) {
            if ($item->getValue()!='') {
                $data          = [];
                $data['value'] = $item->getValue();
                $data['label'] = $item->getLabel();

                $locations[] = $data;
            }
        }

        return $locations;
    }

    public function getAvailableTypes()
    {
        $types = [];

        $collection = $this->getAvailableTypesCollection();

        foreach ($collection as $item) {
            if ($item->getValue()!='') {
                $data          = [];
                $data['value'] = $item->getValue();
                $data['label'] = $item->getLabel();

                $types[] = $data;
            }
        }

        return $types;
    }

    /**
     * @return array
     */
    public function getAvailableEvents()
    {
        $events = [];

        $collection = $this->getAvailableEventsCollection();

        foreach ($collection as $item) {
            $data = [];
            $data['value'] = $item->getId();
            $data['label'] = $item->getEventName();

            $events[] = $data;
        }

        return $events;
    }

    /**
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('course/search/result');
    }

    /**
     * @return bool|\Magento\Eav\Api\Data\AttributeOptionInterface[]|null
     */
    private function getAvailableLocationsCollection()
    {
        $attrCode = "course_location";
        try{
            return $this->productAttributeRepository->get($attrCode)->getOptions();
        }catch(\Exception $e){
            return false;
        }
    }

    /**
     * @return \Magenest\Ticket\Model\ResourceModel\Event\Collection
     */
    private function getAvailableEventsCollection()
    {
        $collection = ($this->eventFactory->create())->getCollection();
        $collection->getSelect()->group('event_name');

        return $collection;
    }

    private function getAvailableTypesCollection()
    {
        $attrCode = "course_type";
        try{
            return $this->productAttributeRepository->get($attrCode)->getOptions();
        }catch(\Exception $e){
            return false;
        }
    }
}
