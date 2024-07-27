<?php


namespace TargetTraining\CustomizedReport\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Sales\Setup\SalesSetup;

class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var EavSetupFactory
     */
    /**
     * @var SalesSetup
     */
    private $salesSetup;

    public function __construct(
        SalesSetup $salesSetup
    ) {
        $this->salesSetup = $salesSetup;
    }
    /**
     * {@inheritdoc}
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), "0.0.2", "<")) {
            $attributeOptions = [
                'type'     => Table::TYPE_TIMESTAMP,
                'visible'  => true,
                'required' => false
            ];
            $this->salesSetup->addAttribute('order_item', 'booking_date', $attributeOptions);
        }
    }
}
