<?php

namespace TargetTraining\Ticket\Block\Catalog\Product\View\Ticket;

use Magenest\Ticket\Block\Product\Ticket as TicketBlock;
use Magenest\Ticket\Model\Ticket as TicketModel;

class Review extends TicketBlock
{
    protected $product;

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
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        array $data
    ) {
        $this->ticketModel = $ticketModel;
        $this->stockRegistry = $stockRegistry;

        parent::__construct($context, $eventFactory, $eventoptionFactory, $eventoptionTypeFactoryFactory,
            $eventLocationFactory, $eventDateFactory, $eventSessionFactory, $currency, $data);

        $this->product = $this->_coreRegistry->registry('current_product');
    }

    public function getQty(){
        $product = $this->product;
        $stock = $this->stockRegistry->getStockItem($product->getId());
        return $stock->getQty();
    }

    public function getCourseQuote() {
        return $this->product->getData('course_quote');
    }

    public function getCourseQuoter() {
        return $this->product->getData('course_quoter');
    }
}