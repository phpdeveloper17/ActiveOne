<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Unilab\Reports\Block\Adminhtml\Sales\Sales;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended// extends \Unilab\Reports\Block\Adminhtml\Grid\AbstractGrid
{
    /**
     * GROUP BY criteria
     *
     * @var string
     */
    // protected $_columnGroupBy = 'period';

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setCountTotals(true);
        $this->setFilterVisibility(false);
        // $this->setPagerVisibility(false);
        $this->setUseAjax(false);
    }



    protected function _getReport()
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (isset($this->_report)) {
            return $this->_report;
        }

        $report_id = $this->getRequest()->getParam('report_id');
        $report = $this->_objectManager->create("Unilab\Reports\Helper\Data")->_getReport($report_id);

        $this->_report = $report;
        return $this->_report;
    }

    
    protected function _createCollection()
    {
        $report = $this->_getReport();
        
        $filterData = ($this->getFilterData()->getData());
       
        $connection = $this->_getConnection();
        $collection = $this->_objectManager->create("Unilab\Reports\Model\ResourceModel\Sales\Collection");
        
		$where_array = array();
        
		$sql = explode(" GROUP BY ",$report->getData('sql_query'));
		$where_date = (@$filterData['report_type'] == "created_at_order" ? "sales_order.created_at" : "updated_at");
		
        $new_from_date = @$filterData["from"];
		$new_to_date = @$filterData["to"];

		switch(@$filterData["period_type"]) {
		
			case "day":
				$from_date = $new_from_date;
				$to_date = $new_to_date;
				//$where_date = "DATE_FORMAT(".$where_date.",'%m/%d/%Y')";
				break;
            case "month":
                
				$from_date = substr(@$filterData["from"],0,-3); // 2018-11
				$to_date = substr(@$filterData["to"],0,-3); // 2018-11
				$where_date = "DATE_FORMAT(".$where_date.",'%Y-%m')";
				break;
			case "year":
				$from_date = substr(@$filterData["from"],0,-6);
				$to_date = substr(@$filterData["to"],0,-6);
				$where_date = "DATE_FORMAT(".$where_date.",'%Y')";
				break;
				
        }
        
		if(@$filterData["from"] != "" && @$filterData["to"] != "") {
			$where_date = " ".$where_date." BETWEEN '".$from_date."' AND '".$to_date."'";
			$where_array []= $where_date;
		
		}
		else 
			$where_date = "";
		if(@$filterData["show_order_statuses"] == "1") {
			$where_status = " status IN ('".implode("','",@$filterData["order_statuses"])."') ";
			$where_array []= $where_status;
			}
		else
			$where_status = "";
		if(@$filterData["store_ids"] != "") {
			$where_store_id = " sales_order.store_id IN (".@$filterData["store_ids"].")";
			$where_array []= $where_store_id;
			}
		else
			$where_store_id = "";
		
		
	
		$where = implode(" AND ",$where_array);
		if($where != "")
			$where = " WHERE ".$where;
        $sql = @$sql[0].$where." GROUP BY ".@$sql[1];
        
        $datacoll = array();
        $datacoll = $connection->fetchAll($sql);
       
        foreach ($datacoll as $data) {
            $collection->addItem(
                new \Magento\Framework\DataObject($data)
            );
        }
       
        return $collection;
    }
    
    protected function _prepareCollection()
    {
        if (isset($this->_collection)) {
            return $this->_collection;
        }

        $collection = $this->_createCollection();
        $this->setCountTotals(false);
        $this->setCountSubTotals(false);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $collection = $this->_createCollection();
       
        $collection->setPageSize(1);
        $collection->load();

        $items = $collection->getItems();
		if (count($items)) {
            $item = reset($items);
            $total_label = false;
            foreach ($item->getData() as $key => $val) {
                    $this->addColumn(
                        $key,
                        [
                            'header' => __($key),
                            'index' => $key,
                            'filter'    => false,
                            'sortable'  => false,
                            'totals_label' => 'Totals',
                            'html_decorators' => ['nobr'],
                            'header_css_class' => 'col-period',
                            'column_css_class' => (strpos($val,"₱ ") === false ? " " : "money")
                        ]
                    );
            }
        }
        
        $this->addExportType('*/*/exportSalesCsv', __('CSV'));
        $this->addExportType('*/*/exportSalesExcel', __('Excel XML'));
        return parent::_prepareColumns();
    }

    protected function _getConnection()
    {
        $this->_resource = $this->_objectManager->get("\Magento\Framework\App\ResourceConnection");
        $this->connection = $this->_resource->getConnection('core_write');
        return $this->connection;
    }
}
