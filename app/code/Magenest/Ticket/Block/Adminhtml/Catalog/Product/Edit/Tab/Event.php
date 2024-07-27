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
namespace Magenest\Ticket\Block\Adminhtml\Catalog\Product\Edit\Tab;

use Magento\Backend\Block\Widget;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magenest\Ticket\Model\Event as EventModel;

class Event extends Widget implements TabInterface
{
    /**
     * Set Template
     *
     * @var string
     */
    protected $_template = 'event/edit/form.phtml';

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'tabs',
            $this->getLayout()->createBlock('Magenest\Ticket\Block\Adminhtml\Event\Edit\Tabs', 'tabs')
        );
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }

    /**
     * Return Tab label
     *
     * @return string
     * @api
     */
    public function getTabLabel()
    {
        return __("Event Booking");
    }

    /**
     * Return Tab title
     *
     * @return string
     * @api
     */
    public function getTabTitle()
    {
        return __("Event Booking");
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     * @api
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     * @api
     */
    public function isHidden()
    {
        if ($this->isEventProduct()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check Event Ticket Product
     *
     * @return bool
     */
    public function isEventProduct()
    {
        /** @var  $product  \Magento\Catalog\Model\Product */
        $product = $this->_coreRegistry->registry('current_product');

        $typeId = $product->getTypeId();
        if ($typeId == EventModel::PRODUCT_TYPE) {
            return true;
        } else {
            return false;
        }
    }
}
