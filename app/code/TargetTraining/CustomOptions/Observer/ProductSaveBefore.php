<?php

namespace TargetTraining\CustomOptions\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class ProductSaveBefore implements ObserverInterface
{
    /**
     * @var DateTime
     */
    protected $date;


    public function __construct(
        DateTime $dateTime
    ){
        $this->date = $dateTime;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $check = false;
        $product = $observer->getEvent()->getProduct();
        foreach ($product->getOptions() as $customOption) {
            $values = $customOption->getData('values');
            foreach ($values as $_value) {
                if(!$_value['disabled']){
                    $title = explode(' ', $_value['title']);
                    if (count($title) > 1) {
                        $optionMonth = substr($title[1], 0, 3);
                        if(isset($title[0]) && isset($title[1]) && isset($title[2])){
                            $dateNow = $this->date->gmtTimestamp();
                            $dateOption = $title[2].'-'.$this->getMonth($optionMonth).'-'.$title[0];
                            $dateOption = $this->date->timestamp($dateOption);
                            if($dateNow < $dateOption){
                                $check = true;
                                break;
                            }
                        }
                    }
                }
            }
        }
        if($check){
            $product['option_available'] = $check;
        }else{
            $product['option_available'] = $check;
        }
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
}