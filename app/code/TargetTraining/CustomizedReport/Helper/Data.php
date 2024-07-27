<?php

namespace TargetTraining\CustomizedReport\Helper;
use \Magento\Framework\App\Helper\AbstractHelper;
 
class Data extends AbstractHelper
{
	const LIMIT = 100;

    const XML_PATH_TIMEZONE = 'general/locale/timezone';
	
    public function getLimitRecords(){
    	return self::LIMIT;
    }

    public function getTimezoneConfig() {
        return $this->scopeConfig->getValue(self::XML_PATH_TIMEZONE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
