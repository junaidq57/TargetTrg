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

use Magenest\Ticket\Model\EventFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\Model\View\Result\Page;
use Magenest\Ticket\Model\ResourceModel\Event\CollectionFactory as EventCollectionFactory;

abstract class Event extends Action
{
    /**
     * Event Factory
     *
     * @var EventFactory
     */
    protected $_eventFactory;

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
     * @var FileFactory
     */
    protected $_fileFactory;

    /**
     * @var EventCollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Event constructor.
     * @param Context $context
     * @param EventFactory $eventFactory
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param FileFactory $fileFactory
     * @param Filter $filter
     * @param EventCollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        EventFactory $eventFactory,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        FileFactory $fileFactory,
        Filter $filter,
        EventCollectionFactory $collectionFactory
    ) {
        $this->_eventFactory = $eventFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_fileFactory = $fileFactory;
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
        $resultPage->setActiveMenu('Magenest_Ticket::event');
        $resultPage->getConfig()->getTitle()->prepend((__('Event')));
        return $this;
    }

    /**
     * Check ACL
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Ticket::event');
    }
}
