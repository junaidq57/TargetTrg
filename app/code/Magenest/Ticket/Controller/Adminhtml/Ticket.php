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
namespace Magenest\Ticket\Controller\Adminhtml;

use Magenest\Ticket\Model\TicketFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magenest\Ticket\Model\ResourceModel\Ticket\CollectionFactory as TicketCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

abstract class Ticket extends Action
{
    /**
     * Ticket Factory
     *
     * @var TicketFactory
     */
    protected $_ticketFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Page result factory
     *
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Page factory
     *
     * @var Page
     */
    protected $_resultPage;

    /**
     * Mass Action Filter
     *
     * @var Filter
     */
    protected $_filter;

    /**
     * @var TicketCollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param TicketFactory $ticketFactory
     * @param Registry $coreRegistry
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param TicketCollectionFactory $collectionFactory
     * @param Filter $filter
     */
    public function __construct(
        TicketFactory $ticketFactory,
        Registry $coreRegistry,
        Context $context,
        PageFactory $resultPageFactory,
        TicketCollectionFactory $collectionFactory,
        Filter $filter
    ) {
        $this->_ticketFactory = $ticketFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_filter = $filter;
        parent::__construct($context);
    }

    /**
     * instantiate result page object
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
     */
    public function getResultPage()
    {
        if (is_null($this->_resultPage)) {
            $this->_resultPage = $this->_resultPageFactory->create();
        }
        return $this->_resultPage;
    }
    /**
     * set page data
     *
     * @return $this
     */
    protected function _setPageData()
    {
        $resultPage = $this->getResultPage();
        $resultPage->setActiveMenu('Magenest_Ticket::ticket');
        $resultPage->getConfig()->getTitle()->prepend((__('Event Ticket')));
        return $this;
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
