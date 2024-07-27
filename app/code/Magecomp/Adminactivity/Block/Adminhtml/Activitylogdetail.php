<?php
namespace Magecomp\Adminactivity\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magecomp\Adminactivity\Api\Activityrepositoryinterface;
use Magecomp\Adminactivity\Helper\Browser;
use Magento\Backend\Block\Template\Context;

/**
 * Class ActivityLogListing
 * @package Magecomp\Adminactivity\Block\Adminhtml
 */
class Activitylogdetail extends Template
{
    /**
     * @var Activityrepositoryinterface
     */
    public $activityRepository;

    /**
     * @var Browser
     */
    public $browser;

    /**
     * Path to template file in theme.
     * @var string
     */
    public $_template = 'Magecomp_Adminactivity::activitylogdetail.phtml';

    /**
     * ActivityLogListing constructor.
     * @param Template\Context $context
     * @param Activityrepositoryinterface $activityRepository
     * @param Browser $browser
     */
    public function __construct(
        Context                     $context,
        Activityrepositoryinterface $activityRepository,
        Browser                     $browser
    ) {
        $this->activityRepository = $activityRepository;
        $this->browser = $browser;
        parent::__construct($context);
    }

    /**
     * Get admin activity log listing
     * @return array
     */
    public function getLogListing()
    {
        $id = $this->getRequest()->getParam('id');
        $data = $this->activityRepository->getActivityLog($id);
        return $data->getData();
    }

    /**
     * Get admin activity details
     * @return array
     */
    public function getAdminDetails()
    {
        $id = $this->getRequest()->getParam('id');
        $activity = $this->activityRepository->getActivityById($id);

        //$this->browser->reset();
        $this->browser->setUserAgent($activity->getUserAgent());
        $browser = $this->browser->__toString();

        $logData = [];
        $logData['username'] = $activity->getUsername();
        $logData['module'] = $activity->getModule();
        $logData['name'] = $activity->getName();
        $logData['fullaction'] = $activity->getFullaction();
        $logData['browser'] = $browser;
        $logData['date'] = $activity->getUpdatedAt();
        return $logData;
    }
}
