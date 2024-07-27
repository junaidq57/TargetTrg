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
namespace Magenest\Ticket\Block\Adminhtml\Product\Edit\Tab\Options;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Option as ProductTabOption;
use Magento\Backend\Block\Widget;
use Magento\Catalog\Model\Product;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Model\Config\Source\Yesno as ConfigYesno;
use Magento\Catalog\Model\Config\Source\Product\Options\Type as ProductType;
use Magento\Framework\Registry;
use Magento\Catalog\Model\ProductOptions\ConfigInterface;
use Magenest\Ticket\Model\Config\Source\Product\Options\Type as TicketProductType;
use Magento\Store\Model\Store;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Model\Product\Option as ProductOption;
use Magento\Framework\DataObject;
use Magenest\Ticket\Model\EventoptionFactory;
use Magenest\Ticket\Model\ProductOptions\Config as TicketConfig;

/**
 * Class Option
 * @package Magenest\Ticket\Block\Adminhtml\Product\Edit\Tab\Options
 */
class Option extends ProductTabOption
{
    /**
     * @var string
     */
    protected $_template = 'catalog/product/edit/options/option.phtml';

    /**
     * @var \Magenest\Ticket\Model\Config\Source\Product\Options\Type
     */
    protected $_optionType;

    /**
     * @var EventoptionFactory
     */
    protected $_eventoptionFactory;

    /**
     * @param Context $context
     * @param ConfigYesno $configYesNo
     * @param ProductType $optionType
     * @param Product $product
     * @param Registry $registry
     * @param ConfigInterface $productOptionConfig
     * @param TicketProductType $optionTicketType
     * @param EventoptionFactory $eventoptionFactory
     * @param TicketConfig $ticketConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigYesno $configYesNo,
        ProductType $optionType,
        Product $product,
        Registry $registry,
        ConfigInterface $productOptionConfig,
        TicketProductType $optionTicketType,
        EventoptionFactory $eventoptionFactory,
        TicketConfig $ticketConfig,
        array $data = []
    ) {
        parent::__construct($context, $configYesNo, $optionType, $product, $registry, $productOptionConfig, $data);
        $this->_optionType = $optionTicketType;
        $this->_eventoptionFactory = $eventoptionFactory;
        $this->_productOptionConfig = $ticketConfig;
    }

    /**
     * @return \Magento\Framework\DataObject[]
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getOptionValues()
    {
        $optionsArr = $this->getProduct()->getOptions();
        if ($optionsArr == null) {
            $optionsArr = [];
        }

        if (!$this->_values || $this->getIgnoreCaching()) {
            $showPrice = $this->getCanReadPrice();
            $values = [];
            $scope = (int)$this->_scopeConfig->getValue(
                Store::XML_PATH_PRICE_SCOPE,
                ScopeInterface::SCOPE_STORE
            );
            foreach ($optionsArr as $option) {
                /* @var $option \Magento\Catalog\Model\Product\Option */
                $this->setItemCount($option->getOptionId());

                $value = [];
                $value['id'] = $option->getOptionId();
                $value['item_count'] = $this->getItemCount();
                $value['option_id'] = $option->getOptionId();
                $value['title'] = $option->getTitle();
                $value['type'] = $option->getType();
                $value['is_require'] = $option->getIsRequire();
                $value['sort_order'] = $option->getSortOrder();
                $value['can_edit_price'] = $this->getCanEditPrice();

                if ($option->getGroupByType() == ProductOption::OPTION_GROUP_SELECT) {
                    $i = 0;
                    $itemCount = 0;
                    foreach ($option->getValues() as $_value) {
                        /* @var $_value \Magento\Catalog\Model\Product\Option\Value */
                        $model = $this->_eventoptionFactory->create();
                        $model->loadByOptionTypeId($_value->getOptionTypeId());
                        $qty = $model->getQty();
                        $value['optionValues'][$i] = [
                            'item_count' => max($itemCount, $_value->getOptionTypeId()),
                            'option_id' => $_value->getOptionId(),
                            'option_type_id' => $_value->getOptionTypeId(),
                            'title' => $_value->getTitle(),
                            'price' => $showPrice ? $this->getPriceValue(
                                $_value->getPrice(),
                                $_value->getPriceType()
                            ) : '',
                            'price_type' => $showPrice ? $_value->getPriceType() : 0,
                            'sku' => $_value->getSku(),
                            'qty' => $qty,
                            'sort_order' => $_value->getSortOrder(),
                        ];

                        if ($this->getProduct()->getStoreId() != '0') {
                            $value['optionValues'][$i]['checkboxScopeTitle'] = $this->getCheckboxScopeHtml(
                                $_value->getOptionId(),
                                'title',
                                is_null($_value->getStoreTitle()),
                                $_value->getOptionTypeId()
                            );
                            $value['optionValues'][$i]['scopeTitleDisabled'] = is_null(
                                $_value->getStoreTitle()
                            ) ? 'disabled' : null;
                            if ($scope == Store::PRICE_SCOPE_WEBSITE) {
                                $value['optionValues'][$i]['checkboxScopePrice'] = $this->getCheckboxScopeHtml(
                                    $_value->getOptionId(),
                                    'price',
                                    is_null($_value->getstorePrice()),
                                    $_value->getOptionTypeId(),
                                    ['$(this).up(1).previous()']
                                );
                                $value['optionValues'][$i]['scopePriceDisabled'] = is_null(
                                    $_value->getStorePrice()
                                ) ? 'disabled' : null;
                            }
                        }
                        $i++;
                    }
                }
                $values[] = new DataObject($value);
            }
            $this->_values = $values;
        }

        return $this->_values;
    }

    /**
     * Retrieve html templates for different types of product custom options
     *
     * @return string
     */
    public function getTemplatesHtml()
    {
        $canEditPrice = $this->getCanEditPrice();
        $canReadPrice = $this->getCanReadPrice();
        $this->getChildBlock('select_option_type')->setCanReadPrice($canReadPrice)->setCanEditPrice($canEditPrice);

        $templates = $this->getChildHtml('select_option_type');

        return $templates;
    }
}
