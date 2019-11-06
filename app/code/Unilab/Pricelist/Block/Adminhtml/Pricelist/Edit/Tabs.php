<?php

namespace Unilab\Pricelist\Block\Adminhtml\Pricelist\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('pricelist_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Manage Price List'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'main_pricelist',
            [
                'label' => __('Price List Information'),
                'title' => __('Price List Information'),
                'content' => $this->getLayout()->createBlock(
                    'Unilab\Pricelist\Block\Adminhtml\Pricelist\Edit\Tab\Main'
                )->toHtml(),
                'active' => true
            ]
        );
        $this->addTab(
            'conditions_pricelist',
            [
                'label' => __('Conditions'),
                'title' => __('Conditions'),
                'content' => $this->getLayout()->createBlock(
                    'Unilab\Pricelist\Block\Adminhtml\Pricelist\Edit\Tab\Conditions'
                )->toHtml(),
                'active' => false
            ]
        );
        $this->addTab(
            'actions_pricelist',
            [
                'label' => __('Actions'),
                'title' => __('Actions'),
                'content' => $this->getLayout()->createBlock(
                    'Unilab\Pricelist\Block\Adminhtml\Pricelist\Edit\Tab\Actions'
                )->toHtml(),
                'active' => false
            ]
        );

        return parent::_beforeToHtml();
    }
}
