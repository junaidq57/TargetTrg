<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Ticket\Model;

/**
 * Class Configuration
 * @package Magenest\Ticket\Model
 */
class Configuration
{

    const XML_PATH_TICKET_PATTERN_CODE = 'event_ticket/general_config/pattern_code';
    const XML_PATH_TICKET_API = 'event_ticket/general_config/google_api_key';
    const XML_PATH_TICKET_EMAIL = 'event_ticket/email_config/email';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Connector constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @return mixed
     */
    public function getPatternCode()
    {
        $pattern =  $this->_scopeConfig->getValue(self::XML_PATH_TICKET_PATTERN_CODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return $pattern;
    }

    /**
     * @return mixed
     */
    public function getGooleApi()
    {
        $api =  $this->_scopeConfig->getValue(self::XML_PATH_TICKET_API, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return $api;
    }

    /**
     * @return mixed
     */
    public function getConfigEmail()
    {
        $emailConfig =  $this->_scopeConfig->getValue(self::XML_PATH_TICKET_EMAIL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return $emailConfig;
    }
}
