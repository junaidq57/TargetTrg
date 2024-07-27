<?php
/**
 * Created by PhpStorm.
 * User: gialam
 * Date: 7/26/2016
 * Time: 1:39 PM
 */

namespace Magenest\Ticket\Observer\Option;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class Cart
 * @package Magenest\Ticket\Observer\Option
 */
class Cart implements ObserverInterface
{
    protected $_logger;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magenest\Ticket\Model\EventoptionFactory
     */
    protected $option;

    /**
     * @var \Magenest\Ticket\Model\EventSessionFactory
     */
    protected $session;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;

    /**
     * @var \Magenest\Ticket\Model\EventDateFactory
     */
    protected $date;

    /**
     * @var \Magenest\Ticket\Model\EventLocationFactory
     */
    protected $location;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * Cart constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Directory\Model\Currency $currency
     * @param \Magenest\Ticket\Model\EventoptionFactory $optionFactory
     * @param \Magenest\Ticket\Model\EventLocationFactory $locationFactory
     * @param \Magenest\Ticket\Model\EventDateFactory $dateFactory
     * @param \Magenest\Ticket\Model\EventSessionFactory $sessionFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Directory\Model\Currency $currency,
        \Magenest\Ticket\Model\EventoptionFactory $optionFactory,
        \Magenest\Ticket\Model\EventLocationFactory $locationFactory,
        \Magenest\Ticket\Model\EventDateFactory $dateFactory,
        \Magenest\Ticket\Model\EventSessionFactory $sessionFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        Json $serializer = null
    ) {
        $this->_productRepository = $productRepository;
        $this->option = $optionFactory;
        $this->_logger = $logger;
        $this->_currency = $currency;
        $this->_request = $request;
        $this->location = $locationFactory;
        $this->date = $dateFactory;
        $this->session = $sessionFactory;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getEvent()->getQuoteItem();
        $product = $item->getProduct();
        $productId = $product->getId();
        $data = $this->_request->getParams();
        $checkTypeProduct = $this->_productRepository->getById($productId)->getTypeId();
        if ($checkTypeProduct == 'ticket') {
            if (!empty($data['additional_options'])) {
                $options = $data['additional_options'];
                $additionalOptions = [];
                if (!empty($data['additional_options']['ticket_price'])) {
                    if (isset($options['checkbox']) && !empty($options['checkbox'])) {
                        $array = json_decode($options['checkbox'], true);
                        $arrayCheckboxs = array_keys($array);
                        $title = $this->getTitleOption($productId, 'checkbox');
                        $additionalOptions[] = array(
                            'label' => $title,
                            'value' => implode(",", $arrayCheckboxs),
                        );
                    }
                    if (isset($options['radio']) && !empty($options['radio'])) {
                        /** @var  \Magenest\Ticket\Model\Eventoption $optionModel */
                        $title = $this->getTitleOption($productId, 'radio_buttons');
                        $select = $options['radio']['radio_select'];
                        $value = explode("_", $select);
                        $additionalOptions[] = array(
                            'label' => $title,
                            'value' => $value[1],
                        );
                    }
                    if (isset($options['dropdow']) && !empty($options['dropdow'])) {
                        $title = $this->getTitleOption($productId, 'drop_down');
                        $additionalOptions[] = array(
                            'label' => $title,
                            'value' => $options['dropdow'],
                        );
                    }

                    $item->setOriginalCustomPrice($options['ticket_price']);
                }
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

                if (!empty($locationId)) {
                    $location = $this->location->create()->load($locationId);

                    $additionalOptions[] = array(
                        'label' => 'Location',
                        'value' => $location->getLocationTitle()
                    );
                }
                if (!empty($dateId)) {
                    $date = $this->date->create()->load($dateId);
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
                    $additionalOptions[] = array(
                        'label' => 'Date',
                        'value' => $dateStr
                    );
                }
                if (!empty($sessionId)) {
                    $session = $this->session->create()->load($sessionId);
                    $startTime = '';
                    if (!empty($session->getStartTime())) {
                        $startTime = 'Start: '.$session->getStartTime().' ';
                    }
                    $endTime = '';
                    if (!empty($session->getEndTime())) {
                        $endTime = 'End: '.$session->getEndTime();
                    }
                    $time = $startTime.$endTime;
                    $additionalOptions[] = array(
                        'label' => 'Time',
                        'value' => $time
                    );
                }

                $item->addOption(array(
                    'code' => 'additional_options',
                    'value' => $this->serializer->serialize($additionalOptions)
                ));
            }
        }
    }

    /**
     * getTitle of option
     * @param $productId
     * @param $option
     * @return mixed
     */
    public function getTitleOption($productId, $option)
    {
        /** @var  \Magenest\Ticket\Model\Eventoption $optionModel */
        $optionModel = $this->option->create()->getCollection()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('option_input_type', $option);
        foreach ($optionModel as $optionModels) {
            $title = $optionModels->getOptionTitle();
        }

        return $title;
    }
}
