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

namespace Magenest\Ticket\Model\ProductOptions;

use Magento\Catalog\Model\ProductOptions\ConfigInterface;
use Magento\Framework\Config\Data as ConfigData;
use Magento\Catalog\Model\ProductOptions\Config\Reader;
use Magento\Framework\Config\CacheInterface;

/**
 * Class Config
 * @package Magenest\Ticket\Model\ProductOptions
 */
class Config extends ConfigData implements ConfigInterface
{
    /**
     * @param \Magento\Catalog\Model\ProductOptions\Config\Reader $reader
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Reader $reader,
        CacheInterface $cache,
        $cacheId = 'product_options_config'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }

    /**
     * Get configuration of product type by name
     *
     * @param string $name
     * @return array
     */
    public function getOption($name)
    {
        return $this->get($name, []);
    }

    /**
     * Get configuration of all registered product types
     *
     * @return array
     */
    public function getAll()
    {
        return [[
            'name' => 'select',
            'renderer' => 'Magenest\Ticket\Block\Adminhtml\Product\Edit\Tab\Options\Type\Select'
        ]];
    }
}
