<?php

namespace TargetTraining\CustomOptions\Plugin\Block\Product\View;

class Options
{
    protected $_jsonEncoder;
    protected $_jsonDecoder;

    public function __construct(
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_jsonDecoder = $jsonDecoder;
    }

    public function afterGetJsonConfig(
        \MageWorx\OptionBase\Block\Product\View\Options $options,
        $result
    ) {
        $result = $this->_jsonDecoder->decode($result);
        foreach ($options->getOptions() as $option) {
            foreach ($option->getValues() as $valueId => $value) {
                $result[$option->getId()][$valueId]['delivered_by'] = $value['option_type_title_id'];
            }
        }
        return $this->_jsonEncoder->encode($result);
    }
}