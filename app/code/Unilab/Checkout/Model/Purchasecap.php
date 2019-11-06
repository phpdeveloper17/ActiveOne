<?php

namespace Unilab\Checkout\Model;

class Purchasecap extends \Magento\Framework\Model\AbstractModel
{
    const SCOPE_STORE = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->resource = $resource;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
    }

    public function update($order) 
    {
        $connection     = $this->getConnection();
        $customer       = $this->customerSession->getCustomer();
        $employeeId     = $customer->getEmployeeId();

        $sqlBenefits    = $connection->select()
                                   ->from('rra_emp_benefits', array('consumed', 'available'))
                                   ->where('is_active=?',1)
                                   ->where('entity_id=?', $customer->getId());
        $benefits       = $connection->fetchRow($sqlBenefits);
        $grandtotal     = $order->getGrandTotal();
		
        // $grandtotal     = $order->getGrandTotal();
        $available      = $benefits['available'];
        $consumed       = $benefits['consumed'];
		
            if($grandtotal > $available ):
                
                //$availExt     =   $available + $extension;
                $curconsumed    =   $consumed + $grandtotal;

                $fields                     = array();
                $fields['available']        = 0;
                $fields['extension']        = 0;
                $fields['consumed']         = $curconsumed;
                $fields['update_time']      = date("Y/m/d H:i:s");
                $where                      = array();
				file_put_contents('./pcapdebug.txt', print_r($fields,1).PHP_EOL,FILE_APPEND);
                $where[]                    = $connection->quoteInto('is_active=?',1);
                $where[]                    = $connection->quoteInto('entity_id =?', $customer->getId());
                $connection->update('rra_emp_benefits', $fields, $where);

            else:
            
                $curval                     = $available - $grandtotal;
                $curconsumed                = $consumed + $grandtotal;

                $fields                     = array();
                $fields['available']        = $curval;
                $fields['consumed']         = $curconsumed;
                $fields['update_time']      = date("Y/m/d H:i:s");
                $where                      = array();
				file_put_contents('./pcapdebug.txt', print_r($fields,1).PHP_EOL,FILE_APPEND);
                $where[]                    = $connection->quoteInto('is_active=?',1);
                $where[]                    = $connection->quoteInto('entity_id =?', $customer->getId());
                $connection->update('rra_emp_benefits', $fields, $where);
        
            endif;
        return true;
    }
 
    protected function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, self::SCOPE_STORE);
    }

    protected function getConnection()
    {
        return $this->resource->getConnection();
    }
}