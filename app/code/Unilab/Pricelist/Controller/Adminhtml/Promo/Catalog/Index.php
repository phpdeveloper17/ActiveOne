<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Pricelist\Controller\Adminhtml\Promo\Catalog;

class Index extends \Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog
{
    /**
     * @return void
     */
    public function execute()
    {
        $dirtyRules = $this->_objectManager->create(\Magento\CatalogRule\Model\Flag::class)->loadSelf();
        // $this->_eventManager->dispatch(
        //     'catalogrule_dirty_notice',
        //     ['dirty_rules' => $dirtyRules, 'message' => $this->getDirtyRulesNoticeMessage()]
        // );
        $this->_initAction()->_addBreadcrumb(__('Pricelist'), __('Pricelist'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Price List'));
        $this->_view->renderLayout();
    }
	
	    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Pricelist::pricelist');
    }
}
