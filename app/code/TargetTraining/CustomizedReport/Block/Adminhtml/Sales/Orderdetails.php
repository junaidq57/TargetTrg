<?php

namespace TargetTraining\CustomizedReport\Block\Adminhtml\Sales;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Filter;

class Orderdetails extends \Magento\Framework\View\Element\Template
{
    protected $_orderCollection;
    protected $_orderConfig;
    protected $_saleOrderGridCollection;
    protected $_helper;
    protected $_productEntity;
    protected $_productFactory;
    protected $_categoryFactory;
    protected $attributeSet;
    protected $_productRepository;
    protected $_resource;

    protected $orderItemRepository;
    protected $searchCriteriaBuilder;
    protected $filterBuilder;
    protected $_filterGroup;
    protected $_filter;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Sales\Model\Order $orderCollection,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $saleOrderGridCollection,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \TargetTraining\CustomizedReport\Helper\Data $helper,
        \Magento\Catalog\Model\Product $productEntity,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\App\ResourceConnection $resource,

        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        FilterGroup $filterGroup,
        Filter $filter,

        array $data = []
    )
    {
        $this->_orderCollection = $orderCollection;
        $this->_orderConfig = $orderConfig;
        $this->_saleOrderGridCollection = $saleOrderGridCollection;
        $this->_helper = $helper;
        $this->_productEntity = $productEntity;
        $this->_productFactory = $productFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->attributeSet = $attributeSet;
        $this->_productRepository = $productRepository;
        $this->_resource = $resource;

        $this->orderItemRepository = $orderItemRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->_filterGroup = $filterGroup;
        $this->_filter = $filter;

        parent::__construct($context, $data);
    }

    public function getSalesOrders()
    {
        $collection = $this->getOrderItemByDateFilter();

        return $collection;
    }

    public function getOrderItemByDateFilter()
    {
        $searchCriter = $this->searchCriteriaBuilder->create();

        $filter = $this->getRequest()->getParam('filter');
        if ($filter) {
            $filter = base64_decode($filter);
            $filter = parse_str($filter, $filterArr);
            $dateFrom = $newDate = date("Y-m-d 00:00:00", strtotime($filterArr['from']));
            $dateTo = date("Y-m-d 23:59:59", strtotime($filterArr['to']));
            $filters = $this->_filter
                ->setField("booking_date")
                ->setValue('')
                ->setConditionType("neq");

            $filterGroup = $this->_filterGroup->setFilters([$filters]);

            $searchCriter->setFilterGroups([$filterGroup]);
            $orderItems = $this->orderItemRepository->getList($searchCriter);

            $orderItems->getSelect()->where("`booking_date` BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'");
        }
        return $orderItems;
    }

    public function getSizeAllOfSalesOrders()
    {
        $saleOrders = $this->getSalesOrders();
        $saleOrders->getSelect()->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        return $saleOrders->getSize();
    }

    public function getSalesOrdersEachPage()
    {
        $filter = $this->getRequest()->getParam('filter');
        if (!$filter) {
            return false;
        }
        $currentPage = $this->getRequest()->getParam('p');
        if (!$currentPage) {
            $currentPage = 1;
        }
        $collection = $this->getSalesOrders();
        $collection->getSelect()
            ->limitPage($currentPage, $this->_helper->getLimitRecords());

        return $collection;
    }

    public function getOrderItems($whenExport = false)
    {
        $resultData = [];
        if ($whenExport) {
            $orderItems = $this->getSalesOrders();
        } else {
            $orderItems = $this->getSalesOrdersEachPage();
        }

        if ($orderItems) {
            foreach ($orderItems as $item) {
                $order = $item->getOrder();
                $orderId = $order->getEntityId();
                $_order = $this->_orderCollection->load($orderId);
                $payment = $_order->getPayment();
                $method = $payment->getMethodInstance();
                if ($order->getIncrementId() != $_order->getIncrementId()) {
                    $payment = '';
                    $method = '';
                }

                if ($item->getProductType() != 'simple') {
                    $productId = $item->getProductId();
                } else {
                    $productId = $this->getProductIdByOrderItem($item);
                }

                try {
                    $product = $this->_productFactory->create()->load($productId);

                    $attributeSetName = '';
                    if ($product->getAttributeSetId()) {
                        $attributeSetRepository = $this->attributeSet->get($product->getAttributeSetId());
                        $attributeSetName = $attributeSetRepository->getAttributeSetName();
                    }

                    if (!$product->getId() || $attributeSetName !== 'Course') {
                        continue;
                    }

                    if (isset($item->getData('product_options')['info_buyRequest']['attendees'])) {
                        $leader = '';
                        $optionDate = '';
                        if (isset($item->getData('product_options')['options'])) {
                            $connection = $this->_resource->getConnection();
                            $tableName = $this->_resource->getTableName('catalog_product_option_type_value');
                            $sql = "Select * FROM " . $tableName . " Where option_type_id = " . $item->getData('product_options')['options'][0]['option_value'];
                            $leader = $connection->fetchAll($sql)[0]['option_type_title_id'];
                            $optionDate = $this->orderItemGetOptionValue($item);
                        }

                        foreach (explode(",", $item->getData('product_options')['info_buyRequest']['attendees']) as $attendees) {
                            $resultData[$_order->getIncrementId()][] = [
                                'product_name' => $item->getName(),
                                'course_location' => $product->getAttributeText('course_location'),
                                'option_date' => explode(" - ", $optionDate)[0],
                                'leader' => $leader,
                                'attendee' => $attendees,
                                'payment_method' => $method->getTitle()
                            ];
                        }
                    }

                } catch (\Exception $e) {
                    continue;
                }
            }
            return $resultData;
        }
    }

    public function orderItemGetOptionValue($item){
        $optionValue = '';
        $optionDate = '';
        $options = $item->getProductOptions();
        if(isset($options['options'])) {
            foreach ($options['options'] as $optionItem){
                $optionValue = $optionItem['option_value'];
            }
        }
        if($optionValue != ''){
            $productOptions = $item->getProduct()->getOptions();
            foreach ($productOptions as $productOption){
                $optionValues  = $productOption->getValues();
                $opData = $optionValues[$optionValue];
                $optionDate = $opData->getTitle();
            }
        }
        return $optionDate;
    }

    public function getTotals()
    {
        $data = $this->getOrderItems();
        $qty = 0;
        $amount = 0;
        foreach ($data as $orderItems) {
            foreach ($orderItems as $items) {
                foreach ($items as $label => $itemData) {
                    if ($label == 'quantity') {
                        $qty += (float)$itemData;
                    }

                    if ($label == 'amount') {
                        $amount += (float)$itemData;
                    }
                }
            }
        }
        return ['qty' => $qty, 'price' => $amount];
    }

    public function getProductIdByOrderItem($item)
    {
        if (!isset($item->getProductOptions()['info_buyRequest']['selected_configurable_option']) || $item->getProductOptions()['info_buyRequest']['selected_configurable_option'] == NULL) {
            return $item->getProductId();
        } else {
            return $item->getProductOptions()['info_buyRequest']['selected_configurable_option'];
        }
    }

    public function getAlreadyData($data)
    {

        return isset($data) ? $data : false;

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

    public function saleOrderGridCollection()
    {
        return $this->_saleOrderGridCollection;
    }

    public function getFilterData()
    {
        $filter = $this->getRequest()->getParam('filter');
        return $filter;
    }

    public function getCurrentPage()
    {
        return $this->getRequest()->getParam('p');
    }
}

?>