<?php
/**
 * @category Mageants AllSlider
 * @package Mageants_AllSlider
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\AllSlider\Block\Product;

class ProductList extends \Magento\Catalog\Block\Product\AbstractProduct
{
    
    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;
    
    /**
     * @var \Mageants\AllSlider\Model\ResourceModel\AllSlider\CollectionFactory
     */
    protected $_allSliderCollection;
    
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productloader;
    
    /**
     * @var \Mageants\AllSlider\Helper\Data
     */
    protected $_allSliderHelper;

    /**
     * @param Context $context
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Mageants\AllSlider\Model\ResourceModel\AllSlider\CollectionFactory $allSliderCollection
     * @param \Magento\Catalog\Model\ProductFactory $productloader
     * @param \Mageants\AllSlider\Helper\Data $allSliderHelper
     * @param \Magento\Catalog\Helper\Output $output
     * @param \Magento\Wishlist\Helper\Data $wishlisthelper
     * @param \Magento\Catalog\Helper\Product\Compare $comparehelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Mageants\AllSlider\Model\ResourceModel\AllSlider\CollectionFactory $allSliderCollection,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Mageants\AllSlider\Helper\Data $allSliderHelper,
        \Magento\Catalog\Helper\Output $output,
        \Magento\Wishlist\Helper\Data $wishlisthelper,
        \Magento\Catalog\Helper\Product\Compare $comparehelper,
        array $data = []
    ) {
        $this->urlHelper = $urlHelper;
        $this->_allSliderCollection = $allSliderCollection;
        $this->_productloader = $productloader;
        $this->_allSliderHelper = $allSliderHelper;
        $this->output = $output;
        $this->wishlisthelper = $wishlisthelper;
        $this->comparehelper = $comparehelper;
        parent::__construct($context, $data);
    }
    
    /**
     * Retrieve All Slider collection
     *
     * @return SliderCollection
     */
    public function getAllSliders()
    {
        $collection =  $this->_allSliderCollection->create()
                        ->addFieldToFilter(
                            'store_id',
                            [
                                    ['finset'=> ['0']],
                                    ['finset'=> [$this->_storeManager->getStore()->getId()]],
                                ]
                        )
                        ->addFilter('slider_status', '1');
                        
        $collection->getSelect()->join(
            ['allslider_rel' =>
            'mageants_product_attachment_rel'],
            'main_table.allslider_id =
            allslider_rel.allslider_id'
        )->group('main_table.allslider_id')->columns(
            [
                'product_ids' => 'GROUP_CONCAT(allslider_rel.product_id)'
                ]
        );
        return $collection;
    }
    
    /**
     * Load Product collection
     *
     * @param mixed $id
     * @return Product array
     */
    public function getLoadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }
    
    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }
    
    /**
     * Retrieve Store Config
     *
     * @return string
     */
    public function getProductLimit()
    {
        return $this->_allSliderHelper->getAllSliderConfig('mageants_allslider/allslider_setting/limit');
    }
    /**
     * Get Items
     */
    public function getItems()
    {
        return $this->_allSliderHelper->getAllSliderConfig('mageants_allslider/allslider_setting/items');
    }
     /**
      * Get Slide Show Speed
      */
    public function getSlideShowSpeed()
    {
        return $this->_allSliderHelper->getAllSliderConfig('mageants_allslider/allslider_setting/slide_show_speed');
    }
    /**
     * Get Value Enabled Addtocart
     */
    public function getIsEnabledAddtocart()
    {
        return $this->_allSliderHelper->getAllSliderConfig('mageants_allslider/button_setting/add_to_cart');
    }
    /**
     * Get Value Enabled Wishlist
     */
    public function getIsEnabledWishlist()
    {
        return $this->_allSliderHelper->getAllSliderConfig('mageants_allslider/button_setting/wishlist');
    }
    /**
     * Get Value Enabled Compare
     */
    public function getIsEnabledCompare()
    {
        return $this->_allSliderHelper->getAllSliderConfig('mageants_allslider/button_setting/compare');
    }
    /**
     * Get Value Enabled Price
     */
    public function getIsEnabledPrice()
    {
        return $this->_allSliderHelper->getAllSliderConfig('mageants_allslider/button_setting/price');
    }
    /**
     * Get Value Enabled Review
     */
    public function getIsEnabledReview()
    {
        return $this->_allSliderHelper->getAllSliderConfig('mageants_allslider/button_setting/review');
    }
    /**
     * Get Value of output helper
     */
    public function output()
    {
        return $this->output;
    }
    /**
     * Get Value wishlistHelper
     */
    public function wishlistHelper()
    {
        return $this->wishlisthelper;
    }
    /**
     * Get Value compareHelper
     */
    public function compareHelper()
    {
        return $this->comparehelper;
    }
}
