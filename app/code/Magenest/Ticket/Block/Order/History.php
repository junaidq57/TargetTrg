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
namespace Magenest\Ticket\Block\Order;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magenest\Ticket\Model\ResourceModel\Ticket\CollectionFactory as TicketCollectionFactory;
use Magenest\Ticket\Helper\Information;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class History
 *
 * @package Magenest\Ticket\Block\Order
 */
class History extends Template
{
    /**
     * Google Map API key
     */
    const XML_PATH_GOOGLE_MAP_API_KEY = 'event_ticket/general_config/google_api_key';

    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'order/history.phtml';

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_ticketCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var string
     */
    protected $tickets;

    /**
     * @var Information
     */
    protected $information;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * History constructor.
     * @param Context $context
     * @param TicketCollectionFactory $ticketCollectionFactory
     * @param CustomerSession $customerSession
     * @param Information $information
     * @param array $data
     */
    public function __construct(
        Context $context,
        TicketCollectionFactory $ticketCollectionFactory,
        CustomerSession $customerSession,
        Information $information,
        Json $serializer = null,
        array $data = []
    ) {
        $this->information = $information;
        $this->_ticketCollectionFactory = $ticketCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Tickets'));
    }

    /**
     * Get Ticket Collection
     *
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getTickets()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->tickets) {
            $this->tickets = $this->_ticketCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'customer_id',
                $customerId
            )->setOrder(
                'main_table.event_id',
                'desc'
            )->setOrder(
                'main_table.created_at',
                'desc'
            );
            $this->tickets->getSelect()->joinLeft(
                ['event' => 'magenest_ticket_event'],
                'main_table.event_id = event.event_id ',
                ['*']
            );
        }
        return $this->tickets;
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getTickets()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'ticket.order.history.pager'
            )->setCollection(
                $this->getTickets()
            );
            $this->setChild('pager', $pager);
            $this->getTickets()->load();
        }
        return $this;
    }

    /**
     * Get Google Map Api key
     *
     * @return mixed
     */
    public function getGoogleApiKey()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_GOOGLE_MAP_API_KEY);
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param object $ticket
     * @return string
     */
    public function getViewUrl($ticket)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $ticket->getOrderId()]);
    }

    /**
     * @param object $ticket
     * @return string
     */
    public function getPrintTicketUrl($ticket)
    {
        return $this->getUrl('ticket/order/pdfticket', ['ticket_id' => $ticket->getId()]);
    }

    /**
     * @param $option
     * @return array
     */
    public function getDataTicket($option)
    {
        $array = $this->serializer->unserialize($option);
        $data = $this->information->getDataTicket($array);

        return $data;
    }
}
