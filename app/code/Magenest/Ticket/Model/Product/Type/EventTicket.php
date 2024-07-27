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
namespace Magenest\Ticket\Model\Product\Type;

use Magento\Catalog\Model\Product\Type\Virtual;

/**
 * Class EventTicket
 * @package Magenest\Ticket\Model\Product\Type
 */
class EventTicket extends Virtual
{
    /**
     * Check if product has options
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return boolean
     */
    public function hasOptions($product)
    {
        return true;
    }
}
