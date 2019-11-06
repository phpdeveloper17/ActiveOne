<?php

namespace Unilab\Pricelist\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $catalogruleTable = 'catalogrule';

        //Setup two columns for quote, quote_address and order
        //Quote address tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($catalogruleTable),
                'price_level_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' =>'11',
                    'default' => 0,
                    'nullable' => true,
                    'comment' =>'Price Level Id',
                    'after' => 'description'
                ]
        );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($catalogruleTable),
                'price_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' =>'255',
                    'default' => '',
                    'nullable' => true,
                    'comment' =>'Price Code',
                    'after' => 'price_level_id'
                ]
        );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($catalogruleTable),
                'prod_sku',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' =>'35',
                    'default' => '',
                    'nullable' => true,
                    'comment' =>'Prod Code',
                    'after' => 'price_code'
                ]
        );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($catalogruleTable),
                'limit_days',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' =>'255',
                    'default' => '',
                    'nullable' => true,
                    'comment' =>'Limited Days',
                    'after' => 'is_active'
                ]
        );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($catalogruleTable),
                'limit_time_from',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' =>'50',
                    'default' => '',
                    'nullable' => true,
                    'comment' =>'Limited Time From',
                    'after' => 'limit_days'
                ]
        );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($catalogruleTable),
                'limit_time_to',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' =>'50',
                    'default' => '',
                    'nullable' => true,
                    'comment' =>'Limited Time To',
                    'after' => 'limit_time_from'
                ]
        );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($catalogruleTable),
                'from_qty',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' =>'11',
                    'default' => 0,
                    'nullable' => true,
                    'comment' =>'From Qty',
                    'after' => 'limit_time_to'
                ]
        );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($catalogruleTable),
                'to_qty',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' =>'11',
                    'default' => 0,
                    'nullable' => true,
                    'comment' =>'To Qty',
                    'after' => 'from_qty'
                ]
        );

        $setup->endSetup();
    }
}
