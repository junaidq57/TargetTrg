<?php


namespace TargetTraining\CatalogCategory\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Category\Attribute\Source\Page;


class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }
    /**
     * {@inheritdoc}
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), "1.0.6", "<")) {

            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

                     $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'category_sidebar_content',
                [
                    'type' => 'int',
                    'label' => 'Sidebar Content',
                    'input' => 'select',
                    'sort_order' => 420,
                    'source' => Page::class,
                    'global' => 1,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => null,
                    'group' => 'Content',
                    'backend' => ''
                ]
            );
        }

        if (version_compare($context->getVersion(), "1.0.2", "<")) {

            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'category_top_content',
                [
                    'type' => 'text',
                    'label' => 'Top Content',
                    'input' => 'textarea',
                    'sort_order' => 400,
                    'source' => '',
                    'global' => 1,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => null,
                    'wysiwyg_enabled' => true,
                    'is_html_allowed_on_front' => true,
                    'group' => 'Content',
                    'backend' => ''
                ]
            );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'category_faqs',
                [
                    'type' => 'text',
                    'label' => 'FAQs',
                    'input' => 'textarea',
                    'sort_order' => 410,
                    'source' => '',
                    'global' => 1,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => null,
                    'wysiwyg_enabled' => true,
                    'is_html_allowed_on_front' => true,
                    'group' => 'Content',
                    'backend' => ''
                ]
            );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'category_bottom_content',
                [
                    'type' => 'text',
                    'label' => 'Bottom Content',
                    'input' => 'textarea',
                    'sort_order' => 420,
                    'source' => '',
                    'global' => 1,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => null,
                    'wysiwyg_enabled' => true,
                    'is_html_allowed_on_front' => true,
                    'group' => 'Content',
                    'backend' => ''
                ]
            );
        }

    }
}
