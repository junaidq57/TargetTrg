<?php
namespace TargetTraining\CustomizedReport\Controller\Adminhtml\Report\Sales;

class ExportCsv extends \Magento\Backend\App\Action
{
    protected $_fileFactory;
    protected $_response;
    protected $_view;
    protected $directory;
    protected $converter;
    protected $directory_list;
    protected $_order;
    protected $_orderConfig;
    protected $_report;
    protected $_customerGroupCollection;
    protected $helper;

    public function __construct(\Magento\Backend\App\Action\Context  $context,
     \Magento\Sales\Model\Order $order,
     \Magento\Sales\Model\Order\Config $orderConfig,
     \TargetTraining\CustomizedReport\Block\Adminhtml\Sales\Report $report,
     \Magento\Customer\Model\Group $customerGroupCollection,
     \TargetTraining\CustomizedReport\Helper\Data $helper
     ) {
      $this->_order = $order;
      $this->_orderConfig = $orderConfig;
      $this->_report = $report;
      $this->_customerGroupCollection = $customerGroupCollection;
      $this->helper = $helper;
      parent::__construct($context);
    }

    public function execute()
    {
      $filter = $this->getRequest()->getParam('filter');
      $currentPage = $this->getRequest()->getParam('p');
        $date = new \DateTime('now');
        $date = $date->setTimezone(new \DateTimeZone($this->helper->getTimezoneConfig()));
        $dtfm =  $date->format('dmY_His');

      $fileName = 'orders_summary_report_'.$dtfm.'.csv';
      $salesOrders = $this->getSalesOrders();
      $content = "";
      $columns = $this->getCoulmns();
      $i = 1;
      // head
      foreach($columns as $fieldName=>$label){
          if($i == count($columns)){
              $content .= $label.PHP_EOL;
              $i = 1;
          }else{
              $content .= $label.',';
          }
          $i++;
      }
      $salesOrdersData = $this->_report->saleOrderGridCollection();
      $gridData = $salesOrdersData->join(
              ['so'=>'sales_order'],
              '`main_table`.`increment_id` = `so`.`increment_id`', 
              ['*']
      );
      if($filter){
          $filter = base64_decode($filter);
          $filter = parse_str($filter, $filterArr);
          $dateFrom = date('Y-m-d 00:00:00', strtotime($filterArr['from']));
          $dateTo = date('Y-m-d 23:59:59', strtotime($filterArr['to']));
          $gridData->getSelect()->where("`so`.`created_at` BETWEEN '".$dateFrom."' AND '".$dateTo."'");
      }
      // if($currentPage){
      //     $gridData->getSelect()->limitPage($currentPage, $this->helper->getLimitRecords());
      // }

        $reportClass = $this->_objectManager->create(
            'TargetTraining\CustomizedReport\Block\Adminhtml\Sales\Report'
        );
      
      foreach($gridData as $salesOrder){
          $salesOrder['increment_id'] = '="'.$salesOrder['increment_id'].'"';
          $storeName = $this->_report->getOrderStoreName($salesOrder);
          $customerGroupCollection = $this->_customerGroupCollection->load($salesOrder->getCustomerGroupId());
          $customerGroupName = $customerGroupCollection->getCustomerGroupCode();

          $j = 1;
          foreach($columns as $fieldName=>$label){
              if($j == count($columns)){
                  if(isset($salesOrder[$fieldName])){
                      if(strpos($salesOrder[$fieldName], ',')){
                        $avoidCommaInDataField = '"'.$salesOrder[$fieldName].'"';
                      }else{
                        $avoidCommaInDataField = $salesOrder[$fieldName];
                      }
                      if($fieldName == 'store_name'){
                          $value = $storeName;
                      }elseif($fieldName == 'customer_group_id'){
                          $value = $customerGroupName;
                      }else{
                          $value = $this->avoidBreakCell($avoidCommaInDataField);
                      }
                      $content .= $value.PHP_EOL; 
                  }else{
                      if($fieldName == 'payment_date_time'){
                          $content .= $salesOrder['updated_at'].PHP_EOL;
                      }elseif($fieldName == 'base_discount_amount_percent'){
                          $content .= number_format(abs(($salesOrder['base_discount_amount']*100)/$salesOrder['base_subtotal_incl_tax']), 3).PHP_EOL;
                      }else{
                          $content .= 'NULL'.PHP_EOL;
                      }
                  }
              }else{
                  if(isset($salesOrder[$fieldName])){
                      if(strpos($salesOrder[$fieldName], ',')){
                        $avoidCommaInDataField = '"'.$salesOrder[$fieldName].'"';
                      }else{
                        $avoidCommaInDataField = $salesOrder[$fieldName];
                      }
                      if($fieldName == 'store_name'){
                          $value = $storeName;
                      }elseif($fieldName == 'created_at'){
                          $value = $reportClass->getOrderAdminDate($salesOrder[$fieldName]);
                      }elseif($fieldName == 'customer_group_id'){
                          $value = $customerGroupName;
                      }else{
                          $value = $this->avoidBreakCell($avoidCommaInDataField);
                      }
                      $content .= $value.',';
                  }
                  else{

                      if($fieldName == 'payment_date_time'){
                          if(isset($salesOrder['updated_at'])){
                              $updateAt = $reportClass->getOrderAdminDate($salesOrder['updated_at']);
                              $content .= $updateAt.',';
                          }
                      }elseif($fieldName == 'base_discount_amount_percent'){
                          $content .= number_format(abs(($salesOrder['base_discount_amount']*100)/$salesOrder['base_subtotal_incl_tax']), 3).',';
                      }else{
                          $content .= 'NULL'.',';
                      }
                  }
              }
              $j++;
          }
      }
      $this->_sendUploadResponse($fileName, $content);

    }

