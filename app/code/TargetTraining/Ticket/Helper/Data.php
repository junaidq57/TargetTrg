<?php

namespace TargetTraining\Ticket\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
class Data extends AbstractHelper
{
    /**
     * @var \Magenest\Ticket\Model\EventDateFactory
     */
    protected $date;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productloader;

    /**
     * @var \Magenest\Ticket\Model\EventLocationFactory
     */
    protected $location;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    private $productAttributeRepository;

    public function __construct(
        \Magenest\Ticket\Model\EventDateFactory $eventDateFactory,
        \Magenest\Ticket\Model\EventLocationFactory $eventLocationFactory,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository
    ) {
        $this->date = $eventDateFactory;
        $this->location = $eventLocationFactory;
        $this->_productloader = $_productloader;
        $this->stockRegistry = $stockRegistry;
        $this->_registry = $registry;
        $this->productAttributeRepository= $productAttributeRepository;
    }

    public function getDateStart($productId)
    {
        $location = $this->getLocation($productId);
        $date = $this->getDate($location->getId())->getFirstItem();
        return $date->getDateStart();
    }

    public function getLocation($productId)
    {
        $modelLocation = $this->location->create()->getCollection()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('location_is_enabled', 1)->getFirstItem();
        return $modelLocation;
    }

    public function getDate($locationId)
    {
        $date = $this->date->create()->getCollection()
            ->addFieldToFilter('event_location_id', $locationId);
        return $date;
    }

    public function getLoadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }

    public function getQty($id){
        $stock = $this->stockRegistry->getStockItem($id);
        return $stock->getQty();
    }

    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * @return array
     */
    public function getAvailableLocations()
    {
        $locations = [];

        $collection = $this->getAvailableLocationsCollection();

        foreach ($collection as $item) {
            if ($item->getValue()!='') {
                $data          = [];
                $data['value'] = $item->getValue();
                $data['label'] = $item->getLabel();

                $locations[] = $data;
            }
        }

        return $locations;
    }

    /**
     * @return bool|\Magento\Eav\Api\Data\AttributeOptionInterface[]|null
     */
    public function getAvailableLocationsCollection()
    {
        $attrCode = "course_location";
        try{
            return $this->productAttributeRepository->get($attrCode)->getOptions();
        }catch(\Exception $e){
            return false;
        }
    }
}