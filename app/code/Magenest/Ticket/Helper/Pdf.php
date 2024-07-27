<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 *
 * Magenest_Ticket extension
 * NOTICE OF LICENSE
 *
 * @category  Magenest
 * @package   Magenest_Ticket
 * @author ThaoPV <thaopw@gmail.com>
 */
namespace Magenest\Ticket\Helper;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;
use Magenest\Ticket\Model\EventFactory;
use Magenest\Ticket\Model\EventLocationFactory;
use Magenest\Ticket\Model\EventDateFactory;
use Magenest\Ticket\Model\EventSessionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class Pdf
 *
 * @package Magenest\Ticket\Helper
 */
class Pdf extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var EventFactory
     */
    protected $_eventFactory;

    /**
     * @var File
     */
    protected $fileFramework;

    /**
     * @var Template
     */
    protected $template;

    /**
     * @var EventSessionFactory
     */
    protected $session;

    /**
     * @var EventLocationFactory
     */
    protected $location;

    /**
     * @var EventDateFactory
     */
    protected $date;

    /**
     * @var Information
     */
    protected $information;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * Pdf constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param EventFactory $eventFactory
     * @param File $fileFramework
     * @param Template $template
     * @param EventSessionFactory $eventSessionFactory
     * @param EventLocationFactory $locationFactory
     * @param EventDateFactory $dateFactory
     * @param Information $information
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        EventFactory $eventFactory,
        File $fileFramework,
        Template $template,
        EventSessionFactory $eventSessionFactory,
        EventLocationFactory $locationFactory,
        EventDateFactory $dateFactory,
        Information $information,
        Json $serializer = null
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->_eventFactory = $eventFactory;
        $this->fileFramework = $fileFramework;
        $this->template = $template;
        $this->session = $eventSessionFactory;
        $this->location = $locationFactory;
        $this->date = $dateFactory;
        $this->information = $information;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
    }

    /**
     * @param \Magenest\Ticket\Model\Ticket $ticket
     * @return \Zend_Pdf
     * @throws \Zend_Pdf_Exception
     */
    public function getPdf($ticket)
    {
        $pdf = new \Zend_Pdf();
        $event = $ticket->getEvent();
        $font_regular = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA);
        $width = $event->getPdfPageWidth();
        $height = $event->getPdfPageHeight();

        $size = $width. ':'. $height;
        $page = $pdf->newPage($size);
        $backgroundLink = unserialize($event->getPdfBackground());
        if (isset($backgroundLink) && !empty($backgroundLink)) {
            $background = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath('ticket/template/'.$backgroundLink['0']['file']);
            if (is_file($background)) {
                $image = \Zend_Pdf_Image::imageWithPath($background);
                $page->drawImage($image, 0, 0, $width, $height);
            }
        }

        $code = $ticket->getCode();
        $page->setFont($font_regular, 15);
        $coordinates = $event->getPdfCoordinates();
        $tableRowsArr = [];
        if (@unserialize($coordinates)) {
            $tableRowsArr = unserialize($coordinates);
        }

        foreach ($tableRowsArr as $param) {
        /**
             * Insert QR Code to PDF File
             */
            if (!empty($param['info']) && $param['info'] == 'qr_code' && !empty($param['x'])  && !empty($param['y'])  && !empty($param['size'])) {
                $fileName = $this->getQrCode($code);
                $pathQrcode = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath($fileName);
                $image = \Zend_Pdf_Image::imageWithPath($pathQrcode);
                $page->drawImage(
                    $image,
                    $param['x'],
                    $param['y'],
                    $param['x'] + $param['size'],
                    $param['y'] + $param['size']
                );

                unlink($pathQrcode);
                continue;
            }

            /**
             * Insert Barcode to PDF File
             */
            if (!empty($param['info']) && $param['info'] == 'bar_code') {
                $barcodeOptions = ['text' => $code, 'drawText' => false];
                $rendererOptions = [];
                $imageResource = \Zend_Barcode::draw('code128', 'image', $barcodeOptions, $rendererOptions);
                $barcode = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath('barcode.jpg');
                imagejpeg($imageResource, $barcode, 100);
                imagedestroy($imageResource);
                $image = \Zend_Pdf_Image::imageWithPath($barcode);
                $page->drawImage(
                    $image,
                    $param['x'],
                    $param['y'],
                    $param['x'] + $param['size']*2,
                    $param['y'] + $param['size']
                );
                unlink($barcode);
                continue;
            }

            /**
             * Insert diffenceinformation
             */
            if (!empty($param['info']) && !empty($param['x'])  && !empty($param['y'])  && !empty($param['size']) && !empty($param['color'])) {
                $page->setFont($font_regular, $param['size']);
                $color = new \Zend_Pdf_Color_Html($param['color']);
                $page->setFillColor($color);
                $text = $this->replaceByTicket($ticket, $param['info']);
                if (!empty($param['title'])) {
                    $resultText = $param['title'].': '.$text;
                } else {
                    $resultText = $text;
                }
                $page->drawText(
                    $resultText,
                    $param['x'],
                    $param['y'],
                    'UTF-8'
                );
            }
        }
        $pdf->pages[] = $page;

        return $pdf;
    }

    /**
     * @param $data
     * @return string
     * @throws \Zend_Pdf_Exception
     */
    public function getPreviewPdf($data)
    {
        $path = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)
            ->getAbsolutePath("template.pdf");

        $pdf = $this->getPrintPdfPreview($data);
        $pdf->render();
        $pdf->save($path);
        $file = $this->template->getBaseUrl()."pub/media/template.pdf";
        return $file;
    }

    public function getQrCode($code)
    {
        $url = "http://api.qrserver.com/v1/create-qr-code/?&size=120x120&data=" . $code;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $raw = curl_exec($ch);
        curl_close($ch);
        $path = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)
            ->getAbsolutePath("qr_".$code.".png");
        if (file_exists($path)) {
            unlink($path);
        }
        $fp = $this->fileFramework->fileOpen($path, 'x');
        $this->fileFramework->fileWrite($fp, $raw);
        $this->fileFramework->fileClose($fp);
        $file = 'qr_'.$code.".png";

        return  $file;
    }

    /**
     * @param \Magenest\Ticket\Model\Ticket $ticket
     * @param $info
     * @return string
     */
    public function replaceByTicket($ticket, $info)
    {
        $event = $this->_eventFactory->create()->load($ticket->getEventId());

        $array = $this->serializer->unserialize($ticket->getInformation());
        $arrayInfo = $this->information->getDataTicket($array);
        $text = '';

        switch ($info) {
            case 'event_name':
                $text = $event->getEventName();
                break;
            case 'location_title':
                $text = $arrayInfo['location_title'];
                break;
            case 'location_detail':
                $text = $arrayInfo['location_detail'];
                break;
            case 'date':
                $text = $arrayInfo['date'];
                break;
            case 'qty':
                $text = $ticket->getQty();
                break;
            case 'start_time':
                $text = $arrayInfo['start_time'];
                break;
            case 'end_time':
                $text = $arrayInfo['end_time'];
                break;
            case 'type':
                $text = $ticket->getNote();
                break;
            case 'code':
                $text = $ticket->getCode();
                break;
            case 'customer_name':
                $text = $ticket->getCustomerName();
                break;
            case 'customer_email':
                $text = $ticket->getCustomerEmail();
                break;
            case 'order_increment_id':
                $text = $ticket->getOrderIncrementId();
                break;
            default:
                break;
        }

        return $text;
    }

    /**
     * Print PDF Template Preview
     *
     * @param array $data
     * @return \Zend_Pdf
     * @throws \Zend_Pdf_Exception
     */
    public function getPrintPdfPreview($data)
    {
        $coordinates = [];
        if ($data['pdf_coordinates']) {
            $coordinates = unserialize($data['pdf_coordinates']);
        }
        $backgroundLink = unserialize($data['pdf_background']);

        $pdf = new \Zend_Pdf();
        $fontRegular = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA);

        $width = $data['pdf_page_width'];
        $height = $data['pdf_page_height'];

        $size = $width. ':'. $height;
        $page = $pdf->newPage($size);
        if (isset($backgroundLink) && !empty($backgroundLink)) {
            $background = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath('ticket/template/'.$backgroundLink['0']['file']);
            if (is_file($background)) {
                $image = \Zend_Pdf_Image::imageWithPath($background);
                $page->drawImage($image, 0, 0, $width, $height);
            }
        }

        $code = 'MagenestA4vM';
        $page->setFont($fontRegular, 15);
        $tableRowsArr = $coordinates;

        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        );

        foreach ($tableRowsArr as $param) {
            /**
             * Insert QR Code to PDF File
             */
            if (!empty($param['info']) && $param['info'] == 'qr_code' && !empty($param['x'])  && !empty($param['y'])  && !empty($param['size'])) {
                $fileName = $this->getQrCode($code);
                $pathQrcode = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath($fileName);
                $image = \Zend_Pdf_Image::imageWithPath($pathQrcode);
                $page->drawImage(
                    $image,
                    $param['x'],
                    $param['y'],
                    $param['x'] + $param['size'],
                    $param['y'] + $param['size']
                );

                if ($path->isFile($fileName)) {
                    $this->filesystem->getDirectoryWrite(
                        DirectoryList::MEDIA
                    )->delete($fileName);
                }

                continue;
            }
            /**
             * Insert Barcode to PDF File
             */
            if (!empty($param['info']) && $param['info'] == 'bar_code') {
                $barcodeOptions = ['text' => $code, 'drawText' => false];
                $rendererOptions = [];
                $imageResource = \Zend_Barcode::draw('code128', 'image', $barcodeOptions, $rendererOptions);
                $barcode = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath('barcode.jpg');
                imagejpeg($imageResource, $barcode, 100);
                imagedestroy($imageResource);
                $image = \Zend_Pdf_Image::imageWithPath($barcode);
                $page->drawImage(
                    $image,
                    $param['x'],
                    $param['y'],
                    $param['x'] + $param['size']*2,
                    $param['y'] + $param['size']
                );
                if ($path->isFile('barcode.jpg')) {
                    $this->filesystem->getDirectoryWrite(
                        DirectoryList::MEDIA
                    )->delete('barcode.jpg');
                }

                continue;
            }

            /**
             * Insert diffenceinformation
             */
            if (!empty($param['info']) && !empty($param['x'])  && !empty($param['y'])  && !empty($param['size']) && !empty($param['color'])) {
                $page->setFont($fontRegular, $param['size']);
                $color = new \Zend_Pdf_Color_Html($param['color']);
                $page->setFillColor($color);
                $text = $this->replaceByText($param['info'], $data['product_id']);
                if (isset($param['title']) && !empty($param['title'])) {
                    $textEnd = $param['title'].': '.$text;
                } else {
                    $textEnd = $text;
                }
                $page->drawText(
                    $textEnd,
                    $param['x'],
                    $param['y'],
                    'UTF-8'
                );
            }
        }
        $pdf->pages[] = $page;
        return $pdf;
    }

    /**
     * @param $info
     * @param $id
     * @return string
     */
    public function replaceByText($info)
    {
        $text = '';
        switch ($info) {
            case 'event_name':
                $text = 'Event Ticket';
                break;
            case 'location_title':
                $text = 'Thearter';
                break;
            case 'location_detail':
                $text = 'California, USA';
                break;
            case 'date':
                $text = '16/11/2016';
                break;
            case 'start_time':
                $text = '8:00';
                break;
            case 'end_time':
                $text = '11:00';
                break;
            case 'type':
                $text = 'Adult';
                break;
            case 'code':
                $text = 'MagenestA4vM';
                break;
            case 'customer_name':
                $text = 'Magenest JSC';
                break;
            case 'customer_email':
                $text = 'example@gmail.com';
                break;
            case 'order_increment_id':
                $text = '00000026';
                break;
            case 'qty':
                $text = '6';
                break;
            default:
                break;
        }

        return $text;
    }
}
