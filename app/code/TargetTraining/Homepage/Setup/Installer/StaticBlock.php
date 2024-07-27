<?php

namespace TargetTraining\Homepage\Setup\Installer;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Model\Block;
use Magento\Cms\Api\Data\BlockInterfaceFactory;
use Magento\Framework\Setup\SampleData\FixtureManager;
use Magento\Framework\Setup\SampleData\InstallerInterface;

/**
 * StaticBlock Installer
 */
class StaticBlock implements InstallerInterface
{
    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    private $fixtureManager;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    private $blockInterfaceFactory;

    /**
     * @param \Magento\Framework\Setup\SampleData\FixtureManager $fixtureManager
     * @param \Magento\Cms\Api\BlockRepositoryInterface          $blockRepository
     * @param \Magento\Cms\Api\Data\BlockInterfaceFactory        $blockInterfaceFactory
     */
    public function __construct(
        FixtureManager $fixtureManager,
        BlockRepositoryInterface $blockRepository,
        BlockInterfaceFactory $blockInterfaceFactory
    ) {
        $this->fixtureManager = $fixtureManager;
        $this->blockRepository = $blockRepository;
        $this->blockInterfaceFactory = $blockInterfaceFactory;
    }

    /**
     * Install static blocks
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function install()
    {
        $this->createStaticBlocks();
    }

    /**
     * Get static block contents from fixture files
     *
     * @param string $file
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getStaticBlockContent($file)
    {
        $filename = $this->fixtureManager->getFixture($file);

        return file_get_contents($filename);
    }

    /**
     * Create CMS static blocks
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    private function createStaticBlocks()
    {
        /** @var array $blockData */
        $blockData = $this->getBlockData();

        foreach ($blockData as $identifier => $data) {
            $block = $this->createStaticBlock($identifier, $data);
            $this->blockRepository->save($block);
        }
    }

    /**
     * Get block data array
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getBlockData()
    {
        return [
            'homepage_logo_block' => [
                'title' => 'Homepage - Logo Column',
                'content' => $this->getStaticBlockContent('TargetTraining_Homepage::fixtures/homepage_widget_1.html'),
            ],
            'homepage_trainer_management' => [
                'title' => 'Homepage - Trainer Management Column',
                'content' => $this->getStaticBlockContent('TargetTraining_Homepage::fixtures/homepage_widget_2.html'),
            ],
            'homepage_open_courses' => [
                'title' => 'Homepage - Open Courses Column',
                'content' => $this->getStaticBlockContent('TargetTraining_Homepage::fixtures/homepage_widget_3.html'),
            ],
            'homepage_train_the_trainer' => [
                'title' => 'Homepage - Train The Trainer Column',
                'content' => $this->getStaticBlockContent('TargetTraining_Homepage::fixtures/homepage_widget_4.html'),
            ],
            'homepage_coaching' => [
                'title' => 'Homepage - Coaching Column',
                'content' => $this->getStaticBlockContent('TargetTraining_Homepage::fixtures/homepage_widget_5.html'),
            ],
            'homepage_management_training' => [
                'title' => 'Homepage - Management Training Column',
                'content' => $this->getStaticBlockContent('TargetTraining_Homepage::fixtures/homepage_widget_6.html'),
            ],
            'homepage_in_house_training' => [
                'title' => 'Homepage - In House Training Column',
                'content' => $this->getStaticBlockContent('TargetTraining_Homepage::fixtures/homepage_widget_7.html'),
            ],
            'homepage_qualification' => [
                'title' => 'Homepage - Qualification Column',
                'content' => $this->getStaticBlockContent('TargetTraining_Homepage::fixtures/homepage_widget_8.html'),
            ],
            'homepage_learner_management' => [
                'title' => 'Homepage - Learner Management Column',
                'content' => $this->getStaticBlockContent('TargetTraining_Homepage::fixtures/homepage_widget_9.html'),
            ],
            'homepage_shop' => [
                'title' => 'Homepage - Shop Column',
                'content' => $this->getStaticBlockContent('TargetTraining_Homepage::fixtures/homepage_widget_10.html'),
            ],
            'homepage_meet_the_team' => [
                'title' => 'Homepage - Meet The Team Column',
                'content' => $this->getStaticBlockContent('TargetTraining_Homepage::fixtures/homepage_widget_11.html'),
            ]
        ];
    }

    /**
     * Create static block instance with data
     *
     * @param string $identifier
     * @param array  $data
     *
     * @return Block
     * @throws \Exception
     */
    private function createStaticBlock($identifier, array $data)
    {
        $block = $this->getStaticBlockInstance();
        $block->setIdentifier($identifier);
        $block->setStores([0]);

        return $block->addData($data);
    }

    /**
     * Get static block instance
     *
     * @return Block
     */
    private function getStaticBlockInstance()
    {
        return $this->blockInterfaceFactory->create();
    }
}
