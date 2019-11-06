<?php

/**
 * SmtpMailer extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Auriga License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.aurigait.com/magento_extensions/license.txt
 *
 * @category      MageAurigaIT
 * @package        MageAurigaIT_SmtpMailer
 * @copyright      Copyright (c) 2017
 * @license        http://www.aurigait.com/magento_extensions/license.txt Auriga License
 */

namespace MageAurigaIT\SmtpMailer\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    const CONFIG_BASE_PATH = 'system/aitsmtpmailer/';
    private static $config_key_map = [];

    /**
     * Check if user has enabled SMTP functionality
     * @param  integer  $store_id
     * @return boolean
     */
    public function isSmtpEnabled($store_id = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_BASE_PATH.'active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }
    
    /**
     * Fetch value from given key
     * @param  string $key
     * @param  integer $store_id
     * @return mixed
     */
    public function getValue($key, $store_id = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_BASE_PATH.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }
    
    /**
     * Generate corresponding key name from getter
     * @param  string $name
     * @return string
     */
    private function getKey($name)
    {
        if (isset(self::$config_key_map[$name])) {
            return self::$config_key_map[$name];
        }
        $result = strtolower(trim(preg_replace('/([A-Z]|[0-9]+)/', "_$1", $name), '_'));
        self::$config_key_map[$name] = $result;
        return $result;
    }
    
    /**
     * Magic method for getter, returns value of a key.
     * @param  string $method
     * @param  array $args
     * @return mixed|null
     */
    public function __call($method, $args)
    {
        if (substr($method, 0, 3) === "get") {
            $key = $this->getKey(substr($method, 3));
            $store_id = isset($args[0]) ? $args[0] : null;
            return $this->getValue($key, $store_id);
        } else {
            return null;
        }
    }
}
