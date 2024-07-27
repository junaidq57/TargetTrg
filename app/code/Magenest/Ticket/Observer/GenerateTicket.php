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

use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\Ticket\Model\TicketFactory;
use Magenest\Ticket\Model\EventoptionFactory;
use Magenest\Ticket\Model\EventoptionTypeFactory;
use Magenest\Ticket\Model\EventFactory;
use Magenest\Ticket\Helper\Event as HelperEvent;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magenest\Ticket\Model\Event;
use Magenest\Ticket\Helper\Information;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class GenerateTicket
 * @package Magenest\Ticket\Observer
 */
class GenerateTicket implements ObserverInterface
{

    /**
     * email config
     */
    const XML_PATH_EMAIL = 'event_ticket/email_config/email';
    /**
     * qty config
     */
    const XML_PATH_QTY = 'event_ticket/general_config/delete_qty';
    /**
     * @var TicketFactory
     */
    protected $_ticketFactory;

    /**
     * @var EventoptionFactory
     */
    protected $_eventoptionFactory;

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
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Information
     */
    protected $information;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * GenerateTicket constructor.
     * @param TicketFactory $ticketFactory
     * @param EventoptionFactory $eventoptionFactory
     * @param EventoptionTypeFactory $optionTypeFactory
     * @param \Psr\Log\LoggerInterface $loggerInterface
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param EventFactory $eventFactory
     * @param HelperEvent $helperEvent
     * @param Information $information
     */
    public function __construct(
        TicketFactory $ticketFactory,
        EventoptionFactory $eventoptionFactory,
        EventoptionTypeFactory $optionTypeFactory,
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        EventFactory $eventFactory,
        HelperEvent $helperEvent,
        Information $information,
        Json $serializer = null
    ) {
        $this->_scopeConfig = $scopeConfigInterface;
        $this->optionType = $optionTypeFactory;
        $this->logger = $loggerInterface;
        $this->_ticketFactory = $ticketFactory;
        $this->_eventoptionFactory = $eventoptionFactory;
        $this->_eventFactory = $eventFactory;
        $this->_helperEvent = $helperEvent;
        $this->information = $information;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
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
        $buyInfo = $orderItem->getBuyRequest();
        $options = $buyInfo->getAdditionalOptions();
        $event = $this->_eventFactory->create()->loadByProductId($orderItem->getProductId());
        if ($event-> getId() && $productType == Event::PRODUCT_TYPE && $orderItem->getStatusId() == OrderItem::STATUS_INVOICED) {
            if (!$this->_helperEvent->getTicket($orderItem->getId())) {
                /** @var \Magento\Sales\Model\Order $order */
                $order = $orderItem->getOrder();
                $qty = $orderItem->getQtyOrdered();
                $email = $order->getCustomerEmail();
                $firstname = $order->getCustomerFirstname();
                $lastname = $order->getCustomerLastname();
                $customerId = $order->getCustomerId();
                $customerName = $firstname . " " . $lastname;

                if (!$customerId) {
                    $customerName = 'Guest';
                }

                $optionDropdown = '';
                if (isset($options['dropdow']) && !empty($options['dropdow'])) {
                        $optionDropdown = $options['dropdow'].',';
                }
                $optionRadio = '';
                if (isset($options['radio']['radio_select']) && !empty($options['radio']['radio_select'])) {
                    $explode = explode("_", $options['radio']['radio_select']);
                    $optionRadio = $explode[1].', ';
                }
                $optionCheckbox = '';
                if (isset($options['checkbox']) && !empty($options['checkbox'])) {
                    $arrayCheck = json_decode($options['checkbox'], true);
                    $arrayKey = array_keys($arrayCheck);
                    $optionCheckbox = implode(", ", $arrayKey);
                }
                $optionInfo = $optionDropdown.$optionRadio.$optionCheckbox;

                $configEmail = $this->_scopeConfig->getValue(self::XML_PATH_EMAIL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                if ($configEmail == 'send_multi_email') {
                    $putQty = 1;
                    $number = $qty;
                } else {
                    $putQty = $qty;
                    $number = 1;
                }
                $arrayInformation = $this->information->getAll($options);

                $ticketData = [
                    'title' => $orderItem->getName(),
                    'event_id' => $event->getId(),
                    'product_id' => $orderItem->getProductId(),
                    'customer_name' => $customerName,
                    'customer_email' => $email,
                    'customer_id' => $customerId,
                    'order_item_id' => $orderItem->getId(),
                    'order_id' => $order->getId(),
                    'order_increment_id' => $order->getIncrementId(),
                    'note' => $optionInfo,
                    'information' => $this->serializer->serialize($arrayInformation),
                    'qty' => $putQty,
                    'status' => 1,
                ];
                for ($i = 0; $i < $number; $i++) {
                    /** @var array $ticketData */
                    $ticketData['code'] = $this->_helperEvent->generateCode();
                    $model = $this->_ticketFactory->create();
                    $model->setData($ticketData)->save();
                    $model->sendMail($model->getTicketId());
                }
                $configQty = $this->_scopeConfig->getValue(self::XML_PATH_QTY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                if ($configQty == 2) {
                    if (isset($options) && !empty($options)) {
                        if (isset($options['dropdow']) && !empty($options['dropdow'])) {
                            $this->saveOption($options['dropdow'], $orderItem->getProductId(), $qty);
                        }
                        if (isset($options['radio']['radio_select']) && !empty($options['radio']['radio_select'])) {
                            $radioTitle = explode("_", $options['radio']['radio_select']);
                            $this->saveOption($radioTitle[1], $orderItem->getProductId(), $qty);
                        }
                        if (isset($options['checkbox']) && !empty($options['checkbox'])) {
                            $arrayCheck = json_decode($options['checkbox'], true);
                            $checkboxTitle = array_keys($arrayCheck);
                            foreach ($checkboxTitle as $title) {
                                $this->saveOption($title, $orderItem->getProductId(), $qty);
                            }
                        }
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
            $availableQty = $models->getAvailableQty() - $qty;
            $purcharsedQty = $models->getPurcharsedQty() + $qty;
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
