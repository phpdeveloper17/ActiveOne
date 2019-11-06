<?php

namespace Unilab\Afptc\Block\Adminhtml\Afptc\Edit;

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
        $this->setId('afptc_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Manage Rule'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'main_afptc',
            [
                'label' => __('Rule Info'),
                'title' => __('Rule Info'),
                'content' => $this->getLayout()->createBlock(
                    'Unilab\Afptc\Block\Adminhtml\Afptc\Edit\Tab\Main'
                )->toHtml(),
                'active' => true
            ]
        );
        $this->addTab(
            'conditions_afptc',
            [
                'label' => __('Conditions'),
                'title' => __('Conditions'),
                'content' => $this->getLayout()->createBlock(
                    'Unilab\Afptc\Block\Adminhtml\Afptc\Edit\Tab\Conditions'
                )->toHtml(),
                'active' => false
            ]
        );
        $this->addTab(
            'actions_afptc',
            [
                'label' => __('Action'),
                'title' => __('Action'),
                'content' => $this->getLayout()->createBlock(
                    'Unilab\Afptc\Block\Adminhtml\Afptc\Edit\Tab\Actions'
                )->toHtml(),
                'active' => false
            ]
        );

        return parent::_beforeToHtml();
    }
}
