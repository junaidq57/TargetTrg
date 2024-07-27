<?php

namespace TargetTraining\ProductAttribute\Plugin;

class CheckoutCartIndex
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * CheckoutCartIndex constructor.
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
    }

    public function afterExecute(
        \Magento\Checkout\Controller\Cart\Index $cartIndex,
        $resultPage
    )
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Basket'));

        $titleBlock = $resultPage->getLayout()->getBlock('page.main.title');
        if ($titleBlock) {
            $titleBlock->setPageTitle(
                __('Basket')
            );
        }
        return $resultPage;
    }

}
