<?php
/**
 * Grid Schema Setup.
 * @category  Unilab
 * @package   Unilab_Grid
 * @author    Unilab
 * @copyright Copyright (c) 2010-2016 Unilab Software Private Limited (https://Unilab.com)
 * @license   https://store.Unilab.com/license.html
 */

namespace Unilab\Benefits\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();

        /*
         * Create table 'wk_grid_records'
         */

        $table = $installer->getConnection()->newTable(
            $installer->getTable('rra_emp_benefits')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Grid Record Id'
        )->addColumn(
            'emp_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => NULL],
            'Employee Id'
        )->addColumn(
            'group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Group Id'
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            'Group Id'
        )->addColumn(
            'emp_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Employee Name'
        )->addColumn(
            'purchase_cap_limit',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            11,
            ['nullable' => false],
            'Purchase Cap Limit'
        )->addColumn(
            'purchase_cap_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Purchase Cap Id'
        )->addColumn(
            'consumed',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            null,
            ['nullable' => false, 'length' => '12,2', 'default' => 0.00],
            'Consumed'
        )->addColumn(
            'available',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            null,
            ['nullable' => false, 'length' => '12,2', 'default' => 0.00],
            'Available'
        )->addColumn(
            'extension',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            null,
            ['nullable' => false, 'length' => '12,2', 'default' => 0.00],
            'Extension'
        )->addColumn(
            'refresh_period',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            11,
            ['nullable' => false],
            'Refresh Period'
        )->addColumn(
            'start_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'Start Date'
        )->addColumn(
            'refresh_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'Refresh Date'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            11,
            [],
            'Active Status'
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
            'uploadedby',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Uploaded By'
        )->addColumn(
            'date_uploaded',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
            ],
            'Date Uploaded'
        )->addIndex(
            $installer->getIdxName(
                'rra_emp_benefits',
                ['entity_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
            ),
            ['entity_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX]
        )->setComment(
            'Row Data Table'
        );

        $installer->getConnection()->createTable($table);

        $table1 = $installer->getConnection()->newTable(
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

            $installer->getConnection()->createTable($table1);

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

            //rra_pricelevelmaster
            if (!$installer->tableExists('rra_pricelevelmaster')) {
                $rra_pricelevelmaster_tbl = $installer->getConnection()->newTable(
                    $installer->getTable('rra_pricelevelmaster')
                )
                    ->addColumn(
                        'id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        11,
                        [
                            'auto_increment' => true,
                            'nullable' => false,
                            'primary' => true,
                        ],
                        'ID'
                    )
                    ->addColumn(
                        'price_name',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        '100',
                        ['nullable' => true],
                        'Price Name'
                    )
                    
                    ->addColumn(
                        'price_level_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        '100',
                        [],
                        'Price Level ID'
                    )
                    ->addColumn(
                        'is_active',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        '50',
                        [],
                        'Active'
                    )
                    ->addColumn(
                        'memo',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        '255',
                        ['nullable' => true],
                        'Memo'
                    )
                    ->addColumn(
                        'prefix',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        '8',
                        ['nullable' => true],
                        'Prefix'
                    )
                    ->addColumn(
                        'created_time',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                        null,
                        ['nullable' => false],
                        'Created Time'
                    )->addColumn(
                        'update_time',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                        null,
                        ['nullable' => false],
                        'Modification Time'
                    )
                    
                    ->setComment('Price Level Master Table')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
                $installer->getConnection()->createTable($rra_pricelevelmaster_tbl);
            }
         
            //rra_pricelistproduct
            if (!$installer->tableExists('rra_pricelistproduct')) {
                $rra_pricelistproduct_tbl = $installer->getConnection()->newTable(
                    $installer->getTable('rra_pricelistproduct')
                )
                    ->addColumn(
                        'pricelist_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        16,
                        ['nullable' => true],
                        'Price List ID'
                    )
                    ->addColumn(
                        'product_sku',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        16,
                        ['nullable' => true],
                        'Product Sku'
                    )
                    ->addColumn(
                        'product_name',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        '255',
                        ['nullable' => true],
                        'Product Name'
                    )
                    ->addColumn(
                        'qty_from',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        '11',
                        ['nullable' => true],
                        'QTY From'
                    )
                    ->addColumn(
                        'qty_to',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        '11',
                        ['nullable' => true],
                        'QTY To'
                    )
                    ->addColumn(
                        'unit_price',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        '11,2',
                        ['nullable' => true],
                        'Unit Price'
                    )
                    ->addColumn(
                        'discount_in_amount',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        '11,2',
                        ['nullable' => true],
                        'Discount Amount'
                    )
                    ->addColumn(
                        'discount_in_percent',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        '11,2',
                        ['nullable' => true],
                        'Discount Percent'
                    )
                    ->addColumn(
                        'from_date',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                        null,
                        ['nullable' => true],
                        'From Date'
                    )
                    
                    ->addColumn(
                        'to_date',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                        null,
                        ['nullable' => true],
                        'To Date'
                    )
                    ->addColumn(
                        'uploaded_by',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        '100',
                        [],
                        'Uploaded By'
                    )
                    ->addColumn(
                        'id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        11,
                        [
                            'auto_increment' => true,
                            'nullable' => false,
                            'primary' => true,
                        ],
                        'ID'
                    )
                    ->setComment('Pricelist Product Table')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
                $installer->getConnection()->createTable($rra_pricelistproduct_tbl);
            }

        $installer->endSetup();
    }
}
