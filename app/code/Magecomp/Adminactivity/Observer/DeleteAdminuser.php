<?php

namespace Magecomp\Adminactivity\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magecomp\Adminactivity\Helper\Data as Helper;
use \Magecomp\Adminactivity\Api\Activityrepositoryinterface;

/**
 * Class DeleteAdminuser
 * @package Magecomp\Adminactivity\Observer
 */
class DeleteAdminuser implements ObserverInterface
{
    /**
     * @var string
     */
    const SYSTEM_CONFIG = 'adminhtml_user_delete';

    /**
     * @var \Magecomp\Adminactivity\Model\Processor
     */
    private $processor;

    /**
     * @var \Magecomp\Adminactivity\Helper\Data
     */
    public $helper;

    /**
     * @var \Magecomp\Adminactivity\Helper\Activitytime
     */
    public $activitytime;

    /**
     * DeleteAdminuser constructor.
     * @param \Magecomp\Adminactivity\Model\Processor $processor
     * @param Helper $helper
     * @param \Magecomp\Adminactivity\Helper\Activitytime $activitytime
     */
    public function __construct(
        \Magecomp\Adminactivity\Model\Processor $processor,
        \Magecomp\Adminactivity\Helper\Data $helper,
        \Magecomp\Adminactivity\Helper\Activitytime $activitytime
    ) {
        $this->processor = $processor;
        $this->helper = $helper;
        $this->activitytime = $activitytime;
    }

    /**
     * Delete after
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Framework\Event\Observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $this->activitytime->start(__METHOD__);
        if (!$this->helper->getConfigValue("module_admin_user")) {
            
            return $observer;
        }
        $object = $observer->getEvent()->getObject();
        if ($this->processor->initAction==self::SYSTEM_CONFIG) {
          
            $this->processor->modelDeleteBefore($object);
        }
      
        $this->processor->modelDeleteAfter($object);
        $this->activitytime->end(__METHOD__);
    }
}
