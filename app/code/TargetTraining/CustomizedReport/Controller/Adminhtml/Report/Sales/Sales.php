<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace TargetTraining\CustomizedReport\Controller\Adminhtml\Report\Sales;

use Magento\Reports\Model\Flag;

class Sales extends \Magento\Reports\Controller\Adminhtml\Report\Sales\Sales
{
    /**
     * Sales report action
     *
     * @return void
     */
    public function execute()
    {

        $this->_showLastExecutionTime(Flag::REPORT_ORDER_FLAG_CODE, 'sales');

        $this->_initAction()->_setActiveMenu(
            'TargetTraining_CustomizedReport::orders_summary'
        )->_addBreadcrumb(
            __('Sales Report'),
            __('Sales Report')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Orders Summary Report'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_sales_sales.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([$gridBlock, $filterFormBlock]);

        $this->_view->renderLayout();
    }
}
