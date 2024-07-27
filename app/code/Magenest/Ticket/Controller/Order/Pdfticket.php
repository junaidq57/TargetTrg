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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magenest\Ticket\Helper\Pdf;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magenest\Ticket\Model\TicketFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Pdfticket extends Action
{
    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var Pdf
     */
    protected $pdfTicket;

    /**
     * @var \Magenest\Ticket\Model\TicketFactory
     */
    protected $ticketFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @param Context $context
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     * @param Pdf $pdfTicket
     * @param CustomerSession $customerSession
     * @param TicketFactory $ticketFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        FileFactory $fileFactory,
        Pdf $pdfTicket,
        CustomerSession $customerSession,
        TicketFactory $ticketFactory,
        PageFactory $resultPageFactory
    ) {
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->pdfTicket = $pdfTicket;
        $this->_customerSession = $customerSession;
        $this->ticketFactory = $ticketFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\View\Result\Page
     * @throws \Exception
     * @throws \Zend_Pdf_Exception
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('/');
        }

        $ticketId = (int)$this->getRequest()->getParam('ticket_id');
        if ($ticketId) {
            $ticket = $this->ticketFactory->create()->load($ticketId);
            if ($ticket->getId() && $customerId == $ticket->getCustomerId()) {
                return $this->fileFactory->create(
                    sprintf('ticket%s.pdf', $this->dateTime->date('Y-m-d_H-i-s')),
                    $this->pdfTicket->getPdf($ticket)->render(),
                    DirectoryList::VAR_DIR,
                    'application/pdf'
                );
            }
        }
        return $resultPage;
    }
}
