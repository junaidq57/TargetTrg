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
namespace Magenest\Ticket\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime as LibDateTime;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class EventoptionType
 * @package Magenest\Ticket\Model\ResourceModel
 */
class EventoptionType extends AbstractDb
{
    /**
     * Date time handler
     *
     * @var LibDateTime
     */
    protected $_dateTime;

    /**
     * Date model
     *
     * @var DateTime
     */
    protected $_date;

    /**
     * constructor
     *
     * @param LibDateTime $dateTime
     * @param DateTime $date
     * @param Context $context
     */
    public function __construct(
        LibDateTime $dateTime,
        DateTime $date,
        Context $context
    ) {

        $this->_dateTime = $dateTime;
        $this->_date     = $date;
        parent::__construct($context);
    }


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_ticket_eventoption_type', 'id');
    }
}
