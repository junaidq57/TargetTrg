<?php

namespace TargetTraining\Homepage\Setup\Installer;

use Magento\Cms\Model\BlockFactory as StaticBlockFactory;
use Magento\Framework\Setup\SampleData\InstallerInterface;
use Magento\Store\Model\Store;
use Magento\Widget\Model\Widget\InstanceFactory as WidgetInstanceFactory;
use Magento\Framework\View\DesignInterface;
use TargetTraining\Homepage\Block\Widget\Column;
use TargetTraining\WordPress\Block\Widget\LatestPosts;

/**
 * Class Widget
 */
class Widget implements InstallerInterface
{
    /**
     * Background colours
     */
    const BACKGROUND_COLOR_WHITE = 'white';
    const BACKGROUND_COLOR_BLUE = 'blue';
    const BACKGROUND_COLOR_RED = 'red';

    /**
     * @var \Magento\Widget\Model\Widget\InstanceFactory
     */
    private $widgetInstanceFactory;

    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    private $blockFactory;

    /**
     * @var \Magento\Framework\View\DesignInterface
     */
    private $design;

    /**
     * @param WidgetInstanceFactory $widgetInstanceFactory
     * @param StaticBlockFactory $blockFactory
     * @param DesignInterface $design
     */
    public function __construct(
        WidgetInstanceFactory $widgetInstanceFactory,
        StaticBlockFactory $blockFactory,
        DesignInterface $design
    ) {
        $this->widgetInstanceFactory = $widgetInstanceFactory;
        $this->blockFactory          = $blockFactory;
        $this->design                = $design;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->createHomepageWidgets();
        $this->createLatestBlogsWidget();
    }

    /**
     * Create homepage widgets
     */
    private function createHomepageWidgets()
    {
        $defaultData = $this->getDefaultWidgetData();
        $data = [
            [
                'title' => 'Homepage - Column 1',
                'widget_parameters' => [
                    'color' => self::BACKGROUND_COLOR_WHITE,
                    'block_id' => $this->getBlockIdByIdentifier('homepage_logo_block'),
                    'css_class' => 'logo'
                ],
                'sort_order' => 1
            ],
            [
                'title' => 'Homepage - Column 2',
                'widget_parameters' => [
                    'color' => self::BACKGROUND_COLOR_BLUE,
                    'block_id' => $this->getBlockIdByIdentifier('homepage_trainer_management')
                ],
                'sort_order' => 2
            ],
            [
                'title' => 'Homepage - Column 4',
                'widget_parameters' => [
                    'color' => self::BACKGROUND_COLOR_BLUE,
                    'block_id' => $this->getBlockIdByIdentifier('homepage_open_courses')
                ],
                'sort_order' => 4
            ],
            [
                'title' => 'Homepage - Column 5',
                'widget_parameters' => [
                    'color' => self::BACKGROUND_COLOR_BLUE,
                    'block_id' => $this->getBlockIdByIdentifier('homepage_train_the_trainer')
                ],
                'sort_order' => 5
            ],
            [
                'title' => 'Homepage - Column 6',
                'widget_parameters' => [
                    'color' => self::BACKGROUND_COLOR_RED,
                    'block_id' => $this->getBlockIdByIdentifier('homepage_coaching')
                ],
                'sort_order' => 6
            ],
            [
                'title' => 'Homepage - Column 7',
                'widget_parameters' => [
                    'color' => self::BACKGROUND_COLOR_BLUE,
                    'block_id' => $this->getBlockIdByIdentifier('homepage_management_training')
                ],
                'sort_order' => 7
            ],
            [
                'title' => 'Homepage - Column 8',
                'widget_parameters' => [
                    'color' => self::BACKGROUND_COLOR_RED,
                    'block_id' => $this->getBlockIdByIdentifier('homepage_in_house_training')
                ],
                'sort_order' => 8
            ],
            [
                'title' => 'Homepage - Column 9',
                'widget_parameters' => [
                    'color' => self::BACKGROUND_COLOR_BLUE,
                    'block_id' => $this->getBlockIdByIdentifier('homepage_qualification')
                ],
                'sort_order' => 9
            ],
            [
                'title' => 'Homepage - Column 10',
                'widget_parameters' => [
                    'color' => self::BACKGROUND_COLOR_RED,
                    'block_id' => $this->getBlockIdByIdentifier('homepage_learner_management')
                ],
                'sort_order' => 10
            ],
            [
                'title' => 'Homepage - Column 11',
                'widget_parameters' => [
                    'color' => self::BACKGROUND_COLOR_BLUE,
                    'block_id' => $this->getBlockIdByIdentifier('homepage_shop')
                ],
                'sort_order' => 11
            ],
            [
                'title' => 'Homepage - Column 12',
                'widget_parameters' => [
                    'color' => self::BACKGROUND_COLOR_RED,
                    'block_id' => $this->getBlockIdByIdentifier('homepage_meet_the_team')
                ],
                'sort_order' => 12
            ],

        ];

        foreach ($data as $widgetData) {
            $widget = array_merge($defaultData, $widgetData);
            $widgetInstance = $this->widgetInstanceFactory->create();
            $widgetInstance->setData($widget);
            $widgetInstance->getResource()->save($widgetInstance);
        }
    }

    /**
     * Get default widget data
     *
     * @return array
     */
    private function getDefaultWidgetData()
    {
        return [
            'instance_type' => Column::class,
            'theme_id' => $this->getThemeId(),
            'widget_parameters' => [],
            'store_ids' => [Store::DEFAULT_STORE_ID],
            'page_groups' => [
                [
                    'page_group' => 'pages',
                    'pages' => [
                        'page_id' => null,
                        'layout_handle' => 'cms_index_index',
                        'block' => 'homepage.widgets',
                        'for' => 'all',
                    ]
                ]
            ]
        ];
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

    /**
     * Get block id by it's identifier
     *
     * @param $string
     *
     * @return int
     */
    private function getBlockIdByIdentifier($string)
    {
        $block = $this->blockFactory->create();
        $block->getResource()->load($block, $string);

        return (int) $block->getId();
    }

    private function createLatestBlogsWidget()
    {
        $defaultData = $this->getDefaultWidgetData();
        $data = [
            'instance_type' => LatestPosts::class,
            'theme_id' => $this->getThemeId(),
            'widget_parameters' => [
                'section_title' => 'latest blogs',
                'post_limit' => 3,
                'show_date' => true,
                'show_excerpt' => true,
                'excerpt_length' => 40,
                'link_text' => 'Read More'
           ],
            'store_ids' => [Store::DEFAULT_STORE_ID],
            'page_groups' => [
                [
                   'page_group' => 'pages',
                   'pages' => [
                       'page_id' => null,
                       'layout_handle' => 'cms_index_index',
                       'block' => 'content.bottom',
                       'for' => 'all',
                       ]
                ]
            ]
       ];

        foreach ($data as $widgetData) {
            $widget = array_merge($defaultData, $widgetData);
            $widgetInstance = $this->widgetInstanceFactory->create();
            $widgetInstance->setData($widget);
            $widgetInstance->getResource()->save($widgetInstance);
        }
    }
}
