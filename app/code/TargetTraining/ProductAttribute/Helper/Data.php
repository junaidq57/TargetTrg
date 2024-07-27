<?php

namespace TargetTraining\ProductAttribute\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
class Data extends AbstractHelper
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
    }

    public function getCourseDelivery()
    {
        return $this->registry->registry('current_product')->getCourseDelivery();
    }
}