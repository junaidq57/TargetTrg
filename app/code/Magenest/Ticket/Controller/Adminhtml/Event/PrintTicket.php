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
namespace Magenest\Ticket\Controller\Adminhtml\Event;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magenest\Ticket\Helper\Pdf as PdfHelper;
use Magenest\Ticket\Model\Ticket;
use Magenest\Ticket\Model\EventFactory;
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
     * @var EventFactory
     */
    protected $eventFactory;

    /**
     * @var Store
     */
    protected $store;

    protected $resultPageFactory;

    protected $dateTime;

    protected $fileFactory;


    /**
     * Preview constructor.
     * @param Context $context
     * @param PdfHelper $pdfHelper
     * @param Ticket $ticket
     * @param Filesystem $filesystem
     * @param LoggerInterface $loggerInterface
     * @param EventFactory $eventFactory
     * @param StoreManagerInterface $storeManager
     * @param Store $store
     * @param PageFactory $resultPageFactory
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        PdfHelper $pdfHelper,
        Ticket $ticket,
        Filesystem $filesystem,
        LoggerInterface $loggerInterface,
        EventFactory $eventFactory,
        StoreManagerInterface $storeManager,
        Store $store,
        PageFactory $resultPageFactory,
        DateTime $dateTime,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->_pdfHelper = $pdfHelper;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->_ticket = $ticket;
        $this->_filesystem = $filesystem;
        $this->_storeManager = $storeManager;
        $this->logger = $loggerInterface;
        $this->eventFactory = $eventFactory;
        $this->store = $store;
        $this->resultPageFactory = $resultPageFactory;
        $this->dateTime = $dateTime;
        $this->fileFactory = $fileFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $id = $this->getRequest()->getParam('id');
        $model = $this->eventFactory->create()->loadByProductId($id);
        if (!empty($model->getData())) {
            return $this->fileFactory->create(
                sprintf('ticket%s.pdf', $this->dateTime->date('Y-m-d_H-i-s')),
                $this->_pdfHelper->getPrintPdfPreview($model->getData())->render(),
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        }
        return $resultPage;
    }

    /**
     * @return string
     */
    public function getUrlFile()
    {
        $url = $this->store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $url;
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
