<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace TargetTraining\CustomizedReport\Block\Adminhtml\Sales;

/**
 * Adminhtml sales report page content block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Sales extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Template file
     *
     * @var string
     */
    protected $_template = 'report/grid/container.phtml';
    public $_productCollectionFactory;
    /**
     * {@inheritdoc}
     */

    protected function _construct()
    {
        $this->_blockGroup = 'TargetTraining_CustomizedReport';
        $this->_controller = 'adminhtml_sales_sales';
        $this->_headerText = __('Total Ordered Report');
        // parent::_construct();

        $this->buttonList->remove('add');
        $this->addButton(
            'filter_form_submit',
            ['label' => __('Show Report'), 'onclick' => 'filterFormSubmit()', 'class' => 'primary']
        );
    }

    /**
     * Get filter URL
     *
     * @return string
     */
    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/sales', ['_current' => true, '_query'=>'']);
    }

    public function getOrderDetailsFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/orderdetails', ['_current' => true, '_query'=>'']);
    }
}
