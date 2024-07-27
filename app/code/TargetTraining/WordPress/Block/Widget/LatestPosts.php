<?php

namespace TargetTraining\WordPress\Block\Widget;

use FishPig\WordPress\Block\Post\ListPost;
use FishPig\WordPress\Model\Post;

/**
 * Class LatestPosts
 *
 * @method int getPostLimit
 */
class LatestPosts extends ListPost
{
    /**
     * @var string Template path
     */
    const TEMPLATE = 'TargetTraining_WordPress::widget/post/list.phtml';

    protected $postCollection;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->setTemplate(self::TEMPLATE);
        parent::_construct();
    }

    /**
     * @return \FishPig\WordPress\Block\Post\ListPost
     */
    protected function _beforeToHtml()
    {
        if (null === $this->postCollection) {
            $this->postCollection = $this->getPosts();
        }

        if(isset($this->postCollection))
        {
            $this->postCollection
            ->setOrderByPostDate()
            ->addIsViewableFilter()
            ->setPageSize($this->getPostLimit());
        }

        

        return parent::_beforeToHtml();
    }

    /**
     * @param \FishPig\WordPress\Model\Post $post
     *
     * @return bool
     */
    public function hasFeaturedImage(Post $post)
    {
        return false !== $post->getFeaturedImage();
    }

    /**
     * @return bool
     */
    public function canShowExcerpt()
    {
        return $this->hasData('show_excerpt') && $this->getData('show_excerpt');
    }

    /**
     * @param \FishPig\WordPress\Model\Post $post
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAvailableImage(Post $post)
    {
        return $post->getAvailableImage();
    }

    /**
     * @param \FishPig\WordPress\Model\Post $post
     *
     * @return string
     */
    public function getPostMonth(Post $post)
    {
        return $post->getPostDate('M');
    }

    /**
     * @param \FishPig\WordPress\Model\Post $post
     *
     * @return string
     */
    public function getPostDay(Post $post)
    {
        return $post->getPostDate('d');
    }
}
