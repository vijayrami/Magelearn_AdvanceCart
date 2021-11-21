<?php

namespace Magelearn\AdvanceCart\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_ADVANCECART = 'advancecart_setting/';

    /**
     * Get ConfigValue.
     *
     * @return {Boolean}
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get GeneralConfig.
     *
     * @return {String}
     */
    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ADVANCECART.'minicart_settings/'.$code, $storeId);
    }
}