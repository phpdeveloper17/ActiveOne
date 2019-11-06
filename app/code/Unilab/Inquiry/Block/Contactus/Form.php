<?php

namespace Unilab\Inquiry\Block\Contactus;

class Form extends \Magento\Framework\View\Element\Template
{
    protected $_customerSession;

    protected $_storeManager;

    protected $_scopeConfig;

    protected $_storeInfo;

    protected $_store;

    protected $_helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\Information $storeInfo,
        \Magento\Store\Model\Store $store,
        \Unilab\Inquiry\Helper\Data $helper
    ){
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeInfo = $storeInfo;
        $this->_store = $store;
        $this->_helper = $helper;
        parent::__construct($context);
    }

    public function customerIsLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }

    public function getUserName()
    {
        if(!$this->_customerSession->isLoggedIn()) {
            return "";
        }

        return $this->_customerSession->getCustomer()->getName();
    }

    public function getUserEmail()
    {
        if(!$this->_customerSession->isLoggedIn()) {
            return "";
        }

        return $this->_customerSession->getCustomer()->getEmail();
    }

    public function getStorePhone()
    {
        return $this->_scopeConfig->getValue(
        'general/store_information/phone',
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getFormAction()
    {
        return $this->getUrl('contact-us/inquiry/post', ['_secure' => false]);
    }

    public function getDepartmentHtmlSelect($value = '', $class = '')
    {

        if ($options = $this->_helper->getDepartmentOptions()){
            try{
                //$layout = $this->getLayout();
                //print_r($layout);exit;
                $select = $this->getLayout()->createBlock('\Magento\Framework\View\Element\Html\Select')
                    ->setName('department')
                    ->setId('department')
                    ->setTitle(__('Department'))
                    ->setClass($class)
                    ->setValue($value)
                    ->setOptions($options);
            } catch (Exception $e){ print_r($e); exit;}
            return $select->toHtml();
        }
        return null;
    }
}
