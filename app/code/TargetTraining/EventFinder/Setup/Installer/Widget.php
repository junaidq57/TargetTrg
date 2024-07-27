<?php

namespace TargetTraining\EventFinder\Setup\Installer;

use Magento\Framework\Setup\SampleData\InstallerInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\Store;
use Magento\Widget\Model\Widget\InstanceFactory as WidgetInstanceFactory;

/**
 * Class Widget
 */
class Widget implements InstallerInterface
{
    /**
     * @var \Magento\Widget\Model\Widget\InstanceFactory
     */
    private $widgetInstanceFactory;

    /**
     * @var \Magento\Framework\View\DesignInterface
     */
    private $design;

    /**
     * @param \Magento\Widget\Model\Widget\InstanceFactory $widgetInstanceFactory
     * @param DesignInterface $design
     */
    public function __construct(
        WidgetInstanceFactory $widgetInstanceFactory,
        DesignInterface $design
    ) {
        $this->widgetInstanceFactory = $widgetInstanceFactory;
        $this->design                = $design;
    }


    /**
     * Install sample data
     */
    public function install()
    {
        $this->createHomepageWidgets();
    }

    /**
     * Create homepage widgets
     */
    private function createHomepageWidgets()
    {
        $data = [
            'title' => 'Homepage - Column 3',
            'widget_parameters' => [],
            'sort_order' => 3,
            'instance_type' => \TargetTraining\EventFinder\Block\Widget::class,
            'theme_id' => $this->getThemeId(),
            'store_ids' => [Store::DEFAULT_STORE_ID],
            'page_groups' => [
                [
                    'page_group' => 'pages',
                    'pages' => [
                        'page_id' => null,
                        'layout_handle' => 'cms_index_index',
                        'block' => 'homepage.widgets',
                        'for' => 'all'
                    ]
                ]
            ]
        ];

        $widgetInstance = $this->widgetInstanceFactory->create();
        $widgetInstance->setData($data);
        $widgetInstance->getResource()->save($widgetInstance);
    }

    /**
     * Get theme id
     *
     * @return int
     */
    private function getThemeId()
    {
        return (int) $this->design->getConfigurationDesignTheme(DesignInterface::DEFAULT_AREA);
    }
}
