<?php
/**
 * @category Mageants AllSlider
 * @package Mageants_AllSlider
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\AllSlider\Controller\Adminhtml\AllSlider;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;
use Magento\Backend\App\Action\Context;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @param Context                             $context
     * @param \Mageants\AllSlider\Model\AllSlider $allslider
     */
    public function __construct(
        Context $context,
        \Mageants\AllSlider\Model\AllSlider $allslider
    ) {
        $this->allslider = $allslider;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('allslider_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model =$this->allslider;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('The Slider has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['allslider_id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a slider to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
