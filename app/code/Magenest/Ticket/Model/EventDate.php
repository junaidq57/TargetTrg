<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Ticket\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class EventDate
 * @package Magenest\Ticket\Model
 *
 * @method int getDateId()
 * @method int getProductId()
 * @method int getEventLocationId()
 * @method string getDateStart()
 * @method string getDateEnd()
 * @method int getDateIsEnabled()
 */
class EventDate extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'magenest_ticket_event_date';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'magenest_ticket_event_date';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_ticket_event_date';


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\Ticket\Model\ResourceModel\EventDate');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
