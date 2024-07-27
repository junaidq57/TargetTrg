<?php
/**
 * Created by Magenest.
 */
namespace Magenest\Ticket\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magenest\Ticket\Model\EventFactory as EventCollection;
use Magenest\Ticket\Model\EventoptionFactory as OptionCollection;
use Magenest\Ticket\Model\EventoptionTypeFactory as OptionTypeCollection;
use Magenest\Ticket\Model\EventLocationFactory;
use Magenest\Ticket\Model\EventDateFactory;
use Magenest\Ticket\Model\EventSessionFactory;

/**
 * Class EventBooking
 *
 * @package Magenest\Ticket\Ui\DataProvider\Product\Form\Modifier
 */
class TicketDataProvider extends AbstractModifier
{

    const PRODUCT_TYPE = 'ticket';
    const CONTROLLER_ACTION_EDIT_PRODUCT = 'catalog_product_edit';
    const CONTROLLER_ACTION_NEW_PRODUCT = 'catalog_product_new';
    const EVENT_TICKET_TAB = 'event';


    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var EventCollection
     */
    protected $event;

    /**
     * @var OptionCollection
     */
    protected $option;

    /**
     * @var OptionTypeCollection
     */
    protected $type;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

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
     * TicketDataProvider constructor.
     * @param RequestInterface $request
     * @param LocatorInterface $locator
     * @param EventCollection $eventCollection
     * @param \Psr\Log\LoggerInterface $loggerInterface
     * @param OptionTypeCollection $optionTypeCollection
     * @param OptionCollection $optionCollection
     * @param EventLocationFactory $eventLocationFactory
     * @param EventDateFactory $eventDateFactory
     * @param EventSessionFactory $eventSessionFactory
     */
    public function __construct(
        RequestInterface $request,
        LocatorInterface $locator,
        EventCollection $eventCollection,
        \Psr\Log\LoggerInterface $loggerInterface,
        OptionTypeCollection $optionTypeCollection,
        OptionCollection $optionCollection,
        EventLocationFactory $eventLocationFactory,
        EventDateFactory $eventDateFactory,
        EventSessionFactory $eventSessionFactory
    ) {
        $this->logger = $loggerInterface;
        $this->type = $optionTypeCollection;
        $this->event = $eventCollection;
        $this->option = $optionCollection;
        $this->request = $request;
        $this->locator = $locator;
        $this->location = $eventLocationFactory;
        $this->date = $eventDateFactory;
        $this->session = $eventSessionFactory;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        $product = $this->locator->getProduct();
        $productId = $product->getId();
        if ($this->isEventTicket()) {
            /** @var \Magenest\Ticket\Model\Event $eventModel */
            $eventModel = $this->event->create()->getCollection()
                ->addFieldToFilter('product_id', $productId)
                ->getFirstItem();
            if ($eventModel) {
                // use date time
                $data[strval($productId)]['event']['enable_date_time'] = $eventModel->getEnableDateTime();
                //data email
                if (!empty($eventModel->getEmailConfig())) {
                    $email = $eventModel->getEmailConfig();
                } else {
                    $email = 'emailtemplate_config';
                }
                $data[strval($productId)]['event']['emailtemplate']['emailtemplate_config'] = $email;
                    //data on pdftemplate
                $data[strval($productId)]['event']['pdftemplate'] = [
                    'background' => $eventModel->getPdfBackground(),
                    'page_width' => $eventModel->getPdfPageWidth(),
                    'page_height' => $eventModel->getPdfPageHeight(),
                ];
                $coordinates = $eventModel->getPdfCoordinates();
                if (!empty($coordinates)) {
                    $arrayCoor = unserialize($coordinates);
                    $size = sizeof($arrayCoor);
                    for ($i = 0; $i < $size; $i++) {
                        $data[strval($productId)]['event']['pdftemplate']['coordinates'][$i] =
                            [
                                'record_id' => $i,
                                'info' => $arrayCoor[$i]['info'],
                                'title' => $arrayCoor[$i]['title'],
                                'x' => $arrayCoor[$i]['x'],
                                'y' => $arrayCoor[$i]['y'],
                                'size' => $arrayCoor[$i]['size'],
                                'color' => $arrayCoor[$i]['color'],
                            ];
                    }
                }
                $background = $eventModel->getPdfBackground();
                if (@unserialize($background) !== false) {
                    $data[$productId]['event']['pdftemplate']['pdf_background'] = unserialize($background);
                }

                //data on event options
                /** @var \Magenest\Ticket\Model\Eventoption $optionModel */
                $optionModel = $this->option->create()->getCollection()
                    ->addFieldToFilter('product_id', $productId);
                $sizeOption = sizeof($optionModel);
                for ($j = 0; $j < $sizeOption; $j++) {

                    /** @var \Magenest\Ticket\Model\Eventoption $optionSame */
                    $optionSame = $this->option->create()->getCollection()
                        ->addFieldToFilter('product_id', $productId)
                        ->addFieldToFilter('option_id', $j);
                    foreach ($optionSame as $optionSames) {
                        $typeModel = $this->type->create()
                            ->getCollection()
                            ->addFieldToFilter('product_id', $productId)
                            ->addFieldToFilter('option_id', $j)
                            ->addFieldToSelect(['title', 'price', 'price_type', 'qty', 'sku', 'description'])
                            ->getData();
                    }
                    $data[strval($productId)]['event']['event_options'][$j] = [
                        'record_id' => $j,
                        'row' => $typeModel,
                        'option_title' => $optionSames->getOptionTitle(),
                        'input_type' => $optionSames->getOptionInputType(),
                        'is_required' => $optionSames->getIsRequired(),
                    ];
                }
                //data on schedule

                /** @var \Magenest\Ticket\Model\EventLocation $optionLocations */
                $optionLocation = $this->location->create()->getCollection()
                    ->addFieldToFilter('product_id', $productId);
                $arrayLocation = [];
                foreach ($optionLocation as $value) {
                    $locationArray = [
                        'id_location' => $value->getLocationId(),
                        'location_title' => $value->getLocationTitle(),
                        'location_detail' => $value->getLocationDetail(),
                        'is_enabled' => $value->getLocationIsEnabled(),
                        'row_day' => $this->getDayData($value->getLocationId())
                    ];
                    $arrayLocation [] = $locationArray;
                }
                $data[strval($productId)]['event']['event_schedule'] = $arrayLocation;
            }
        }

        return $data;
    }

