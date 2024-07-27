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
namespace Magenest\Ticket\Model\ResourceModel\EventDate;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'date_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_ticket_event_date_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'event_date_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\Ticket\Model\EventDate', 'Magenest\Ticket\Model\ResourceModel\EventDate');
    }
}
