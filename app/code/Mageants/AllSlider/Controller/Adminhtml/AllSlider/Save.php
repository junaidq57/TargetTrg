<?php
/**
 * @category Mageants AllSlider
 * @package Mageants_AllSlider
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\AllSlider\Controller\Adminhtml\AllSlider;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\TestFramework\ErrorLog\Logger;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $_jsHelper;

    /**
     * @var \Mageants\AllSlider\Model\ResourceModel\Contact\CollectionFactory
     */
    protected $_allSliderCollectionFactory;

   /**
    *
    * @param Context $context
    * @param \Magento\Backend\Helper\Js $jsHelper
    * @param \Mageants\AllSlider\Model\ResourceModel\AllSlider\CollectionFactory $allSliderCollectionFactory
    * @param \Mageants\AllSlider\Model\AllSlider $allslider
    * @param  \Magento\Backend\Model\Session $session
    */
    public function __construct(
        Context $context,
        \Magento\Backend\Helper\Js $jsHelper,
        \Mageants\AllSlider\Model\ResourceModel\AllSlider\CollectionFactory $allSliderCollectionFactory,
        \Mageants\AllSlider\Model\AllSlider $allslider,
        \Magento\Backend\Model\Session $session
    ) {
        $this->_jsHelper = $jsHelper;
        $this->_allSliderCollectionFactory = $allSliderCollectionFactory;
        $this->allslider = $allslider;
        $this->session = $session;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
        
            /** @var \Mageants\AllSlider\Model\AllSlider $model */
            $model = $this->allslider;
            $id = $this->getRequest()->getParam('allslider_id');
            
            if (isset($data['stores'])) {
                if (in_array('0', $data['stores'])) {
                    $data['store_id'] = '0';
                } else {
                    $data['store_id'] = implode(",", $data['stores']);
                }
                unset($data['stores']);
            }
            
            if ($id) {
                $model->load($id);
            }
            $model->setData($data);
            try {
                $model->save();
                $this->saveProducts($model, $data);

                $this->messageManager->addSuccess(__('You saved this slider.'));

                $this->session->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', [
                        'allslider_id' =>
                        $model->getId(), '_current'
                        => true
                    ]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the slider.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', [
                'allslider_id' =>
                $this->getRequest()->getParam('allslider_id')
            ]);
        }
        return $resultRedirect->setPath('*/*/');
    }
    /**
     * To save products
     *
     * @param mixed $model
     * @param mixed $post
     * @return void
     */
    public function saveProducts($model, $post)
    {
        // Attach the attachments to slider
        if (isset($post['products'])) {
            $productIds = $this->_jsHelper->decodeGridSerializedInput($post['products']);
            try {
                $oldProducts = (array) $model->getProducts($model);
                $newProducts = (array) $productIds;

                $this->_resources = $this->_objectManager->get(\Magento\Framework\App\ResourceConnection::class);

                $connection = $this->_resources->getConnection();

                $table = $this->_resources->getTableName(
                    \Mageants\AllSlider\Model\ResourceModel\AllSlider::TBL_ATT_PRODUCT
                );
                $insert = array_diff($newProducts, $oldProducts);
                $delete = array_diff($oldProducts, $newProducts);

                if ($delete) {
                    $where = ['allslider_id = ?' => (int)$model->getId(), 'product_id IN (?)' => $delete];
                    $connection->delete($table, $where);
                }

                if ($insert) {
                    $data = [];
                    foreach ($insert as $product_id) {
                        $data[] = ['allslider_id' => (int)$model->getId(), 'product_id' => (int)$product_id];
                    }
                    $connection->insertMultiple($table, $data);
                }
            } catch (Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the slider.'));
            }
        }
    }
}
