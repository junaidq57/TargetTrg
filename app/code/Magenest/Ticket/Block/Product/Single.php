<?php
/**
 * Created by PhpStorm.
 * User: gialam
 * Date: 7/21/2016
 * Time: 11:37 AM
 */
namespace Magenest\Ticket\Block\Product;

/**
 * Class Single
 * @package Magenest\Ticket\Block\Product
 */
class Single extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'catalog/product/single.phtml';

    /**
     * Google Map API key
     */
    const XML_PATH_GOOGLE_MAP_API_KEY = 'event_ticket/general_config/google_api_key';
    /**
     * @var \Magenest\Ticket\Model\EventFactory
     */
    protected $eventFactory;

    /**
     * @var \Magenest\Ticket\Model\EventoptionFactory
     */
    protected $option;

    /**
     * @var \Magenest\Ticket\Model\EventoptionTypeFactory
     */
    protected $type;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_currency;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magenest\Ticket\Model\EventLocationFactory
     */
    protected $location;

    /**
     * @var \Magenest\Ticket\Model\EventDateFactory
     */
    protected $date;

    /**
     * @var \Magenest\Ticket\Model\EventSessionFactory
     */
    protected $session;

    /**
     * Ticket constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magenest\Ticket\Model\EventFactory $eventFactory
     * @param \Magenest\Ticket\Model\EventoptionFactory $eventoptionFactory
     * @param \Magenest\Ticket\Model\EventoptionTypeFactory $eventoptionTypeFactoryFactory
     * @param \Magenest\Ticket\Model\EventLocationFactory $eventLocationFactory
     * @param \Magenest\Ticket\Model\EventDateFactory $eventDateFactory
     * @param \Magenest\Ticket\Model\EventSessionFactory $eventSessionFactory
     * @param \Magento\Directory\Model\Currency $currency
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magenest\Ticket\Model\EventFactory $eventFactory,
        \Magenest\Ticket\Model\EventoptionFactory $eventoptionFactory,
        \Magenest\Ticket\Model\EventoptionTypeFactory $eventoptionTypeFactoryFactory,
        \Magenest\Ticket\Model\EventLocationFactory $eventLocationFactory,
        \Magenest\Ticket\Model\EventDateFactory $eventDateFactory,
        \Magenest\Ticket\Model\EventSessionFactory $eventSessionFactory,
        \Magento\Directory\Model\Currency $currency,
        array $data = []
    ) {
        $this->location = $eventLocationFactory;
        $this->date = $eventDateFactory;
        $this->session = $eventSessionFactory;
        $this->_currency = $currency;
        $this->option = $eventoptionFactory;
        $this->type = $eventoptionTypeFactoryFactory;
        $this->eventFactory = $eventFactory;
        $this->_coreRegistry = $context->getRegistry();
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getCurrentProductId()
    {
        $id = $this->_coreRegistry->registry('current_product')->getId();

        return $id;
    }
    /**
     * enable time
     * @return mixed
     */
    public function enableTime()
    {
        $event = $this->eventFactory->create()->getCollection()->addFieldToFilter('product_id', $this->getCurrentProductId())->getFirstItem();

        return $event->getEnableDateTime();
    }
    /**
     * Get Google Map Api key
     *
     * @return mixed
     */
    public function getGoogleApiKey()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_GOOGLE_MAP_API_KEY);
    }

    /**
     * @return $this
     */
    public function getLocation()
    {
        $modelLocation = $this->location->create()->getCollection()
            ->addFieldToFilter('product_id', $this->getCurrentProductId())
            ->addFieldToFilter('location_is_enabled', 1);

        return $modelLocation;
    }

    /**
     * @param $locationId
     * @return $this
     */
    public function getDate($locationId)
    {
        $date = $this->date->create()->getCollection()
            ->addFieldToFilter('event_location_id', $locationId);

        return $date;
    }

    /**
     * @param $dateId
     * @return $this
     */
    public function getSession($dateId)
    {
        $session = $this->session->create()->getCollection()
            ->addFieldToFilter('event_date_id', $dateId);

        return $session;
    }

    /**
     * @return string
     */
    public function getReturnSession()
    {
        return $this->getUrl('ticket/order/session');
    }
}
