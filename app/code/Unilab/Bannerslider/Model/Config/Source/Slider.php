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
 * @copyright   Copyright (c) 2012-2016 Unilab (http://www.Unilab.com/)
 * @license     http://www.Unilab.com/license-agreement.html
 */

namespace Unilab\Bannerslider\Model\Config\Source;

class Slider implements \Magento\Framework\Option\ArrayInterface
{
    protected $sliderFactory;

    public function __construct(
        \Unilab\Bannerslider\Model\SliderFactory $sliderFactory
    ) {
        $this->sliderFactory = $sliderFactory;
    }

    public function getSliders()
    {
        $sliderModel = $this->sliderFactory->create();
        return $sliderModel->getCollection()->getData();
    }

    public function toOptionArray()
    {
        $sliders = [];
        foreach ($this->getSliders() as $slider) {
            array_push($sliders,[
                'value' => $slider['slider_id'],
                'label' => $slider['title']
            ]);
        }
        return $sliders;
    }
}