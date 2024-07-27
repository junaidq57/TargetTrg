<?php
/**
 * Created by PhpStorm.
 * User: gialam
 * Date: 7/26/2016
 * Time: 1:39 PM
 */

namespace Magenest\Ticket\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magenest\Ticket\Model\TicketFactory;
use Magenest\Ticket\Model\EventFactory;

/**
 * Class PlaceOrder
 * @package Magenest\Ticket\Observer
 */
class PlaceOrder implements ObserverInterface
{
    const XML_PATH_QTY = 'event_ticket/general_config/delete_qty';

    protected $_logger;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magenest\Ticket\Model\EventoptionDateFactory
     */
    protected $optionType;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * PlaceOrder constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Directory\Model\Currency $currency
     * @param \Magenest\Ticket\Model\EventoptionTypeFactory $eventoptionTypeFactory
     * @param TicketFactory $ticketFactory
     * @param EventFactory $eventFactory
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Cart $cart,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Directory\Model\Currency $currency,
        \Magenest\Ticket\Model\EventoptionTypeFactory $eventoptionTypeFactory,
        TicketFactory $ticketFactory,
        EventFactory $eventFactory,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_cart = $cart;
        $this->optionType = $eventoptionTypeFactory;
        $this->_logger = $logger;
        $this->_currency = $currency;
        $this->_request = $request;
        $this->_scopeConfig = $scopeConfig;
        $this->ticketFactory = $ticketFactory;
        $this->eventFactory = $eventFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderItem = $observer->getEvent()->getOrder();
        foreach ($orderItem->getAllItems() as $item) {
            $qty = $item->getQtyOrdered();
            $productType = $item->getProductType();
            $productOption = $item->getProductOptions();
            if ($productType == 'ticket') {
                $configQty = $this->_scopeConfig->getValue(self::XML_PATH_QTY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                if ($configQty == 1) {
                    foreach ($productOption as $option) {
                        $options = $option['additional_options'];
                        $eventoption = $this->optionType->create();
                        if (isset($options) && !empty($options)) {
                            if (isset($options['dropdow']) && !empty($options['dropdow'])) {
                                /** @var \Magenest\Ticket\Model\EventoptionType $model */
                                $model = $this->optionType->create()->getCollection()
                                    ->addFieldToFilter('product_id', $option['product'])
                                    ->addFieldToFilter('title', $options['dropdow']);
                                foreach ($model as $models) {
                                    $available_qty = $models->getAvailableQty() - $qty;
                                    $purcharsed_qty = $models->getPurcharsedQty() + $qty;
                                    $revenue = $models->getRevenue() + $models->getPrice()*$qty;
                                    $data = array(
                                        'available_qty' => $available_qty,
                                        'purcharsed_qty' => $purcharsed_qty,
                                        'revenue' => $revenue
                                    );
                                    $eventoption->load($models->getId())->addData($data)->save();
                                }
                            }
                            if (isset($options['radio']['radio_select']) && !empty($options['radio']['radio_select'])) {
                                $radioTitle = explode("_", $options['radio']['radio_select']);
                                /** @var \Magenest\Ticket\Model\EventoptionType $model */
                                $model = $this->optionType->create()->getCollection()
                                    ->addFieldToFilter('product_id', $option['product'])
                                    ->addFieldToFilter('title', $radioTitle[1]);
                                foreach ($model as $models) {
                                    $available_qty = $models->getAvailableQty() - $qty;
                                    $purcharsed_qty = $models->getPurcharsedQty() + $qty;
                                    $revenue = $models->getRevenue() + $models->getPrice()*$qty;
                                    $data = array(
                                        'available_qty' => $available_qty,
                                        'purcharsed_qty' => $purcharsed_qty,
                                        'revenue' => $revenue
                                    );
                                    $eventoption->load($models->getId())->addData($data)->save();
                                }
                            }
                            if (isset($options['checkbox']) && !empty($options['checkbox'])) {
                                $arrayCheck = json_decode($options['checkbox'], true);
                                $checkboxTitle = array_keys($arrayCheck);
                                foreach ($checkboxTitle as $title) {
                                    /** @var \Magenest\Ticket\Model\EventoptionType $model */
                                    $model = $this->optionType->create()->getCollection()
                                        ->addFieldToFilter('product_id', $option['product'])
                                        ->addFieldToFilter('title', $title);
                                    foreach ($model as $models) {
                                        $available_qty = $models->getAvailableQty() - $qty;
                                        $purcharsed_qty = $models->getPurcharsedQty() + $qty;
                                        $revenue = $models->getRevenue() + $models->getPrice()*$qty;
                                        $data = array(
                                            'available_qty' => $available_qty,
                                            'purcharsed_qty' => $purcharsed_qty,
                                            'revenue' => $revenue
                                        );
                                        $eventoption->load($models->getId())->addData($data)->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
