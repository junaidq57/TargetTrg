<?php

namespace Magenest\Ticket\Controller\Adminhtml\Ticket;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magenest\Ticket\Helper\Pdf as PdfHelper;
use Magenest\Ticket\Model\TicketFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\Store;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class PrintTicket
 * @package Magenest\Ticket\Controller\Adminhtml\Ticket
 */
class PrintTicket extends Action
{
    /**
     * @var PdfHelper
     */
    protected $_pdfHelper;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    protected $_directory;

    /**
     * @var Ticket
     */
    protected $_ticket;

    /**
     * @var Filesystem
     */
    protected $_filesystem;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Store
     */
    protected $store;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var FileFactory
     */
    protected $fileFactory;


    /**
     * PrintTicket constructor.
     * @param Context $context
     * @param PdfHelper $pdfHelper
     * @param TicketFactory $ticketFactory
     * @param Filesystem $filesystem
     * @param LoggerInterface $loggerInterface
     * @param StoreManagerInterface $storeManager
     * @param Store $store
     * @param PageFactory $resultPageFactory
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        PdfHelper $pdfHelper,
        TicketFactory $ticketFactory,
        Filesystem $filesystem,
        LoggerInterface $loggerInterface,
        StoreManagerInterface $storeManager,
        Store $store,
        PageFactory $resultPageFactory,
        DateTime $dateTime,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->_pdfHelper = $pdfHelper;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->_ticket = $ticketFactory;
        $this->_filesystem = $filesystem;
        $this->_storeManager = $storeManager;
        $this->logger = $loggerInterface;
        $this->store = $store;
        $this->resultPageFactory = $resultPageFactory;
        $this->dateTime = $dateTime;
        $this->fileFactory = $fileFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $ticketId = (int)$this->getRequest()->getParam('id');
        if ($ticketId) {
            $ticket = $this->_ticket->create()->load($ticketId);
            if ($ticket->getId()) {
                return $this->fileFactory->create(
                    sprintf('ticket%s.pdf', $this->dateTime->date('Y-m-d_H-i-s')),
                    $this->_pdfHelper->getPdf($ticket)->render(),
                    DirectoryList::VAR_DIR,
                    'application/pdf'
                );
            }
        }

        return $resultPage;
    }

    /**
     * Check ACL
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Ticket::tickets');
    }
}
