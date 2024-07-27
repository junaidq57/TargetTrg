<?php

namespace Magecomp\Adminactivity\Ui\Component\Listing\Column\ActionType;

/**
 * Class Options
 * @package Magecomp\Adminactivity\Ui\Component\Listing\Column\ActionType
 */
class Options implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magecomp\Adminactivity\Helper\Data
     */
    public $helper;

    /**
     * Options constructor.
     * @param \Magecomp\Adminactivity\Helper\Data $helper
     */
    public function __construct(\Magecomp\Adminactivity\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * List all option to get in filter
     * @return array
     */
    public function toOptionArray()
    {
        $data = [];
        $lableList = $this->helper->getAllActions();
        foreach ($lableList as $key => $value) {
            $data[] = ['value'=> $key,'label'=> __($value)];
        }
        return $data;
    }
}
