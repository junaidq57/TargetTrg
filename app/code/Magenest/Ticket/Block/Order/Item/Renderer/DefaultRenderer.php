<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Ticket\Block\Order\Item\Renderer;

/**
 * Order item render block
 */
class DefaultRenderer extends \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer
{
    /**
     * @var \Magenest\Ticket\Helper\Information
     */
    protected $information;

    /**
     * DefaultRenderer constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Magenest\Ticket\Helper\Information $information
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magenest\Ticket\Helper\Information $information,
        array $data
    ) {
        $this->information = $information;
        parent::__construct($context, $string, $productOptionFactory, $data);
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
