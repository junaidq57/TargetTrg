<?php
namespace TargetTraining\FeaturedCourse\Block;

/**
 * Class Category
 * @package TargetTraining\FeaturedCourse\Block
 */
class Category extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $_cartHelper;

    /**
     * Category constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Checkout\Helper\Cart $cartHelper,
        array $data = []
    )
    {
        $this->_registry = $registry;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_cartHelper = $cartHelper;
        parent::__construct($context, $data);
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }

    public function getCollectionProduct(){
        $sku= $this->getCurrentCategory()->getFeaturedCourse();
        $arraySku = explode(',', $sku??'');
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addFieldToFilter('sku', array('in' => $arraySku));
        $collection->addAttributeToFilter('option_available', 1);
        return $collection;
    }

    public function getAddToCartUrl($product, $additional = [])
    {
        return $this->_cartHelper->getAddUrl($product, $additional);
    }

}
?>