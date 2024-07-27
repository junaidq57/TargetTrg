<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Ticket\Model\Config\Source;

/**
 * Class ChangeQty
 * @package Magenest\Ticket\Model\Config\Source
 */
class ChangeQty implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Change quantity of ticket type after order placed'),
                'value' => 1
            ],
            [
                'label' => __('Change quantity of ticket type after order invoiced'),
                'value' => 2
            ],
        ];
    }
}
