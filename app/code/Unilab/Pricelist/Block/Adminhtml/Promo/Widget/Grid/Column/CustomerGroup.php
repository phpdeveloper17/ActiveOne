<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Pricelist\Block\Adminhtml\Promo\Widget\Grid\Column;

/**
 * Grid column block that is displayed only in multistore mode
 *
 * @api
 * @deprecated 100.2.0 in favour of UI component implementation
 * @since 100.0.2
 */
class CustomerGroup extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders grid column
     *
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase|string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $id = $row->getData('rule_id');
        $customer_group = $this->getCustomerGroupCatalogRule($id);
        if(empty($customer_group)){
            return '';
        }
        return @$customer_group['customer_groups'];
    }
    public function getCustomerGroupCatalogRule($id){
        $connection = $this->_getConnection();
        $query = "SELECT group_concat(customer_group_code) as customer_groups FROM catalogrule_customer_group ccg
        LEFT JOIN customer_group cg ON ccg.customer_group_id=cg.customer_group_id
        WHERE ccg.rule_id= ". $id;
        return $connection->fetchRow($query);
    }
    protected function _getConnection()
    {
        $this->_resource = $this->_objectManager->get("\Magento\Framework\App\ResourceConnection");
        $this->connection = $this->_resource->getConnection('core_write');
        return $this->connection;
    }
}