    public function getDayData($locationId)
    {
        $modelDate = $this->date->create()->getCollection()->addFieldToFilter('event_location_id', $locationId);
        $arrayDate = [];
        foreach ($modelDate as $date) {
            $dateArray = [
                'id_date' => $date->getDateId(),
                'time_date_start' =>  $date->getDateStart(),
                'time_date_end' =>  $date->getDateEnd(),

                'row_session' => $this->getSessionData($date->getDateId())
            ];
            $arrayDate [] = $dateArray;
        }
        return $arrayDate;
    }

    public function getSessionData($dateId)
    {
        $modelSession = $this->session->create()->getCollection()->addFieldToFilter('event_date_id', $dateId);
        $arraySession = [];
        foreach ($modelSession as $session) {
            $sessionArray = [
                'id_session' => $session->getSessionId(),
                'start_time' =>  $session->getStartTime(),
                'end_time' => $session->getEndTime(),
            ];
            $arraySession [] = $sessionArray;
        }
        return $arraySession;
    }
    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        if ($this->isEventTicket()) {
            unset($meta['event']);
        }
        return $meta;
    }

    /**
     * @return bool
     */
    protected function isEventTicket()
    {
        $actionName = $this->request->getFullActionName();
        $isEventTicket = false;
        if ($actionName == self::CONTROLLER_ACTION_EDIT_PRODUCT) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->locator->getProduct();
            if ($product->getTypeId() == self::PRODUCT_TYPE) {
                $isEventTicket = true;
            }
        } elseif ($actionName == self::CONTROLLER_ACTION_NEW_PRODUCT) {
            if (self::PRODUCT_TYPE == $this->request->getParam('type')) {
                $isEventTicket = true;
            }
        }

        return $isEventTicket;
    }
}
