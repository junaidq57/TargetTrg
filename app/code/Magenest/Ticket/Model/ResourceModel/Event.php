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

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime as LibDateTime;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Event extends AbstractDb
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
        $this->_init('magenest_ticket_event', 'event_id');
    }

    /**
     * before save callback
     *
     * @param AbstractModel|\Magenest\Ticket\Model\Event $object
     * @return $this
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setUpdatedAt($this->_date->date());
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->_date->date());
        }
        foreach (['start_time', 'end_time', 'reminder_before_day'] as $field) {
            $value = !$object->getData($field) ? null : $object->getData($field);
            $object->setData($field, $this->_dateTime->formatDate($value));
        }
        return parent::_beforeSave($object);
    }
}
