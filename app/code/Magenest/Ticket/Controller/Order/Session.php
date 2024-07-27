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
use Magenest\Ticket\Model\EventSessionFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Session
 * @package Magenest\Ticket\Controller\Order
 */
class Session extends Action
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
     * @var
     */
    protected $session;

    /**
     * Date constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param LoggerInterface $loggerInterface
     * @param EventSessionFactory $eventSessionFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        LoggerInterface $loggerInterface,
        EventSessionFactory $eventSessionFactory
    ) {
        $this->session = $eventSessionFactory;
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
        $dateId = $params['date_id'];
        $modelSession = $this->session->create()->getCollection()->addFieldToFilter('event_date_id', $dateId);
        $arraySession = [];
        if (!empty($modelSession->getData())) {
            foreach ($modelSession as $session) {
                $array = [
                    'session_id' => $session->getSessionId(),
                    'time_session'=> $session->getStartTime().' - '.$session->getEndTime()
                ];
                $arraySession [] = $array;
            }
        }
        $resultArray = json_encode($arraySession);
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $resultJson->setData($resultArray);
        return $resultJson;
    }
}
