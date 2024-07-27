<?php

namespace TargetTraining\EventFinder\Controller\Search;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context as ActionContext;
use TargetTraining\EventFinder\Model\Layer\EventFinder;

class Result extends Action
{
    private $layerResolver;

    public function __construct(ActionContext $context, Resolver $layerResolver)
    {
        parent::__construct($context);
        $this->layerResolver = $layerResolver;
    }

    public function execute()
    {
        $this->layerResolver->create(EventFinder::LAYER_TYPE);

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
