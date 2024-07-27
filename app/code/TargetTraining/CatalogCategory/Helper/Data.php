<?php

namespace TargetTraining\CatalogCategory\Helper;



use Magento\Framework\App\Helper\Context;
use Laminas\Filter\FilterInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Laminas\Filter\FilterInterface
     */
    protected $templateProcessor;

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
     * Data constructor.
     * @param Context $context
     * @param \Laminas\Filter\FilterInterface $templateProcessor
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet
     */
    public function __construct(
        Context $context,
        \Laminas\Filter\FilterInterface $templateProcessor,
        \Magento\Framework\Registry $registry,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet
    )
    {
        parent::__construct($context);
        $this->templateProcessor = $templateProcessor;
        $this->_coreRegistry = $registry;
        $this->attributeSet = $attributeSet;
    }

    public function filterOutputHtml($string)
    {
        return $this->templateProcessor->filter($string);
    }

    public function getAttributeSetName()
    {
        $attributeSetRepository = $this->attributeSet->get($this->_coreRegistry->registry('product')->getAttributeSetId());
        if ($attributeSetRepository->getAttributeSetName()){
            return $attributeSetRepository->getAttributeSetName();
        }
        return false;
    }

    public function getCurrentCategory()
    {
        return $this->_coreRegistry->registry('current_category');
    }
}