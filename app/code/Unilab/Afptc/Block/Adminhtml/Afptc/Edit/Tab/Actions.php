<?php

namespace Unilab\Afptc\Block\Adminhtml\Afptc\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;

class Actions extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Tutorial\SimpleNews\Model\Config\Status
     */
    protected $_newsStatus;

   /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Status $newsStatus
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
       /** @var $model \Tutorial\SimpleNews\Model\News */
        $rule = $this->_coreRegistry->registry('awafptc_rule');

        
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('afptc_');
        // $form->setFieldNameSuffix('afptc');

        $fieldset = $form->addFieldset(
            'action_fieldset',
            ['legend' => __('Action')]
            // ['legend' => __('Update Prices Using the Following Information')]
        );
        if(!@$rule->getId()) {
            $rule->setDiscount(100);
			$rule->setdiscount_step(1);
			$rule->sety_qty(1);
        }
        $_ruleAction = $fieldset->addField(
            'simple_action',
            'select',
            [
                'name'      => 'simple_action',
                'label'     => __('Apply'),
                'id'        => 'simple_action',
                'title'     => __('Apply'),
                'style'     =>'width:70%',
                'values'    => 
                            [
                                \Unilab\Afptc\Model\Rule::BY_PERCENT_ACTION  => __('Percent of product price discount'),
                                \Unilab\Afptc\Model\Rule::BUY_X_GET_Y_ACTION => __('Buy X get Y free'),
                                // 1  => __('Percent of product price discount'),
                                // 2  => __('Buy X get Y free'),
                            ],
                'required'  => false,
            ]
        );
        $_discountAmount = $fieldset->addField(
            'discount',
            'text',
            [
                'name'      => 'discount',
                'label'     => __('Discount Amount Applied to Product, %'),
                'id'        => 'discount',
                'title'     => __('Discount Amount Applied to Product, %'),
                'style'     =>'width:70%',
                'class'     => 'required-entry validate-not-negative-number validate-percents',
                'required'  => true,
            ]
        );
        $_discountStep = $fieldset->addField(
            'discount_step',
            'text',
            [
                'name'      => 'discount_step',
                'label'     => __('Target Qty (Buy X)'),
                'id'        => 'discount_step',
                'title'     => __('Target Qty (Buy X)'),
                'style'     =>'width:70%',
                'class'     => 'required-entry validate-not-negative-number',
                'required'  => true,
            ]
        );
        $y_qty = $fieldset->addField(
            'y_qty',
            'text',
            [
                'name'      => 'y_qty',
                'label'     => __('Quantity of Free Product (Get Y)'),
                'id'        => 'y_qty',
                'title'     => __('Quantity of Free Product (Get Y)'),
                'style'     =>'width:70%',
                'class'     => 'required-entry validate-not-negative-number',
                'required'  => true,
            ]
        );
        $_auto_increment = $fieldset->addField(
            'auto_increment',
            'select',
            [
                'name'      => 'auto_increment',
                'label'     => __('Auto Increment Free Product'),
                'id'        => 'auto_increment',
                'title'     => __('Auto Increment Free Product'),
                'style'     =>'width:70%',
                'values'    => 
                            [
                                1  => __('Yes'),
                                0  => __('No'),
                            ],
                'required'  => true,
                'note'		=> 'Free product will automatically increment. (Get Y)'
            ]
        );
        $_two_promoitem = $fieldset->addField(
            'two_promoitem',
            'select',
            [
                'name'      => 'two_promoitem',
                'label'     => __('2 Promo Items'),
                'id'        => 'two_promoitem',
                'title'     => __('2 Promo Items'),
                'style'     =>'width:70%',
                'values'    => 
                            [
                                1  => __('Yes'),
                                0  => __('No'),
                            ],
                'required'  => true,
                'note'		=> '2 Promo Items (X and Y get Z) '
            ]
        );
        $fieldset->addField(
            'free_shipping',
            'select',
            [
                'name'      => 'free_shipping',
                'label'     => __('Free Shipping'),
                'id'        => 'free_shipping',
                'title'     => __('Free Shipping'),
                'style'     =>'width:70%',
                'values'    => 
                            [
                                1  => __('Yes'),
                                0  => __('No'),
                            ],
                'required'  => true,
                'note'		=> '2 Promo Items (X and Y get Z) '
            ]
        );
        $fieldset->addField(
            'stop_rules_processing',
            'select',
            [
                'name'      => 'stop_rules_processing',
                'label'     => __('Stop Further Rules Processing'),
                'id'        => 'stop_rules_processing',
                'title'     => __('Stop Further Rules Processing'),
                'style'     =>'width:70%',
                'values'    => [1 =>'Yes', 0=>'No'],
                'required'  => false,
            ]
        );
       
        
        $productsRenderBlock = $this->getLayout()->createBlock('Unilab\Afptc\Block\Adminhtml\Afptc\Edit\Renderer\Products');
        $form
            ->addFieldset('awafptc_grid_fieldset',
                array(
                   'fieldset_container_id' => 'aw-afptc-grid-products',
                   'class'                 => 'aw-afptc-grid-products',
                   'legend'                => __('Action Product')
                )
            )
            ->addField('awafptc_grid_product', 'select',
                array(
                    'name'     => 'awafptc_grid_product',
                    'formdata' => $rule,
                )
            )
            ->setRenderer($productsRenderBlock)
        ;
        $form->setValues($rule->getData());
        $this->setForm($form);
        
        
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )->addFieldMap(
                $_ruleAction->getHtmlId(), $_ruleAction->getName()
            )
            ->addFieldMap(
                $_discountAmount->getHtmlId(), $_discountAmount->getName()
            )
            ->addFieldDependence(
                $_discountAmount->getName(),
                $_ruleAction->getName(),
                \Unilab\Afptc\Model\Rule::BY_PERCENT_ACTION
            )
            // ->addFieldDependence(
            //     $_discountStep->getName(),
            //     $_ruleAction->getName(),
            //     \Unilab\Afptc\Model\Rule::BUY_X_GET_Y_ACTION
            // )
        );
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Actions');
    }
 
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Actions');
    }
 
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
