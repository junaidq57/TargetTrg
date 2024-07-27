<?php
namespace TargetTraining\CustomizedReport\Block\Adminhtml\Sales;
class Pagination extends \Magento\Framework\View\Element\Template
{    
    const FLAG_PAGE = 7;
    protected $_report; 
    protected $_helper;

    public function __construct(
        \TargetTraining\CustomizedReport\Block\Adminhtml\Sales\Report $report,
        \Magento\Backend\Block\Template\Context $context,
        \TargetTraining\CustomizedReport\Helper\Data $helper,  
        array $data = []
    )
    {
        $this->_report = $report;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getTotalPages(){
        $size = $this->_report->getSizeAllOfSalesOrders();
        $totalPages = ceil($size/$this->_helper->getLimitRecords());
        return $totalPages;
    }

    public function getCurrentPage(){
        $currentPage = $this->getRequest()->getParam('p');
        if(!$currentPage){
            $currentPage = 1;
        }
        return $currentPage;
    }

    // Make The bottom pagination same google pagination.
    public function paginationFormat()
    {   
        $currentPage = $this->getRequest()->getParam('p');
        $totalPages = $this->getTotalPages();

        $flatPage = self::FLAG_PAGE;
        if($totalPages < 10)
        {
            $range =  [1, $totalPages];
            $fullRange = $this->getFullRange($range);
        }
        else
        {
            if($currentPage >= $flatPage)
            {
                $fistPage = $currentPage - 5;
                $lastPageTemporary = $currentPage + 4;
                if($lastPageTemporary > $totalPages)
                {
                    $lastPage = $totalPages;
                }
                else
                {
                    $lastPage = $lastPageTemporary;
                }
                $fullRange = $this->getFullRange([$fistPage, $lastPage]);
            }
            else
            {
                $fullRange = $this->getFullRange([1, 10]);
            }
        }
        return $fullRange;
    }

    // get full paginatin range, $range is an array which contains 2 elements.
    public function getFullRange($range)
    {
        $fullRange = [];
        for($i = $range[0]; $i <= $range[1]; $i++)
        {
            $fullRange[] = $i;
        }
        return $fullRange;
    }

    public function getLimit(){
        return $this->_helper->getLimitRecords();
    }

    public function getCurrentUrl()
    {
        $currentUrl = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => false]);
        $currentUrl = parse_url($currentUrl);
        unset($currentUrl['query']);
        return $currentUrl['scheme'].'://'.$currentUrl['host'].$currentUrl['path'];
    }

    public function getPreviewPage(){
        $currentPage = $this->getRequest()->getParam('p');
        if((int)$currentPage != 1){
            return (int)$currentPage - 1;
        }else{
            return 1;
        }
    }

    public function getNextPage(){
        $currentPage = $this->getRequest()->getParam('p');
        $totalPages = $this->getTotalPages();
        if((int)$currentPage != $totalPages){
            return (int)$currentPage + 1;
        }else{
            return $totalPages;
        }
    }
}
?>