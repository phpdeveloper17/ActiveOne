<?php

namespace Unilab\Pricelist\Model\Source;

use Magento\Framework\App\ResourceConnection;
class Pricelevel implements \Magento\Framework\Option\ArrayInterface{

    protected $connection;
    protected $_resource;

    public function __construct(
        ResourceConnection $resource
    ) {
        $this->_resource = $resource;
    }
    public function getAllOptions()
    {

		$connection = $this->getConnection();
		
		$connection->beginTransaction();
		
		//Search agree terms from customer_entity_varchar
		$selectPricelevel 	=	$connection->select()->from('rra_pricelevelmaster', array('*')); 
		
		$priceLV 			=	$connection->fetchAll($selectPricelevel);
		
		$priceLevel[] = array('value'=> 0,'label'=> __(''));
		
		foreach($priceLV as $val):
		
			
			$priceLevel[] = array('value'=> $val['id'],'label'=> __($val['price_level_id']));
				
					
		endforeach;
		
        $connection->commit();
					
		return $priceLevel;
    }
	
	
	public function toOptionArray()
    {

        return $this->getAllOptions();

    }
    public function getPricelevelBId($id){
        $priceLV = array();
        $connection = $this->getConnection();
        $connection->beginTransaction();
        $selectPricelevel 	=	$connection->select()
                                            ->from('rra_pricelevelmaster', array('*'))
                                            ->where('id='.$id); 
		$priceLV 			=	$connection->fetchRow($selectPricelevel);
		$connection->commit();
		return $priceLV;		
    }
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resource->getConnection('core_write');
        }
        return $this->connection;
    }
}
