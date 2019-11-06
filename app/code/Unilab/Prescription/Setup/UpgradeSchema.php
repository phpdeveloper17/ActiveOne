<?php 

namespace Unilab\Prescription\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            $connection->addColumn(
            $installer->getTable('sales_order_item'),
               'prescription_id',
               [
                   'type' => Table::TYPE_INTEGER,
                   'length' => 11,
                   'nullable' => true,
                   'default' => null,
                   'comment' => 'Prescription'
               ]
           );
        }
         $installer->endSetup();
    }
}