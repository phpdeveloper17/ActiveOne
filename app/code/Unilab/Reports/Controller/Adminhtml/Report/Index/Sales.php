<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Reports\Controller\Adminhtml\Report\Index;

class Sales extends \Unilab\Reports\Controller\Adminhtml\Report\Sales
{
    /**
     * Sales report action
     *
     * @return void
     */
    public function execute()
    {
        
        $this->_initAction()->_setActiveMenu(
            'Unilab_Reports::reports'
        )->_addBreadcrumb(
            __('Sales Report'),
            __('Sales Report')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__($this->_getReport()->getTitle()));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_sales_sales.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');
        
        $this->_initReportAction([$gridBlock, $filterFormBlock]);
        $this->_view->renderLayout();
    }
    protected function _getReport()
    {
        if (isset($this->_report)) {
            return $this->_report;
        }
        $report_id = $this->getRequest()->getParam('report_id');
        $report = $this->_objectManager->create("Unilab\Reports\Helper\Data")->_getReport($report_id);

        $this->_report = $report;
        return $this->_report;
    }
}
