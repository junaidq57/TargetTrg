<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Ticket\Helper;

use Magenest\Ticket\Model\EventLocationFactory;
use Magenest\Ticket\Model\EventDateFactory;
use Magenest\Ticket\Model\EventSessionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class Information
 *
 * @package Magenest\Ticket\Helper
 */
class Information extends AbstractHelper
{
    public function __construct(
        Context $context,
        EventLocationFactory $locationFactory,
        EventDateFactory $dateFactory,
        EventSessionFactory $sessionFactory
    ) {
        $this->location = $locationFactory;
        $this->date = $dateFactory;
        $this->session = $sessionFactory;
        parent::__construct($context);
    }

    /**
     * @param $options
     * @return array
     */
    public function getAll($options)
    {
        $locationId = '';
        $dateId = '';
        $sessionId = '';
        if (isset($options['single']) && !empty($options['single'])) {
            if (!empty($options['single']['ticket_location'])) {
                $locationId = $options['single']['ticket_location'];
            }
            if (!empty($options['single']['ticket_date'])) {
                $dateId = $options['single']['ticket_date'];
            }
            if (!empty($options['single']['ticket_session'])) {
                $sessionId = $options['single']['ticket_session'];
            }
        } else {
            if (isset($options['ticket_location']) && !empty($options['ticket_location'])) {
                $locationId = $options['ticket_location'];
            }

            if (isset($options['ticket_date']) && !empty($options['ticket_date'])) {
                $dateId = $options['ticket_date'];
            }

            if (isset($options['ticket_session']) && !empty($options['ticket_session'])) {
                $sessionId = $options['ticket_session'];
            }
        }

        return $array = [
            'location' => $locationId,
            'date' => $dateId,
            'session' => $sessionId
        ];
    }

    /**
     * @param $array
     * @return array
     */
    public function getDataTicket($array)
    {
        $locationTitle = '';
        $locationDetail = '';
        $dateStr= '';
        $startTime = '';
        $endTime = '';
        if (!empty($array['location'])) {
            $location = $this->location->create()->load($array['location']);
            $locationTitle = $location->getLocationTitle();
            $locationDetail = $location->getLocationDetail();
        }
        if (!empty($array['date'])) {
            $date = $this->date->create()->load($array['date']);
            $start = 'no limit';
            $end = 'no limit';

            if (!empty($date->getDateStart())) {
                $dateStart = substr($date->getDateStart(), 0, 10);
                $start = trim(date("d-m-Y", strtotime($dateStart)));
            }
            if (!empty($date->getDateEnd())) {
                $dateEnd = substr($date->getDateEnd(), 0, 10);
                $end = trim(date("d-m-Y", strtotime($dateEnd)));
            }
            $dateStr= 'From '.$start.' To '.$end;
        }
        if (!empty($array['session'])) {
            $session = $this->session->create()->load($array['session']);
            if (!empty($session->getStartTime())) {
                $startTime = 'Start: '.$session->getStartTime().' ';
            }
            if (!empty($session->getEndTime())) {
                $endTime = 'End: '.$session->getEndTime();
            }
        }

        return $array = [
            'location_title' => $locationTitle,
            'location_detail' => $locationDetail,
            'date' => $dateStr,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
    }
}
