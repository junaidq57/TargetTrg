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
namespace Magenest\Ticket\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class TicketActions extends Column
{
    /**
     * Url path  to edit
     *
     * @var string
     */
    const URL_PATH_EDIT = 'magenest_ticket/ticket/printTicket';

    const URL_PATH_MAIL = 'magenest_ticket/ticket/sendTicket';
    /**
     * URL builder
     *
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * constructor
     *
     * @param UrlInterface $urlBuilder
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['order_id'])) {
                    $viewUrlPath = $this->getData('config/viewUrlPath') ?: '#';
                    $urlEntityParamName = $this->getData('config/urlEntityParamName') ?: 'order_id';
                    $item[$this->getData('name')] = [
                        'view' => [
                            'href' => $this->_urlBuilder->getUrl(
                                $viewUrlPath,
                                [
                                    $urlEntityParamName => $item['order_id']
                                ]
                            ),
                            'label' => __('View')
                        ]
                    ];
                    $item[$this->getData('name')]['send_mail'] = [
                        'href' => $this->_urlBuilder->getUrl(
                            static::URL_PATH_MAIL,
                            [
                                'id' => $item['ticket_id'],
                            ]
                        ),
                        'label' => __('Resend Email'),
                        'hidden' => false,
                    ];
                    $item[$this->getData('name')]['print'] = [
                        'href' => $this->_urlBuilder->getUrl(
                            static::URL_PATH_EDIT,
                            [
                                'id' => $item['ticket_id'],
                            ]
                        ),
                        'label' => __('Print'),
                        'hidden' => false,
                    ];
                }
            }
        }

        return $dataSource;
    }
}
