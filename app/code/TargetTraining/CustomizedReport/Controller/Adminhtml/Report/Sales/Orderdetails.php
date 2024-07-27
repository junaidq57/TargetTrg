<?php

namespace TargetTraining\CustomizedReport\Controller\Adminhtml\Report\Sales;

use Magento\Reports\Model\Flag;

class Orderdetails extends \Magento\Reports\Controller\Adminhtml\Report\Sales\Sales
{
    /**
     * Sales report action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_setActiveMenu(
            'TargetTraining_CustomizedReport::order_details'
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Order Details Report'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_sales_sales.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([$gridBlock, $filterFormBlock]);

        $this->_view->renderLayout();
    }
}
