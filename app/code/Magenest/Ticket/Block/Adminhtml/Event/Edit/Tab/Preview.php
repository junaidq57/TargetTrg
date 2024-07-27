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
namespace Magenest\Ticket\Block\Adminhtml\Event\Edit\Tab;

use Magento\Backend\Block\Widget;
use Magento\Framework\Registry;
use Magenest\Ticket\Model\EventFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\DataObject;

/**
 * Setting PDF template
 *
 * Class Settings
 * @package Magenest\Ticket\Block\Adminhtml\Event\Edit\Tab
 */
class Preview extends Widget
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Set Template
     *
     * @var string
     */
    protected $_template = 'event/preview.phtml';

    /**
     * @var EventFactory
     */
    protected $_evenFactory;

    /**
     * Preview constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param EventFactory $eventFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        EventFactory $eventFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_eventFactory = $eventFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get Event Model
     *
     * @return mixed
     */
    public function getEvent()
    {
        $product = $this->_coreRegistry->registry('current_product');
        $id = $product->getId();

        return $id;
    }

    /**
     * @param $productId
     * @return string
     */
    public function getSaveImage($productId)
    {
        return $this->getUrl('magenest_ticket/event/preview', ['id'=>$productId]);
    }
}
