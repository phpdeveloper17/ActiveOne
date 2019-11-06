<?php

namespace Unilab\Customers\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup,
                            ModuleContextInterface $context){
        $setup->startSetup();
        

        // Get module table
        $tableName = $setup->getTable('customer_entity');

        // Check if the table already exists
        if ($setup->getConnection()->tableColumnExists($tableName, 'account_id') === false) {
            // Declare data
            $columns = [
                'account_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'Account ID',
                ],
            ];

            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }

        }
    

        $setup->endSetup();
    }
}