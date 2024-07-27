<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 13/10/2016
 * Time: 21:42
 */
namespace Magenest\Ticket\Block\Order\Email\Items\Order;

/**
 * Class Ticket
 * @package Magenest\Ticket\Block\Order\Email\Items\Order
 */
class Ticket extends \Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder
{
    /**
     * @var \Magenest\Ticket\Helper\Information
     */
    protected $information;

    /**
     * Ticket constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magenest\Ticket\Helper\Information $information
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magenest\Ticket\Helper\Information $information,
        array $data
    ) {
        $this->information = $information;
        parent::__construct($context, $data);
    }

    /**
     * @param $options
     */
    public function getDataTicket($options)
    {
        $data = $this->information->getAll($options);
        $info = $this->information->getDataTicket($data);

        return $info;
    }
}
