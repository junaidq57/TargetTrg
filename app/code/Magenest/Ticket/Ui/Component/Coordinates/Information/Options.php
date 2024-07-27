<?php

namespace Magenest\Ticket\Ui\Component\Coordinates\Information;

class Options implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Event Name'),
                'value' => 'event_name'
            ],
            [
                'label' => __('Location Title'),
                'value' => 'location_title'
            ],
            [
                'label' => __('Location Detail'),
                'value' => 'location_detail'
            ],
            [
                'label' => __('QR Code'),
                'value' => 'qr_code'
            ],
            [
                'label' => __('Bar Code'),
                'value' => 'bar_code'
            ],
            [
                'label' => __('Code'),
                'value' => 'code'
            ],
            [
                'label' => __('Ticket Type'),
                'value' => 'type'
            ],
            [
                'label' => __('Date'),
                'value' => 'date'
            ],
            [
                'label' => __('Start Time'),
                'value' => 'start_time'
            ],
            [
                'label' => __('End Time'),
                'value' => 'end_time'
            ],
            [
                'label' => __('Customer Name'),
                'value' => 'customer_name'
            ],
            [
                'label' => __('Customer Email'),
                'value' => 'customer_email'
            ],
            [
                'label' => __('Order #'),
                'value' => 'order_increment_id'
            ],
            [
                'label' => __('Quantity'),
                'value' => 'qty'
            ]
        ];
    }
}
