<?php
namespace TargetTraining\EventFinder\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\Element;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Url\Helper\Data;

/**
 * Product list
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{


    /**
     * @param Context $context
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Data $urlHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    /**
     * Retrieve loaded product collection
     *
     * The goal of this method is to choose whether the existing collection should be returned
     * or a new one should be initialized.
     *
     * It is not just a caching logic, but also is a real logical check
     * because there are two ways how collection may be stored inside the block:
     *   - Product collection may be passed externally by 'setCollection' method
     *   - Product collection may be requested internally from the current Catalog Layer.
     *
     * And this method will return collection anyway,
     * even when it did not pass externally and therefore isn't cached yet
     *
     * @return AbstractCollection
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $this->_productCollection = $this->initializeProductCollection();
        }

        return $this->_productCollection;
    }

    /**
     * Get catalog layer model
     *
     * @return Layer
     */
    public function getLayer()
    {
        return $this->_catalogLayer;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        if ($this->getChildBlock('toolbar')) {
            return $this->getChildBlock('toolbar')->getCurrentMode();
        }

        return $this->getDefaultListingMode();
    }

    /**
     * Get listing mode for products if toolbar is removed from layout.
     * Use the general configuration for product list mode from config path catalog/frontend/list_mode as default value
     * or mode data from block declaration from layout.
     *
     * @return string
     */
    protected function getDefaultListingMode()
    {
        // default Toolbar when the toolbar layout is not used
        $defaultToolbar = $this->getToolbarBlock();
        $availableModes = $defaultToolbar->getModes();

        // layout config mode
        $mode = $this->getData('mode');

        if (!$mode || !isset($availableModes[$mode])) {
            // default config mode
            $mode = $defaultToolbar->getCurrentMode();
        }

        return $mode;
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $collection = $this->_getProductCollection();

        $this->addToolbarBlock($collection);

        $collection->load();

        return parent::_beforeToHtml();
    }

    /**
     * Add toolbar block from product listing layout
     *
     * @param Collection $collection
     */
    protected function addToolbarBlock(Collection $collection)
    {
        $toolbarLayout = $this->getToolbarFromLayout();

        if ($toolbarLayout) {
            $this->configureToolbar($toolbarLayout, $collection);
        }
    }

    /**
     * Retrieve Toolbar block from layout or a default Toolbar
     *
     * @return Toolbar
     */
    public function getToolbarBlock()
    {
        $block = $this->getToolbarFromLayout();

        if (!$block) {
            $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, uniqid(microtime()));
        }

        return $block;
    }

    /**
     * Get toolbar block from layout
     *
     * @return bool|Toolbar
     */
    protected function getToolbarFromLayout()
    {
        $blockName = $this->getToolbarBlockName();

        $toolbarLayout = false;

        if ($blockName) {
            $toolbarLayout = $this->getLayout()->getBlock($blockName);
        }

        return $toolbarLayout;
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * @param AbstractCollection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_productCollection = $collection;
        return $this;
    }

    /**
     * @param array|string|integer| Element $code
     * @return $this
     */
    public function addAttribute($code)
    {
        $this->_getProductCollection()->addAttributeToSelect($code);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriceBlockTemplate()
    {
        return $this->_getData('price_block_template');
    }

    /**
     * Retrieve Catalog Config object
     *
     * @return Config
     */
    protected function _getConfig()
    {
        return $this->_catalogConfig;
    }

    /**
     * Prepare Sort By fields from Category Data
     *
     * @param Category $category
     * @return $this
     */
    public function prepareSortableFieldsByCategory($category)
    {
        if (!$this->getAvailableOrders()) {
            $this->setAvailableOrders($category->getAvailableSortByOptions());
        }
        $availableOrders = $this->getAvailableOrders();
        if (!$this->getSortBy()) {
            $categorySortBy = $this->getDefaultSortBy() ?: $category->getDefaultSortBy();
            if ($categorySortBy) {
                if (!$availableOrders) {
                    $availableOrders = $this->_getConfig()->getAttributeUsedForSortByArray();
                }
                if (isset($availableOrders[$categorySortBy])) {
                    $this->setSortBy($categorySortBy);
                }
            }
        }

        return $this;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];

        $category = $this->getLayer()->getCurrentCategory();
        if ($category) {
            $identities[] = Product::CACHE_PRODUCT_CATEGORY_TAG . '_' . $category->getId();
        }

        //Check if category page shows only static block (No products)
        if ($category->getData('display_mode') == Category::DM_PAGE) {
            return $identities;
        }

        foreach ($this->_getProductCollection() as $item) {
            $identities = array_merge($identities, $item->getIdentities());
        }

        return $identities;
    }

    /**
     * Get post parameters
     *
     * @param Product $product
     * @return string
     */
    public function getAddToCartPostParams(Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

    /**
     * @param Product $product
     * @return string
     */
    public function getProductPrice(Product $product)
    {
        $priceRender = $this->getPriceRender();

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => Render::ZONE_ITEM_LIST,
                    'list_category_page' => true
                ]
            );
        }

        return $price;
    }

    /**
     * Specifies that price rendering should be done for the list of products
     * i.e. rendering happens in the scope of product list, but not single product
     *
     * @return Render
     */
    protected function getPriceRender()
    {
        return $this->getLayout()->getBlock('product.price.render.default')
            ->setData('is_product_list', true);
    }

    /**
     * Configures product collection from a layer and returns its instance.
     *
     * Also in the scope of a product collection configuration, this method initiates configuration of Toolbar.
     * The reason to do this is because we have a bunch of legacy code
     * where Toolbar configures several options of a collection and therefore this block depends on the Toolbar.
     *
     * This dependency leads to a situation where Toolbar sometimes called to configure a product collection,
     * and sometimes not.
     *
     * To unify this behavior and prevent potential bugs this dependency is explicitly called
     * when product collection initialized.
     *
     * @return Collection
     */
    protected function initializeProductCollection()
    {
        $layer = $this->getLayer();
        /* @var $layer Layer */
        if ($this->getShowRootCategory()) {
            $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
        }

        // if this is a product view page
        if ($this->_coreRegistry->registry('product')) {
            // get collection of categories this product is associated with
            $categories = $this->_coreRegistry->registry('product')
                ->getCategoryCollection()->setPage(1, 1)
                ->load();
            // if the product is associated with any category
            if ($categories->count()) {
                // show products from this category
                $this->setCategoryId(current($categories->getIterator())->getId());
            }
        }

        $origCategory = null;
        if ($this->getCategoryId()) {
            try {
                $category = $this->categoryRepository->get($this->getCategoryId());
            } catch (NoSuchEntityException $e) {
                $category = null;
            }

            if ($category) {
                $origCategory = $layer->getCurrentCategory();
                $layer->setCurrentCategory($category);
            }
        }
        $collection = $layer->getProductCollection();

        $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

        if ($origCategory) {
            $layer->setCurrentCategory($origCategory);
        }
        $params = $this->getRequest()->getParams();
        if($this->getRequest()->getRouteName() == 'course_finder'){
            $collection = $this->_productCollectionFactory->create();
            $collection->addAttributeToSelect('*');

            if(isset($params['location']) && $params['location'] > 0 && isset($params['type']) && $params['type'] > 0){
                $collection->addAttributeToFilter('course_location', $params['location']);
                $collection->addAttributeToFilter('course_type', $params['type']);
            }else if(isset($params['location']) && $params['location'] > 0){
                $collection->addAttributeToFilter('course_location', $params['location']);
            }else if(isset($params['type']) && $params['type'] > 0){
                $collection->addAttributeToFilter('course_type', (int)$params['type']);
            }
            $collection->addAttributeToFilter('option_available', 1);
        }
        $this->addToolbarBlock($collection);

        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $collection]
        );


        return $collection;
    }

    /**
     * Configures the Toolbar block with options from this block and configured product collection.
     *
     * The purpose of this method is the one-way sharing of different sorting related data
     * between this block, which is responsible for product list rendering,
     * and the Toolbar block, whose responsibility is a rendering of these options.
     *
     * @param ProductList\Toolbar $toolbar
     * @param Collection $collection
     * @return void
     */
    protected function configureToolbar(Toolbar $toolbar, Collection $collection)
    {
        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }
        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);
        $this->setChild('toolbar', $toolbar);
    }
}
