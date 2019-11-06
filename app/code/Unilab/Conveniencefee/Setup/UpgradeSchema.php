<?php

namespace Unilab\Conveniencefee\Setup;

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

        $quoteAddressTable = 'quote_address';
        $quoteTable = 'quote';
        $orderTable = 'sales_order';
        $invoiceTable = 'sales_invoice';
        $creditmemoTable = 'sales_creditmemo';

        //Setup two columns for quote, quote_address and order
        //Quote address tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteAddressTable),
                'conveniencefee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' =>'10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Convenience Fee'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteAddressTable),
                'base_conveniencefee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' =>'10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Base Convenience Fee'
                ]
            );
       
        // Quote tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteTable),
                'conveniencefee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' =>'10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Convenience Fee'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteTable),
                'base_conveniencefee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' =>'10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Base Convenience Fee'
                ]
            );

        //Order tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'conveniencefee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' =>'10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Convenience Fee'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'base_conveniencefee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' =>'10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Base Convenience Fee'
                ]
            );

        // //Invoice tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($invoiceTable),
                'conveniencefee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' =>'10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Convenience Fee'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($invoiceTable),
                'base_conveniencefee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' =>'10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Base Convenience Fee'
                ]
            );
        
        // //Credit memo tables
         $setup->getConnection()
            ->addColumn(
                $setup->getTable($creditmemoTable),
                'conveniencefee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' =>'10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Convenience Fee'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($creditmemoTable),
                'base_conveniencefee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' =>'10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Base Convenience Fee'
                ]
            );
        $setup->endSetup();
    }
}
