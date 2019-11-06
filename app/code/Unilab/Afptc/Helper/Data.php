<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Reports data helper
 */
namespace Unilab\Afptc\Helper;

use Magento\Framework\Data\Collection;
use Magento\Framework\Stdlib\DateTime;

/**
 * @api
 * @since 100.0.2
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const EE_PLATFORM = 100;
    const PE_PLATFORM = 10;
    const CE_PLATFORM = 0;

    const ENTERPRISE_DETECT_COMPANY = 'Enterprise';
    const ENTERPRISE_DETECT_EXTENSION = 'Enterprise';
    const ENTERPRISE_DESIGN_NAME = "enterprise";
    const PROFESSIONAL_DESIGN_NAME = "pro";
    
    protected static $_platform = -1;

    // afptc helper
    const AW_AFPTC_RULE_DECLINE       = 'aw-afptc-rule-decline';
    const AW_AFPTC_POPUP_DECLINE      = 'aw-afptc-popup-decline';

    const GENERAL_ENABLED             = 'awafptc/general/enable';
    const GENERAL_ALLOW_READD_TO_CART = 'awafptc/general/allow_readd_to_cart';
    const POPUP_DO_NOT_SHOW_ALLOWED   = 'awafptc/popup/do_not_show_allowed';
    const POPUP_COOKIE_LIFETIME       = 'awafptc/popup/cookie_lifetime';
   
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }
    public static function getPlatform()
    {
        if(!defined('DS')){
            define('DS',DIRECTORY_SEPARATOR);
        }
        if (self::$_platform == -1) {
            $pathToClaim = BP . DS . "app" . DS . "etc" . DS . "modules" . DS . self::ENTERPRISE_DETECT_COMPANY . "_" . self::ENTERPRISE_DETECT_EXTENSION .  ".xml";
            $pathToEEConfig = BP . DS . "app" . DS . "code" . DS . "core" . DS . self::ENTERPRISE_DETECT_COMPANY . DS . self::ENTERPRISE_DETECT_EXTENSION . DS . "etc" . DS . "config.xml";
            $isCommunity = !file_exists($pathToClaim) || !file_exists($pathToEEConfig);
            if ($isCommunity) {
                 self::$_platform = self::CE_PLATFORM;
            } else {
                $_xml = @simplexml_load_file($pathToEEConfig,'SimpleXMLElement', LIBXML_NOCDATA);
                if(!$_xml===FALSE) {
                    $package = (string)$_xml->default->design->package->name;
                    $theme = (string)$_xml->install->design->theme->default;
                    $skin = (string)$_xml->stores->admin->design->theme->skin;
                    $isProffessional = ($package == self::PROFESSIONAL_DESIGN_NAME) && ($theme == self::PROFESSIONAL_DESIGN_NAME) && ($skin == self::PROFESSIONAL_DESIGN_NAME);
                    if ($isProffessional) {
                        self::$_platform = self::PE_PLATFORM;
                        return self::$_platform;
                    }
                }
                self::$_platform = self::EE_PLATFORM;
            }
        }
        return self::$_platform;
    }

    /**
     * Convert platform from string to int and backwards
     * @static
     * @param $platformCode
     * @return int|string
     */
    public static function convertPlatform($platformCode)
    {
        if (is_numeric($platformCode)) {
            // Convert predefined to letters code
            $platform = ($platformCode == self::EE_PLATFORM ? 'ee' : ($platformCode == self::PE_PLATFORM ? 'pe'
                    : 'ce'));
        } elseif (is_string($platformCode)) {
            $platformCode = strtolower($platformCode);
            $platform = ($platformCode == 'ee' ? self::EE_PLATFORM : ($platformCode == 'pe' ? self::PE_PLATFORM
                    : self::CE_PLATFORM));
        }else{$platform = self::CE_PLATFORM;}
        return $platform;
    }

    public static function convertVersion($v)
    {
        $digits = @explode(".", $v);
        $version = 0;
        if (is_array($digits)) {
            foreach ($digits as $k => $v) {
                $version += ($v * pow(10, max(0, (3 - $k))));
            }

        }
        return $version;
    }

    public function isAllowReAddToCart($store = null)
    {
        $store = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::GENERAL_ALLOW_READD_TO_CART, $store);
    }

    public function getCustomerGroup()
    {        
        return $this->_session()->isLoggedIn() ? $this->_session()->getCustomer()->getGroupId() : 0;
    }
    
    public function getCustomerId()
    {
        return $this->_session()->getCustomer()->getId();
    }

    public function getDeclineRuleCookieName($ruleId)
    {
        return self::AW_AFPTC_RULE_DECLINE . '-' . $ruleId;
    }

    public function getDeclinePopupCookieName()
    {
        return self::AW_AFPTC_POPUP_DECLINE;
    }

    public function setDeclineRuleCookie($ruleId)
    {
        $this->cookieManager = $this->_objectManager->create('Magento\Framework\Stdlib\CookieManagerInterface');
        // $cookie->set($this->getDeclineRuleCookieName($ruleId), '1', time() + $this->getCookieLifetime(), '/');
        $this->cookieMetadataFactory = $this->_objectManager->create('\Magento\Framework\Stdlib\Cookie\CookieMetadataFactory');
        $this->sessionManager = $this->_objectManager->create("\Magento\Framework\Session\Config\ConfigInterface");
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration(time() + $this->getCookieLifetime())
            ->setPath($this->sessionManager->getCookiePath())
            ->setDomain($this->sessionManager->getCookieDomain());

        $this->cookieManager->setPublicCookie($this->getDeclineRuleCookieName($ruleId), 1);
        return $this;
        
    }

    public function getDeclineRuleCookie($ruleId)
    {
        $cookie = $this->_objectManager->create('\Magento\Framework\Stdlib\CookieManagerInterface');
        return $cookie->getCookie($this->getDeclineRuleCookieName($ruleId));
    }

    public function extensionDisabled($store = null)
    {
        $store = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;  
        return !$this->isModuleOutputEnabled()
            || !$this->scopeConfig->getValue(self::GENERAL_ENABLED, $store)
        ;
    }

    public function isDoNotShowOptionAllowed($store = null)
    {
        $store = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::POPUP_DO_NOT_SHOW_ALLOWED, $store);
    }

    public function getCookieLifetime($store = null)
    {
        $store = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return (int)$this->scopeConfig->getValue(self::POPUP_COOKIE_LIFETIME, $store);
    }

    protected function _session()
    {
        return $this->_objectManager->create('\Magento\Customer\Model\Session');
    }

    public function removeDeclineCookies()
    {
        $this->cookieManager = $this->_objectManager->create('Magento\Framework\Stdlib\CookieManagerInterface');
        $store = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $rulesCollection = $this->_objectManager->create("Unilab\Afptc\Model\Rule")->getActiveRulesCollection($store);
        if(!empty($rulesCollection)):
            foreach ($rulesCollection as $rule) {
                $this->cookieManager->setPublicCookie($this->getDeclineRuleCookieName($rule->getId()), 0);
            }
        endif;
        return $rulesCollection;
    }
    
}
