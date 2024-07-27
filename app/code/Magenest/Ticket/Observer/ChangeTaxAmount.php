<?php
/**
 * Created by PhpStorm.
 * User: gialam
 * Date: 7/26/2016
 * Time: 1:39 PM
 */

namespace Magenest\Ticket\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class ChangeTaxAmount
 * @package Magenest\Ticket\Observer
 */
class ChangeTaxAmount implements ObserverInterface
{
    const XML_PATH_QTY = 'event_ticket/general_config/delete_qty';

    protected $_logger;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * PlaceOrder constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Directory\Model\Currency $currency
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Cart $cart,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_cart = $cart;
        $this->_logger = $logger;
        $this->_currency = $currency;
        $this->_request = $request;
        $this->_scopeConfig = $scopeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getQuote();
        foreach ($quote->getAllItems() as $item) {
            $productType = $item->getProductType();
            if ($productType == 'ticket') {
                $product = $item->getProduct();
                $product->setTaxClassId(0);
            }
        }
    }
}
