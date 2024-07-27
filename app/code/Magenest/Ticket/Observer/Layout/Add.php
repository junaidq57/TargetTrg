<?php
/**
 * Created by PhpStorm.
 * User: hoang
 * Date: 05/07/2016
 * Time: 02:21
 */
namespace Magenest\Ticket\Observer\Layout;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Add
 * @package Magenest\Ticket\Observer\Layout
 */
class Add implements ObserverInterface
{
    protected $_request;
    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ) {
    
        $this->_request = $request;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == 'ticket') {
            $product->setHasOptions(true);
        }
    }
}
