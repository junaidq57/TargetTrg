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
namespace Magenest\Ticket\Controller\Adminhtml\Event;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magenest\Ticket\Controller\Adminhtml\Event as EventController;

/**
 * Class ExportAttendeesCsv
 * @package Magenest\Ticket\Controller\Adminhtml\Event
 */
class ExportAttendeesCsv extends EventController
{
    /**
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Exception
     */
    public function execute()
    {
        $fileName = 'attendees.csv';
        $id = (int)$this->getRequest()->getParam('id');
        $model = $this->_eventFactory->create();
        $this->_coreRegistry->register('magenest_ticket_event_attendees', $model->loadByProductId($id));
        $grid = $this->_view->getLayout()->createBlock('Magenest\Ticket\Block\Adminhtml\Event\Edit\Tab\Attendees');
        return $this->_fileFactory->create(
            $fileName,
            $grid->getCsvFile(),
            DirectoryList::VAR_DIR
        );
    }
}
