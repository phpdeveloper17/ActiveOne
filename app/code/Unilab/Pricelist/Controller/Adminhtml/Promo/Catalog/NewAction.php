<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Pricelist\Controller\Adminhtml\Promo\Catalog;

class NewAction extends \Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
	
	    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Unilab_Pricelist::add_pricelist');
    }
}
