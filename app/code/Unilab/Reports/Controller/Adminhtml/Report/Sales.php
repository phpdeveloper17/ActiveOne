<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Sales report admin controller
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Unilab\Reports\Controller\Adminhtml\Report;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @api
 * @since 100.0.2
 */
abstract class Sales extends AbstractReport
{
    /**
     * Add report/sales breadcrumbs
     *
     * @return $this
     */
    
    public function _initAction()
    {
		
        parent::_initAction();
        $this->_addBreadcrumb(__('Sales'), __('Sales'));
        return $this;
    }

    /**
     * Determine if action is allowed for reports module
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'sales':
                return $this->_authorization->isAllowed('Unilab_Reports::countryAndCity');
				return $this->_authorization->isAllowed('Unilab_Reports::customerName');
				return $this->_authorization->isAllowed('Unilab_Reports::gender');
				return $this->_authorization->isAllowed('Unilab_Reports::monthlySummary');
				return $this->_authorization->isAllowed('Unilab_Reports::orderDetails');
				return $this->_authorization->isAllowed('Unilab_Reports::orderSummary');
				return $this->_authorization->isAllowed('Unilab_Reports::products');
                break;
            case 'exportSalesCsv':
                return $this->_authorization->isAllowed('Unilab_Reports::exportSalesCsv');
                break;
            case 'exportSalesExcel':
                return $this->_authorization->isAllowed('Unilab_Reports::exportSalesExcel');
                break;
        }
    }
}
