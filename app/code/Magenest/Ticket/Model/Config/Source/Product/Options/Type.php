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
namespace Magenest\Ticket\Model\Config\Source\Product\Options;

use Magento\Catalog\Model\Config\Source\Product\Options\Type as ProductOptionsType;

/**
 * Product option types mode source
 */
class Type extends ProductOptionsType
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $groups = [['value' => '', 'label' => __('-- Please select --')]];

        foreach ($this->_productOptionConfig->getAll() as $option) {
            $types = [];
            foreach ($option['types'] as $type) {
                if ($type['disabled']) {
                    continue;
                }
                $types[] = ['label' => __($type['label']), 'value' => $type['name']];
            }
            if (count($types)) {
                if ($option['label'] != 'Select') {
                    continue;
                }
                $groups[] = ['label' => __($option['label']), 'value' => $types, 'optgroup-name' => $option['label']];
            }
        }
        return $groups;
    }
}
