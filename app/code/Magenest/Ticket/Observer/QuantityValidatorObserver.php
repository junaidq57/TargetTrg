<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 *
 * Magenest_Ticket extension
 * NOTICE OF LICENSE
 *
 * @category  Magenest
 * @package   Magenest_Ticket
 * @author ThaoPV <thaopw@gmail.com>
 */
namespace Magenest\Ticket\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magenest\Ticket\Model\Quote\Item\QuantityValidator;
use Magenest\Ticket\Helper\Event;
use \Magento\Framework\Event\Observer;
use Psr\Log\LoggerInterface;

/**
 * Class QuantityValidatorObserver
 * @package Magenest\Ticket\Observer
 */
class QuantityValidatorObserver implements ObserverInterface
{
    /**
     * @var \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator $quantityValidator
     */
    protected $quantityValidator;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var
     */
    protected $helperEvent;

    /**
     * QuantityValidatorObserver constructor.
     * @param QuantityValidator $quantityValidator
     * @param LoggerInterface $loggerInterface
     * @param Event $helperEvent
     */
    public function __construct(
        QuantityValidator $quantityValidator,
        LoggerInterface $loggerInterface,
        Event $helperEvent
    ) {
        $this->logger = $loggerInterface;
        $this->quantityValidator = $quantityValidator;
        $this->helperEvent = $helperEvent;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /* @var $quoteItem \Magento\Quote\Model\Quote\Item */
        $quoteItem = $observer->getEvent()->getItem();
        if (!$this->helperEvent->isEvent($quoteItem->getProduct()->getId())) {
            return;
        }

        $this->quantityValidator->validate($observer);
    }
}
