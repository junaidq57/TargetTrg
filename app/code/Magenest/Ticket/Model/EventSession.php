<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Ticket\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class EventSession
 * @package Magenest\Ticket\Model
 *
 * @method int getSessionId()
 * @method int getProductId()
 * @method int getEventDateId()
 * @method string getStartTime()
 * @method string getEndTime()
 * @method int getSessionIsEnabled()
 */
class EventSession extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'magenest_ticket_event_session';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'magenest_ticket_event_session';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_ticket_event_session';


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\Ticket\Model\ResourceModel\EventSession');
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
