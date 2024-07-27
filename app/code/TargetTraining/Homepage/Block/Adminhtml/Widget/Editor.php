<?php

namespace TargetTraining\Homepage\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Element as WidgetFormElement;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory as ElementFactory;

/**
 * Class Editor
 */
class Editor extends WidgetFormElement
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    private $wysiwygConfig;

    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    private $elementFactory;

    /**
     * Editor constructor.
     *
     * @param \Magento\Backend\Block\Template\Context      $context
     * @param \Magento\Cms\Model\Wysiwyg\Config            $wysiwygConfig
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array                                        $data
     */
    public function __construct(
        Context $context,
        WysiwygConfig $wysiwygConfig,
        ElementFactory $elementFactory,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $editor = $this->createElementInstance('editor', ['data' => $element->getData()])
                       ->setLabel('')
                       ->setForm($element->getForm())
                       ->setWysiwyg(true)
                       ->setConfig($this->wysiwygConfig->getConfig(['add_variables' => false, 'add_widgets' => false]));

        if ($element->getRequired()) {
            $editor->addClass('required-entry');
        }

        $element->setData(
            'after_element_html', $this->_getAfterElementHtml() . $editor->getElementHtml()
        );

        return $element;
    }

    /**
     * @return string
     */
    private function _getAfterElementHtml()
    {
        $html = <<<HTML
    <style>
        .admin__field-control.control .control-value {
            display: none !important;
        }
    </style>
HTML;

        return $html;
    }

    /**
     * @param string $type
     * @param array  $options
     *
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    private function createElementInstance($type = 'editor', array $options = [])
    {
        return $this->elementFactory->create($type, $options);
    }
}
