<?php

namespace TargetTraining\Homepage\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Cms\Block\Widget\Page\Link as PageLinkWidget;

class Column extends Template
{
    const TEMPLATE = 'TargetTraining_Homepage::widget/column.phtml';

    private $blockFactory;
    private $filterProvider;
    private $pageLinkWidget;
    private $cmsPage;

    public function __construct(
        Context $context,
        BlockFactory $blockFactory,
        FilterProvider $filterProvider,
        PageLinkWidget $pageLinkWidget,
        \Magento\Cms\Helper\Page $cmsPage,
        array $data = []
    ) {
        $this->blockFactory = $blockFactory;
        $this->filterProvider = $filterProvider;
        $this->pageLinkWidget = $pageLinkWidget;
        $this->cmsPage = $cmsPage;
        parent::__construct($context, $data);
    }

    public function getTemplate()
    {
        if (null === $this->_template) {
            $this->setTemplate(self::TEMPLATE);
        }

        return parent::getTemplate();
    }

    public function hasLink()
    {
        return $this->getLinkHref() !== null;
    }

    public function getLinkHref()
    {
        $url = '';
        $pageLink = $this->getPageLink();
        $customLink = $this->getCustomLink();

        if (filter_var($pageLink, FILTER_VALIDATE_URL)) {
            $url = $pageLink;
        }

        if (filter_var($customLink, FILTER_VALIDATE_URL)) {
            $url = $customLink;
        }

        return $url;
    }

    public function getColorClass()
    {
        return strtolower($this->getColor());
    }

    public function getStaticBlockHtml()
    {
        $block = $this->getBlock();

        return $this->filterBlockContent($block->getContent());
    }

    private function getBlock()
    {
        $blockId = $this->getBlockId();
        $storeId = $this->getCurrentStoreId();
        $blockInstance = $this->createBlockInstance();

        return $blockInstance->setStoreId($storeId)->load($blockId);
    }

    private function createBlockInstance()
    {
        return $this->blockFactory->create();
    }

    private function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    private function filterBlockContent($content)
    {
        return $this->filterProvider
            ->getBlockFilter()
            ->setStoreId($this->getCurrentStoreId())
            ->filter($content);
    }

    private function getPageLink()
    {
        if (!$this->hasPageId()) {
            return false;
        }

        return $this->cmsPage->getPageUrl($this->getPageId());
    }
}
