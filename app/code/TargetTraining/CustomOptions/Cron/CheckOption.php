<?php

namespace TargetTraining\CustomOptions\Cron;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Catalog\Model\Product\OptionFactory;
use \Magento\Framework\ObjectManagerInterface as ObjectManager;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
/**
 * Class CheckOption
 * @package TargetTraining\CustomOptions\Cron
 */
class CheckOption
{
    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var OptionFactory
     */
    protected $optionFactory;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var ProductFactory
     */
    protected $productFactory;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * CheckOption constructor.
     * @param DateTime $date
     * @param OptionFactory $optionFactory
     * @param ObjectManager $objectManager
     * @param ProductFactory $productFactory
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        DateTime $date,
        OptionFactory $optionFactory,
        ObjectManager $objectManager,
        ProductFactory $productFactory,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->date = $date;
        $this->optionFactory = $optionFactory;
        $this->_objectManager = $objectManager;
        $this->productFactory = $productFactory;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
    }


    public function execute()
    {
        $optionActive = array();
        $optionInActive = array();
        $collection = $this->productFactory->create()->getCollection();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('attribute_set_id', $this->getConfig('mage_digital/attribute_set/course'));
        foreach ($collection as $item){
            if($this->checkOption($item)){
                $optionActive[]   = $item->getId();
            }else{
                $optionInActive[] = $item->getId();
            }
        }
        $storeIds = array_keys($this->_storeManager->getStores());
        $updateAttributes['option_available']    = true;
        $updateAttributesTwo['option_available'] = false;
        $this->productAction = $this->_objectManager->create(\Magento\Catalog\Model\Product\Action::class);
        foreach ($storeIds as $storeId) {
            $this->productAction->updateAttributes($optionActive, $updateAttributes, $storeId);
            $this->productAction->updateAttributes($optionInActive, $updateAttributesTwo, $storeId);
        }
        return $this;
    }

    public function checkOption($product){
        $customOptions = $this->optionFactory->create()->getProductOptionCollection($product);
        foreach ($customOptions as $customOption) {
            $values = $customOption->getValues();
            foreach ($values as $_value) {
                if(!$_value->getData('disabled')){
                    $title = explode(' ', $_value->getTitle());
                    if (count($title) > 1) {
                        $optionMonth = substr($title[1], 0, 3);
                        if(isset($title[0]) && isset($title[1]) && isset($title[2])){
                            $dateNow = $this->date->gmtTimestamp();
                            $dateOption = $title[2].'-'.$this->getMonth($optionMonth).'-'.$title[0];
                            $dateOption = $this->date->timestamp($dateOption);
                            if($dateNow < $dateOption){
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    public function getMonth($month){
        $months = array (
            1  => 'Jan',
            2  => 'Feb',
            3  => 'Mar',
            4  => 'Apr',
            5  => 'May',
            6  => 'Jun',
            7  => 'Jul',
            8  => 'Aug',
            9  => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec'
        );
        return array_search($month, $months);
    }

    public function getConfig($path){
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
