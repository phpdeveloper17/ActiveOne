<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Pricelist\Model\ResourceModel\Grid;

class Collection extends \Magento\CatalogRule\Model\ResourceModel\Rule\Collection
{
    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();

        $this->getSelect()
            ->joinLeft(
                ['secondTable' => $this->getTable('catalogrule_customer_group')],
                'main_table.rule_id = secondTable.rule_id',
                [''])
            ->joinLeft(
                ['customer_group_tbl' => $this->getTable('customer_group')],
                'secondTable.customer_group_id = customer_group_tbl.customer_group_id',
                ['customer_group_tbl.customer_group_code'])
            ->joinLeft(
                ['rra_pricelevelmaster_tbl' => $this->getTable('rra_pricelevelmaster')],
                'main_table.price_level_id = rra_pricelevelmaster_tbl.id',
                ['rra_pricelevelmaster_tbl.price_level_id as price_level']
            );
        $this->addFilterToMap('price_level', 'rra_pricelevelmaster_tbl.price_level_id');
        $this->addFilterToMap('is_active', 'main_table.is_active');
        $this->addFilterToMap('rule_id', 'main_table.rule_id');
       
        return $this;
    }
   

}
