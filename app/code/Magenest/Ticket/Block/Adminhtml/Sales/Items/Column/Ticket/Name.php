<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 05/10/2016
 * Time: 14:05
 */
namespace Magenest\Ticket\Block\Adminhtml\Sales\Items\Column\Ticket;

/**
 * Class Name
 * @package Magenest\Ticket\Block\Adminhtml\Sales\Items\Column\Ticket
 */
class Name extends \Magento\Sales\Block\Adminhtml\Items\Column\Name
{
    /**
     * @var \Magenest\Ticket\Helper\Information
     */
    protected $information;

    /**
     * Name constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param \Magenest\Ticket\Helper\Information $information
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Magenest\Ticket\Helper\Information $information,
        array $data
    ) {
        $this->information = $information;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }

    /**
     * @param $options
     * @return array
     */
    public function getInfoTicket($options)
    {
        $data = $this->information->getAll($options);
        $info = $this->information->getDataTicket($data);

        return $info;
    }
}
