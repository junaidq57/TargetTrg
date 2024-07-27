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
namespace Magenest\Ticket\Observer\Backend\Layout;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\RequestInterface;
use Magenest\Ticket\Model\EventFactory;
use Magenest\Ticket\Model\Event;

/**
 * Class Tab check and add Layout to catalog_product_edit/catalog_product_new handle
 *
 * @package Magenest\Ticket\Observer\Backend\Layout
 */
class Tab implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var EventFactory
     */
    protected $_eventFactory;

    /**
     * @param RequestInterface $request
     * @param EventFactory $eventFactory
     */
    public function __construct(
        RequestInterface $request,
        EventFactory $eventFactory
    ) {
        $this->_request = $request;
        $this->_eventFactory = $eventFactory;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $params = $this->_request->getParams();
        if ($observer->getFullActionName() == 'catalog_product_edit' && !empty($params['id'])) {
            $model = $this->_eventFactory->create();
            $model->loadByProductId($params['id']);
            if (!$model->getId()) {
                return;
            }
        } elseif ($observer->getFullActionName() == 'catalog_product_new' && !empty($params['type'])) {
            if ($params['type'] != Event::PRODUCT_TYPE) {
                return;
            }
        } else {
            return;
        }

        /** @var  $layout \Magento\Framework\View\LayoutInterface */
        $layout = $observer->getEvent()->getLayout();
        $handler = 'magenest_ticket_event_edit';
        $layout->getUpdate()->addHandle($handler);
    }
}
