<?php

namespace TargetTraining\CustomOptions\Block\Product\View;

class Tabs extends \Magento\Framework\View\Element\Template
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

    /**
     * @var \Magento\Eav\Api\AttributeSetRepositoryInterface
     */
    protected $attributeSet;

    /**
     * Tabs constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
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

    public function getCourseTimetable()
    {
        $courseTimetable = $this->getProduct()->getData('course_timetable');
        return $courseTimetable;
    }

    public function getDownloads()
    {
        $downloads = $this->getProduct()->getData('downloads');
        return $downloads;
    }

    public function getAttributeSetName()
    {
        $attributeSetRepository = $this->attributeSet->get($this->getProduct()->getAttributeSetId());
        return $attributeSetRepository->getAttributeSetName();
    }
}