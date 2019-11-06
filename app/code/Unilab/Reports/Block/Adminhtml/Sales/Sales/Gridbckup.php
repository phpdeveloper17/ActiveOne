<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Unilab\Reports\Block\Adminhtml\Sales\Sales;

/**
 * Adminhtml sales report grid block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
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

    /**
     * {@inheritdoc}
     */
    public function getResourceCollectionName()
    {
        return $this->getFilterData()->getData('report_type') == 'updated_at_order'
            ? \Magento\Sales\Model\ResourceModel\Report\Order\Updatedat\Collection::class
            : \Magento\Sales\Model\ResourceModel\Report\Order\Collection::class;
    }

    protected function _getReport()
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (isset($this->_report)) {
            return $this->_report;
        }

        $report = $this->_objectManager->create('Unilab\Reports\Model\Reports');
        if ($this->getRequest()->getParam('report_id')) {
            $report->load($this->getRequest()->getParam('report_id'));
        }

        $this->_report = $report;
        return $this->_report;
    }

    
    protected function _createCollection()
    {
        
        $report = $this->_getReport();

        // $collection = $this->_getConnection();
        $collection = $this->_objectManager->create("Unilab\Reports\Model\ResourceModel\Reports\Collection");
        // $collection = new \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

		$where_array = array();
		
		$sql = explode(" GROUP BY ",$report->getData('sql_query'));
		$where_date = (@$_GET["report_type"] == "created_at_order" ? "sales_order.created_at" : "updated_at");
		$from_date = explode("/",@$_GET["from"]);
		$to_date = explode("/",@$_GET["to"]);
		
		if(strlen(@$from_date[1]) == 1){
            $new_from_date = $from_date[0] .'/0'.$from_date[1].'/'.$from_date[2];
		}
		else{
		    $new_from_date = @$_GET["from"];
		}
		
		if(strlen(@$to_date[1]) == 1){
		    $new_to_date = $to_date[0] .'/0'.$to_date[1].'/'.$to_date[2];
		}
		else{
		    $new_to_date = @$_GET["to"];
		}

		switch(@$_GET["period_type"]) {
		
			case "day":
				$from_date = "STR_TO_DATE('$new_from_date','%m/%d/%Y')";
				$to_date = "STR_TO_DATE('$new_to_date','%m/%d/%Y')";
				//$where_date = "DATE_FORMAT(".$where_date.",'%m/%d/%Y')";
				break;
			case "month":
				$from_date = $from_date[0].'/'.$from_date[2];
				$to_date = $to_date[0].'/'.$to_date[2];
				$where_date = "DATE_FORMAT(".$where_date.",'%m/%Y')";
				break;
			case "year":
				$from_date = $from_date[2];
				$to_date = $to_date[2];
				$where_date = "DATE_FORMAT(".$where_date.",'%Y')";
				break;
				
		}
		if(@$_GET["from"] != "" && @$_GET["to"] != "") {
			$where_date = " ".$where_date." BETWEEN ".$from_date." AND ".$to_date."";
			$where_array []= $where_date;
		
		}
		else 
			$where_date = "";
		if(@$_GET["show_order_statuses"] == "1") {
			$where_status = " status IN ('".implode("','",@$_GET["order_statuses"])."') ";
			$where_array []= $where_status;
			}
		else
			$where_status = "";
		if(@$_GET["store_ids"] != "") {
			$where_store_id = " sales_order.store_id IN (".@$_GET["store_ids"].")";
			$where_array []= $where_store_id;
			}
		else
			$where_store_id = "";
		
		
	
		$where = implode(" AND ",$where_array);
		if($where != "")
			$where = " WHERE ".$where;
		$sql = $sql[0].$where." GROUP BY ".$sql[1];
		
        // $collection = $collection->fetchAll($sql);
        $collection->getSelect()->from(new \Zend_Db_Expr("(" .$sql . ")"));
      
        return $collection;
    }
   
    protected function _prepareCollection()
    {
        // if (isset($this->_collectionFactory)) {
        //     return $this->_collectionFactory;
        // }
        $collection = $this->_createCollection();
        
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
            foreach ($item->getData() as $key => $val) {
                    $this->addColumn(
                        $key,
                        [
                            'header' => __($key),
                            'index' => $key,
                            'filter'    => false,
                            'sortable'  => false,
                            'totals_label' => __('Total'),
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
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_resource = $this->_objectManager->get("\Magento\Framework\App\ResourceConnection");
        $this->connection = $this->_resource->getConnection('core_write');
        return $this->connection;
    }
}
