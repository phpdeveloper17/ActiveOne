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

namespace Unilab\Bannerslider\Block\Adminhtml\Report\Helper\Renderer;

/**
 * Image renderder
 * @category Unilab
 * @package  Unilab_Bannerslider
 * @module   Bannerslider
 * @author   Unilab Developer
 */
class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * banner factory.
     *
     * @var \Unilab\Bannerslider\Model\BannerFactory
     */
    protected $_bannerFactory;

    /**
     * [__construct description].
     *
     * @param \Magento\Backend\Block\Context              $context       [description]
     * @param \Magento\Store\Model\StoreManagerInterface  $storeManager  [description]
     * @param \Unilab\Bannerslider\Model\BannerFactory $bannerFactory [description]
     * @param array                                       $data          [description]
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Unilab\Bannerslider\Model\BannerFactory $bannerFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_bannerFactory = $bannerFactory;
    }

    /**
     * Render action.
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $banner = $this->_bannerFactory->create()->setStoreViewId(null)->load($row->getBannerId());
        $srcImage = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $banner->getImage();

        return '<image width="150" height="50" src ="'.$srcImage.'" alt="'.$banner->getImage().'" >';
    }
}
