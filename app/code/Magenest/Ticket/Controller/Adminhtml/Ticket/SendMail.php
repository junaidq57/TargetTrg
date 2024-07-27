<?php

namespace Magenest\Ticket\Controller\Adminhtml\Ticket;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magenest\Ticket\Model\Ticket;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\Store;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class SendMail
 * @package Magenest\Ticket\Controller\Adminhtml\Ticket   ,
 */
class SendMail extends Action
{
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
     * SendMail constructor.
     * @param Context $context
     * @param Ticket $ticketFactory
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
        Ticket $ticketFactory,
        Filesystem $filesystem,
        LoggerInterface $loggerInterface,
        StoreManagerInterface $storeManager,
        Store $store,
        PageFactory $resultPageFactory,
        DateTime $dateTime,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);
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
     * @return $this
     */
    public function execute()
    {
        $resultPage = $this->resultRedirectFactory->create();
        $ticketId = (int)$this->getRequest()->getParam('id');

        if ($ticketId) {
            $this->_ticket->sendMail($ticketId);
        }
        return $resultPage->setPath('magenest_ticket/ticket/index');
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
