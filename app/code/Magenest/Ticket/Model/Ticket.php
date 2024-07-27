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
namespace Magenest\Ticket\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\App\Area;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magenest\Ticket\Model\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magenest\Ticket\Helper\Pdf;
use Magenest\Ticket\Helper\Information;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class Ticket
 * @package Magenest\Ticket\Model
 *
 * @method setCode(string $code)
 * @method string getCode()
 * @method string getCustomerName()
 * @method int getCustomerId()
 * @method string getCustomerEmail()
 * @method string getTitle()
 * @method string getOrderIncrementId()
 * @method int getEventId()
 * @method Ticket setStatus(int $status)
 * @method string getNote()
 * @method string getInformation()
 * @method int getQty()
 * @method int getProductId()
 */
class Ticket extends AbstractModel
{
    /**
     * Const Email
     */
    const XML_PATH_EMAIL_SENDER = 'trans_email/ident_general/email';

    /**
     * Const Name
     */
    const XML_PATH_NAME_SENDER = 'trans_email/ident_general/name';

    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'magenest_ticket_ticket';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'magenest_ticket_ticket';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_ticket_ticket';

    /**
     * @var EventFactory
     */
    protected $_eventFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Pdf
     */
    protected $_pdf;

    /**
     * @var Information
     */
    protected $information;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\Ticket\Model\ResourceModel\Ticket');
    }

    /**
     * Ticket constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param StoreManagerInterface $storeManager
     * @param EventSessionFactory $eventSessionFactory
     * @param EventFactory $eventFactory
     * @param Pdf $pdf
     * @param Information $information
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        StoreManagerInterface $storeManager,
        EventSessionFactory $eventSessionFactory,
        EventFactory $eventFactory,
        Pdf $pdf,
        Information $information,
        Json $serializer = null,
        array $data = []
    ) {
        parent::__construct($context, $registry);
        $this->_scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->_storeManager = $storeManager;
        $this->_eventFactory = $eventFactory;
        $this->_pdf = $pdf;
        $this->session = $eventSessionFactory;
        $this->information = $information;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
    }

    /**
     * Send Mail to customer
     *
     * @param $eventName
     */
    public function sendMail($ticketId)
    {
//        if (!$this->getId()) {
//            return;
//        }
        $modelTicket = $this->load($ticketId);
        $arrayId = $this->serializer->unserialize($modelTicket->getInformation());
        $info = $this->information->getDataTicket($arrayId);

        $pdf = $this->_pdf->getPdf($this);
        $file = $pdf->render();
        $this->inlineTranslation->suspend();
        $emailTemplate = $this->getEvent()->getEmailConfig();
        $transport = $this->_transportBuilder->setTemplateIdentifier($emailTemplate)->setTemplateOptions(
            [
                'area' => Area::AREA_FRONTEND,
                'store' => $this->_storeManager->getStore()->getId(),
            ]
        )->setTemplateVars(
            [
                'store' => $this->_storeManager->getStore(),
                'store URL' => $this->_storeManager->getStore()->getBaseUrl(),
                'ticket_code' => $this->getCode(),
                'customer_name' => $this->getCustomerName(),
                'title' => $this->getTitle(),
                'option_type' => $this->getNote(),
                'location_title' => $info['location_title'],
                'location_detail' => $info['location_detail'],
                'date' => $info['date'],
                'time' => $info['start_time'].$info['end_time'],
                'qty' => $this->getQty(),
            ]
        )->setFrom(
            [
                'email' => $this->_scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER),
                'name' => $this->_scopeConfig->getValue(self::XML_PATH_NAME_SENDER)
            ]
        )->addTo(
            $this->getCustomerEmail(),
            $this->getCustomerName()
        )->createAttachment($file)->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        $model = $this->_eventFactory->create();
        return $model->load($this->getEventId());
    }
}
