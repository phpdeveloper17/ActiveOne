<?php

/**
 * Unilab
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Unilab.com license that is
 * available through the world-wide-web at this URL:
 * http://www.Unilab.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Unilab
 * @package     Unilab_Bannerslider
 * @copyright   Copyright (c) 2012 Unilab (http://www.Unilab.com/)
 * @license     http://www.Unilab.com/license-agreement.html
 */

namespace Unilab\Bannerslider\Controller;

/**
 * Index action
 * @category Unilab
 * @package  Unilab_Bannerslider
 * @module   Bannerslider
 * @author   Unilab Developer
 */
abstract class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Slider factory.
     *
     * @var \Unilab\Bannerslider\Model\SliderFactory
     */
    protected $_sliderFactory;

    /**
     * banner factory.
     *
     * @var \Unilab\Bannerslider\Model\BannerFactory
     */
    protected $_bannerFactory;

    /**
     * Report factory.
     *
     * @var \Unilab\Bannerslider\Model\ReportFactory
     */
    protected $_reportFactory;

    /**
     * Report collection factory.
     *
     * @var \Unilab\Bannerslider\Model\ResourceModel\Report\CollectionFactory
     */
    protected $_reportCollectionFactory;

    /**
     * A result that contains raw response - may be good for passing through files
     * returning result of downloads or some other binary contents.
     *
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $_resultRawFactory;


    /**
     * logger.
     *
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $_monolog;

    /**
     * stdlib timezone.
     *
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $_stdTimezone;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context                                $context
     * @param \Unilab\Bannerslider\Model\SliderFactory                          $sliderFactory
     * @param \Unilab\Bannerslider\Model\BannerFactory                          $bannerFactory
     * @param \Unilab\Bannerslider\Model\ReportFactory                          $reportFactory
     * @param \Unilab\Bannerslider\Model\ResourceModel\Report\CollectionFactory $reportCollectionFactory
     * @param \Magento\Framework\Controller\Result\RawFactory                      $resultRawFactory
     * @param \Magento\Framework\Logger\Monolog                                    $monolog
     * @param \Magento\Framework\Stdlib\DateTime\Timezone                          $stdTimezone
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Unilab\Bannerslider\Model\SliderFactory $sliderFactory,
        \Unilab\Bannerslider\Model\BannerFactory $bannerFactory,
        \Unilab\Bannerslider\Model\ReportFactory $reportFactory,
        \Unilab\Bannerslider\Model\ResourceModel\Report\CollectionFactory $reportCollectionFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Logger\Monolog $monolog,
        \Magento\Framework\Stdlib\DateTime\Timezone $stdTimezone
    ) {
        parent::__construct($context);
        $this->_sliderFactory = $sliderFactory;
        $this->_bannerFactory = $bannerFactory;
        $this->_reportFactory = $reportFactory;
        $this->_reportCollectionFactory = $reportCollectionFactory;

        $this->_resultRawFactory = $resultRawFactory;
        $this->_monolog = $monolog;
        $this->_stdTimezone = $stdTimezone;
    }


    public function getCookieManager(){
        return $this->_objectManager->create('Magento\Framework\Stdlib\CookieManagerInterface');
    }
    /**
     * get user code.
     *
     * @param mixed $id
     *
     * @return string
     */
    protected function getUserCode($id)
    {
        $ipAddress = $this->_objectManager->create('Magento\Framework\HTTP\PhpEnvironment\Request')->getClientIp(true);
//        var_dump($ipAddress);die('ssssssss');
        $cookiefrontend = $this->getCookieManager()->getCookie('frontend');
        $usercode = $ipAddress.$cookiefrontend.$id;

        return md5($usercode);
    }
}
