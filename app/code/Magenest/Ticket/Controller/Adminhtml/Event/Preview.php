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

/**
 * Class Preview
 * @package Magenest\Ticket\Controller\Adminhtml\Ticket
 */
class Preview extends Action
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
     * Preview constructor.
     * @param Context $context
     * @param PdfHelper $pdfHelper
     * @param Ticket $ticket
     * @param Filesystem $filesystem
     * @param LoggerInterface $loggerInterface
     * @param EventFactory $eventFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        PdfHelper $pdfHelper,
        Ticket $ticket,
        Filesystem $filesystem,
        LoggerInterface $loggerInterface,
        EventFactory $eventFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_pdfHelper = $pdfHelper;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->_ticket = $ticket;
        $this->_filesystem = $filesystem;
        $this->_storeManager = $storeManager;
        $this->logger = $loggerInterface;
        $this->eventFactory = $eventFactory;
    }

    /**
     * execute the action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->eventFactory->create()->loadByProductId($id);
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        if (!empty($model->getData())) {
            try {
                $url = $this->_pdfHelper->getPreviewPdf($model->getData());
                $html  =  "<div class='clearfix' align='center'>";
                $html .= "<embed id='pdf_preview' name='pdf_preview' src='$url' width='1000' height='700' type='application/pdf' >";
                $html .= "</div>";
                $data = $html;
            } catch (\Exception $e) {
                $data = ['error' => $e->getMessage()];
            }
            return $resultPage->setData($data);
        }
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
