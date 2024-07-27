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
 * Class EventoptionType
 *
 * @package Magenest\Ticket\Model
 *
 * @method int getId()
 * @method int getEventOptionId()
 * @method int getProductId()
 * @method int getOptionId()
 * @method int getAvailableQty()
 * @method int getPurchasedQty()
 * @method double getRevenue()
 * @method int getQty()
 * @method int getPrice()
 * @method string getPriceType()
 * @method string getSortOrder()
 * @method string getTitle()
 * @method string getDescription()
 * @method string getTax()
 */
class EventoptionType extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'magenest_ticket_eventoption_type';
    
    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'magenest_ticket_eventoption_type';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_ticket_eventoption_type';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\Ticket\Model\ResourceModel\EventoptionType');
    }
}
