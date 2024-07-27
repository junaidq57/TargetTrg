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
namespace Magenest\Ticket\Model\Quote\Item;

use Magenest\Ticket\Model\EventoptionFactory;
use Magenest\Ticket\Model\EventoptionTypeFactory;
use Magento\Framework\Event\Observer;
use Magento\CatalogInventory\Helper\Data;
use Psr\Log\LoggerInterface;

/**
 * Class QuantityValidator
 * @package Magenest\Ticket\Model\Quote\Item
 */
class QuantityValidator
{
    /**
     * @var EventoptionFactory
     */
    protected $_eventoptionFactory;

    /**
     * @var EventoptionTypeFactory
     */
    protected $optionType;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * QuantityValidator constructor.
     * @param EventoptionFactory $eventoptionFactory
     * @param EventoptionTypeFactory $optionTypeFactory
     * @param LoggerInterface $loggerInterface
     */
    public function __construct(
        EventoptionFactory $eventoptionFactory,
        EventoptionTypeFactory $optionTypeFactory,
        \Magento\Framework\Message\ManagerInterface $managerInterface,
        LoggerInterface $loggerInterface
    ) {
        $this->messageManager = $managerInterface;
        $this->optionType = $optionTypeFactory;
        $this->_eventoptionFactory = $eventoptionFactory;
        $this->_logger = $loggerInterface;
    }

    /**
     * Check product option event data when quote item quantity declaring
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     */
    public function validate(Observer $observer)
    {
        /* @var $quoteItem \Magento\Quote\Model\Quote\Item */
        $quoteItem = $observer->getEvent()->getItem();
        $buyInfo = $quoteItem->getBuyRequest();
        $options = $buyInfo->getAdditionalOptions();
        if (!$quoteItem ||
            !$quoteItem->getProductId() ||
            !$quoteItem->getQuote() ||
            $quoteItem->getQuote()->getIsSuperMode()
        ) {
            return;
        }
        $qty = $quoteItem->getQty();
        if (!empty($options)) {
            if (isset($options['dropdow']) && !empty($options['dropdow'])) {
                $this->getErrorMessage($options['dropdow'], $quoteItem);
            }
            if (isset($options['radio']['radio_select']) && !empty($options['radio']['radio_select'])) {
                $value = explode("_", $options['radio']['radio_select']);
                $type = $value[1];
                $this->getErrorMessage($type, $quoteItem);
            }
            if (isset($options['checkbox']) && !empty($options['checkbox'])) {
                $arrayCkeck = json_decode($options['checkbox'], true);
                $arrayCheckboxs = array_keys($arrayCkeck);
                foreach ($arrayCheckboxs as $checkbox) {
                    $this->getErrorMessage($checkbox, $quoteItem);
                }
            }
        }
    }

    /**
     * get Error if not enough qty
     * @param $type
     * @param $quoteItem
     */
    public function getErrorMessage($type, $quoteItem)
    {
        /** @var \Magenest\Ticket\Model\EventoptionType $modelType */
        $model = $this->optionType->create()->getCollection()
            ->addFieldToFilter('product_id', $quoteItem->getProduct()->getId())
            ->addFieldToFilter('title', $type);
        foreach ($model as $models) {
            $availableQty = $models->getAvailableQty();
            $title = $models->getTitle();
            if ($availableQty && ($quoteItem->getQty()) > $availableQty) {
                $quoteItem->addErrorInfo(
                    'erro_info',
                    Data::ERROR_QTY,
                    __('We don\'t have as many option "' . $title . '" ticket as you requested. You can buy max '.$availableQty.' this option')
                );
                return ;
            }
        }
    }
}
