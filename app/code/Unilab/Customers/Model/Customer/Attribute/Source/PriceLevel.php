<?php

namespace Unilab\Customers\Model\Customer\Attribute\Source;

class PriceLevel extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{ 
    
    protected $resourceConnection;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    public function getAllOptions()
    {

        $connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $result = $connection->query("SELECT * FROM ".\Unilab\Customers\Controller\Adminhtml\Customer\SubmitImport::TABLE_PREFIX."_pricelevelmaster");
        
        $options = [];
        $options[] = [
            'value' => '',
            'label' => __(' ')
        ];

        while ($items = $result->fetch()) {
            $options[] = array(
                'value' => $items['id'],
                'label' => __($items['price_level_id']),
            );
        }

        $this->_options = $options;

        return $this->_options;
    }
}