<?php
namespace TargetTraining\CustomizedReport\Controller\Adminhtml\Report\Sales;

class OrderDetailsExportCsv extends \Magento\Backend\App\Action
{
    protected $_orderdetails;
    protected $helper;

    public function __construct(
        \Magento\Backend\App\Action\Context  $context,
        \TargetTraining\CustomizedReport\Block\Adminhtml\Sales\Orderdetails $orderdetails,
        \TargetTraining\CustomizedReport\Helper\Data $helper
     ) {
        $this->_orderdetails = $orderdetails;
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

        $fileName = 'order_details_report_'.$dtfm.'.csv';
        $data = $this->_orderdetails->getOrderItems(true);
        $content = "";
        $columns = $this->getColumns();
        $i = 1;
        // head
        foreach($columns as $fieldName=>$label){
            if($i == count($columns)){
                $content .= $label.PHP_EOL;
            }else{
                $content .= $label.',';
            }
            $i++;
        }
        //body
        foreach($data as $orderItems){
            foreach($orderItems as $items){
                $j = 1;
                foreach($items as $itemData){
                    if($j == count($items)){
                      $content .= $itemData.PHP_EOL;
                    }else{
                        $content .= $itemData.',';
                    }
                    $j++;
                }
            }
        }
        $this->_sendUploadResponse($fileName, $content);

    }

    public function avoidCommaInLine($string){
        if(strpos($string, ',')){
            return '"'.$string.'"';
        }else{
            return $string;
        }
    }

    public function getColumns(){
        return [
        'product_name' => 'Course',
        'course_location' => 'Venue',
        'option_date' => 'Date',
        'leader' => 'Leader',
        'attendee' => 'Attendee',
        'payment_method' => 'Payment Method',
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
