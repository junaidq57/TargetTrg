<?php

namespace TargetTraining\EventFinder\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\SampleData\Executor;

class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Framework\Setup\SampleData\Executor
     */
    private $executor;

    /**
     * @var \TargetTraining\EventFinder\Setup\Installer\Widget
     */
    private $widgetInstanceInstaller;

    /**
     * @param $executor
     * @param $widgetInstanceInstaller
     */
    public function __construct(Executor $executor, Installer\Widget $widgetInstanceInstaller)
    {
        $this->executor                = $executor;
        $this->widgetInstanceInstaller = $widgetInstanceInstaller;
    }


    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->executor->exec($this->widgetInstanceInstaller);
    }
}
