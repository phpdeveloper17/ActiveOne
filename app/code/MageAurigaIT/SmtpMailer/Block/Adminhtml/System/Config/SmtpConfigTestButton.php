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
 * @package       MageAurigaIT_SmtpMailer
 * @copyright     Copyright (c) 2017
 * @license       http://www.aurigait.com/magento_extensions/license.txt Auriga License
 */

namespace MageAurigaIT\SmtpMailer\Block\Adminhtml\System\Config;

class SmtpConfigTestButton extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * Set template
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('MageAurigaIT_SmtpMailer::system/config/SmtpConfigTest.phtml');
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'MageAurigaIT_SmtpMailer_Test',
                'label' => __('Test')
            ]
        );

        return $button->toHtml();
    }

    /**
     * Render button
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }
    
    /**
     * Generate test action url
     * @return string
     */
    public function getSmtpTestUrl()
    {
        return $this->getUrl('ait_smtpmailer/smtpconfigtest');
    }
}
