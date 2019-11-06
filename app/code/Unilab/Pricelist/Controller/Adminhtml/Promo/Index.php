<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Pricelist\Controller\Adminhtml\Promo;

class Index extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Unilab_Pricelist::promo';

    /**
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Unilab_Pricelist::promo');
        $this->_addBreadcrumb(__('Promotions'), __('Promo'));
        $this->_view->renderLayout();
    }
}
