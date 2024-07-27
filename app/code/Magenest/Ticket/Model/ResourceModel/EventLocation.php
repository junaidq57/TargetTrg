<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Ticket\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Stdlib\DateTime as LibDateTime;

/**
 * Class EventLocation
 * @package Magenest\Ticket\Model\ResourceModel
 */
class EventLocation extends AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_ticket_event_location', 'location_id');
    }
}
