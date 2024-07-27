<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @category  Magenest
 */
namespace Magenest\Ticket\Model\Config\Source;

/**
 * Class Email
 * @package Magenest\Ticket\Model\Config\Source
 */
class Email implements \Magento\Framework\Option\ArrayInterface
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
                'label' => __('Send one email for all items with same type'),
                'value' => 'send_one_email'
            ],
            [
                'label' => __('Send multi email for all items with same type'),
                'value' => 'send_multi_email'
            ],
        ];
    }
}
