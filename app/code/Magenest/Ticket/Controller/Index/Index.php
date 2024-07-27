<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ProductLabel
 * @author   ThaoPV <thaopw@gmail.com>
 */
namespace Magenest\Ticket\Controller\Index;

/**
 * Class Index
 *
 * @package Magenest\ProductLabel\Controller\Adminhtml\Label
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $encryptor;
    protected $_productRepository;
    protected $ticketFactory;
    protected $quote;
    protected $item;
    protected $orderFactory;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Magento\Catalog\Model\ProductFactory $productRepository,
        \Magenest\Ticket\Model\TicketFactory $ticketFactory,
        \Magento\Quote\Model\Quote\ItemFactory $itemFactory,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->orderFactory = $orderFactory;
        $this->quote = $quote;
        $this->item = $itemFactory;
        $this->ticketFactory = $ticketFactory;
        $this->_productRepository = $productRepository;
        $this->encryptor = $encryptor;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return void
     */
    public function execute()
    {
        $model = $this->orderFactory->create()->load(24)->getData();
//        $model = $this->removeItem(11);
        print_r($model);
    }

    /**
     * Remove quote item by item identifier
     *
     * @param   int $itemId
     * @return $this
     */
    public function removeItem($itemId)
    {
//        $item = $this->item->create()->load(25);
        $quote = $this->quote->create()->load(6)->getData();
        return $quote;
    }
}
