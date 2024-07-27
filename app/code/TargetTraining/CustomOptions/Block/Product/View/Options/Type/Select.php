<?php

namespace TargetTraining\CustomOptions\Block\Product\View\Options\Type;

class Select extends \Magento\Catalog\Block\Product\View\Options\Type\Select
{

    protected $date;

    protected $logger;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        $this->date = $date;
        $this->logger = $logger;
        parent::__construct($context, $pricingHelper, $catalogData, $data);
    }
    /**
     * Return html for control element
     *
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getValuesHtml(): string
    {
        $_option = $this->getOption();

        $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $_option->getId());
        $availablebooknumber = ($this->getProduct()->getData('availabletobooknumbers'));
        $getProductPhrases = ($this->getProduct()->getData('phrases'));
        $store = $this->getProduct()->getStore();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
        $mediaUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)."wysiwyg/NEW_DATES_ADDED.png";

        $this->setSkipJsReloadPrice(1);
        // Remove inline prototype onclick and onchange events

        if ($_option->getType() == \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_DROP_DOWN ||
            $_option->getType() == \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_MULTIPLE
        ) {
            $require = $_option->getIsRequire() ? ' required' : '';
            $extraParams = '';
            $select = $this->getLayout()->createBlock(
                \Magento\Framework\View\Element\Html\Select::class
            )->setData(
                [
                    'id' => 'select_' . $_option->getId(),
                    'class' => $require . ' product-custom-option admin__control-select'
                ]
            );
            if ($_option->getType() == \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_DROP_DOWN) {
                $select->setName('options[' . $_option->getId() . ']')->addOption('', __('-- Please Select --'));
            } else {
                $select->setName('options[' . $_option->getId() . '][]');
                $select->setClass('multiselect admin__control-multiselect' . $require . ' product-custom-option');
            }
            foreach ($_option->getValues() as $_value) {
                
                $title = explode(' ', $_value->getTitle());
                if (count($title) > 1) {
                    $optionDate = explode(' ', $_value->getTitle())[0];
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
                $priceStr = $this->_formatPrice(
                    [
                        'is_percent' => $_value->getPriceType() == 'percent',
                        'pricing_value' => $_value->getPrice($_value->getPriceType() == 'percent'),
                    ],
                    false
                );
                $select->addOption(
                    $_value->getOptionTypeId(),
                    $_value->getTitle() . ' ' . strip_tags($priceStr) . '',
                    ['price' => $this->pricingHelper->currencyByStore($_value->getPrice(true), $store, false)]
                );
            }
            if ($_option->getType() == \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_MULTIPLE) {
                $extraParams = ' multiple="multiple"';
            }
            if (!$this->getSkipJsReloadPrice()) {
                $extraParams .= ' onchange="opConfig.reloadPrice()"';
            }
            $extraParams .= ' data-selector="' . $select->getName() . '"';
            $select->setExtraParams($extraParams);

            if ($configValue) {
                $select->setValue($configValue);
            }

            return $select->getHtml();
        }
        if ($_option->getType() == \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_RADIO ||
            $_option->getType() == \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_CHECKBOX
        ) {
            $selectHtml = '<div class="options-list nested" id="options-' . $_option->getId() . '-list">';
            $require = $_option->getIsRequire() ? ' required' : '';
            $arraySign = '';
            switch ($_option->getType()) {
                case \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_RADIO:
                    $type = 'radio';
                    $class = 'radio admin__control-radio';
                    if (!$_option->getIsRequire()) {
                        $selectHtml .= '<div class="field choice admin__field admin__field-option">' .
                            '<input type="radio" id="options_' .
                            $_option->getId() .
                            '" class="' .
                            $class .
                            ' product-custom-option" name="options[' .
                            $_option->getId() .
                            ']"' .
                            ' data-selector="options[' . $_option->getId() . ']"' .
                            ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"') .
                            ' value="" checked="checked" /><label class="label admin__field-label" for="options_' .
                            $_option->getId() .
                            '"><span>' .
                            __('None') . '</span></label></div>';
                    }
                    break;
                case \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_CHECKBOX:
                    $type = 'checkbox';
                    $class = 'checkbox admin__control-checkbox';
                    $arraySign = '[]';
                    break;
            }
            $count = 1;
            foreach ($_option->getValues() as $_value) {
                $count++;
                $optionDate = '';
                $optionMonth = '';
                $title = explode(' ', $_value->getTitle());
                if (count($title) > 1) {
                    $optionDate = explode(' ', $_value->getTitle())[0];
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
                $priceStr = $this->_formatPrice(
                    [
                        'is_percent' => $_value->getPriceType() == 'percent',
                        'pricing_value' => $_value->getPrice($_value->getPriceType() == 'percent'),
                    ]
                );

                $htmlValue = $_value->getOptionTypeId();
                if ($arraySign) {
                    $checked = is_array($configValue) && in_array($htmlValue, $configValue) ? 'checked' : '';
                } else {
                    $checked = $configValue == $htmlValue ? 'checked' : '';
                }

                $dataSelector = 'options[' . $_option->getId() . ']';
                if ($arraySign) {
                    $dataSelector .= '[' . $htmlValue . ']';
                }

          

      
                if($_value->getOptionTypeTitleId() == "Ralph Moody"){

                    $url = "https://targettrg.co.uk/about-us/the-team#ralph_moody";

                    $trainerLink =  "<a href='$url' target='_blank'>"  . $_value->getOptionTypeTitleId() . "</a>";

                }
                
                else if($_value->getOptionTypeTitleId() == "Claire Moody"){

                    $url = "https://targettrg.co.uk/about-us/the-team#claire_moody";

                    $trainerLink =  "<a href='$url' target='_blank'>" . $_value->getOptionTypeTitleId() . "</a>";
                }

                else if($_value->getOptionTypeTitleId() == "Scott Fraser")
                {
                    $url = "https://targettrg.co.uk/about-us/the-team#scott_fraser";

                    $trainerLink =  "<a href='$url' target='_blank'>" . $_value->getOptionTypeTitleId() . "</a>";
                }

                else if($_value->getOptionTypeTitleId() =="Bryan Shendon"){

                    $url = "https://targettrg.co.uk/about-us/the-team#bryan_shendon";

                    $trainerLink =  "<a href='$url' target='_blank'>" . $_value->getOptionTypeTitleId() . "</a>";
                }

                else{
                    $trainerLink = $_value->getOptionTypeTitleId();
                }


                if($availablebooknumber == 1){

                $selectHtml .= '<div class="field choice admin__field admin__field-option' .
                    $require .
                    '">' .
                    '<input type="' .
                    $type .
                    '" class="' .
                    $class .
                    ' ' .
                    $require .
                    ' product-custom-option"' .
                    ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"') .
                    ' name="options[' .
                    $_option->getId() .
                    ']' .
                    $arraySign .
                    '" id="options_' .
                    $_option->getId() .
                    '_' .
                    $count .
                    '" value="' .
                    $htmlValue .
                    '" ' .
                    $checked .
                    ' data-selector="' . $dataSelector . '"' .
                    ' price="' .
                    $this->pricingHelper->currencyByStore($_value->getPrice(true), $store, false) .
                    '" />' .
                    '<div class="date calendar" for="options_' . $_option->getId() . '_' . $count . '">'
                    .'<span class="date">' . $optionDate . '</span>'
                    .'<span class="month">' . $optionMonth .'</span>'.
                    '</div>'
                    .'<div class="course-info">'
                    .'<div class="spaces-remaining">'
                    . '<span class="turnoff-number"> ' . $getProductPhrases .'</span>'
                    .'</div>'
                    .'<div class="course-deliver">'
                    .'<span class="label">' . __('Course Delivery By') . '</span>'
                    .'<span class="author">' . $trainerLink .'</span>'
                    . ($_value->getDescription() == "New Dates Added" ?  '<span class="newly-added"> ' . '<img src="https://targettrg.co.uk/media/wysiwyg/New_Dates_Added.png" >' .'</span>'  :  '' ).
                    '</div>'
                    .'</div>'
                    .'<div class="label-qty label-show" style="display: inline-block; padding: 5px;"><b>' . __('Spaces') . '</b>'
                    . '<input name="value_' . $_value->getId() . '"'
                    . ' class="mageworx-value-qty value_' . $_value->getId() . '" type="number" min="0" disabled'
                    . ' style="width: 3em; text-align: center; vertical-align: middle;" />' .
                    '</div>';
                $selectHtml .= '</div>';
            }

            else{


                                $selectHtml .= '<div class="field choice admin__field admin__field-option' .
                    $require .
                    '">' .
                    '<input type="' .
                    $type .
                    '" class="' .
                    $class .
                    ' ' .
                    $require .
                    ' product-custom-option"' .
                    ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"') .
                    ' name="options[' .
                    $_option->getId() .
                    ']' .
                    $arraySign .
                    '" id="options_' .
                    $_option->getId() .
                    '_' .
                    $count .
                    '" value="' .
                    $htmlValue .
                    '" ' .
                    $checked .
                    ' data-selector="' . $dataSelector . '"' .
                    ' price="' .
                    $this->pricingHelper->currencyByStore($_value->getPrice(true), $store, false) .
                    '" />' .
                    '<div class="date calendar" for="options_' . $_option->getId() . '_' . $count . '">'
                    .'<span class="date">' . $optionDate . '</span>'
                    .'<span class="month">' . $optionMonth .'</span>'.
                    '</div>'
                    .'<div class="course-info">'
                    .'<div class="spaces-remaining">'
                    . ((int)$_value->getQty() == 0 ?  '<span class="labelsold"> ' . __('SOLD OUT') .'</span>'  :  '' ) 
                    . ((int)$_value->getQty() < 5 ?  '<span class="count2">' . (int)$_value->getQty() . '</span> <span class="nearly-full"> ' . __('Nearly Full') .'</span>'  :  '<span class="count">' . (int)$_value->getQty() . '</span> <span class="label"> ' . __('remaining spaces available') .'</span>' )
                    .'</div>'
                    .'<div class="course-deliver">'
                    .'<span class="label">' . __('Course Delivery By') . '</span>'
                    .'<span class="author">' . $trainerLink .'</span>'
                    . ($_value->getDescription() == "New Dates Added" ?  '<span class="newly-added"> ' . '<img src="https://targettrg.co.uk/media/wysiwyg/New_Dates_Added.png" >' .'</span>'  :  '' ).
                    '</div>'
                    .'</div>'
                    .'<div class="label-qty label-show" style="display: inline-block; padding: 5px;"><b>' . __('Spaces') . '</b>'
                    . '<input name="value_' . $_value->getId() . '"'
                    . ' class="mageworx-value-qty value_' . $_value->getId() . '" type="number" min="0" disabled'
                    . ' style="width: 3em; text-align: center; vertical-align: middle;" />' .
                    '</div>';
                $selectHtml .= '</div>';

                }
            }
            $selectHtml .= '</div>';

            return $selectHtml;
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
