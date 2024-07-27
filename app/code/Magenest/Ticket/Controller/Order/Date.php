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
namespace Magenest\Ticket\Controller\Order;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Magenest\Ticket\Model\EventDateFactory;
use Magenest\Ticket\Model\EventLocationFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Date
 * @package Magenest\Ticket\Controller\Order
 */
class Date extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EventDateFactory
     */
    protected $date;

    /**
     * @var EventLocationFactory
     */
    protected $location;

    /**
     * Date constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param LoggerInterface $loggerInterface
     * @param EventDateFactory $eventDateFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        LoggerInterface $loggerInterface,
        EventDateFactory $eventDateFactory,
        EventLocationFactory $eventLocationFactory
    ) {
        $this->location = $eventLocationFactory;
        $this->date = $eventDateFactory;
        $this->logger = $loggerInterface;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * View my ticket
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $locationId = $params['location_id'];
        $modelDate = $this->date->create()->getCollection()->addFieldToFilter('event_location_id', $locationId);
        $modelLocation = $this->location->create()->load($locationId);
        $arrayDate = [];
        if (!empty($modelDate->getData())) {
            foreach ($modelDate as $date) {
                $dateStart = substr($date->getDateStart(), 0, 10);
                $start = trim(date("d-m-Y", strtotime($dateStart)));
                if (!empty($date->getDateEnd())) {
                    $dateEnd = substr($date->getDateEnd(), 0, 10);
                    $end = trim(date("d-m-Y", strtotime($dateEnd)));
                    $dateStr= 'From '.$start.' to '.$end;
                } else {
                    $dateStr= $start;
                }

                $array = [
                    'date_id' => $date->getDateId(),
                    'date'=> $dateStr,
                ];
                $arrayDate [] = $array;
            }
        }
        $array1['date_data'] = $arrayDate;
        $array2['location_data'] = $modelLocation->getLocationDetail();
        $arrayResult = array_merge($array1, $array2);
        $resultArray = json_encode($arrayResult);
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $resultJson->setData($resultArray);
        return $resultJson;
    }
}
