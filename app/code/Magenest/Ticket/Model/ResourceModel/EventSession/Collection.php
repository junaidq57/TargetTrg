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
namespace Magenest\Ticket\Model\ResourceModel\EventSession;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'session_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_ticket_event_session_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'event_session_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\Ticket\Model\EventSession', 'Magenest\Ticket\Model\ResourceModel\EventSession');
    }

    /**
     * @return $this
     */
    public function getSessionInfo($producId = 0)
    {
        if ($producId) {
            $this->getSelect()->joinLeft(
                ['ticket_date' => 'magenest_ticket_event_date'],
                'main_table.event_date_id = ticket_date.date_id ',
                ['*']
            )
                ->joinLeft(
                    ['ticket_location' => 'magenest_ticket_event_location'],
                    'ticket_date.event_location_id = ticket_location.location_id ',
                    ['*']
                )
                ->where("main_table.product_id =?", $producId)->group(['main_table.session_id']);
        } else {
            $this->getSelect()->joinLeft(
                ['ticket_date' => 'magenest_ticket_event_date'],
                'main_table.event_date_id = ticket_date.date_id ',
                ['*']
            )->group(['main_table.session_id']);
        }

        return $this;
    }
}