    // avoid break cell
    public function avoidBreakCell($string){
        $value = urlencode($string);
        $value = str_replace('%0A', '+', $value);
        $value = urldecode($value);
        return $value;
    }

    public function getSalesOrders(){
      $collection = $this->_order->getCollection()
          ->addFieldToSelect(
              '*'
          )
          ->addFieldToFilter(
              'status',
              ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
          );
      return $collection;
    }

    public function getCoulmns(){
        return [
        'entity_id' => 'ID',
        'store_name' => 'Purchase Point',
        'created_at' => 'Purchase Date',
        'payment_date_time' => 'Payment Date Time',
        'increment_id' => 'Order number',
        'billing_name' => 'Bill-to Name',
        'shipping_name' => 'Ship-to Name',
        'base_grand_total' => 'Grand Total (Base)',
        'base_discount_amount_percent' => 'Discount Percent',
        'base_discount_amount' => 'Discount Total',
        'base_tax_amount' => 'Tax Amount',
        'grand_total' => 'Grand Total (Purchased)',
        'status' => 'Status',
        'billing_address' => 'Billing Address',
        'shipping_address' => 'Shipping Address',
        'shipping_description' => 'Shipping Information',
        'customer_email' => 'Customer Email',
        'customer_group_id' => 'Customer Group',
        'base_subtotal_incl_tax' => 'Subtotal',
        'shipping_and_handling' => 'Shipping and Handling',
        'customer_name' => 'Customer Name',
        'payment_method' => 'Payment Method',
        'base_total_refunded' => 'Total Refunded',
        'coupon_code' => 'Coupon Code Used',
        'updated_at' => 'Updated Date Time',
        // 'Payment Reference ID' => 
      ];
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {

       $this->_response->setHttpResponseCode(200)
       ->setHeader('Pragma', 'public', true)
       ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
       ->setHeader('Content-type', $contentType, true)
       ->setHeader('Content-Length', strlen($content), true)
       ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"', true)
       ->setHeader('Last-Modified', date('r'), true)
       ->setBody($content)
       ->sendResponse();
       die;
    }
}
