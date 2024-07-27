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
namespace Magenest\Ticket\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magenest\Ticket\Model\EventFactory;
use Magenest\Ticket\Model\TicketFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Checkout default helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Event extends AbstractHelper
{

    const XML_PATH_TICKET_PATTERN_CODE = 'event_ticket/general_config/pattern_code';
    /**
     *
     * @var \Magenest\Ticket\Model\EventFactory
     */
    protected $_eventFactory;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Event constructor.
     * @param Context $context
     * @param EventFactory $eventFactory
     * @param TicketFactory $ticketFactory
     */
    public function __construct(
        Context $context,
        EventFactory $eventFactory,
        TicketFactory $ticketFactory
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $this->scopeConfig;
        $this->_eventFactory = $eventFactory;
        $this->_ticketFactory = $ticketFactory;
    }

    /**
     * Generate code
     *
     * @return mixed
     */
    public function generateCode()
    {
        $gen_arr = [];

        $pattern = $this->_scopeConfig->getValue(self::XML_PATH_TICKET_PATTERN_CODE, ScopeInterface::SCOPE_STORE);
        if (!$pattern) {
            $pattern = '[A2][N1][A2]Magenest[N1][A1]';
        }

        preg_match_all("/\[[AN][.*\d]*\]/", $pattern, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $delegate = substr($match [0], 1, 1);
            $length = substr($match [0], 2, strlen($match [0]) - 3);
            $gen = '';
            if ($delegate == 'A') {
                $gen = $this->generateString($length);
            } elseif ($delegate == 'N') {
                $gen = $this->generateNum($length);
            }

            $gen_arr [] = $gen;
        }
        foreach ($gen_arr as $g) {
            $pattern = preg_replace('/\[[AN][.*\d]*\]/', $g, $pattern, 1);
        }
        return $pattern;
    }

    /**
     * Generate String
     *
     * @param $length
     * @return string
     */
    public function generateString($length)
    {
        if ($length == 0 || $length == null || $length == '') {
            $length = 5;
        }
        $c = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $rand = '';
        for ($i = 0; $i < $length; $i ++) {
            $rand .= $c [rand(0, 51)];
        }

        return $rand;
    }

    /**
     * Generate Number
     *
     * @param $length
     * @return string
     */
    public function generateNum($length)
    {
        if ($length == 0 || $length == null || $length == '') {
            $length = 5;
        }
        $c = "0123456789";
        $rand = '';
        for ($i = 0; $i < $length; $i ++) {
            $rand .= $c [rand(0, 9)];
        }
        return $rand;
    }

    /**
     * Check Event
     *
     * @param $id
     * @return bool
     */
    public function isEvent($id)
    {
        $model = $this->_eventFactory->create();
        $collection = $model->getCollection()->addFilter('product_id', $id)->addFilter('enable', 1, 'and');
        if ($collection->getSize() > 0) {
            return $collection->getFirstItem()->getId();
        }
        return false;
    }

    /**
     * Get Ticket
     *
     * @param $id
     * @return bool
     */
    public function getTicket($id)
    {
        $model = $this->_ticketFactory->create();
        $collection = $model->getCollection()->addFilter('order_item_id', $id);
        if ($collection->getSize() > 0) {
            return $collection->getFirstItem();
        }
        return false;
    }
}
