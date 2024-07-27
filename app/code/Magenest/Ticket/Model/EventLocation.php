<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Ticket\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class EventLocation
 *
 * @package Magenest\Ticket\Model
 *
 * @method int getLocationId()
 * @method int getProductId()
 * @method string getLocationTitle()
 * @method string getLocationDetail()
 * @method string getLocationIsEnabled()
 */
class EventLocation extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'magenest_ticket_event_location';

    /**
     * Product Type
     */
    const PRODUCT_TYPE = 'ticket';
    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'magenest_ticket_event_location';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_ticket_event_location';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\Ticket\Model\ResourceModel\EventLocation');
    }
}
