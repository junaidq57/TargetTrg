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
 * Class Eventoption
 * @package Magenest\Ticket\Model
 *
 * @method string getOptionTitle()
 * @method string getOptionInputType()
 * @method int getIsRequired()
 * @method string getStoreId()
 * @method int getEventId()
 *  @method int getProductId()
 *  @method int getOptionId()
 */
class Eventoption extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'magenest_ticket_eventoption';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'magenest_ticket_eventoption';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_ticket_eventoption';


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\Ticket\Model\ResourceModel\Eventoption');
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
     * Load by Option Type Id
     *
     * @param $id
     * @return $this
     */
//    public function loadByOptionTypeId($id)
//    {
//        return $this->load($id, 'option_type_id');
//    }
    /**
     * @param $id
     * @return $this
     */
    public function loadBySku($sku)
    {
        return $this->load($sku, 'sku');
    }
}
