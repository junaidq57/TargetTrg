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
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Ticket
 *
 * @package Magenest\Ticket\Model\ResourceModel
 */
class Ticket extends AbstractDb
{
    /**
     * Date model
     *
     * @var DateTime
     */
    protected $_date;

    /**
     * constructor
     *
     * @param DateTime $date
     * @param Context $context
     */
    public function __construct(
        DateTime $date,
        Context $context
    ) {
    
        $this->_date = $date;
        parent::__construct($context);
    }


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_ticket_ticket', 'ticket_id');
    }

    /**
     * before save callback
     *
     * @param AbstractModel|\Magenest\Ticket\Model\Ticket $object
     * @return $this
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setUpdatedAt($this->_date->date());
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->_date->date());
        }
        return parent::_beforeSave($object);
    }
}
