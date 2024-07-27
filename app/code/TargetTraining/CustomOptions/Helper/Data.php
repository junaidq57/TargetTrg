<?php

namespace TargetTraining\CustomOptions\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends AbstractHelper
{
    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Data constructor.
     * @param DateTime $date
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        DateTime $date,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->date = $date;
        $this->_scopeConfig = $scopeConfig;
    }

    public function getAttributeSet($product)
    {
        if($product->getAttributeSetId() == $this->getConfig('mage_digital/attribute_set/course')){
            return true;
        }
        return false;
    }


    public function getCustomOption($product){
        $customOptions = $product->getOptions();
        foreach ($customOptions as $customOption) {
            $values = $customOption->getValues();
            if(is_array($values) && !empty($values)){
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