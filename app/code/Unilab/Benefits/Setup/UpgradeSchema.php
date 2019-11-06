<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Unilab\Benefits\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.3', '<')) 
        {
            /** 
             * Company Branch Table
            */
          
            $installer = $setup;

            $installer->startSetup();

            /*
             * Create table 'rra_company_branches'
             */

            $table = $installer->getConnection()->newTable(
                $installer->getTable('rra_company_branches')
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'company_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Company Id'
            )->addColumn(
                'contact_person',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Contact Person'
            )->addColumn(
                'contact_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                16,
                ['nullable' => false],
                'Contact Number'
            )->addColumn(
                'branch_address',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Branch Address'
            )->addColumn(
                'branch_province',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Branch Province'
            )->addColumn(
                'branch_city',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Branch City'
            )->addColumn(
                'branch_postcode',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Branch Postcode'
            )->addColumn(
                'shipping_address',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                2,
                ['nullable' => false],
                'Shipping Address'
            )->addColumn(
                'billing_address',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                2,
                ['nullable' => false],
                'Billing Address'
            )->addColumn(
                'created_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Creation Time'
            )->addColumn(
                'update_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Modification Time'
            )->addColumn(
                'ship_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => false],
                'Ship Code'
            );

            $installer->getConnection()->createTable($table);

            //rra_tender_type
            $table2 = $installer->getConnection()->newTable(
                $installer->getTable('rra_tender_type')
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'tender_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Tender name'
            )->addColumn(
                'payment_method_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Payment Method'
            )->addColumn(
                'created_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Creation Time'
            )->addColumn(
                'update_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Modification Time'
            );

            $installer->getConnection()->createTable($table2);

            //rra_transaction_type
            $table3 = $installer->getConnection()->newTable(
                $installer->getTable('rra_transaction_type')
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'transaction_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Transaction name'
            )->addColumn(
                'tender_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Tender Type'
            )->addColumn(
                'tax_class',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Tax Class'
            )->addColumn(
                'created_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Creation Time'
            )->addColumn(
                'update_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Modification Time'
            )->addIndex(
                $installer->getIdxName(
                    'rra_transaction_type',
                    ['tender_type'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['tender_type'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX]
            );
            $installer->getConnection()->createTable($table3);

            //rra_emp_purchasecap
            $table4 = $installer->getConnection()->newTable(
                $installer->getTable('rra_emp_purchasecap')
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'purchase_cap_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Purchase Cap Id'
            )->addColumn(
                'purchase_cap_des',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Purchase Cap Description'
            )->addColumn(
                'tnx_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'TNX Id'
            )->addColumn(
                'created_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Creation Time'
            )->addColumn(
                'update_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Modification Time'
            );
            $installer->getConnection()->createTable($table4);
            
            $installer->endSetup();
        }
    }
}