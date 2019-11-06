<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Reports\Controller\Adminhtml\Report\Index;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportSalesExcel extends \Unilab\Reports\Controller\Adminhtml\Report\Sales
{
    /**
     * Export sales report grid to Excel XML format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $report_id = $this->getRequest()->getParam('report_id');
        $reportDtls = $this->_objectManager->create("Unilab\Reports\Helper\Data");
        $fileName = $reportDtls->filenameExport($report_id).'.xml';
        $grid = $this->_view->getLayout()->createBlock(\Unilab\Reports\Block\Adminhtml\Sales\Sales\Grid::class);
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName), DirectoryList::VAR_DIR);
    }
	protected function _isAllowed()
    {
        return true;
    }
}
