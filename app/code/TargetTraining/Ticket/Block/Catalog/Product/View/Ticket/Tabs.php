<?php

namespace TargetTraining\Ticket\Block\Catalog\Product\View\Ticket;

use Magenest\Ticket\Block\Product\Ticket as TicketBlock;
use Magenest\Ticket\Model\Ticket as TicketModel;

class Tabs extends TicketBlock
{
    protected $ticketModel;

    protected $stockRegistry;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magenest\Ticket\Model\EventFactory $eventFactory,
        \Magenest\Ticket\Model\EventoptionFactory $eventoptionFactory,
        \Magenest\Ticket\Model\EventoptionTypeFactory $eventoptionTypeFactoryFactory,
        \Magenest\Ticket\Model\EventLocationFactory $eventLocationFactory,
        \Magenest\Ticket\Model\EventDateFactory $eventDateFactory,
        \Magenest\Ticket\Model\EventSessionFactory $eventSessionFactory,
        \Magento\Directory\Model\Currency $currency,
        TicketModel $ticketModel,
        array $data
    ) {
        $this->ticketModel = $ticketModel;

        parent::__construct($context, $eventFactory, $eventoptionFactory, $eventoptionTypeFactoryFactory,
            $eventLocationFactory, $eventDateFactory, $eventSessionFactory, $currency, $data);
    }

    public function getCourseTimetable()
    {
        $product = $this->_coreRegistry->registry('current_product');
        $courseTimetable = $product->getData('course_timetable');
        return $courseTimetable;
    }
}