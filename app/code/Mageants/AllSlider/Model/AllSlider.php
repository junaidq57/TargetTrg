<?php
/**
 * @category Mageants AllSlider
 * @package Mageants_AllSlider
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\AllSlider\Model;

use Magento\Framework\DataObject\IdentityInterface;

class AllSlider extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{

    /**
     * CMS page cache tag
     */
    public const CACHE_TAG = 'allslider_products_grid';

    /**
     * @var string
     */
    protected $_cacheTag = 'allslider_products_grid';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'allslider_products_grid';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mageants\AllSlider\Model\ResourceModel\AllSlider::class);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
    /**
     * To Get Products
     *
     * @param \Mageants\AllSlider\Model\AllSlider $object
     */
    public function getProducts(\Mageants\AllSlider\Model\AllSlider $object)
    {
        $tbl = $this->getResource()->getTable(\Mageants\AllSlider\Model\ResourceModel\AllSlider::TBL_ATT_PRODUCT);
        $select = $this->getResource()->getConnection()->select()->from(
            $tbl,
            ['product_id']
        )
        ->where(
            'allslider_id = ?',
            (int)$object->getId()
        );
        return $this->getResource()->getConnection()->fetchCol($select);
    }
}
