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
namespace Magenest\Ticket\Block\Adminhtml\Product\Edit\Tab\Options\Type;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Type\Select as ProductOptionsSelect;

class Select extends ProductOptionsSelect
{
    /**
     * @var string
     */
    protected $_template = 'catalog/product/edit/options/type/select.phtml';
}
