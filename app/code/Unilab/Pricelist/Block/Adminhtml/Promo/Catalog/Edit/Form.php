<?php

namespace Unilab\Pricelist\Block\Adminhtml\Promo\Catalog\Edit;

use Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic
{
   /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context,
     * @param \Magento\Framework\Registry $registry,
     * @param \Magento\Framework\Data\FormFactory $formFactory,
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
     * @param \Unilab\Shipping\Model\Status $options,
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Unilab\Movshipping\Model\Status $options,
        array $data = []
    ) {
        $this->_options = $options;
        // $this->_cityFactory = $cityFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        
        $form = $this->_formFactory->create(
            ['data' => [
                    'id' => 'edit_form',
                    'enctype' => 'multipart/form-data',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ]
            ]
        );
        $form->setHtmlIdPrefix('pricelist_');
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

