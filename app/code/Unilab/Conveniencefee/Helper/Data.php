<?php

namespace Unilab\Conveniencefee\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * Convenience fee config path
     */
    const CONFIG_CONVENIENCE_FEE = 'payment/dragonpay/convenience_fee';

    /**
     * Get Convenience fee Set Value in config
     *
     * @return mixed
     */
    public function getConveniencefeeValue()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $fee = $this->scopeConfig->getValue(self::CONFIG_CONVENIENCE_FEE, $storeScope);
        return $fee;
    }

    public function getConvenienceFeeLabel()
    {       
        return 'Convenience Fee';
    }

}
