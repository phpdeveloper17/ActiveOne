<?php

namespace Unilab\Customers\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class PrebookStatus
 */
class Company implements OptionSourceInterface
{
    protected $resourceConnection;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $connectdb = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

        $readresult = $connectdb->query("SELECT customer_group_id,customer_group_code FROM customer_group");

        while ($items = $readresult->fetch() ) {
                $companyitem[] = array(
                                'value'     => $items['customer_group_id'],
                                'label'      => __($items['customer_group_code']),
                            );
        }   
        
        return $companyitem;
    }
}