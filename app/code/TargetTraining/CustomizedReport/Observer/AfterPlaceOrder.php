<?php

namespace TargetTraining\CustomizedReport\Observer;
class AfterPlaceOrder implements \Magento\Framework\Event\ObserverInterface
{
    protected $orderItemRepository;

    public function __construct(
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository
    )
    {
        $this->orderItemRepository = $orderItemRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $orderItems = $order->getAllItems();
        $date = '';
        $optionValue = '';
        foreach ($orderItems as $item) {
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
                    $date = $opData->getTitle();
                    // $dateFormat = (new \DateTime($date));
                    $dateFormat = (new \DateTime(str_replace('-', '', $date)));
                    $dateFormat = date_format($dateFormat, 'Y-m-d H:i:s');

                    $item->setData('booking_date',$dateFormat);
                }
            }
        }

    }
}