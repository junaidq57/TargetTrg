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

use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelperData;
use Magenest\Ticket\Model\EventFactory;
use Magenest\Ticket\Model\EventoptionFactory;
use Magenest\Ticket\Model\EventoptionTypeFactory;
use Magento\Framework\Registry;
use Magento\Framework\DataObject;

class Summary extends Extended
{
    /**
     * Count totals
     *
     * @var boolean
     */
    protected $_countTotals = true;

    /**
     * Totals
     *
     * @var \Magento\Framework\DataObject
     */
    protected $_varTotals;

    /**
     * @var EventoptionFactory
     */
    protected $_eventoptionFactory;

    /**
     * @var EventFactory
     */
    protected $_eventFactory;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var EventoptionTypeFactory
     */
    protected $typeFactory;

    /**
     * @param Context $context
     * @param BackendHelperData $backendHelper
     * @param EventoptionFactory $eventoptionFactory
     * @param EventFactory $eventFactory
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        BackendHelperData $backendHelper,
        EventoptionFactory $eventoptionFactory,
        EventoptionTypeFactory $typeFactory,
        EventFactory $eventFactory,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->typeFactory = $typeFactory;
        $this->_eventoptionFactory = $eventoptionFactory;
        $this->_eventFactory = $eventFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Get Totals
     *
     * @return \Magento\Framework\DataObject
     */
    public function getTotals()
    {
        $this->_varTotals = new DataObject;
        $fields = [
            'revenue' => 0,
            'available_qty' => 0,
            'purcharsed_qty' => 0,
            'qty' => 0,
        ];
        foreach ($this->getCollection() as $item) {
            foreach ($fields as $field => $value) {
                $fields[$field] += $item->getData($field);
            }
        }
        $fields['title']='Totals';
        $this->_varTotals->setData($fields);
        return $this->_varTotals;
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
        $model = $this->_eventFactory->create();
        $event = $model->loadByProductId($id);

        return $event;
    }

    /**
     * Prepare Collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $event = $this->getEvent();

        $collection = $this->typeFactory->create()->getCollection()->addFilter('event_option_id', $event->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index' => 'title',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'qty',
            [
                'header' => __('Initial Qty'),
                'index' => 'qty'
            ]
        );
        $this->addColumn(
            'available_qty',
            [
                'header' => __('Available Qty'),
                'index' => 'available_qty'
            ]
        );
        $this->addColumn(
            'purcharsed_qty',
            [
                'header' => __('Purcharsed Qty'),
                'index' => 'purcharsed_qty'
            ]
        );
        $this->addColumn(
            'revenue',
            ['header' => __('Revenue'),
                'index' => 'revenue'
            ]
        );
    }
}
