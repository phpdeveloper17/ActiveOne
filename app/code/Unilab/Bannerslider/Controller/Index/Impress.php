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

namespace Unilab\Bannerslider\Controller\Index;

/**
 * Impress action
 * @category Unilab
 * @package  Unilab_Bannerslider
 * @module   Bannerslider
 * @author   Unilab Developer
 */
class Impress extends \Unilab\Bannerslider\Controller\Index
{
    /**
     * Default customer account page.
     */
    public function execute()
    {
        $resultRaw = $this->_resultRawFactory->create();
        $userCode = $this->getUserCode(null);
        $date = $this->_stdTimezone->date()->format('Y-m-d');
        $sliderId = $this->getRequest()->getParam('slider_id');
        $slider = $this->_sliderFactory->create()->load($sliderId);
        if ($slider->getId()) {
            $sliderOwnBannerCollection = $slider->getOwnBanerCollection();
            if ($slider->getStyleSlide() == \Unilab\Bannerslider\Model\Slider::STYLESLIDE_POPUP) {
                $sliderOwnBannerCollection->setPageSize(1)->setCurPage(1);
            }
            $bannerIds = $sliderOwnBannerCollection->getColumnValues('banner_id');
            if ($this->getCookieManager()->getCookie('bannerslider_user_code_impress_slider'.$sliderId) === null) {
                $this->getCookieManager()->setPublicCookie('bannerslider_user_code_impress_slider'.$sliderId, $userCode);
                $reportCollection = $this->_reportCollectionFactory->create()
                    ->addFieldToFilter('date_click', $date)
                    ->addFieldToFilter('slider_id', $sliderId)
                    ->addFieldToFilter('banner_id', array('in' => $bannerIds));

                foreach ($reportCollection as $report) {
                    $report->setImpmode($report->getImpmode() + 1);
                    try {
                        $report->save();
                    } catch (\Exception $e) {
                        $this->_monolog->addError($e->getMessage());
                    }
                }

                //Banner Ids reported
                $bannerIdsReported = $reportCollection->getColumnValues('banner_id');

                //Banner Ids reported
                $bannerIdsNotReported = array_diff($bannerIds, $bannerIdsReported);
                foreach ($bannerIdsNotReported as $bannerId) {
                    $report = $this->_reportFactory->create()
                        ->setBannerId($bannerId)
                        ->setSliderId($slider->getId())
                        ->setImpmode(1)
                        ->setData('date_click', $date);
                    try {
                        $report->save();
                    } catch (\Exception $e) {
                        $this->_monolog->addError($e->getMessage());
                    }
                }
            }
        }

        return $resultRaw;
    }
}