<?php

namespace Unilab\Benefits\Model\Source;

class PurchaseCapName implements \Magento\Framework\Option\ArrayInterface
{
    //Here you can __construct Model

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->_resourceConnection = $resourceConnection;
    }

    public function toOptionArray() 
    {
        $connectdb = $this->_resourceConnection->getConnection('core_read');

        $readresult = $connectdb->query("SELECT * FROM rra_emp_purchasecap");

        while ($items = $readresult->fetch() ) {                    
        
                $transactiontype[] = array(
                                'value'     => $items['id'],
                                'label'     => __($items['purchase_cap_des']),
                            );      
        }   
        
        return $transactiontype;
    }
}