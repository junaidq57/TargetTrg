<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Ticket\Controller\Sidebar;

use Magento\Framework\Controller\ResultFactory;

class RemoveItem extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Sidebar
     */
    protected $sidebar;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quote;

    /**
     * @var \Magento\Quote\Model\Quote\ItemFactory
     */
    protected $item;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;

    /**
     * RemoveItem constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Sidebar $sidebar
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Quote\Model\Quote\ItemFactory $itemFactory
     * @param \Magento\Quote\Model\QuoteFactory $quote
     * @param \Magento\Framework\App\Response\Http $response
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Sidebar $sidebar,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Quote\Model\Quote\ItemFactory $itemFactory,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->sidebar = $sidebar;
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
        $this->resultPageFactory = $resultPageFactory;
        $this->quote = $quote;
        $this->item = $itemFactory;
        $this->response = $response;
        $this->_productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $this->removeItem($this->_request->getParam('item_id'));
        $productId = $this->_request->getParam('product_id');
        $product = $this->_productRepository->getById($productId);
        $test = $product->getUrlModel()->getUrl($product);
        return $this->response->setRedirect($test);
    }

    /**
     * Remove quote item by item identifier
     *
     * @param   int $itemId
     * @return $this
     */
    public function removeItem($itemId)
    {
        $item = $this->item->create()->load($itemId);
        $quote = $this->quote->create()->load($item->getQuoteId());
        if (!empty($quote->getData()) && $quote->getItemsCount() == 1) {
            $quote->delete();
        } else {
            $item->delete();
        }
    }
}
