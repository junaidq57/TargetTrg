<?php

namespace TargetTraining\Homepage\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\SampleData\Executor;
use TargetTraining\Homepage\Setup\Installer\StaticBlock as StaticBlockInstaller;
use TargetTraining\Homepage\Setup\Installer\Widget as WidgetInstaller;

/**
 * Class InstallData
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Framework\Setup\SampleData\Executor
     */
    private $executor;

    /**
     * @var \TargetTraining\Homepage\Setup\Installer\StaticBlock
     */
    private $staticBlockInstaller;

    /**
     * @var \TargetTraining\Homepage\Setup\Installer\Widget
     */
    private $widgetInstanceInstaller;

    /**
     * @param \Magento\Framework\Setup\SampleData\Executor         $executor
     * @param \TargetTraining\Homepage\Setup\Installer\StaticBlock $staticBlockInstaller
     * @param \TargetTraining\Homepage\Setup\Installer\Widget      $widgetInstaller
     */
    public function __construct(
        Executor $executor,
        StaticBlockInstaller $staticBlockInstaller,
        WidgetInstaller $widgetInstaller
    ) {
        $this->executor = $executor;
        $this->staticBlockInstaller = $staticBlockInstaller;
        $this->widgetInstanceInstaller = $widgetInstaller;
    }

    /**
     * Install CMS static block and widget data
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface   $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->executor->exec($this->staticBlockInstaller);
        $this->executor->exec($this->widgetInstanceInstaller);
    }
}
