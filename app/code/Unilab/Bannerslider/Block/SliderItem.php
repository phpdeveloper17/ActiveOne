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

namespace Unilab\Bannerslider\Block;

use Unilab\Bannerslider\Model\Slider as SliderModel;
use Unilab\Bannerslider\Model\Status;

/**
 * Slider item.
 * @category Unilab
 * @package  Unilab_Bannerslider
 * @module   Bannerslider
 * @author   Unilab Developer
 */
class SliderItem extends \Magento\Framework\View\Element\Template
{
    /**
     * template for evolution slider.
     */
    const STYLESLIDE_EVOLUTION_TEMPLATE = 'Unilab_Bannerslider::slider/evolution.phtml';

    /**
     * template for popup.
     */
    const STYLESLIDE_POPUP_TEMPLATE = 'Unilab_Bannerslider::slider/popup.phtml';

    /**
     * template for note slider.
     */
    const STYLESLIDE_SPECIAL_NOTE_TEMPLATE = 'Unilab_Bannerslider::slider/special/note.phtml';

    /**
     * template for flex slider.
     */
    const STYLESLIDE_FLEXSLIDER_TEMPLATE = 'Unilab_Bannerslider::slider/flexslider.phtml';

    /**
     * template for custom slider.
     */
    const STYLESLIDE_CUSTOM_TEMPLATE = 'Unilab_Bannerslider::slider/custom.phtml';

    /**
     * Date conversion model.
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_stdlibDateTime;

    /**
     * slider factory.
     *
     * @var \Unilab\Bannerslider\Model\SliderFactory
     */
    protected $_sliderFactory;

    /**
     * slider model.
     *
     * @var \Unilab\Bannerslider\Model\Slider
     */
    protected $_slider;

    /**
     * slider id.
     *
     * @var int
     */
    protected $_sliderId;

    /**
     * banner slider helper.
     *
     * @var \Unilab\Bannerslider\Helper\Data
     */
    protected $_bannersliderHelper;

    /**
     * @var \Unilab\Bannerslider\Model\ResourceModel\Banner\CollectionFactory
     */
    protected $_bannerCollectionFactory;

    /**
     * scope config.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * stdlib timezone.
     *
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $_stdTimezone;

    /**
     * [__construct description].
     *
     * @param \Magento\Framework\View\Element\Template\Context                $context
     * @param \Unilab\Bannerslider\Model\ResourceModel\Banner\CollectionFactory $bannerCollectionFactory
     * @param \Unilab\Bannerslider\Model\SliderFactory                     $sliderFactory
     * @param SliderModel $slider
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                     $stdlibDateTime
     * @param \Unilab\Bannerslider\Helper\Data                             $bannersliderHelper
     * @param \Magento\Store\Model\StoreManagerInterface                      $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\Timezone                     $_stdTimezone
     * @param array                                                           $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Unilab\Bannerslider\Model\ResourceModel\Banner\Collection $bannerCollectionFactory,
        \Unilab\Bannerslider\Model\SliderFactory $sliderFactory,
        SliderModel $slider,
        \Magento\Framework\Stdlib\DateTime\DateTime $stdlibDateTime,
        \Unilab\Bannerslider\Helper\Data $bannersliderHelper,
        \Magento\Framework\Stdlib\DateTime\Timezone $_stdTimezone,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_sliderFactory = $sliderFactory;
        $this->_slider = $slider;
        $this->_stdlibDateTime = $stdlibDateTime;
        $this->_bannersliderHelper = $bannersliderHelper;
        $this->_bannerCollectionFactory = $bannerCollectionFactory;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_stdTimezone = $_stdTimezone;
    }

    /**
     * @return
     */
    protected function _toHtml()
    {
        $store = $this->_storeManager->getStore()->getId();

        $configEnable = $this->_scopeConfig->getValue(
            SliderModel::XML_CONFIG_BANNERSLIDER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        if (!$configEnable
            || $this->_slider->getStatus() === Status::STATUS_DISABLED
            || !$this->_slider->getId()
            || !$this->getBannerCollection()->getSize()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * set slider Id and set template.
     *
     * @param int $sliderId
     */
    public function setSliderId($sliderId)
    {
        $this->_sliderId = $sliderId;

        $slider = $this->_sliderFactory->create()->load($this->_sliderId);
        if ($slider->getId()) {
            $this->setSlider($slider);

            if ($slider->getStyleContent() == SliderModel::STYLE_CONTENT_NO) {
                $this->setTemplate(self::STYLESLIDE_CUSTOM_TEMPLATE);
            } else {
                $this->setStyleSlideTemplate($slider->getStyleSlide());
            }
        }

        return $this;
    }

    /**
     * set style slide template.
     *
     * @param int $styleSlideId
     *
     * @return string
     */
    public function setStyleSlideTemplate($styleSlideId)
    {
        switch ($styleSlideId) {
            //Evolution slide
            case SliderModel::STYLESLIDE_EVOLUTION_ONE:
            case SliderModel::STYLESLIDE_EVOLUTION_TWO:
            case SliderModel::STYLESLIDE_EVOLUTION_THREE:
            case SliderModel::STYLESLIDE_EVOLUTION_FOUR:
                $this->setTemplate(self::STYLESLIDE_EVOLUTION_TEMPLATE);
                break;

            case SliderModel::STYLESLIDE_POPUP:
                $this->setTemplate(self::STYLESLIDE_POPUP_TEMPLATE);
                break;
            //Note all page
            case SliderModel::STYLESLIDE_SPECIAL_NOTE:
                $this->setTemplate(self::STYLESLIDE_SPECIAL_NOTE_TEMPLATE);
                break;

            // Flex slide
            default:
                $this->setTemplate(self::STYLESLIDE_FLEXSLIDER_TEMPLATE);
                break;
        }
    }

    public function isShowTitle()
    {
        return $this->_slider->getShowTitle() == SliderModel::SHOW_TITLE_YES ? TRUE : FALSE;
    }

    /**
     * get banner collection of slider.
     *
     * @return \Unilab\Bannerslider\Model\ResourceModel\Banner\Collection
     */
    public function getBannerCollection()
    {
        $sliderId = $this->_slider->getId();
        return $this->_bannerCollectionFactory->getBannerCollection($sliderId);
    }

    /**
     * get first banner.
     *
     * @return \Unilab\Bannerslider\Model\Banner
     */
    public function getFirstBannerItem()
    {
        return $this->getBannerCollection()
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem();
    }

    /**
     * get position note.
     *
     * @return string
     */
    public function getPositionNote()
    {
        return $this->_slider->getPositionNoteCode();
    }

    /**
     * set slider model.
     *
     * @param \Unilab\Bannerslider\Model\Slider $slider [description]
     */
    public function setSlider(\Unilab\Bannerslider\Model\Slider $slider)
    {
        $this->_slider = $slider;

        return $this;
    }

    /**
     * @return \Unilab\Bannerslider\Model\Slider
     */
    public function getSlider()
    {
        return $this->_slider;
    }

    /**
     * get banner image url.
     *
     * @param \Unilab\Bannerslider\Model\Banner $banner
     *
     * @return string
     */
    public function getBannerImageUrl(\Unilab\Bannerslider\Model\Banner $banner)
    {
        return $this->_bannersliderHelper->getBaseUrlMedia($banner->getImage());
    }

    /**
     * get flexslider html id.
     *
     * @return string
     */
    public function getFlexsliderHtmlId()
    {
        return 'Unilab-bannerslider-flex-slider-'.$this->getSlider()->getId().$this->_stdlibDateTime->gmtTimestamp();
    }
}
