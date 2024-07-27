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
namespace Magenest\Ticket\Observer\Backend;

use Magenest\Ticket\Model\Event;
use Magenest\Ticket\Model\EventDateFactory;
use Magenest\Ticket\Model\EventFactory;
use Magenest\Ticket\Model\EventLocationFactory;
use Magenest\Ticket\Model\EventoptionFactory;
use Magenest\Ticket\Model\EventoptionTypeFactory;
use Magenest\Ticket\Model\EventSessionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class EventTicketObserver
 *
 * @method Observer getProduct()
 */
class EventTicketObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magenest\Ticket\Model\EventFactory
     */
    protected $_eventFactory;

    /**
     * @var \Magenest\Ticket\Model\EventoptionFactory
     */
    protected $_eventoptionFactory;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var Filesystem
     */
    protected $_filesystem;

    /**
     * @var UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManage;

    /**
     * @var EventoptionTypeFactory
     */
    protected $eventTypeFactory;

    /**
     * @var EventLocationFactory
     */
    protected $location;

    /**
     * @var EventDateFactory
     */
    protected $date;

    /**
     * @var EventSessionFactory
     */
    protected $session;

    /**
     * EventTicketObserver constructor.
     * @param RequestInterface $request
     * @param EventFactory $eventFactory
     * @param EventoptionFactory $eventoptionFactory
     * @param EventoptionTypeFactory $eventoptionTypeFactory
     * @param Filesystem $filesystem
     * @param UploaderFactory $fileUploaderFactory
     * @param StoreManagerInterface $storeManagerInterface
     * @param EventLocationFactory $eventLocationFactory
     * @param EventDateFactory $eventDateFactory
     * @param EventSessionFactory $eventSessionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $request,
        EventFactory $eventFactory,
        EventoptionFactory $eventoptionFactory,
        EventoptionTypeFactory $eventoptionTypeFactory,
        Filesystem $filesystem,
        UploaderFactory $fileUploaderFactory,
        StoreManagerInterface $storeManagerInterface,
        EventLocationFactory $eventLocationFactory,
        EventDateFactory $eventDateFactory,
        EventSessionFactory $eventSessionFactory,
        LoggerInterface $logger
    ) {
        $this->eventTypeFactory = $eventoptionTypeFactory;
        $this->storeManage = $storeManagerInterface;
        $this->_request = $request;
        $this->_eventFactory = $eventFactory;
        $this->_eventoptionFactory = $eventoptionFactory;
        $this->_filesystem = $filesystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->location = $eventLocationFactory;
        $this->date = $eventDateFactory;
        $this->session = $eventSessionFactory;
        $this->_logger = $logger;
    }

    /**
     * Set new customer group to all his quotes
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getProduct();
        $productId = $product->getId();
        $status = $product->getStatus();
        $productTypeId = $product->getTypeId();
        $params = $this->_request->getParams();
        if (!empty($params['event']) && $productTypeId == Event::PRODUCT_TYPE) {
            $data = $params['event'];

            $model = $this->_eventFactory->create()->load($productId, 'product_id');

            $data['product_id'] = $productId;
            $data['event_name'] = $params['product']['name'];
            $data['pdf_coordinates'] = serialize([]);
            $result = [];
            if (isset($params['event']['pdftemplate']['coordinates'])) {
                $coordinates = $params['event']['pdftemplate']['coordinates'];
                if (isset($coordinates) && !empty($coordinates)) {
                    $size = sizeof($coordinates);
                    for ($i = 0; $i<$size; $i++) {
                        if (empty($coordinates[$i]['is_delete'])) {
                            $data['pdf_coordinates'] = $coordinates[$i];
                            $result[] = $data['pdf_coordinates'];
                        }
                    }
                    $data['pdf_coordinates'] = serialize($result);
                    unset($coordinates);
                }
            }
            $background = [];
            if (isset($params['event']['pdftemplate']['pdf_background'])) {
                $background = $params['event']['pdftemplate']['pdf_background'];
            }
            $data['pdf_background'] = serialize([]);
            if (isset($background) && !empty($background)) {
                $data['pdf_background'] = serialize($background);
            }
            $data['enable_date_time'] = $params['event']['enable_date_time'];
            $data['pdf_page_height'] = $params['event']['pdftemplate']['page_height'];
            $data['pdf_page_width'] = $params['event']['pdftemplate']['page_width'];
            $data['enable'] = $status;
            $data['email_config'] = $params['event']['emailtemplate']['emailtemplate_config'];
            $model->addData($data);
            $model->save();
            $data['event_id'] = $model->getId();
            if (isset($params['event']['event_schedule']) && !empty($params['event']['event_schedule'])) {
                $this->saveSchedule($params['event']['event_schedule'], $productId);
            } else {
                $this->location->create()->getCollection()->addFieldToFilter('product_id', $model->getProductId())->walk('delete');
                $this->date->create()->getCollection()->addFieldToFilter('product_id', $model->getProductId())->walk('delete');
            }
            if (!empty($params['event']['event_options'])) {
                $this->saveEventOption($params['event']['event_options'], $data);
            } else {
                $this->deleteAllOptions($model->getProductId());
            }
        }

        return;
    }

    private function deleteAllOptions($productId)
    {
        $this->_eventoptionFactory->create()->getCollection()->addFieldToFilter('product_id', $productId)->walk('delete');
        $this->eventTypeFactory->create()->getCollection()->addFieldToFilter('product_id', $productId)->walk('delete');
    }

    /**
     * @param $schedule
     * @param $productId
     */
    public function saveSchedule($schedule, $productId)
    {
        foreach ($schedule as $schedules) {
            if (!isset($once)) {
                $once = true;
            }
            if (array_key_exists('delete_option', $schedules)) {
                $modelLocation = $this->location->create()->load($schedules['id_location']);
                $modelDate = $this->date->create()->getCollection()->addFilter('event_location_id', $schedules['id_location']);
                foreach ($modelDate as $date) {
                    $dateId = $date->getId();
                    $modelSession = $this->session->create()->getCollection()->addFilter('event_date_id', $dateId);
                    foreach ($modelSession as $session) {
                        $session->delete();
                    }
                    $date->delete();
                }
                $modelLocation->delete();
                continue;
            } else {
                if ($once) {
                    $deletedSchedules = $this->getDeletedItem('schedule', $schedule, $productId);
                    foreach ($deletedSchedules as $schedulesItem) {
                        $this->location->create()->load($schedulesItem)->delete();
                        $this->removeSchedulesComponent($schedulesItem, $productId);
                    }
                    $once = false;
                }
            }
            $modelLocation = $this->location->create();
            if (isset($schedules['id_location'])) {
                $modelLocation->load($schedules['id_location']);
            }
            $data['product_id'] = $productId;
            $data['location_title'] = $schedules['location_title'];
            $data['location_detail'] = $schedules['location_detail'];
            $data['location_is_enabled'] = $schedules['is_enabled'];
            $modelLocation->addData($data)->save();
            $idLocation  = $modelLocation->getId();
            if (isset($schedules['row_day']) && !empty($schedules['row_day']) && $idLocation) {
                $this->saveDate($schedules['row_day'], $idLocation, $productId);
            } else {
                $this->date->create()->getCollection()
                    ->addFieldToFilter('event_location_id', $idLocation)->addFieldToFilter('product_id', $productId)->walk('delete');
            }
        }
    }

    /**
     * @param $dateData
     * @param $idLocation
     * @param $productId
     */
    public function saveDate($dateData, $idLocation, $productId)
    {
        foreach ($dateData as $dateInfo) {
            if (!isset($once)) {
                $once = true;
            }
            if (array_key_exists('delete_day', $dateInfo)) {
                $modelDate = $this->date->create()->load($dateInfo['id_date']);
                $modelSession = $this->session->create()->getCollection()->addFilter('event_date_id', $dateInfo['id_date']);
                foreach ($modelSession as $session) {
                    $session->delete();
                }
                $modelDate->delete();
                continue;
            } else {
                if ($once) {
                    $deletedDates = $this->getDeletedItem('date', $dateData, $productId, $idLocation);
                    foreach ($deletedDates as $dates) {
                        $this->date->create()->load($dates)->delete();
                        $this->removeDateComponents($dates, $productId);
                    }
                    $once = false;
                }
            }
            $modelDate = $this->date->create();
            if (!empty($dateInfo['id_date'])) {
                $modelDate->load($dateInfo['id_date']);
            }
            $data['product_id'] = $productId;
            $data['event_location_id'] = $idLocation;
            if ($dateInfo['time_date_start']) {
                $data['date_start'] = date('Y-m-d H:i:s', strtotime($dateInfo['time_date_start']));
            } else {
                continue;
            }
            if ($dateInfo['time_date_end']) {
                $data['date_end'] = date('Y-m-d H:i:s', strtotime($dateInfo['time_date_end']));
            } else {
                $data['date_end'] = null;
            }
            $modelDate->addData($data)->save();
            $idDate = $modelDate->getId();
            if (isset($dateInfo['row_session']) && !empty($dateInfo['row_session']) && $idDate) {
                $this->saveSession($dateInfo['row_session'], $idDate, $productId);
            } else {
                $this->session->create()->getCollection()
                    ->addFieldToFilter('event_date_id', $idDate)->addFieldToFilter('product_id', $productId)->walk('delete');
            }
        }
    }

    /**
     * @param $dateSession
     * @param $idDate
     * @param $productId
     */
    public function saveSession($dateSession, $idDate, $productId)
    {
        foreach ($dateSession as $session) {
            if (!isset($once)) {
                $once = true;
            }
            if (array_key_exists('delete_session', $session)) {
                $modelSession = $this->session->create()->load($session['id_session']);
                $modelSession->delete();
                continue;
            } else {
                if ($once) {
                    $deletedSessions = $this->getDeletedItem('session', $dateSession, $productId, null, $idDate);
                    foreach ($deletedSessions as $sessions) {
                        $this->session->create()->load($sessions)->delete();
                    }
                    $once = false;
                }
            }
            $modelSession = $this->session->create();
            if (!empty($session['id_session'])) {
                $modelSession->load($session['id_session']);
            }
            $data['product_id'] = $productId;
            $data['event_date_id'] = $idDate;
            $data['start_time'] = $session['start_time'];
            $data['end_time'] = $session['end_time'];
            $modelSession->addData($data)->save();
        }
    }

    /**
     * Save Option
     *
     * @param $options
     * @param $data
     */
    public function saveEventOption($options, $data)
    {
        $i = 0;
        foreach($options as $option) {
            if (!isset($once)) {
                $once = true;
            }
            if (isset($option['is_delete']) && $option['is_delete'] == 1) {
                $modelEventoption = $this->_eventoptionFactory->create()
                    ->getCollection()
                    ->addFilter('event_id', $data['event_id'])
                    ->addFilter('option_id', $option['record_id']);
                $modelEventoptionType = $this->eventTypeFactory->create()
                    ->getCollection()
                    ->addFilter('event_option_id', $data['event_id'])
                    ->addFilter('option_id', $option['record_id']);
                /** @var \Magenest\Ticket\Model\Eventoption $collection */
                foreach($modelEventoption as $collectionEvent) {
                    $collectionEvent->delete();
                }
                foreach($modelEventoptionType as $collectionType) {
                    $collectionType->delete();
                }
                continue;
            } else {
                if ($once) {
                    $deletedOptions = $this->getDeletedItem('option', $options, $data['product_id']);
                    foreach($deletedOptions as $optionItem) {
                        $delItem = $this->_eventoptionFactory->create()->load($optionItem);
                        $delItem->delete();
                        $this->removeOptionsComponent($optionItem, $data['product_id']);
                    }
                    $once = false;
                }
            }
            $option['event_id'] = $data['event_id'];
            $option['product_id'] = $data['product_id'];
            $option['option_id'] = $i;
            $option['store_id'] = $this->storeManage->getStore()->getId();
            $option['option_input_type'] = $option['input_type'];

            /** @var \Magenest\Ticket\Model\Eventoption $model */
            if (isset($option['id_option'])) {
                $model = $this->_eventoptionFactory->create()->load($option['id_option']);
            } else {
                $model = $this->_eventoptionFactory->create();
            }
            $model->addData($option);
            $model->save();
            if (!empty($data['event_options'][$i]['row'])) {
                $this->saveOptionType($data['event_options'][$i]['row'], $model->getData());
            } else {
                $this->eventTypeFactory->create()->getCollection()
                    ->addFieldToFilter('product_id', $data['product_id'])->addFieldToFilter('event_option_id', $model->getId())->walk('delete');
            }
            $i++;
        }
    }

    /**
     * save event option type
     * @param $type
     * @param $option
     */
    public function saveOptionType($type, $option)
    {
        $default['event_option_id']= $option['id'] ;
        $default['product_id']= $option['product_id'];
        $default['option_id']= $option['option_id'];
        $j = 0;
        foreach ($type as $typeOptions) {
            if (!isset($once)) {
                $once = true;
            }
            if (isset($typeOptions['is_delete']) && $typeOptions['is_delete'] == 1) {
                $modelType = $this->eventTypeFactory->create()->load($typeOptions['id']);
                $modelType->delete();
                continue;
            } else {
                if ($once) {
                    $deletedTypes = $this->getDeletedItem('type', $type, $option['product_id'], null, null, @$option['id_option']);
                    foreach ($deletedTypes as $optionItem) {
                        $this->eventTypeFactory->create()->load($optionItem)->delete();
                    }
                    $once = false;
                }
            }
            /** @var \Magenest\Ticket\Model\EventoptionType $model */
            $modelType = $this->eventTypeFactory->create();
            if (isset($typeOptions['id']) && !empty($typeOptions['id'])) {
                $modelType->load($typeOptions['id']);
                $purchased = $modelType->getPurcharsedQty();
            } else {
                $purchased = 0;
            }
            $infoRow = [
                'sort_order' => $j,
                'title' => $typeOptions['title'],
                'price' => $typeOptions['price'],
                'price_type' => $typeOptions['price_type'],
                'qty' => $typeOptions['qty'],
                'available_qty' => $typeOptions['qty'] - $purchased,
                'sku' => $typeOptions['sku'],
                'description' => $typeOptions['description'],
            ];
            $array = array_merge($default, $infoRow);
            $modelType->addData($array);
            $modelType->save();
            $j++;
        }
    }

    /**
     * @param $type
     * @param $data
     * @param $productId
     * @param null $locationId
     * @param null $dateId
     * @param null $optionId
     * @return array|null
     */
    public function getDeletedItem($type, $data, $productId, $locationId = null, $dateId = null, $optionId = null)
    {
        switch ($type) {
            case 'schedule':
                {
                    $currentRecord = [];
                    $oldRecord = [];
                    foreach ($data as $schedule) {
                        if (empty($schedule['id_location'])) {
                            continue;
                        }
                        array_push($currentRecord, intval($schedule['id_location']));
                    }
                    $oldScheduleCollections = $this->location->create()->getCollection()->addFieldToFilter('product_id', $productId);
                    foreach ($oldScheduleCollections as $schedules) {
                        array_push($oldRecord, intval($schedules->getLocationId()));
                    }
                    return array_diff($oldRecord, $currentRecord);
                }
            case 'date':
                {
                    $currentRecord = [];
                    $oldRecord = [];
                    foreach ($data as $date) {
                        if (empty($date['id_date'])) {
                            continue;
                        }
                        array_push($currentRecord, intval($date['id_date']));
                    }
                    $oldScheduleCollections = $this->date->create()->getCollection()->addFieldToFilter('product_id', $productId)->addFieldToFilter('event_location_id', $locationId);
                    foreach ($oldScheduleCollections as $dates) {
                        array_push($oldRecord, intval($dates->getDateId()));
                    }
                    return array_diff($oldRecord, $currentRecord);
                }
            case 'session':
                {
                    $currentRecord = [];
                    $oldRecord = [];
                    foreach ($data as $date) {
                        if (empty($date['id_session'])) {
                            continue;
                        }
                        array_push($currentRecord, intval($date['id_session']));
                    }
                    $oldScheduleCollections = $this->session->create()->getCollection()->addFieldToFilter('product_id', $productId)->addFieldToFilter('event_date_id', $dateId);
                    foreach ($oldScheduleCollections as $sessions) {
                        array_push($oldRecord, intval($sessions->getSessionId()));
                    }
                    return array_diff($oldRecord, $currentRecord);
                }
            case 'option':
                {
                    $currentRecord = [];
                    $oldRecord = [];
                    foreach ($data as $date) {
                        if (empty($date['id_option'])) {
                            continue;
                        }
                        array_push($currentRecord, intval($date['id_option']));
                    }
                    $oldScheduleCollections = $this->_eventoptionFactory->create()->getCollection()->addFieldToFilter('product_id', $productId);
                    foreach ($oldScheduleCollections as $sessions) {
                        array_push($oldRecord, intval($sessions->getId()));
                    }
                    return array_diff($oldRecord, $currentRecord);
                }
            case 'type':
                {
                    $currentRecord = [];
                    $oldRecord = [];
                    foreach ($data as $date) {
                        if (empty($date['id'])) {
                            continue;
                        }
                        array_push($currentRecord, intval($date['id']));
                    }
                    $oldScheduleCollections = $this->eventTypeFactory->create()->getCollection()->addFieldToFilter('product_id', $productId)->addFieldToFilter('event_option_id', $optionId);
                    foreach ($oldScheduleCollections as $sessions) {
                        array_push($oldRecord, intval($sessions->getId()));
                    }
                    return array_diff($oldRecord, $currentRecord);
                }
            default:
                {
                    return null;
                }
        }
    }

    /**
     * @param $id_location
     * @param $productId
     */
    private function removeSchedulesComponent($id_location, $productId)
    {
        $scheduleDates = $this->date->create()->getCollection()->addFieldToFilter('product_id', $productId)->addFieldToFilter('event_location_id', $id_location);
        foreach ($scheduleDates as $date) {
            $this->removeDateComponents($date->getDateId(), $productId);
        }
        $this->date->create()->getCollection()->addFieldToFilter('product_id', $productId)->addFieldToFilter('event_location_id', $id_location)->walk('delete');
    }

    /**
     * @param $id_date
     * @param $productId
     */
    private function removeDateComponents($id_date, $productId)
    {
        $this->session->create()->getCollection()->addFieldToFilter('product_id', $productId)->addFieldToFilter('event_date_id', $id_date)->walk('delete');
    }

    /**
     * @param $optionItem
     * @param $product_id
     */
    private function removeOptionsComponent($optionItem, $product_id)
    {
        $this->eventTypeFactory->create()->getCollection()->addFieldToFilter('product_id', $product_id)->addFieldToFilter('event_option_id', $optionItem)->walk('delete');
    }
}