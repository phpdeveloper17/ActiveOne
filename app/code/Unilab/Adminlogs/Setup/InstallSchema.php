<?php

namespace Unilab\Adminlogs\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
	
	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (!$installer->tableExists('unilab_adminlogs')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('unilab_adminlogs')
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
					'logdate',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					100,
					['nullable' => true],
					'Log Date'
				)
				->addColumn(
					'username',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					100,
					['nullable' => false],
					'Username'
				)
				->addColumn(
					'fullname',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					100,
					[],
					'Name'
				)
				->addColumn(
					'fullname',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					100,
					[],
					'Name'
				)
				->addColumn(
					'ipaddress',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					100,
					[],
					'Ip Address'
				)
				->addColumn(
					'activity',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					100,
					[],
					'Activity'
				)
				->addColumn(
					'status',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					50,
					[],
					'Status'
				)
				
                ->setComment('Adminlogs')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
			$installer->getConnection()->createTable($table);
		}
		$installer->endSetup();
	}
}