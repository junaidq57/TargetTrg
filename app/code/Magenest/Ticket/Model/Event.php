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
namespace Magenest\Ticket\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Event
 *
 * @package Magenest\Ticket\Model
 *
 * @method string getEventName()
 * @method int getProductId()
 * @method string getStartTime()
 * @method string getEndTime()
 * @method string getLocation()
 * @method string getReminderBeforeDay()
 * @method int getPdfPageWidth()
 * @method int getPdfPageHeight()
 * @method int getEnableDateTime()
 * @method string getPdfCoordinates()
 * @method string getPdfBackground()
 * @method string getEmailConfig()
 * @method Event setUpdatedAt(string $time)
 * @method Event setCreatedAt(string $time)
 */
class Event extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'magenest_ticket_event';

    /**
     * Product Type
     */
    const PRODUCT_TYPE = 'ticket';
    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'magenest_ticket_event';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_ticket_event';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\Ticket\Model\ResourceModel\Event');
    }

    /**
     * get entity default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }

    /**
     * @param $id
     * @return $this
     */
    public function loadByProductId($id)
    {
        return $this->load($id, 'product_id');
    }
}
