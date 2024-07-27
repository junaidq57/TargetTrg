<?php
namespace TargetTraining\CustomizedReport\Block\Adminhtml\Sales;
class Report extends \Magento\Framework\View\Element\Template
{    
    protected $_orderCollection;
    protected $_orderConfig;
    protected $_saleOrderGridCollection;
    protected $_localeDate;
    protected $timezone;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Sales\Model\Order $orderCollection,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $saleOrderGridCollection,
        array $data = []
    )
    {
        $this->_orderCollection = $orderCollection;
        $this->_orderConfig = $orderConfig;
        $this->_saleOrderGridCollection = $saleOrderGridCollection;
        $this->_localeDate = $context->getLocaleDate();
        $this->timezone = $context->getLocaleDate();
        parent::__construct($context, $data);
    }

    public function getSalesOrders(){
        
        $collection = $this->_saleOrderGridCollection
            ->addFieldToSelect(
                '*'
            )
            ->addFieldToFilter(
                'status',
                ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
            );
        
        $filter = $this->getRequest()->getParam('filter');
        if($filter){
            $filter = base64_decode($filter);
            $filter = parse_str($filter, $filterArr);
            $dateFrom = date('Y-m-d 00:00:00', strtotime($filterArr['from']));
            $dateTo = date('Y-m-d 23:59:59', strtotime($filterArr['to']));
            $collection->getSelect()->where("`created_at` BETWEEN '".$dateFrom."' AND '".$dateTo."'");
        }
            
        return $collection;
    }

    public function getSizeAllOfSalesOrders(){
        $saleOrders = $this->getSalesOrders();
        // $saleOrders->getSelect()->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        return $saleOrders->getSize();
        // $i = 0;
        // foreach($saleOrders as $order){
        //     $orderId = $order->getEntityId();
        //     $_order = $this->_orderCollection->load($orderId);
        //     if($order->getIncrementId() != $_order->getIncrementId()){
        //         continue;
        //     }
        //     $i++;
        // }
        // return $i;
    }

    public function getSalesOrdersEachPage(){
        $filter = $this->getRequest()->getParam('filter');
        if(!$filter){
            return false;
        }
        $currentPage = $this->getRequest()->getParam('p');
        if(!$currentPage){
            $currentPage = 1;
        }
        $collection = $this->getSalesOrders();
        $collection->getSelect()
        ->limitPage($currentPage, $this->getLayout()->createBlock('TargetTraining\CustomizedReport\Block\Adminhtml\Sales\Pagination')->getLimit());
        
        return $collection;
    }

    public function getAlreadyData($data){

        return isset($data)? $data : false;

    }

    public function getOrderStoreName($order)
    {
        if ($order) {
            $storeId = $order->getStoreId();
            if ($storeId === null) {
                $deleted = __(' [deleted]');
                return nl2br($this->getOrder()->getStoreName()) . $deleted;
            }
            $store = $this->_storeManager->getStore($storeId);
            $name = [$store->getWebsite()->getName(), $store->getGroup()->getName(), $store->getName()];

            return implode(" - ", $name);
        }

        return null;
    }

    public function saleOrderGridCollection(){
        return $this->_saleOrderGridCollection;
    }

    public function getFilterData(){
        $filter = $this->getRequest()->getParam('filter');
        return $filter;
    }

    public function getCurrentPage(){
        return $this->getRequest()->getParam('p');
    }

    public function getOrderAdminDate($createdAt)
    {
        $orderCreate = $this->_localeDate->date(new \DateTime($createdAt));
        $format = $this->timezone->formatDate(
            $orderCreate,
            \IntlDateFormatter::MEDIUM,
            true
        );
        return date("d/m/Y H:i:s", strtotime($format));
    }
}
?>