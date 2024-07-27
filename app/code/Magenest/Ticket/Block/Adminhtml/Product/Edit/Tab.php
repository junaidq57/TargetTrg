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
namespace Magenest\Ticket\Block\Adminhtml\Product\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tab as WidgetTab;

/**
 * Class Tab
 *
 * @method Tab setCanShow(boolean $status)
 *
 * @package Magenest\Ticket\Block\Adminhtml\Product\Edit
 */
class Tab extends WidgetTab
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        if (!$this->_request->getParam('id')) {
            $this->setCanShow(false);
        }
    }
}
