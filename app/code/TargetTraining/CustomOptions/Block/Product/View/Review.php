<?php

namespace TargetTraining\CustomOptions\Block\Product\View;

class Review extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Product
     */
    protected $_product = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $_stockRegistry;

    /**
     * @var \Magento\Eav\Api\AttributeSetRepositoryInterface
     */
    protected $attributeSet;

    /**
     * Review constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_stockRegistry = $stockRegistry;
        $this->attributeSet = $attributeSet;
        parent::__construct($context, $data);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }

    public function getQty(){
        $product = $this->getProduct();
        $stock = $this->_stockRegistry->getStockItem($product->getId());
        return $stock->getQty();
    }

    public function getCourseQuote() {
        return $this->getProduct()->getData('course_quote');
    }

    public function getCourseQuoter() {
        return $this->getProduct()->getData('course_quoter');
    }

    public function getAttributeSetName()
    {
        $attributeSetRepository = $this->attributeSet->get($this->getProduct()->getAttributeSetId());
        return $attributeSetRepository->getAttributeSetName();
    }
}