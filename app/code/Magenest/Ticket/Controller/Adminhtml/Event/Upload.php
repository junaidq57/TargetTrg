<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Ticket\Controller\Adminhtml\Event;

use Magento\Framework\Controller\ResultFactory;
use Magenest\Ticket\Controller\Adminhtml\Event as EventController;
use Magenest\Ticket\Model\EventFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\Model\View\Result\Page;
use Magenest\Ticket\Model\ResourceModel\Event\CollectionFactory as EventCollectionFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Upload
 * @package Magenest\Ticket\Controller\Adminhtml\Event
 */
class Upload extends EventController
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $coreFileStorageDatabase;

    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * Upload constructor.
     * @param Context $context
     * @param EventFactory $eventFactory
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param FileFactory $fileFactory
     * @param Filter $filter
     * @param \Psr\Log\LoggerInterface $loggerInterface
     * @param UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase
     * @param EventCollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        EventFactory $eventFactory,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        FileFactory $fileFactory,
        Filter $filter,
        \Psr\Log\LoggerInterface $loggerInterface,
        UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        EventCollectionFactory $collectionFactory
    ) {
    
        parent::__construct($context, $eventFactory, $coreRegistry, $resultPageFactory, $fileFactory, $filter, $collectionFactory);
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->logger = $loggerInterface;
        $this->filesystem = $filesystem;
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::categories');
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    
    public function execute()
    {
        $files = $this->getRequest()->getFiles();
        $background = $files['event']['pdftemplate'];
        try {
            $result = $this->saveBackground($background['pdf_background']);
            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

    /**
     * @param $imageName
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function moveFileFromTmp($imageName)
    {
        $baseTmpPath = 'ticket/template';
        $basePath = 'ticket/template';
        $baseImagePath = $this->getFilePath($basePath, $imageName);
        $baseTmpImagePath = $this->getFilePath($baseTmpPath, $imageName);

        try {
            $this->coreFileStorageDatabase->copyFile(
                $baseTmpImagePath,
                $baseImagePath
            );
            $this->mediaDirectory->renameFile(
                $baseTmpImagePath,
                $baseImagePath
            );
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong while saving the file(s).')
            );
        }

        return $imageName;
    }

    /**
     * @param $path
     * @param $imageName
     * @return string
     */
    public function getFilePath($path, $imageName)
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }

    /**
     * @param $files
     * @return array
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveBackground($files)
    {
        $path = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
        ->getAbsolutePath(
            'ticket/template'
        );
        $uploader = $this->uploaderFactory->create(['fileId' => $files]);
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
        $uploader->setAllowRenameFiles(true);
        $result = $uploader->save($path);
        if (!$result) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('File can not be saved to the destination folder.')
            );
        }

        /**
         * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
         */
        $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
        $result['path'] = str_replace('\\', '/', $path);

        $result['url'] = $this->storeManager
                ->getStore()
                ->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . $this->getFilePath('ticket/template', $result['file']);
        $result['name'] = $result['file'];

        if (isset($result['file'])) {
            try {
                $relativePath = rtrim('ticket/template', '/') . '/' . ltrim($result['file'], '/');
                $this->coreFileStorageDatabase->saveFile($relativePath);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while saving the file(s).')
                );
            }
        }
        return $result;
    }
}
