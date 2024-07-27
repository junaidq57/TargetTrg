<?php

namespace Magecomp\Adminactivity\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ViewAction
 * @package Magecomp\Adminactivity\Ui\Component\Listing\Column
 */
class Viewaction extends Column
{
    /**
     * @var UrlInterface
     */
    public $urlBuilder;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    public $layout;

    /**
     * ViewAction constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Magento\Framework\View\LayoutInterface $layout,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->layout = $layout;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Get item url
     * @return string
     */
    public function getViewUrl()
    {
        return $this->urlBuilder->getUrl(
            $this->getData('config/viewUrlPath')
        );
    }

    /**
     * Prepare Data Source
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['entity_id'])) {
                    $item[$this->getData('name')] = $this->layout->createBlock(
                        \Magento\Backend\Block\Widget\Button::class,
                        '',
                        [
                            'data' => [
                                'label' => __('Check Activity'),
                                'type' => 'button',
                                'disabled' => false,
                                'class' => 'action-activity-log-view',
                                'onclick' => 'adminActivityLogView.open(\''
                                    . $this->getViewUrl() . '\', \'' . $item['entity_id']
                                    . '\', \'' . $item['is_revertable'] . '\')',
                            ]
                        ]
                    )->toHtml();
                }
            }
        }

        return $dataSource;
    }
}
