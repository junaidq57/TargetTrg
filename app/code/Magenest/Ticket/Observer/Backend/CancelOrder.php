<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Ticket\Observer\Backend;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\Ticket\Model\TicketFactory;
use Magenest\Ticket\Model\EventoptionTypeFactory;
use Magenest\Ticket\Model\EventFactory;
use Magenest\Ticket\Helper\Event as HelperEvent;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magenest\Ticket\Model\Event;
use Magenest\Ticket\Model\Configuration;

/**
 * Class GenerateTicket
 * @package Magenest\Ticket\Observer
 */
class CancelOrder implements ObserverInterface
{
    /**
     * @var TicketFactory
     */
    protected $_ticketFactory;

    /**
     * @var HelperEvent
     */
    protected $_helperEvent;

    /**
     * @var EventFactory
     */
    protected $_eventFactory;

    /**
     * @var EventoptionTypeFactory
     */
    protected $optionType;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * CancelOrder constructor.
     * @param TicketFactory $ticketFactory
     * @param EventoptionTypeFactory $optionTypeFactory
     * @param \Psr\Log\LoggerInterface $loggerInterface
     * @param EventFactory $eventFactory
     * @param HelperEvent $helperEvent
     * @param Configuration $configuration
     */
    public function __construct(
        TicketFactory $ticketFactory,
        EventoptionTypeFactory $optionTypeFactory,
        \Psr\Log\LoggerInterface $loggerInterface,
        EventFactory $eventFactory,
        HelperEvent $helperEvent,
        Configuration $configuration
    ) {
        $this->optionType = $optionTypeFactory;
        $this->logger = $loggerInterface;
        $this->_ticketFactory = $ticketFactory;
        $this->_eventFactory = $eventFactory;
        $this->_helperEvent = $helperEvent;
        $this->config = $configuration;
    }

    /**
     * Set new customer group to all his quotes
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var OrderItem $orderItem */
        $orderItem = $observer->getEvent()->getItem();
        /** @var \Magento\Catalog\Model\Product $product */
        $productType = $orderItem->getProductType();
        $event = $this->_eventFactory->create()->loadByProductId($orderItem->getProductId());
        if ($event->getId() && $productType == Event::PRODUCT_TYPE) {
            $qty = $orderItem->getQtyOrdered();
            $buyInfo = $orderItem->getBuyRequest();
            $options = $buyInfo->getAdditionalOptions();
            if (isset($options) && !empty($options)) {
                if (isset($options['dropdow']) && !empty($options['dropdow'])) {
                    $this->saveOption($options['dropdow'], $orderItem->getProductId(), $qty);
                }
                if (isset($options['radio']['radio_select']) && !empty($options['radio']['radio_select'])) {
                    $radioTitle = explode("_", $options['radio']['radio_select']);
                    $this->saveOption($radioTitle[1], $orderItem->getProductId(), $qty);
                }
                if (isset($options['checkbox']) && !empty($options['checkbox'])) {
                    $checkboxTitle = array_keys($options['checkbox']);
                    foreach ($checkboxTitle as $title) {
                        $this->saveOption($title, $orderItem->getProductId(), $qty);
                    }
                }
            }
        }

        return;
    }

    /**
     * @param $title
     * @param $productId
     * @param $qty
     */
    public function saveOption($title, $productId, $qty)
    {
        /** @var \Magenest\Ticket\Model\EventoptionType $model */
        $model = $this->optionType->create()->getCollection()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('title', $title);
        $eventoption = $this->optionType->create();
        foreach ($model as $models) {
            $availableQty = $models->getAvailableQty() + $qty;
            $purcharsedQty = $models->getPurcharsedQty() - $qty;
            $revenue = ($models->getPrice())*($purcharsedQty);
            $data = array(
                'available_qty' => $availableQty,
                'purcharsed_qty' => $purcharsedQty,
                'revenue' => $revenue
            );
            $eventoption->load($models->getId())->addData($data)->save();
        }
    }
}
