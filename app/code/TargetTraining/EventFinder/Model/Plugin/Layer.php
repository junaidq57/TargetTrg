<?php

namespace TargetTraining\EventFinder\Model\Plugin;

use Magento\Catalog\Model\Layer as LayerModel;
use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection;

class Layer
{
    protected $locationHelper;

    public function beforePrepareProductCollection(
        LayerModel $subject,
        AbstractCollection $collection
    ) {
        return $subject;
    }
}
