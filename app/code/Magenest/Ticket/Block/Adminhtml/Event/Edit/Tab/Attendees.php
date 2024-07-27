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

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data  as BackendHelper;
use Magenest\Ticket\Model\ResourceModel\Ticket\CollectionFactory as TicketCollectionFactory;
use Magenest\Ticket\Model\EventFactory;
use Magenest\Ticket\Model\Event;
use Magento\Framework\Registry;
use Magenest\Ticket\Model\Ticket\Attribute\Source\Status;

/**
 * Class Attendees
 *
 * @package Magenest\Ticket\Block\Adminhtml\Event\Edit\Tab
 */
class Attendees extends Extended
{
    /**
     * @var TicketCollectionFactory
     */
    protected $_ticketCollectionFactory;

    /**
     * @var EventFactory
     */
    protected $_eventFactory;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var Status
     */
    protected $_status;

    /**
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param TicketCollectionFactory $ticketCollectionFactory
     * @param EventFactory $eventFactory
     * @param Registry $coreRegistry
     * @param Status $status
     * @param array $data
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        TicketCollectionFactory $ticketCollectionFactory,
        EventFactory $eventFactory,
        Registry $coreRegistry,
        Status $status,
        array $data = []
    ) {
        $this->_ticketCollectionFactory = $ticketCollectionFactory;
        $this->_eventFactory = $eventFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_status = $status;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Get Event Model
     *
     * @return Event
     */
    public function getEvent()
    {
        $product = $this->_coreRegistry->registry('current_product');
        if ($product) {
            $id = $product->getId();
            $model = $this->_eventFactory->create();
            $event = $model->loadByProductId($id);

            return $event;
        } else {
            return $this->_coreRegistry->registry('magenest_ticket_event_attendees');
        }
    }

    /**
     * Prepare collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $event = $this->getEvent();
        $collection = $this->_ticketCollectionFactory->create()->addFilter('event_id', $event->getId());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare Columns
     *
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'ticket_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'ticket_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn('code', ['header' => __('Code'), 'index' => 'code']);
        $this->addColumn('qty', ['header' => __('Qty'), 'index' => 'qty']);
        $this->addColumn('note', ['header' => __('Option'), 'index' => 'note']);
        $this->addColumn('customer_name', ['header' => __('Customer Name'), 'index' => 'customer_name']);
        $this->addColumn('customer_email', ['header' => __('Customer Email'), 'index' => 'customer_email']);
        $this->addColumn('order_increment_id', ['header' => __('Order #'), 'index' => 'order_increment_id']);
        $this->addColumn('created_at', ['header' => __('Created At'), 'index' => 'created_at']);
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type'=> 'options',
                'options' => $this->_status->getOptionArray()
            ]
        );
        $this->addExportType('magenest_ticket/Event/exportAttendeesCsv', __('CSV'));
        return parent::_prepareColumns();
    }
}
