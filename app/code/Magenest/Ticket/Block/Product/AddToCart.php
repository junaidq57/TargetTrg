<?php
/**
 * Created by PhpStorm.
 * User: gialam
 * Date: 17/03/2017
 * Time: 14:58
 */

namespace Magenest\Ticket\Block\Product;

/**
 * Product list
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AddToCart extends \Magento\Catalog\Block\Product\ListProduct
{
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        array $data = []
    ) {
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
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
        $type = $product->getTypeId();
        if ($type == 'ticket') {
            $url = $this->getUrlTicket($product);
        }

        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

    protected function getUrlTicket($product, $additional = [])
    {
        return $product->getUrlModel()->getUrl($product, $additional);
    }
}
