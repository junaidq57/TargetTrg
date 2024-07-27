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
namespace Magenest\Ticket\Block\Adminhtml\Event\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

/**
 * Class AssignAttendees
 * @package Magenest\Ticket\Block\Adminhtml\Event\Edit
 */
class AssignAttendees extends WidgetTabs
{
    /**
     * construct
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('event_attendees_tabs');
        $this->setDestElementId('event_attendees_tab_content');
    }

    /**
     * Prepare layout
     *
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _prepareLayout()
    {
        $this->addTab(
            'adttendees',
            [
                'content' => $this->getLayout()->createBlock(
                    'Magenest\Ticket\Block\Adminhtml\Event\Edit\Tab\Attendees',
                    'event.ticket.attendees'
                )->toHtml()
            ]
        );
        return parent::_prepareLayout();
    }
}
