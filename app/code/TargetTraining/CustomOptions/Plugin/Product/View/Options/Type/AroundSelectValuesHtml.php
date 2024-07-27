<?php

namespace TargetTraining\CustomOptions\Plugin\Product\View\Options\Type;

use \Magento\Catalog\Block\Product\View\Options\Type\Select;


class AroundSelectValuesHtml extends \MageWorx\OptionInventory\Plugin\Product\View\Options\Type\AroundSelectValuesHtml
{

    protected $date;
    public function __construct(
        \MageWorx\OptionInventory\Helper\Data $helperData,
        \MageWorx\OptionInventory\Helper\Stock $stockHelper,
        \Zend\Stdlib\StringWrapper\MbString $mbString,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->date = $date;
        parent::__construct($helperData, $stockHelper, $mbString);
    }

    /**
     * @param Select $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundGetValuesHtml(Select $subject, \Closure $proceed)
    {
        $isDisabledOutOfStockOptions = $this->helperData->isDisabledOutOfStockOptions();

        $result = $proceed();
        $option = $subject->getOption();

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;

        $this->mbString->setEncoding('UTF-8', 'html-entities');
        $result = $this->mbString->convert($result);
        
        libxml_use_internal_errors(true);
        $dom->loadHTML($result);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        $count = 1;
        foreach ($option->getValues() as $value) {
            $count++;
            $title = explode(' ', $value->getTitle());
            if (count($title) > 1) {
                $optionDate = explode(' ', $value->getTitle())[0];
                $optionMonth = substr($title[1], 0, 3);
                if(isset($title[0]) && isset($title[1]) && isset($title[2])){
                    $dateNow = $this->date->gmtTimestamp();
                    $dateOption = $title[2].'-'.$this->getMonth($optionMonth).'-'.$title[0];
                    $dateOption = $this->date->timestamp($dateOption);
                    if($dateNow > $dateOption){
                        continue;
                    }
                }
            }
            if ($option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DROP_DOWN ||
                $option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_MULTIPLE
            ) {
                $element = $elementSelect = $elementTitle =
                    $xpath->query('//option[@value="'.$value->getId().'"]')->item(0);
            }

            if ($option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_RADIO ||
                $option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_CHECKBOX
            ) {
                $element = $xpath
                    ->query('//div/div[descendant::div[@for="options_'.$option->getId().'_'.$count.'"]]')->item(0);
                $elementSelect = $element->getElementsByTagName('input')->item(0);
                $elementTitle = $xpath->query('//div[@for="options_'.$option->getId().'_'.$count.'"]')->item(0);
            }

            if ($option->getType() == 'swatch' || $option->getType() == 'multiswatch') {
                continue;
            }

            $isOutOfStockOption = $this->stockHelper->isOutOfStockOption($value);
            if ($isOutOfStockOption) {
                if (!$isDisabledOutOfStockOptions) {
                    $this->stockHelper->hideOutOfStockOption($element);
                    continue;
                } else {
                    $this->stockHelper->disableOutOfStockOption($elementSelect);
                }
            }

            $stockMessage = $this->stockHelper->getStockMessage($value, $option->getProductId());
//            $this->stockHelper->setStockMessage($dom, $elementTitle, $stockMessage);
        }

        $resultBody = $dom->getElementsByTagName('body')->item(0);//$dom->saveHTML();
        $result = $this->getInnerHtml($resultBody);

        return $result;
    }

    /**
     * @param \DOMElement $node
     * @return string
     */
    protected function getInnerHtml(\DOMElement $node)
    {
        $innerHTML= '';
        $children = $node->childNodes;
        foreach ($children as $child) {
            $innerHTML .= $child->ownerDocument->saveXML($child);
        }

        return $innerHTML;
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
